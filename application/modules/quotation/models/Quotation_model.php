<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
error_reporting(0);
class Quotation_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_billing_items($table, $where = null)
    {
        $this->db->select('*, LEFT(billing_description, 40) as stock_description')->from($table);
        if (!is_null($where)) {
            $this->db->where($where);
        }
        $this->db->order_by('stock_code', 'ASC');

        return $this->db->get();
    }

    public function get_latest_reference($text_prefix)
    {
        $query = $this->db->query("SELECT quotation_ref_no FROM `quotation_master` WHERE `quotation_ref_no` LIKE '$text_prefix%' ORDER BY quotation_ref_no DESC LIMIT 1");
        // print_r($this->db->last_query());
        $tbls = $query->result();

        return $tbls[0]->quotation_ref_no;
    }

    public function get_tbl_data($table, $where, $order_by)
    {
        $this->db->select('*')->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($order_by)) {
            $this->db->order_by($order_by, 'ASC');
        }

        return $this->db->get();
    }

    public function get_quotations($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
        $this->db->select('*')->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($join_table) && !is_null($join_condition)) {
            for ($i = 0; $i < count($join_table); ++$i) {
                $this->db->join($join_table[$i], $join_condition[$i]);
            }
        }

        $this->db->order_by($columns[1], 'DESC');

        $query = $this->db->get();

        // print_r($this->db->last_query());

        return $query->result();
    }

    public function get_quotation_items($row_id)
    {
        $this->db->select('*');
        $this->db->order_by('q_p_id');
        $query = $this->db->get_where('quotation_product_master', ['quotation_id' => $row_id]);

        return $query->result();
    }

    public function get_all_employee()
    {
        $query = $this->db->query('SELECT * FROM `master_employee`');
        $tbls = $query->result();
        foreach ($tbls as $tbl) {
            $arr[] = ['id' => $tbl->e_id, 'name' => $tbl->name.' ( '.$tbl->code.' ) '];
        }

        return $arr;
    }

    public function get_employee($id, $col)
    {
        $query = $this->db->query("SELECT * FROM `master_employee` WHERE `e_id` = $id LIMIT 1");
        $tbls = $query->result();

        return $tbls[0]->$col;
    }

    public function get_customer($id, $col)
    {
        $query = $this->db->query("SELECT $col FROM `master_customer` WHERE `customer_id` = $id LIMIT 1");
        $tbl = $query->result();
        if (count($tbl) == 0) {
            return $id;
        } else {
            return $tbl[0]->$col;
        }
    }

    public function get_summary_by_status($status)
    {
        $from = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $to = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));

        $employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : 'Company';
        if ($employee_id != 'Company') {
            $this->db->where('employee_id', $employee_id);
        }

        $this->db->where('status', $status);
        $this->db->where("(`modified_on` >= '$from' AND `modified_on` <= '$to')");

        $query = $this->db->get('quotation_master');

        $tbl = $query->result();
        if ($query->num_rows() == 0) {
            $result['total'] = 0;
            $result['sum'] = 0;
        } else {
            $result['total'] = count($tbl);
            $sum = 0;
            foreach ($tbl as $row) {
                $sum += $row->sub_total / $row->currency_rate;
            }
            $result['sum'] = round($sum, 2);
        }

        return $result;
    }

    public function get_detailed_by_status($status)
    {
        $from = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $to = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));
        $by = isset($_GET['employee_id']) ? $_GET['employee_id'] : 'Company';

        if ($by == 'Company') {
        } else {
            $this->db->where('employee_id', $_GET['employee_id']);
        }

        if ($status == 'all') {
        } else {
            $this->db->where('status', $status);
        }

        $this->db->where("(`modified_on` >= '$from' AND `modified_on` <= '$to')");

        $this->db->order_by('modified_on', 'ASC');

        $query = $this->db->get('quotation_master');

        // print_r($this->db->last_query());

        return $query->result();
    }

    public function updateUnitPrice($billing_id, $billing_price_per_uom)
    {
        $this->db->where('billing_id', $billing_id);
        $this->db->update('master_billing', ['billing_price_per_uom' => $billing_price_per_uom]);
        echo 'updated';
    }

    public function get_summary_analysis()
    {
        $result['Success'] = $this->get_summary_by_status('SUCCESSFUL');
        $result['Rejected'] = $this->get_summary_by_status('REJECTED');
        $result['Deleted'] = $this->get_summary_by_status('DELETED');
        $result['Pending'] = $this->get_summary_by_status('SUBMITTED');

        $total_quotation = $result['Success']['total'] + $result['Pending']['total'] + $result['Deleted']['total'] + $result['Rejected']['total'];
        $total_amount = $result['Success']['sum'] + $result['Pending']['sum'] + $result['Deleted']['sum'] + $result['Rejected']['sum'];
        // redirect($result['Success']['sum'].">>".$result['Pending']['sum']." >> ".$result['Deleted']['sum']." >> ".$result['Rejected']['sum']);

        $result['Success']['rate'] = 0;
        $result['Rejected']['rate'] = 0;
        $result['Deleted']['rate'] = 0;
        $result['Pending']['rate'] = 0;

        $result['Success']['rate_amount'] = 0;
        $result['Rejected']['rate_amount'] = 0;
        $result['Deleted']['rate_amount'] = 0;
        $result['Pending']['rate_amount'] = 0;

        if ($total_quotation != 0) {
            $result['Success']['rate'] = round(($result['Success']['total'] * 100) / $total_quotation, 2);
            $result['Rejected']['rate'] = round(($result['Rejected']['total'] * 100) / $total_quotation, 2);
            $result['Deleted']['rate'] = round(($result['Deleted']['total'] * 100) / $total_quotation, 2);
            $result['Pending']['rate'] = round(($result['Pending']['total'] * 100) / $total_quotation, 2);

            $result['Success']['rate_amount'] = round(($result['Success']['sum'] * 100) / $total_amount, 2);
            $result['Rejected']['rate_amount'] = round(($result['Rejected']['sum'] * 100) / $total_amount, 2);
            $result['Deleted']['rate_amount'] = round(($result['Deleted']['sum'] * 100) / $total_amount, 2);
            $result['Pending']['rate_amount'] = round(($result['Pending']['sum'] * 100) / $total_amount, 2);
        }

        $total_rate = $result['Success']['rate'] + $result['Pending']['rate'] + $result['Deleted']['rate'] + $result['Rejected']['rate'];
        $total_rate_amount = $result['Success']['rate_amount'] + $result['Pending']['rate_amount'] + $result['Deleted']['rate_amount'] + $result['Rejected']['rate_amount'];

        $total_rate_diff = 0;
        if ($total_rate > 100) {
            $total_rate_diff = 100 - $total_rate;
            $total_rate = 100;
        } elseif ($total_rate != 0 && $total_rate < 100) {
            $total_rate_diff = 100 - $total_rate;
            $total_rate = 100;
        }

        if ($result['Pending']['total'] != 0) {
            $result['Pending']['rate'] += $total_rate_diff;
        } else {
            $result['Pending']['rate'] = 0;
        }

        $total_rate_amount_diff = 0;
        if ($total_rate_amount > 100) {
            $total_rate_amount_diff = 100 - $total_rate_amount;
            $total_rate_amount = 100;
        } elseif ($total_rate_amount != 0 && $total_rate_amount < 100) {
            $total_rate_amount_diff = 100 - $total_rate_amount;
            $total_rate_amount = 100;
        }

        if ($result['Pending']['total'] != 0) {
            $result['Pending']['rate_amount'] += $total_rate_amount_diff;
        } else {
            $result['Pending']['rate_amount'] = 0;
        }

        $result['Total']['rate'] = $total_rate;
        $result['Total']['rate_amount'] = $total_rate_amount;
        $result['Total']['sum'] = number_format($total_amount, 2);
        $result['Total']['total'] = $total_quotation;

        return $result;
    }

    public function get_detailed_analysis()
    {
        $by = isset($_GET['employee_id']) ? $_GET['employee_id'] : 'Company';
        if ($by == 'Company') {
        } else {
            $by = $this->get_employee($by, 'name');
        }

        $success = $this->get_detailed_by_status('SUCCESSFUL');
        $reject = $this->get_detailed_by_status('REJECTED');
        $delete = $this->get_detailed_by_status('DELETED');
        $submitted = $this->get_detailed_by_status('SUBMITTED');

        $from = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $to = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));
        $employee = isset($_GET['employee_id']) ? $_GET['employee_id'] : 'Company';
        $one = '';

        if ($employee !== 'Company') {
            $employee = $this->get_employee($employee, 'name').' ( '.$this->get_employee($employee, 'code').' ) ';
            $one .= "<h3 align='center'>Detailed Quotation Analysis By Employee </h3>";
            $one .= "<h4 align='center'>$employee</h4>";
        } else {
            $one .= "<h3 align='center'>Detailed Quotation Analysis By Company </h3>";
        }

        $type = $by == 'Company' ? 'Company' : $by;
        $sno = 1;

        $one .= "
        <table width='100%'>
            <tr>
                <td style='border: none;'>
                    <strong>Period: </strong> <i>".date('d-m-Y', strtotime($from)).'</i> <strong>to</strong> <i>'.date('d-m-Y', strtotime($to))."</i>
                </td>
                <td style='border: none; text-align: right'>
                    <strong>Report Date: </strong>".date('d-m-Y').'
                </td>
            </tr>
        </table>
        <style>
            table { border-collapse: collapse;}
            table th {
                background: gainsboro;
                padding: 10px; text-align: left;
            }
            table td {
                border: 1px solid gainsboro;
                padding: 8px;
            }
            table th:last-child,
            table td:last-child,
            table th:nth-last-child(2),
            table td:nth-last-child(2) {
                text-align: right;
            }

            table tbody td:nth-last-child(-n+2) {
                text-align: right;
            }
        </style>';

        $one .= '<hr /><br /><h4>Total Successful</h4>';
        $one .= "
        <table style='width: 100%'>
          <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Customer</th>
            <th>FAMT Excl. GST</th>
            <th>SGD Excl. GST</th>
          </tr>
        </thead>
        <tbody>";

        foreach ($success as $tbl) {
            $sub = $tbl->net_after_lsd;
            if ($sub > 0 and $tbl->currency_rate > 0) {
                $i = $sub / $tbl->currency_rate;
            } else {
                $i = 0;
            }

            $one .= "<tr>
        <td style='width: 20px; text-align: center'> ".$sno." </td>
        <td style='width: 80px;'> ".date('d-m-Y', strtotime($tbl->modified_on))." </td>
        <td style='width: 100px;'> ".$tbl->quotation_ref_no." </td>
        <td style='width: 200px;'> ".$this->get_customer($tbl->customer_id, 'name').' ('.$this->get_customer($tbl->customer_id, 'code').") </td>
        <td style='width: 150px;'> ".$sub." </td>
        <td style='width: 150px;'> ".number_format($i, 2).' </td>
        </tr>';
            $sgd[] = $i;
            ++$sno;
        }

        $one .= '</tbody><tfoot>';
        if ($sno > 1) {
            $one .= "<tr>
                    <td colspan='5' align='right' style='color: red; font-weight: bold;'>Total</td>
                    <td style='font-weight: bold;'>$".number_format(array_sum($sgd), 2).'</td>
                </tr>';
        } else { // no records found
            $one .= '<tr><td colspan="6">No quotations found</td></tr>';
        }
        $one .= '</tfoot></table><br />';

        unset($sgd);
        $sno = 1;

        $two = '<h4>Total Rejected</h4>';

        $two .= "
          <table style='width: 100%'>
              <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Reference</th>
                <th>Customer</th>
                <th>FAMT Excl. GST</th>
                <th>SGD Excl. GST</th>
              </tr>
            </thead>
      <tbody>";

        foreach ($reject as $tbl) {
            $sub = $tbl->net_after_lsd;
            if ($sub > 0 and $tbl->currency_rate > 0) {
                $i = $sub / $tbl->currency_rate;
            } else {
                $i = 0;
            }

            $two .= "<tr>
        <td style='width: 20px; align: center'> ".$sno." </td>
        <td style='width: 80px;'> ".date('d-m-Y', strtotime($tbl->modified_on))." </td>
        <td style='width: 100px;'> ".$tbl->quotation_ref_no." </td>
        <td style='width: 200px;'> ".$this->get_customer($tbl->customer_id, 'name').'('.$this->get_customer($tbl->customer_id, 'code').") </td>
        <td style='width: 150px;'> ".$sub." </td>
        <td style='width: 150px;'> ".number_format($i, 2).' </td>
      </tr>';

            $sgd[] = $i;
            ++$sno;
        }

        $two .= '</tbody><tfoot>';
        if ($sno > 1) {
            $two .= "<tr>
                        <td colspan='5' align='right' style='color: red; font-weight: bold;'>Total</td>
                        <td style='font-weight: bold;'>$".number_format(array_sum($sgd), 2).'</td>
                    </tr>';
        } else { // no records found
            $two .= '<tr><td colspan="6">No quotations found</td></tr>';
        }
        $two .= '</tfoot></table><br />';

        unset($sgd);
        $sno = 1;

        $three = '<h4>Total Deleted</h4>';
        $three .= "
    <table style='width: 100%'>
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Reference</th>
          <th>Customer</th>
          <th>FAMT Excl. GST</th>
          <th>SGD Excl. GST</th>
        </tr>
      </thead>
    <tbody>";

        foreach ($delete as $tbl) {
            $sub = $tbl->net_after_lsd;
            if ($sub > 0 and $tbl->currency_rate > 0) {
                $i = $sub / $tbl->currency_rate;
            } else {
                $i = 0;
            }

            $three .= "<tr>
      <td style='width: 20px; align: center'> ".$sno." </td>
      <td style='width: 80px;'> ".date('d-m-Y', strtotime($tbl->modified_on))." </td>
      <td style='width: 100px'> ".$tbl->quotation_ref_no." </td>
      <td style='width: 200px'> ".$this->get_customer($tbl->customer_id, 'name').'('.$this->get_customer($tbl->customer_id, 'code').") </td>
      <td style='width: 150px'> ".$sub." </td>
      <td style='width: 150px'> ".number_format($i, 2).' </td>
      </tr>';
            $sgd[] = $i;
            ++$sno;
        }

        $three .= '</tbody><tfoot>';
        if ($sno > 1) {
            $three .= "<tr>
                            <td colspan='5' align='right' style='color: red; font-weight: bold;'>Total</td>
                            <td style='font-weight: bold;'>$".number_format(array_sum($sgd), 2).'</td>
                        </tr>';
        } else { // no records found
            $three .= '<tr><td colspan="6">No quotations found</td></tr>';
        }

        $three .= '</tfoot></table><br />';

        unset($sgd);
        $sno = 1;

        $four = '<h4>Total Pending</h4>';
        $four .= "<table style='width: 100%'>
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Reference</th>
          <th>Customer</th>
          <th>FAMT Excl. GST</th>
          <th>SGD Excl. GST</th>
        </tr>
      </thead>
    <tbody>";

        foreach ($submitted as $tbl) {
            $sub = $tbl->net_after_lsd;
            if ($sub > 0 and $tbl->currency_rate > 0) {
                $i = $sub / $tbl->currency_rate;
            } else {
                $i = 0;
            }

            $four .= "<tr>
        <td style='width: 20px; align: center'> ".$sno." </td>
        <td style='width: 80px;'> ".date('d-m-Y', strtotime($tbl->modified_on))." </td>
        <td style='width: 100px;'> ".$tbl->quotation_ref_no." </td>
        <td style='width: 200px;'> ".$this->get_customer($tbl->customer_id, 'name').'('.$this->get_customer($tbl->customer_id, 'code').") </td>
        <td style='width: 150px;'> ".$sub." </td>
        <td style='width: 150px;'> ".number_format($i, 2).' </td>
        </tr>';
            $sgd[] = $i;
            ++$sno;
        }

        $four .= '</tbody><tfoot>';
        if ($sno > 1) {
            $four .= "<tr>
                        <td colspan='5' align='right' style='color: red; font-weight: bold;'>Total</td>
                        <td style='font-weight: bold;'>$".number_format(array_sum($sgd), 2).'</td>
                    </tr>';
        } else { // no records found
            $four .= '<tr><td colspan="6">No quotations found</td></tr>';
        }
        $four .= '</tfoot></table><br />';        

        $file = 'report_'.date('YmdHis').'.pdf';
        include 'application/third_party/mpdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => '10mm',
            'margin_right' => '10mm',
            'margin_top' => '10mm',
            'margin_bottom' => '20mm',
            ]);
        $mpdf->showWatermarkText = true;
        $mpdf->setFooter('Page {PAGENO} of {nb}');
        $mpdf->SetHeader();

        echo $hed;
        $mpdf->WriteHTML($one);
        if (count($success) > 10) {
            $mpdf->AddPage();
        }
        $mpdf->WriteHTML($two);
        if (count($reject) > 10) {
            $mpdf->AddPage();
        }
        $mpdf->WriteHTML($three);
        if (count($delete) > 10) {
            $mpdf->AddPage();
        }
        $mpdf->WriteHTML($four);

        $mpdf->Output($file, 'I');
        exit;
    }
}
