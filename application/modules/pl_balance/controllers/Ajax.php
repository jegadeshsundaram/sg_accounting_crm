<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->logged_id = $this->session->user_id;
        $this->load->model('pl_balance/pl_balance_model', 'pl_balance');
    }

    public function generate_pl_statment()
    {
        is_ajax();
        $this->body_file = 'pl_balance/blank.php';
        $this->header_file = 'pl_balance/blank.php';
        $this->footer_file = 'pl_balance/blank.php';

        $post = $this->input->post();

        if ($post) {
            $from_date = date('Y-m-d', strtotime($post['date-from']));
            $to_date = date('Y-m-d', strtotime($post['date-to']));
            $closing_stock_amount = $post['amount'];

            // Total Sales - S0 Series Items
            // SALES (S0) Logic
            // SALES ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE) (SALES REVERSAL)
            // SALES ACCOUNT ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT SALES OR DEBIT SALES
            // CREDIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
            // DEBIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

            $sql_S0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'S0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_S0 = $this->db->query($sql_S0);
            $S0_data = $query_S0->result();
            $total_sales = 0;
            foreach ($S0_data as $key => $value) {
                $sales_item_amount = (-1) * $value->account_total_amount;
                $total_sales += $sales_item_amount;
            }

            // OPENING STOCK - CA002 - Control Account. It should not change and can not be used for any Other Purpose.
            $sql_OS = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn = 'CA002' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_OS = $this->db->query($sql_OS);
            $OS_data = $query_OS->result();

            $opening_stock = 0;
            foreach ($OS_data as $key => $value) {
                $opening_stock += $value->account_total_amount;
            }

            // COST C0 Items
            // COST (C0) Logic
            // COST ITEMS ARE ALWAYS DEBIT (POSITIVE) BUT SOMETIMES IT MAY BE CREDIT (NEGATIVE)
            // DEBIT COST ITEM - THE AMOUNT WILL BE DISPLAYED AS IT IS AS POSITIVE NUMBER
            // CREDIT COST ITEM - THE AMOUNT HAS TO BE MULTIPLIED BY -1 AND DISPLAY INSIDE BRACKETS

            $sql_C0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'C0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_C0 = $this->db->query($sql_C0);
            $C0_data = $query_C0->result();
            $C0_account_total = 0;
            foreach ($C0_data as $key => $value) {
                $C0_account_total += $value->account_total_amount;
            }
            $opening_stock_with_C0_total = $opening_stock + $C0_account_total;

            // COST OF SALES
            $cost_of_sales = $opening_stock_with_C0_total - $closing_stock_amount;

            // Gross Margin
            $gross_margin = $total_sales - $cost_of_sales;

            // Total Other Income
            // I0 ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE)
            // I0 ITEMS ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT OR DEBIT AMOUNT
            // CREDIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
            // DEBIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

            $sql_I0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'I0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_I0 = $this->db->query($sql_I0);
            $I0_data = $query_I0->result();
            $total_other_income = 0;
            foreach ($I0_data as $key => $value) {
                $i0_item_amount = (-1) * $value->account_total_amount;
                $total_other_income += $i0_item_amount;
            }

            // TOTAL INCOME
            $total_income = 0;
            if ($gross_margin < 0 && $total_other_income > 0) {
                $total_income = $gross_margin + $total_other_income;
            } elseif ($total_other_income < 0 && $gross_margin > 0) {
                $total_income = $total_other_income + $gross_margin;
            } else {
                $total_income = $gross_margin + $total_other_income;
            }

            // Total Expenses
            $sql_E0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'E0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_E0 = $this->db->query($sql_E0);
            $E0_data = $query_E0->result();
            $total_expenses = 0;
            foreach ($E0_data as $key => $value) {
                $total_expenses += $value->account_total_amount;
            }

            // OPERATIONAL NET PROFIT / (LOSS) BEFORE TAX = TOTAL INCOME - TOTAL EXPENSES
            $net_profit_before_tax = $total_income - $total_expenses;

            // Income Tax - T0 Series
            $sql_T0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'T0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_T0 = $this->db->query($sql_T0);
            $T0_data = $query_T0->result();
            $income_tax = 0;
            foreach ($T0_data as $key => $value) {
                $income_tax += $value->account_total_amount;
            }

            // NET PROFIT / (LOSS) AFTER TAX = NET PROFIT / (LOSS) BEFORE TAX - T0 Series Total Amount (Income Tax)
            $net_profit_after_tax = 0;
            $net_profit_after_tax = $net_profit_before_tax - $income_tax;

            // Extra Ordinary Items
            $sql_X0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'X0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_X0 = $this->db->query($sql_X0);
            $X0_data = $query_X0->result();
            $ex_ordinary = 0;
            foreach ($X0_data as $key => $value) {
                $ex_ordinary += $value->account_total_amount;
            }

            // NET PROFIT / (LOSS) AFTER EXTRAORDINARY ITEMS
            $net_profit_after_Exo = $net_profit_after_tax - $ex_ordinary;

            // Update PL_DATA TABLE
            $where = ['pl_type' => 'PL'];
            $result = $this->custom->updateRow('pl_data', ['start_date' => $from_date, 'end_date' => $to_date, 'closing_stock' => $closing_stock_amount, 'current_pl' => $net_profit_after_Exo], $where);

            $data['current_profit_after_pl'] = number_format($net_profit_after_Exo, 2);
            echo json_encode($data);
        } else {
            redirect('/pl_balance');
        }
    }
}
