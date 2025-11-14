<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pl_balance extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pl_balance/pl_balance_model', 'pl_balance');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $pl_data = $this->custom->getSingleRow('pl_data', ['pl_type' => 'PL']);

        $this->body_vars['start_date'] = $start_date = date('d-m-Y', strtotime($pl_data->start_date));
        $this->body_vars['end_date'] = $end_date = date('d-m-Y', strtotime($pl_data->end_date));
        $this->body_vars['closing_stock'] = $closing_stock = $pl_data->closing_stock;

        $this->body_file = 'pl_balance.php';
    }

    public function print()
    {
        $post = $this->input->post();

        if ($post) {
            $from_date = date('Y-m-d', strtotime($post['date-from']));
            $to_date = date('Y-m-d', strtotime($post['date-to']));
            $closing_stock_amount = $post['amount'];

            $pl_tbl_data['start_date'] = $from_date;
            $pl_tbl_data['end_date'] = $to_date;
            $pl_tbl_data['closing_stock'] = $closing_stock_amount;
            $pl_entry = $this->custom->getCount('pl_data', ['pl_type' => 'PL']);
            if ($pl_entry == 0) {
                $pl_tbl_data['pl_type'] = 'PL';
                $inserted = $this->custom->insertRow('pl_data', $pl_tbl_data);
            } else {
                $updated = $this->custom->updateRow('pl_data', $pl_tbl_data, ['pl_type' => 'PL']);
            }

            $html = '';

            $html .= '<style type="text/css">
					table { border-collapse: collapse; font-size: 11pt;}
					table th {
						background: #fff;
						padding: 6px 10px;
						text-align: left;
					}
					table td {
						padding: 3px 10px;
						text-align: left;
						border: 1px solid #f5f5f5;
					}
					.special td {
                        padding: 6px 10px;
						font-weight: bold;
						text-align: left;
						border-top: 1px solid #f5f5f5;
						border-bottom: 1px solid #f5f5f5;
					}
				</style>';

            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td></tr></table><br />';

            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;"><h4>PROFIT & LOSS STATEMENT</h4></td></tr></table>';
            $html .= '<br /><table style="border: none; width: 100%; border-bottom: 1px solid brown"><tr><td style="border: none; width: 50%"><strong>Period:</strong> '.$post['date-from'].' <strong><i>to</i></strong> '.$post['date-to'].'</td><td style="border: none; width: 50%; text-align: right"><strong>Date: </strong>'.date('d-m-Y').'</td></tr></table><br />';

            // ************************************************************************************************
            // 1. PROFIT & LOSS STATEMENT
            // ************************************************************************************************

            // Total Sales - S0 Series Items
            // SALES (S0) Logic
            // SALES ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE) (SALES REVERSAL)
            // SALES ACCOUNT ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT SALES OR DEBIT SALES
            // CREDIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
            // DEBIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

            $sql_S0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'S0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_S0 = $this->db->query($sql_S0);
            $S0_data = $query_S0->result();
            $html .= '<table style="width: 100%">';
            $total_sales = 0;
            foreach ($S0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $sales_item_amount = (-1) * $value->account_total_amount;

                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                if ($sales_item_amount < 0) {
                    $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $sales_item_amount, 2).')</td>';
                } else {
                    $html .= '<td style="width: 25%; text-align: right">'.number_format($sales_item_amount, 2).'</td>';
                }
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $total_sales += $sales_item_amount;
            }

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000">Total Sales</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($total_sales < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $total_sales, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($total_sales, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '</table>';

            // OPENING STOCK - CA002 - Control Account. It should not change and can not be used for any Other Purpose.
            $sql_OS = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn = 'CA002' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_OS = $this->db->query($sql_OS);
            $OS_data = $query_OS->result();
            $html .= '<table style="width: 100%">';
            $opening_stock = 0;
            $os = 0;
            foreach ($OS_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                if ($value->account_total_amount < 0) {
                    $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $value->account_total_amount, 2).')</td>';
                } else {
                    $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                }
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $opening_stock += $value->account_total_amount;
            }
            $html .= '</table>';

            // COST C0 Items
            // COST (C0) Logic
            // COST ITEMS ARE ALWAYS DEBIT (POSITIVE) BUT SOMETIMES IT MAY BE CREDIT (NEGATIVE)
            // DEBIT COST ITEM - THE AMOUNT WILL BE DISPLAYED AS IT IS AS POSITIVE NUMBER
            // CREDIT COST ITEM - THE AMOUNT HAS TO BE MULTIPLIED BY -1 AND DISPLAY INSIDE BRACKETS

            $sql_C0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'C0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_C0 = $this->db->query($sql_C0);
            $C0_data = $query_C0->result();
            $html .= '<table style="width: 100%">';
            $C0_account_total = 0;
            foreach ($C0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                if ($value->account_total_amount < 0) {
                    $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $value->account_total_amount, 2).')</td>';
                } else {
                    $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                }
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $C0_account_total += $value->account_total_amount;
            }

            $opening_stock_with_C0_total = $opening_stock + $C0_account_total;

            // Closing Stock
            $closing_stock_total = 0;
            if ($closing_stock_amount !== '' && $closing_stock_amount > 0) {
                $html .= '<tr>';
                $html .= '<td style="width: 50%;">Closing Stock</td>';
                $html .= '<td style="width: 25%; text-align: right">('.number_format($closing_stock_amount, 2).')</td>';
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $closing_stock_total += $closing_stock_amount;
            } else {
                $html .= '<tr>';
                $html .= '<td style="width: 50%;">Closing Stock</td>';
                $html .= '<td style="width: 25%; text-align: right">('.number_format($closing_stock_amount, 2).')</td>';
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
            }

            $html .= '</table>';
            // COST OF SALES
            $html .= '<table style="width: 100%">';
            $cost_of_sales = $opening_stock_with_C0_total - $closing_stock_total;
            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Cost Of Sales</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($cost_of_sales < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $cost_of_sales, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($cost_of_sales, 2).'</td>';
            }

            $html .= '</tr>';

            $html .= '</table>';

            // Gross Margin
            $html .= '<table style="width: 100%">';
            $gross_margin = 0;

            $gross_margin = $total_sales - $cost_of_sales;

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Gross Profit / (Loss)</strong></td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($gross_margin < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $gross_margin, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($gross_margin, 2).'</td>';
            }
            $html .= '</tr>';
            $html .= '</table>';

            // Total Other Income
            // I0 ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE)
            // I0 ITEMS ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT OR DEBIT AMOUNT
            // CREDIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
            // DEBIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

            $sql_I0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'I0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_I0 = $this->db->query($sql_I0);
            $I0_data = $query_I0->result();
            $html .= '<table style="width: 100%">';
            $total_other_income = 0;
            foreach ($I0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $i0_item_amount = (-1) * $value->account_total_amount;

                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                if ($i0_item_amount < 0) {
                    $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $i0_item_amount, 2).')</td>';
                } else {
                    $html .= '<td style="width: 25%; text-align: right">'.number_format($i0_item_amount, 2).'</td>';
                }
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $total_other_income += $i0_item_amount;
            }

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Total Other Income</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($total_other_income < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $total_other_income, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($total_other_income, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '</table>';

            // TOTAL INCOME
            $html .= '<table style="width: 100%">';
            $total_income = 0;

            if ($gross_margin < 0 && $total_other_income > 0) {
                $total_income = $gross_margin + $total_other_income;
            } elseif ($total_other_income < 0 && $gross_margin > 0) {
                $total_income = $total_other_income + $gross_margin;
            } else {
                $total_income = $gross_margin + $total_other_income;
            }

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Total Income</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($total_income < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $total_income, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($total_income, 2).'</td>';
            }
            $html .= '</tr>';
            $html .= '</table>';

            // Total Expenses
            $sql_E0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'E0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_E0 = $this->db->query($sql_E0);
            $E0_data = $query_E0->result();
            $html .= '<table style="width: 100%">';
            $total_expenses = 0;
            foreach ($E0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                // The accounts with 0 amount will not be displayed
                if ($value->account_total_amount == 0.00) {
                } else {
                    $html .= '<tr>';
                    $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                    if ($value->account_total_amount < 0) {
                        $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                    } else {
                        $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                    }
                    $html .= '<td style="width: 25%;"></td>';
                    $html .= '</tr>';
                    $total_expenses += $value->account_total_amount;
                }
            }

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Total Expenses</td>';
            $html .= '<td style="width: 25%;"></td>';
            $html .= '<td style="width: 25%; text-align: right">'.number_format($total_expenses, 2).'</td>';
            $html .= '</tr>';

            $html .= '</table>';

            // OPERATIONAL NET PROFIT / (LOSS) BEFORE TAX = TOTAL INCOME - TOTAL EXPENSES
            $html .= '<table style="width: 100%">';
            $net_profit_before_tax = 0;

            $net_profit_before_tax = $total_income - $total_expenses;

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Operational Net Profit / (Loss) Before Tax</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($net_profit_before_tax < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $net_profit_before_tax, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($net_profit_before_tax, 2).'</td>';
            }
            $html .= '</tr>';
            $html .= '</table>';

            // Income Tax - T0 Series
            $sql_T0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'T0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";

            $query_T0 = $this->db->query($sql_T0);
            $T0_data = $query_T0->result();
            $html .= '<table style="width: 100%">';
            $income_tax = 0;
            foreach ($T0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $income_tax += $value->account_total_amount;
            }
            $html .= '</table>';

            // NET PROFIT / (LOSS) AFTER TAX = NET PROFIT / (LOSS) BEFORE TAX - T0 Series Total Amount (Income Tax)
            $html .= '<table style="width: 100%">';
            $net_profit_after_tax = 0;

            $net_profit_after_tax = $net_profit_before_tax - $income_tax;

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Net Profit / (Loss) After Tax</td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($net_profit_after_tax < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $net_profit_after_tax, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($net_profit_after_tax, 2).'</td>';
            }
            $html .= '</tr>';
            $html .= '</table>';

            // Extra Ordinary Items
            $sql_X0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'X0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";

            $query_X0 = $this->db->query($sql_X0);
            $X0_data = $query_X0->result();
            $html .= '<table style="width: 100%">';
            $ex_ordinary = 0;
            foreach ($X0_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);
                $html .= '<tr>';
                $html .= '<td style="width: 50%;">'.ucwords(strtolower($coa_description)).'</td>';
                $html .= '<td style="width: 25%; text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                $html .= '<td style="width: 25%;"></td>';
                $html .= '</tr>';
                $ex_ordinary += $value->account_total_amount;
            }

            $html .= '</table>';

            // NET PROFIT / (LOSS) AFTER EXTRAORDINARY ITEMS
            $html .= '<table style="width: 100%">';
            $net_profit_after_Exo = 0;

            $net_profit_after_Exo = $net_profit_after_tax - $ex_ordinary;

            $html .= '<tr class="special">';
            $html .= '<td style="width: 50%; color: #000;">Net Profit / (Loss) After Extraordinary Items</strong></td>';
            $html .= '<td style="width: 25%;"></td>';
            if ($net_profit_after_Exo < 0) {
                $html .= '<td style="width: 25%; text-align: right">('.number_format((-1) * $net_profit_after_Exo, 2).')</td>';
            } else {
                $html .= '<td style="width: 25%; text-align: right">'.number_format($net_profit_after_Exo, 2).'</td>';
            }
            $html .= '</tr>';
            $html .= '</table>';

            // Update PL_DATA TABLE with Current Profit
            $where = ['pl_type' => 'PL'];
            $updated = $this->custom->updateRow('pl_data', ['current_pl' => $net_profit_after_Exo], $where);

            // ************************************************************************************************
            // 2. BALANCE SHEET
            // ************************************************************************************************
            $html .= '<div style="page-break-before: always;">';
            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td></tr></table> <br />';

            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;"><h3>BALANCE SHEET</h3></td></tr></table><br />';
            $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid brown"><tr><td style="border: none; width: 50%"><strong>Period:</strong> '.$post['date-from'].' <strong><i>to</i></strong> '.$post['date-to'].'</td><td style="border: none; width: 50%; text-align: right"><strong>Date:</strong> '.date('d-m-Y').'</td></tr></table>';

            // 1. FIXED ASSETS & DEPRECIATION
            $sql_FA = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'FA%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_FA = $this->db->query($sql_FA);
            $FA_data = $query_FA->result();

            $html .= '<br /><table style="width: 100%">';
            $html .= '<tr>';
            $html .= '<th valign="bottom">Fixed Assets</th>';
            $html .= '<th valign="bottom">Cost</th>';
            $html .= '<th valign="bottom">Accumulated Depreciation</th>';
            $html .= '<th valign="bottom" style="text-align: right; padding-left: 2px">Net-Book Value</th>';
            $html .= '</tr>';
            $fa_sub_total = 0;
            $pd_sub_total = 0;
            $net_book_total = 0;
            foreach ($FA_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);
                $pd_suffix = substr($value->accn, 2, 5);

                $sql_PD = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS pd_account_total_amount FROM gl WHERE accn = 'PD$pd_suffix' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
                $query_PD = $this->db->query($sql_PD);
                $PD_data = $query_PD->result();

                $pd_total = 0;
                foreach ($PD_data as $key => $pd_value) {
                    $pd_total = $pd_value->pd_account_total_amount;
                    $pd_accn = $pd_value->accn;
                }

                if ($pd_total < 0) {
                    $pd_total_display = '('.number_format((-1) * $pd_total, 2).')';
                    $net_book_value = $pd_total + $value->account_total_amount;
                } else {
                    $pd_total_display = '('.number_format($pd_total, 2).')';
                    $net_book_value = $value->account_total_amount - $pd_total;
                }

                $fa_sub_total += $value->account_total_amount;
                $pd_sub_total += $pd_total;
                $net_book_total += $net_book_value;

                $html .= '<tr>';
                $html .= '<td style="width: 40%;">'.ucwords(strtolower($coa_description)).'</td>';
                $html .= '<td style="width: 20%;">'.number_format($value->account_total_amount, 2).'</td>';
                $html .= '<td style="width: 20%;">'.$pd_total_display.'</td>';
                if ($net_book_value < 0) {
                    $html .= '<td style="width: 20%; text-align: right">('.number_format((-1) * $net_book_value, 2).')</td>';
                } else {
                    $html .= '<td style="width: 20%; text-align: right">'.number_format($net_book_value, 2).'</td>';
                }

                $html .= '</tr>';
            }

            $html .= '<tr class="special">';
            $html .= '<td style="border: none;"></td>';
            $html .= '<td>'.number_format($fa_sub_total, 2).'</td>';
            if ($pd_sub_total < 0) {
                $html .= '<td>('.number_format((-1) * $pd_sub_total, 2).')</td>';
            } else {
                $html .= '<td>('.number_format($pd_sub_total, 2).')</td>';
            }
            $html .= '<td style="text-align: right">'.number_format($net_book_total, 2).'</td>';
            $html .= '</tr>';

            // Intangible ASSETS
            $sql_IA = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'IA%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";

            $query_IA = $this->db->query($sql_IA);
            $IA_data = $query_IA->result();
            $intangible_assets_total = 0;
            $iat_header = true;
            foreach ($IA_data as $key => $value) {
                if ($iat_header) {
                    $html .= '<tr><td colspan="4" height="5" style="border: none;"></td></tr>';
                    $html .= '<tr>';
                    $html .= '<th>Intangible Assets</th>';
                    $html .= '<th></th>';
                    $html .= '<th></th>';
                    $html .= '<th></th>';
                    $html .= '</tr>';
                }
                $iat_header = false;

                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td>'.ucwords(strtolower($coa_description)).'</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td>'.number_format($value->account_total_amount, 2).'</td>';
                $html .= '</tr>';

                $intangible_assets_total += $value->account_total_amount;
            }

            $html .= '<tr><td colspan="4" height="5" style="border: none;"></td></tr>';

            // Current ASSETS
            $sql_CA = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'CA%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_CA = $this->db->query($sql_CA);
            $CA_data = $query_CA->result();
            $html .= '<tr>';
            $html .= '<th colspan="4">Current Assets</th>';
            $html .= '</tr>';
            $ca_sub_total = 0;
            $cs = 0;
            foreach ($CA_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                if ($value->accn == 'CA001') {
                    $sql_BadDebt = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn = 'CL002' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
                    $query_BadDebt = $this->db->query($sql_BadDebt);
                    $BadDebt_data = $query_BadDebt->result();

                    $pd_total = 0;
                    foreach ($BadDebt_data as $key => $bd_value) {
                        $pd_total += $bd_value->account_total_amount;
                    }

                    if ($pd_total < 0) {
                        $net_debtor = $pd_total + $value->account_total_amount;
                    } else {
                        $net_debtor = $value->account_total_amount - $pd_total;
                    }

                    $html .= '<tr>';
                    $html .= '<td>Net Debtor<br /><span style="font-size:10px;">Trade Debtors Control Less Provision for bad Debts</span></td>';
                    if ($net_debtor < 0) {
                        $html .= '<td colspan="3">('.number_format((-1) * $net_debtor, 2).')</td>';
                    } else {
                        $html .= '<td colspan="3">'.number_format($net_debtor, 2).'</td>';
                    }

                    $html .= '</tr>';

                    $ca_sub_total += $net_debtor;

                    // The accounts with 0 amount will not be displayed
                    if ($closing_stock_amount == 0.00) {
                    } else {
                        $html .= '<tr>';
                        $html .= '<td>Closing Stock</td>';
                        $html .= '<td colspan="3">'.number_format($closing_stock_amount, 2).'</td>';
                        $html .= '</tr>';

                        $ca_sub_total += $closing_stock_amount;
                    }
                }

                if ($value->accn == 'CA002' || $value->accn == 'CA001') {
                } else {
                    // The accounts with 0 amount will not be displayed
                    if ($value->account_total_amount == 0.00) {
                    } else {
                        $html .= '<tr>';
                        $html .= '<td>'.ucwords(strtolower($coa_description)).'</td>';
                        if ($value->account_total_amount < 0) {
                            $html .= '<td colspan="3">('.number_format((-1) * $value->account_total_amount, 2).')</td>';
                        } else {
                            $html .= '<td colspan="3">'.number_format($value->account_total_amount, 2).'</td>';
                        }
                        $html .= '</tr>';

                        $ca_sub_total += $value->account_total_amount;
                    }
                }
            }

            $html .= '<tr class="special">';
            $html .= '<td style="color: #000;" colspan="2">Total</td>';
            if ($ca_sub_total < 0) {
                $html .= '<td colspan="2">('.number_format((-1) * $ca_sub_total, 2).')</td>';
            } else {
                $html .= '<td colspan="2">'.number_format($ca_sub_total, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';

            // CL Series
            $sql_CL = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'CL%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
            $query_CL = $this->db->query($sql_CL);
            $CL_data = $query_CL->result();
            $html .= '<tr>';
            $html .= '<th colspan="4">Current Liabilities</th>';
            $html .= '</tr>';
            $cl_sub_total = 0;
            foreach ($CL_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                // CL002 : BAD Debts is already included in Current Assets Section
                if ($value->accn == 'CL002') {
                } else {
                    $html .= '<tr>';
                    $html .= '<td>'.ucwords(strtolower($coa_description)).'</td>';
                    if ($value->account_total_amount < 0) {
                        $html .= '<td colspan="3">'.number_format((-1) * $value->account_total_amount, 2).'</td>';
                    } else {
                        $html .= '<td colspan="3">('.number_format($value->account_total_amount, 2).')</td>';
                    }
                    $html .= '</tr>';
                    $cl_sub_total += $value->account_total_amount;
                }
            }

            $html .= '<tr class="special">';
            $html .= '<td style="color: #000;" colspan="2">Total</td>';
            if ($cl_sub_total < 0) {
                $html .= '<td colspan="2">'.number_format((-1) * $cl_sub_total, 2).'</td>';
            } else {
                $html .= '<td colspan="2">('.number_format($cl_sub_total, 2).')</td>';
            }
            $html .= '</tr>';

            $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';

            // WORKING CAPITAL = CURRENT ASSETS - CURRENT LIABILITIES

            /* NOT USED - Working Capital will just sum all the Current Assets and Liabilities
            if ($ca_sub_total > 0 && $cl_sub_total < 0) {
                $working_capital = $cl_sub_total + $ca_sub_total;
            } elseif ($ca_sub_total < 0 && $cl_sub_total < 0) {
                $working_capital = $cl_sub_total + ((-1) * $ca_sub_total);
            } elseif ($ca_sub_total < 0 && $cl_sub_total > 0) {
                $working_capital = ((-1) * $cl_sub_total) + ((-1) * $ca_sub_total);
            } else {
                $working_capital = $cl_sub_total + $ca_sub_total;
            }*/

            $working_capital = $ca_sub_total + $cl_sub_total;

            $html .= '<tr class="special">';
            $html .= '<td style="color: #000;" colspan="3">Working Capital</td>';
            if ($working_capital < 0) {
                $html .= '<td style="text-align: right">('.number_format((-1) * $working_capital, 2).')</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format($working_capital, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';

            // NET ASSETS = NET BOOK TOTAL + INTANGIBLE ASSETS + WORKING CAPITAL
            $net_assets = $net_book_total + $intangible_assets_total + $working_capital;
            $html .= '<tr class="special">';
            $html .= '<td style="color: #000;" colspan="3">Net Assets</td>';
            if ($net_assets < 0) {
                $html .= '<td style="text-align: right">('.number_format((-1) * $net_assets, 2).')</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format($net_assets, 2).'</td>';
            }
            $html .= '</tr>';

            // FINANCED BY (CREDIT) - All accounts under this section are CREDIT Values but there may be DEBIT values some times

            $html .= '<tr><td colspan="4" height="5" style="border: none;"></td></tr>';

            $html .= '<tr>';
            $html .= '<th colspan="4">Financed By:-</th>';
            $html .= '</tr>';

            // Account : PAID UP CAPITAL (PC001)
            $sql_pc = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn in ('PC001') and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn ORDER BY accn ASC";
            $query_pc = $this->db->query($sql_pc);
            $pc_data = $query_pc->result();
            $paid_capital_total = 0;
            foreach ($pc_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $pc_total = (-1) * $value->account_total_amount;

                $html .= '<tr>';
                $html .= '<td colspan="2">'.ucwords(strtolower($coa_description)).'</td>';
                if ($pc_total < 0) {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $pc_total, 2).')</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($pc_total, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
                $paid_capital_total += $pc_total;
            }

            // Account : RETAINED PROFITS (RP Series, Eg: RP001, RP002, RP003 ...)
            $sql_rp = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'RP%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn ORDER BY accn ASC";
            $query_rp = $this->db->query($sql_rp);
            $rp_data = $query_rp->result();
            $retained_profits_total = 0;
            $rp_entry = 0;
            foreach ($rp_data as $key => $value) {
                $retained_profits_total += (-1) * $value->account_total_amount;
                ++$rp_entry;
            }

            if ($rp_entry > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="2">Retained Profits</td>';
                if ($retained_profits_total < 0) {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $retained_profits_total, 2).')</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($retained_profits_total, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }

            // Account : CAPITAL RESERVES (CR001)
            $sql_cr = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn in ('CR001') and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn ORDER BY accn DESC";
            $query_cr = $this->db->query($sql_cr);
            $cr_data = $query_cr->result();
            $capital_reserves_total = 0;
            foreach ($cr_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td colspan="2">'.ucwords(strtolower($coa_description)).'</td>';
                if ($value->account_total_amount < 0) {
                    $html .= '<td style="text-align: right">'.number_format((-1) * $value->account_total_amount, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right">('.number_format($value->account_total_amount, 2).')</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
                $capital_reserves_total += (-1) * $value->account_total_amount;
            }

            // Account : DIVIDEND (D0 Series, Eg: D0001, D0002 ...)
            $sql_d0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'D0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn ORDER BY accn DESC";
            $query_d0 = $this->db->query($sql_d0);
            $d0_data = $query_d0->result();
            $dividend_total = 0;
            $d0_entry = 0;
            foreach ($d0_data as $key => $value) {
                $dividend_total += (-1) * $value->account_total_amount;
                ++$d0_entry;
            }

            if ($d0_entry > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="2">Dividends</td>';
                if ($dividend_total < 0) {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $dividend_total, 2).')</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($dividend_total, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }

            $net_equity_value = $paid_capital_total + $retained_profits_total + $capital_reserves_total + $dividend_total;

            // $net_profit_after_Exo = CURRENT PROFIT
            $html .= '<tr>';
            $html .= '<td colspan="2">Current Profit</td>';
            if ($net_profit_after_Exo < 0) {
                $html .= '<td style="text-align: right">('.number_format((-1) * $net_profit_after_Exo, 2).')</td>';
                $net_equity_value += $net_profit_after_Exo;
            } else {
                $html .= '<td style="text-align: right">'.number_format($net_profit_after_Exo, 2).'</td>';
                $net_equity_value += $net_profit_after_Exo;
            }
            $html .= '<td></td>';
            $html .= '</tr>';

            $html .= '<tr class="special">';
            $html .= '<td colspan="3" style="color: #000;">Net Equity Value</td>';
            if ($net_equity_value < 0) {
                $html .= '<td style="text-align: right">('.number_format((-1) * $net_equity_value, 2).')</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format($net_equity_value, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';

            // MT Series - NON EQUITY & NON CURRENT LOANS
            $sql_mt = "SELECT *, sum(total_amount) as account_total_amount FROM gl WHERE accn like 'MT%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn, sign";
            $query_mt = $this->db->query($sql_mt);
            $mt_data = $query_mt->result();
            $mt_total = 0;
            foreach ($mt_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td colspan="2">'.ucwords(strtolower($coa_description)).'</td>';
                if ($value->sign == '+') {
                    $html .= '<td style="text-align: right">('.number_format($value->account_total_amount, 2).')</td>';
                    $mt_total -= $value->account_total_amount;
                } else {
                    $html .= '<td style="text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                    $mt_total += $value->account_total_amount;
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }

            if ($mt_total !== 0) {
                $html .= '<tr class="special">';
                $html .= '<td colspan="2" style="color: #000;">Sub Total of MT Series</td>';
                if ($mt_total < 0) {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $mt_total, 2).')</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($mt_total, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }

            $html .= '<tr><td colspan="4" height="10" style="border: none"></td></tr>';

            // LT Series
            $sql_lt = "SELECT *, sum(total_amount) as account_total_amount FROM gl WHERE accn like 'LT%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn, sign";
            $query_lt = $this->db->query($sql_lt);
            $lt_data = $query_lt->result();
            $lt_total = 0;
            foreach ($lt_data as $key => $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td colspan="2">'.ucwords(strtolower($coa_description)).'</td>';

                if ($value->sign == '+') {
                    $html .= '<td style="text-align: right">('.number_format($value->account_total_amount, 2).')</td>';
                    $lt_total -= $value->account_total_amount;
                } else {
                    $html .= '<td style="text-align: right">'.number_format($value->account_total_amount, 2).'</td>';
                    $lt_total += $value->account_total_amount;
                }
                $html .= '<td></td>';
                $html .= '</tr>';
            }

            if ($lt_total !== 0) {
                $html .= '<tr class="special">';
                $html .= '<td colspan="2" style="color: #000;">Sub Total Of LT Series</td>';
                if ($mt_total < 0) {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $lt_total, 2).')</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($lt_total, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';
                $html .= '<tr><td colspan="4" height="10" style="border: none"></td></tr>';
            }

            $mt_lt_series_total = $mt_total + $lt_total;
            $net_equity_loan_total = $net_equity_value + $mt_lt_series_total;

            $html .= '<tr class="special">';
            $html .= '<td colspan="3" style="color: #000;">Net Equity & Non Current Loans</td>';
            if ($net_equity_loan_total < 0) {
                $html .= '<td style="text-align: right">('.number_format((-1) * $net_equity_loan_total, 2).')</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format($net_equity_loan_total, 2).'</td>';
            }
            $html .= '</tr>';

            $html .= '</table></div>';

            $file = 'pl_bs_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);

        } else {
            redirect('/general_ledger/reports');
        }
    }
}
