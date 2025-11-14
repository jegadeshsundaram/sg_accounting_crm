<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Quotation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotation/quotation_model', 'qt_model');
    }

    public function index() {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'quotation/options.php';
    }

    public function test()
    {
        $from = date('Y-m-d', strtotime('01-01-2023'));
        $to = date('Y-m-d', strtotime('31-12-2023'));

        $employee_id = 'Company';
        if ($employee_id != 'Company') {
            $this->db->where('employee_id', $employee_id);
        }

        $this->db->where('status', 'SUCCESSFUL');
        $this->db->where("(`modified_on` >= '$from' AND `modified_on` <= '$to')");

        $query = $this->db->get('quotation_master');

        print_r($this->db->last_query());

        $tbl = $query->result();
    }    

    public function create()
    {
        is_logged_in('admin');
        has_permission();

        $setting = $this->custom->getLastInsertedRow('quotation_setting', 'updated_on');        
        if (is_null($setting)) {
            set_flash_message('message', 'warning', 'Define a Quotation Settings First !');
            redirect('quotation/');
        }
        
        $this->body_vars['quotation_ref_no'] = $this->generate_ref_no($setting);
        $this->body_vars['header_notes'] = $setting->header_notes;
        $this->body_vars['footer_notes'] = $setting->footer_notes;
        $this->body_vars['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

        // data to bind in dropdown lists
        $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
        $this->body_vars['employee_options'] = $this->custom->createDropdownSelect('master_employee', ['e_id', 'name', 'code', 'email'], 'Staff-in-charge', ['( ', ') ']);
        $this->body_vars['currency_options'] = $this->custom->populateCurrencyList();
        $this->body_vars['department_options'] = $this->custom->createDropdownSelect('master_department', ['d_id', 'name', 'code'], 'Department', ['( ', ') ', ' ']);

        $this->body_vars['billings'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], '', [' : ', ' ']);
        $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply'], ['SR']);

        $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);
        $this->body_vars['std_gst_rate'] = $std_gst_rate;

        $this->body_file = 'quotation/create.php';
    }

    public function generate_ref_no($setting) {

        $number_suffix = $setting->number_suffix;
        $ref_no = $setting->text_prefix.'.'.$number_suffix;

        $first_digit = substr($number_suffix, 0, 1);
        if($first_digit == 0) {
            $number_suffix = substr_replace($number_suffix, "1", 0, 1);
        }

        $number_suffix = intval($number_suffix) + 1;

        if($first_digit == 0) {
            $number_suffix = substr_replace($number_suffix,"0", 0, 1);
        }

        $update = $this->custom->updateRow('quotation_setting', ['number_suffix' => $number_suffix], ['text_prefix' => $setting->text_prefix]);        

        return $ref_no;
    }

    public function listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function report_options()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function save($action = 'new')
    {
        is_logged_in('admin');
        has_permission();
        $data = $this->input->post();

        if ($data) {
            // Insert values into quotation_master.TBL
            $mt_data['quotation_ref_no'] = $data['quotation_ref_no'];
            $mt_data['customer_id'] = $data['customer_id'];
            $mt_data['employee_id'] = $data['employee_id'];
            $mt_data['header_notes'] = $data['header_notes'];
            $mt_data['sub_total'] = $data['sub_total'];
            $mt_data['lsd_code'] = $data['lsd_code'];
            $mt_data['lsd_percentage'] = $data['lsd_percentage'];
            $mt_data['lsd_value'] = $data['lsd_value'];
            $mt_data['net_after_lsd'] = $data['net_after_lsd'];
            $mt_data['gst_total'] = $data['gst_total'];
            $mt_data['net_total'] = $data['net_total'];
            $mt_data['currency_rate'] = $data['customer_currency_rate'];
            $mt_data['f_gst_total'] = $data['f_gst_total'];
            $mt_data['f_net_total'] = $data['f_net_total'];
            $mt_data['footer_notes'] = $data['footer_notes'];
            $mt_data['status'] = 'SUBMITTED';
            $mt_data['created_on'] = date('Y-m-d', strtotime($data['created_on']));
            $mt_data['modified_on'] = date('Y-m-d');
            $mt_data['user_id'] = $this->session->user_id;

            if ($action == 'new') {
                $quotation_id = $this->custom->insertRow('quotation_master', $mt_data);
                $qt_status = 'Created';
            } elseif ($action == 'edit') {
                $quotation_id = $data['quotation_id'];
                $where = ['quotation_id' => $quotation_id];

                // Update values in quotation_master.TBL
                $updated = $this->custom->updateRow('quotation_master', $mt_data, $where);

                // delete all the items from quotation_product_master.TBL
                $res[] = $this->custom->deleteRow('quotation_product_master', $where);

                $qt_status = 'Updated';
            }

            // Insert into quotation_product_master.TBL
            $total_items = count($data['billing_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $pr_data['quotation_id'] = $quotation_id;
                $pr_data['billing_id'] = $data['billing_id'][$i];
                $pr_data['quantity'] = $data['quantity'][$i];
                $pr_data['discount'] = $data['discount'][$i];
                $pr_data['unit_price'] = $data['unit_price'][$i];
                $pr_data['amount'] = $data['amount'][$i];
                $pr_data['gst_category'] = $data['gst_code'][$i];
                $pr_data['gst_amount'] = $data['gst_amount'][$i];
                $pr_data['details'] = $data['item_details'][$i];

                $inserted[] = $this->custom->insertRow('quotation_product_master', $pr_data);
            }

            if ($this->db->trans_status() === false || in_array('error', $inserted)) {
                set_flash_message('message', 'danger', 'Error in creating Quotation');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'Quotation '.$qt_status);
                $this->db->trans_commit();
            }

            redirect('quotation/listing/submitted');
        }
        exit;
    }

    public function manage($mode, $row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $this->body_vars['mt_data'] = $mt_data = $this->custom->getSingleRow('quotation_master', ['quotation_id' => $row_id]);
            if ($mt_data) {
                $this->body_vars['quotation_id'] = $row_id;

                $this->body_vars['pr_data'] = $this->qt_model->get_quotation_items($row_id);

                $this->body_vars['customer'] = $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $mt_data->customer_id]);
                $this->body_vars['customer_address'] = $this->custom->populateCustomerAddress($customer);

                $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer->currency_id]);
                $this->body_vars['customer_currency'] = $currency_data->code;
                $this->body_vars['customer_currency_rate'] = $currency_data->rate;

                $this->body_vars['billings'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], '', [' : ', ' ']);
                $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply'], ['SR']);
                $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);
                $this->body_vars['std_gst_rate'] = $std_gst_rate;

                $this->body_vars['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);
                $this->body_vars['gst_rate'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);

                // employee details
                $employee = $this->custom->getSingleRow('master_employee', ['e_id' => $mt_data->employee_id]);
                $edata['name'] = $employee->name;
                $edata['code'] = $employee->code;
                $edata['email'] = $employee->email;
                $department = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $employee->department_id]);
                $edata['department'] = $department;
                $this->body_vars['employee_data'] = $edata;

                if ($mode == 'edit') {
                    $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['(', ')', ' '], [], [$mt_data->customer_id]);
                    $this->body_vars['employee_options'] = $this->custom->createDropdownSelect('master_employee', ['e_id', 'name'], 'Staff-in-charge', [' '], [], [$mt_data->employee_id]);
                    $this->body_file = 'quotation/edit.php';
                }

                if ($mode == 'view') {
                    $this->body_vars['mode'] = 'view';
                    $this->body_file = 'quotation/view.php';
                }
            } else {
                redirect('quotation/listing/submitted', 'refresh');
            }
        }
    }

    public function rpt_12months_sales_by_qty_sold()
    {
        is_logged_in('admin');
        has_permission();
        $month = $_GET['month'];
        $year = $_GET['year'];
        $day = '1';

        $html = '';

        $start = new DateTime();
        $start->setDate($year, $month, $day); // Normalize the day to 1
        $start->setTime(0, 0, 0); // Normalize time to midnight
        $start->sub(new DateInterval('P0M'));
        $interval = new DateInterval('P1M');
        $recurrences = 12;

        $month_year_array = [];
        $i = 0;
        foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
            $date_text = $date->format('Y-m-d');
            if ($i == 0) {
                $date_parts = explode('-', $date_text);
                $start_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $start_year = $date_parts[0];

                $year_start_date = $date->format('Y-m-d');
            }

            if ($i == 11) {
                $date_parts = explode('-', $date_text);
                $end_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $end_year = $date_parts[0];

                $ye_date = $date->format('Y-m-d');
                $year_end_date = date('Y-m-t', strtotime($ye_date));
            }

            $date_parts = explode('-', $date_text);
            array_push($month_year_array, date('M', mktime(0, 0, 0, $date_parts[1], 10)));
            ++$i;
        }

        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th { background: #f5f5f5; text-transform: uppercase}
            table th, table td {
                border: 1px solid #f5f5f5;
                padding: 8px; text-align: left;
            }
         </style>';

        $html .= '<table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $html .= '<h3>Sales Performance by Quantity Sold (12 Months)</h3>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" height="10" style="border: none;">';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="border: none; border-bottom: 2px solid #ccc"><strong>Period:</strong> '.$start_month.' '.$start_year.' <i>to</i> '.$end_month.' '.$end_year.'</td>';
        $html .= '<td style="text-align: right; border: none; border-bottom: 2px solid #ccc"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br /><table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 150px">Product</th>';
        $html .= '<th>'.$month_year_array[0].'</th>';
        $html .= '<th>'.$month_year_array[1].'</th>';
        $html .= '<th>'.$month_year_array[2].'</th>';
        $html .= '<th>'.$month_year_array[3].'</th>';
        $html .= '<th>'.$month_year_array[4].'</th>';
        $html .= '<th>'.$month_year_array[5].'</th>';
        $html .= '<th>'.$month_year_array[6].'</th>';
        $html .= '<th>'.$month_year_array[7].'</th>';
        $html .= '<th>'.$month_year_array[8].'</th>';
        $html .= '<th>'.$month_year_array[9].'</th>';
        $html .= '<th>'.$month_year_array[10].'</th>';
        $html .= '<th>'.$month_year_array[11].'</th>';
        $html .= '<th style="text-align: right; color: brown">TOTAL QTY SOLD</th>';
        $html .= '</tr>';

        // Populate Product List
        $sql_product_list = 'SELECT quotation_product_master.billing_id, master_billing.stock_code, master_billing.billing_description FROM quotation_master, quotation_product_master, master_billing WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = master_billing.billing_id AND quotation_master.modified_on >= "'.$year_start_date.'" AND quotation_master.modified_on <= "'.$year_end_date.'" AND quotation_master.status = "SUCCESSFUL" GROUP BY quotation_product_master.billing_id ORDER BY master_billing.billing_description ASC, master_billing.stock_code ASC';
        $query_product_list = $this->db->query($sql_product_list);
        $product_list = $query_product_list->result();

        $grand_total_by_month = [];
        $grand_total_by_all_product = 0;
        foreach ($product_list as $key => $value) {
            $product_name_code = $value->billing_description.' ('.$value->stock_code.')';
            $billing_id = $value->billing_id;

            $html .= '<tr>';
            $html .= '<td style="width: 150px">'.$product_name_code.'</th>';

            $grand_total_by_product = 0;
            $date_entry = 0;
            foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
                $start_date = $date->format('Y-m-d');
                $end_date = date('Y-m-t', strtotime($start_date));

                $entry = 0;
                $total_qty_by_product = 0;

                $sql_monthly_data = 'SELECT quantity FROM quotation_master, quotation_product_master WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = '.$billing_id.' AND quotation_master.modified_on >= "'.$start_date.'" AND quotation_master.modified_on <= "'.$end_date.'" AND quotation_master.status = "SUCCESSFUL"';
                $query_monthly_data = $this->db->query($sql_monthly_data);
                $monthly_data_by_product = $query_monthly_data->result();
                foreach ($monthly_data_by_product as $keys => $values) {
                    $total_qty_by_product += $values->quantity;
                    ++$entry;
                }

                $grand_total_by_month[$date_entry] += $total_qty_by_product;
                ++$date_entry;

                if ($entry > 0) {
                    $html .= '<td>'.$total_qty_by_product.'</td>';
                } else {
                    $html .= '<td>0</td>';
                }

                $grand_total_by_product += $total_qty_by_product;
            }

            $html .= '<td style="text-align: right; color: brown">'.number_format($grand_total_by_product, 2).'</td>';
            $html .= '</tr>';

            $grand_total_by_all_product += $grand_total_by_product;
        }

        $html .= '<tr>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">TOTAL QTY SOLD</th>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[0].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[1].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[2].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[3].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[4].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[5].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[6].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[7].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[8].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[9].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[10].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_by_month[11].'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">'.$grand_total_by_all_product.'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="50" valign="bottom" colspan="14" style="border: none; text-align: left; color: brown"><strong>Note:</strong> SALES Quantities are based only on successful quotations which can be invoiced.</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $file = 'qty_sold_sls_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_12months_sales_by_product()
    {
        is_logged_in('admin');
        has_permission();
        $month = $_GET['month'];
        $year = $_GET['year'];
        $day = '1';

        $html = '';

        $start = new DateTime();
        $start->setDate($year, $month, $day); // Normalize the day to 1
        $start->setTime(0, 0, 0); // Normalize time to midnight
        $start->sub(new DateInterval('P0M'));
        $interval = new DateInterval('P1M');
        $recurrences = 12;

        $month_year_array = [];
        $i = 0;
        foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
            $date_text = $date->format('Y-m-d');
            if ($i == 0) {
                $year_start_date = $date->format('Y-m-d');

                $date_parts = explode('-', $date_text);
                $start_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $start_year = $date_parts[0];
            }

            if ($i == 11) {
                $date_parts = explode('-', $date_text);
                $end_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $end_year = $date_parts[0];

                $ye_date = $date->format('Y-m-d');
                $year_end_date = date('Y-m-t', strtotime($ye_date));
            }

            $date_parts = explode('-', $date_text);
            array_push($month_year_array, date('M', mktime(0, 0, 0, $date_parts[1], 10)));
            ++$i;
        }

        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th { background: #f5f5f5; text-transform: uppercase}
            table th, table td {
                border: 1px solid #f5f5f5;
                padding: 8px; text-align: left;
            }
         </style>';

        $html .= '<table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $html .= '<h3>SALES COMPARISON REPORT BY PRODUCT (12 Months)</h3>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" height="10" style="border: none;">';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td style="border: none; border-bottom: 2px solid #ccc"><strong>Period:</strong> '.$start_month.' '.$start_year.' <i>to</i> '.$end_month.' '.$end_year.'</td>';
        $html .= '<td style="text-align: right; border: none; border-bottom: 2px solid #ccc"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br /><table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 150px">Product</th>';
        $html .= '<th>'.$month_year_array[0].'</th>';
        $html .= '<th>'.$month_year_array[1].'</th>';
        $html .= '<th>'.$month_year_array[2].'</th>';
        $html .= '<th>'.$month_year_array[3].'</th>';
        $html .= '<th>'.$month_year_array[4].'</th>';
        $html .= '<th>'.$month_year_array[5].'</th>';
        $html .= '<th>'.$month_year_array[6].'</th>';
        $html .= '<th>'.$month_year_array[7].'</th>';
        $html .= '<th>'.$month_year_array[8].'</th>';
        $html .= '<th>'.$month_year_array[9].'</th>';
        $html .= '<th>'.$month_year_array[10].'</th>';
        $html .= '<th>'.$month_year_array[11].'</th>';
        $html .= '<th style="text-align: right; color: brown">Total</th>';
        $html .= '</tr>';

        // Populate Product List
        $sql_product_list = 'SELECT quotation_product_master.billing_id, master_billing.stock_code, master_billing.billing_description FROM quotation_master, quotation_product_master, master_billing WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = master_billing.billing_id AND quotation_master.modified_on >= "'.$year_start_date.'" AND quotation_master.modified_on <= "'.$year_end_date.'" AND quotation_master.status = "SUCCESSFUL" GROUP BY quotation_product_master.billing_id ORDER BY master_billing.billing_description ASC, master_billing.stock_code ASC';
        $query_product_list = $this->db->query($sql_product_list);
        $product_list = $query_product_list->result();

        $grand_total_by_month = [];
        $grand_total_by_all_product = 0;
        foreach ($product_list as $key => $value) {
            $product_name_code = $value->billing_description.' ('.$value->stock_code.')';
            $billing_id = $value->billing_id;

            $html .= '<tr>';
            $html .= '<td style="width: 150px">'.$product_name_code.'</th>';

            $grand_total_by_product = 0;
            $date_entry = 0;
            foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
                $start_date = $date->format('Y-m-d');
                $end_date = date('Y-m-t', strtotime($start_date));

                $entry = 0;
                $total_value_by_product = 0;

                $sql_monthly_data = 'SELECT customer_id, amount FROM quotation_master, quotation_product_master WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = '.$billing_id.' AND quotation_master.modified_on >= "'.$start_date.'" AND quotation_master.modified_on <= "'.$end_date.'" AND quotation_master.status = "SUCCESSFUL"';
                $query_monthly_data = $this->db->query($sql_monthly_data);
                $monthly_data_by_product = $query_monthly_data->result();
                foreach ($monthly_data_by_product as $keys => $values) {
                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $values->customer_id]);
                    $customer_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

                    $total_value_by_product += $values->amount / $customer_currency_rate;

                    ++$entry;
                }

                $grand_total_by_month[$date_entry] += $total_value_by_product;
                ++$date_entry;

                if ($entry > 0) {
                    $html .= '<td>'.number_format($total_value_by_product, 2).'</td>';
                } else {
                    $html .= '<td>0.00</td>';
                }

                $grand_total_by_product += $total_value_by_product;
            }

            $html .= '<td style="text-align: right; color: brown">'.number_format($grand_total_by_product, 2).'</td>';
            $html .= '</tr>';

            $grand_total_by_all_product += $grand_total_by_product;
        }

        $html .= '<tr>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">TOTAL</th>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[0], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[1], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[2], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[3], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[4], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[5], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[6], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[7], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[8], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[9], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[10], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[11], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">'.number_format($grand_total_by_all_product, 2).'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="50" valign="bottom" colspan="14" style="border: none; text-align: left; color: brown"><strong>Note:</strong> SALES FIGURES are based only on successful quotations which can be invoiced.</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $file = 'prd_sls_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_12months_sales_by_staff()
    {
        is_logged_in('admin');
        has_permission();
        $month = $_GET['month'];
        $year = $_GET['year'];
        $day = '1';

        $html = '';

        $start = new DateTime();
        $start->setDate($year, $month, $day); // Normalize the day to 1
        $start->setTime(0, 0, 0); // Normalize time to midnight
        $start->sub(new DateInterval('P0M'));
        $interval = new DateInterval('P1M');
        $recurrences = 12;

        $month_year_array = [];
        $i = 0;
        foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
            $date_text = $date->format('Y-m-d');
            if ($i == 0) {
                $date_parts = explode('-', $date_text);
                $start_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $start_year = $date_parts[0];

                $year_start_date = $date->format('Y-m-d');
            }

            if ($i == 11) {
                $date_parts = explode('-', $date_text);
                $end_month = date('M', mktime(0, 0, 0, $date_parts[1], 10));
                $end_year = $date_parts[0];

                $ye_date = $date->format('Y-m-d');
                $year_end_date = date('Y-m-t', strtotime($ye_date));
            }

            $date_parts = explode('-', $date_text);
            array_push($month_year_array, date('M', mktime(0, 0, 0, $date_parts[1], 10)));
            ++$i;
        }

        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th { background: #f5f5f5; text-transform: uppercase}
            table th, table td {
                border: 1px solid #f5f5f5;
                padding: 8px; text-align: left;
            }
         </style>';

        $html .= '<table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $html .= '<h3>SALES COMPARISON REPORT BY STAFF (12 Months)</h3>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="border: none; border-bottom: 2px solid #ccc"><strong>Period:</strong> '.$start_month.' '.$start_year.' <i>to</i> '.$end_month.' '.$end_year.'</td>';
        $html .= '<td style="text-align: right; border: none; border-bottom: 2px solid #ccc"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br /><table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 150px">Staff</th>';
        $html .= '<th>'.$month_year_array[0].'</th>';
        $html .= '<th>'.$month_year_array[1].'</th>';
        $html .= '<th>'.$month_year_array[2].'</th>';
        $html .= '<th>'.$month_year_array[3].'</th>';
        $html .= '<th>'.$month_year_array[4].'</th>';
        $html .= '<th>'.$month_year_array[5].'</th>';
        $html .= '<th>'.$month_year_array[6].'</th>';
        $html .= '<th>'.$month_year_array[7].'</th>';
        $html .= '<th>'.$month_year_array[8].'</th>';
        $html .= '<th>'.$month_year_array[9].'</th>';
        $html .= '<th>'.$month_year_array[10].'</th>';
        $html .= '<th>'.$month_year_array[11].'</th>';
        $html .= '<th style="text-align: right; color: brown">Total</th>';
        $html .= '</tr>';

        $sql_staff_list = 'SELECT employee_id, name, code FROM quotation_master, master_employee WHERE quotation_master.employee_id = master_employee.e_id AND quotation_master.modified_on >= "'.$year_start_date.'" AND quotation_master.modified_on <= "'.$year_end_date.'" AND quotation_master.status = "SUCCESSFUL" GROUP BY quotation_master.employee_id ORDER BY master_employee.name ASC, master_employee.code ASC';
        $query_staff_list = $this->db->query($sql_staff_list);
        $staff_list = $query_staff_list->result();

        $grand_total_by_month = [];
        $grand_total_by_all_staff = 0;
        foreach ($staff_list as $key => $value) {
            $staff_name_code = $value->name.' ('.$value->code.')';
            $employee_id = $value->employee_id;

            $html .= '<tr>';
            $html .= '<td style="width: 150px">'.$staff_name_code.'</th>';

            $grand_total_by_staff = 0;
            $date_entry = 0;
            foreach (new DatePeriod($start, $interval, $recurrences, true) as $date) {
                $start_date = $date->format('Y-m-d');
                $end_date = date('Y-m-t', strtotime($start_date));
                // $html .= '<td>'.$start_date.' :: '.$end_date.'</td>';
                // redirect('dashboard?'.$start_date.'###'.$end_date);

                $sql_other_report = 'SELECT customer_id, sub_total FROM quotation_master WHERE employee_id = '.$employee_id.' AND quotation_master.modified_on >= "'.$start_date.'" AND quotation_master.modified_on <= "'.$end_date.'" AND status = "SUCCESSFUL"';
                $query_other_report = $this->db->query($sql_other_report);
                $other_report_data = $query_other_report->result();
                $entry = 0;
                $total_value = 0;
                foreach ($other_report_data as $keys => $values) {
                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $values->customer_id]);
                    $customer_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);
                    $total_SGD = $values->sub_total / $customer_currency_rate;

                    $total_value += $total_SGD;

                    ++$entry;
                }

                $grand_total_by_month[$date_entry] += $total_value;
                ++$date_entry;

                if ($entry > 0) {
                    $html .= '<td>'.number_format($total_value, 2).'</td>';
                } else {
                    $html .= '<td>0.00</td>';
                }

                $grand_total_by_staff += $total_value;
            }

            $html .= '<td style="text-align: right; color: brown">'.number_format($grand_total_by_staff, 2).'</td>';
            $html .= '</tr>';

            $grand_total_by_all_staff += $grand_total_by_staff;
        }

        $html .= '<tr>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">TOTAL</th>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[0], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[1], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[2], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[3], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[4], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[5], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[6], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[7], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[8], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[9], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[10], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_by_month[11], 2).'</td>';
        $html .= '<td style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">'.number_format($grand_total_by_all_staff, 2).'</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td height="50" valign="bottom" colspan="14" style="border: none; text-align: left; color: brown"><strong>Note:</strong> SALES FIGURES are based only on successful quotations which can be invoiced.</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $file = 'stf_sls_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_sales_performance_by_product()
    {
        is_logged_in('admin');
        has_permission();
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $html = '';

        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th { background: #f5f5f5; }
            table th, table td {
                border: 1px solid #f5f5f5;
                padding: 10px; text-align: left;
            }
         </style>';

        $html .= '<table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $html .= '<h3>Sales Performance By Product</h3>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="border: none; border-bottom: 2px solid #ccc"><strong>Period:</strong> '.$start_date.' <i>to</i> '.$end_date.'</td>';
        $html .= '<td style="text-align: right; border: none; border-bottom: 2px solid #ccc"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br /><br /><table style="width: 100%;">
         <tr>
            <th>Product</th>
            <th>Total Value</th>
            <th>Percentage</th>            
         </tr>';

        $grand_total_product = 0;

        $sql_total_product = 'SELECT customer_id, amount FROM quotation_master, quotation_product_master WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_master.modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND quotation_master.modified_on <= "'.date('Y-m-d', strtotime($end_date)).'"';
        $query_total_product = $this->db->query($sql_total_product);
        $total_product = $query_total_product->result();
        foreach ($total_product as $key => $value) {
            $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $value->customer_id]);
            $customer_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

            $grand_total_product += $value->amount / $customer_currency_rate;
        }

        $sql_product_list = 'SELECT quotation_product_master.billing_id, master_billing.stock_code, master_billing.billing_description FROM quotation_master, quotation_product_master, master_billing WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = master_billing.billing_id AND quotation_master.modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND quotation_master.modified_on <= "'.date('Y-m-d', strtotime($end_date)).'" GROUP BY quotation_product_master.billing_id ORDER BY master_billing.billing_description ASC, master_billing.stock_code ASC';
        $query_product_list = $this->db->query($sql_product_list);
        $product_list = $query_product_list->result();
        foreach ($product_list as $key => $value) {
            $product_name_code = $value->billing_description.' ('.$value->stock_code.')';
            $billing_id = $value->billing_id;

            $total_value_by_product = 0;

            $sql_other_report = 'SELECT customer_id, amount FROM quotation_master, quotation_product_master WHERE quotation_master.quotation_id = quotation_product_master.quotation_id AND quotation_product_master.billing_id = '.$billing_id.' AND quotation_master.modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND quotation_master.modified_on <= "'.date('Y-m-d', strtotime($end_date)).'"';
            $query_other_report = $this->db->query($sql_other_report);
            $other_report_data = $query_other_report->result();
            foreach ($other_report_data as $keys => $values) {
                $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $values->customer_id]);
                $customer_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

                $total_value_by_product += $values->amount / $customer_currency_rate;
            }
            $total_precentage_by_product = $total_value_by_product * 100 / $grand_total_product;
            $grand_total_percentage += $total_precentage_by_product;

            $html .= '<tr>';
            $html .= '<td valign="top">'.$product_name_code.'</td>';
            $html .= '<td valign="top">'.number_format($total_value_by_product, 2).'</td>';
            $html .= '<td valign="top">'.number_format($total_precentage_by_product, 2).'</td>';
            $html .= '</tr>';
        }

        if ($grand_total_percentage < 100 || $grand_total_percentage > 100) {
            $grand_total_percentage = 100;
        }

        $html .= '<tr>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">Total</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_product, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_percentage, 2).'</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $file = 'prd_sls_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_sales_performance_by_staff()
    {
        is_logged_in('admin');
        has_permission();
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $html = '';

        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th { background: #f5f5f5; }
            table th, table td {
                border: 1px solid #f5f5f5;
                padding: 10px; text-align: left;
            }
         </style>';

        $html .= '<table style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $html .= '<h3>Sales Performance by Staff</h3>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="border: none; border-bottom: 2px solid #ccc"><strong>Period:</strong> '.$start_date.' <i>to</i> '.$end_date.'</td>';
        $html .= '<td style="text-align: right; border: none; border-bottom: 2px solid #ccc"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br /><br /><table style="width: 100%;">
         <tr>
            <th style="width: 200px">Staff</th>
            <th>No.Quotation</th>
            <th style="width: 50px">Percentage</th>
            <th style="width: 100px">Total $</th>
            <th style="width: 100px">Successful</th>
            <th style="width: 100px">Rejected</th>
            <th style="width: 100px">Deleted</th>
            <th style="width: 100px">Pending</th>
         </tr>';

        $sql_total_quotation = 'SELECT employee_id FROM quotation_master WHERE modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND modified_on <= "'.date('Y-m-d', strtotime($end_date)).'"';
        $query_total_quotation = $this->db->query($sql_total_quotation);
        $total_quotation = $query_total_quotation->result();
        $grand_total_quotation = 0;
        foreach ($total_quotation as $key => $value) {
            ++$grand_total_quotation;
        }

        $sql_staff_list = 'SELECT employee_id, name, code FROM quotation_master, master_employee WHERE quotation_master.employee_id = master_employee.e_id AND quotation_master.modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND quotation_master.modified_on <= "'.date('Y-m-d', strtotime($end_date)).'" GROUP BY quotation_master.employee_id ORDER BY master_employee.name ASC, master_employee.code ASC';
        $query_staff_list = $this->db->query($sql_staff_list);
        $staff_list = $query_staff_list->result();
        foreach ($staff_list as $key => $value) {
            $staff_name_code = $value->name.' '.$value->code;
            $employee_id = $value->employee_id;

            $sql_other_report = 'SELECT customer_id, sub_total, status FROM quotation_master WHERE employee_id = '.$employee_id.' AND quotation_master.modified_on >= "'.date('Y-m-d', strtotime($start_date)).'" AND quotation_master.modified_on <= "'.date('Y-m-d', strtotime($end_date)).'" ORDER BY status';
            $query_other_report = $this->db->query($sql_other_report);
            $other_report_data = $query_other_report->result();

            $total_value = 0;
            $deleted_value = 0;
            $rejected_value = 0;
            $submitted_value = 0;
            $successful_value = 0;

            $deleted_count = 0;
            $rejected_count = 0;
            $submitted_count = 0;
            $successful_count = 0;

            $total_quotation_by_staff = 0;
            foreach ($other_report_data as $keys => $values) {
                $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $values->customer_id]);
                $customer_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);
                $total_SGD = $values->sub_total / $customer_currency_rate;
                if ($values->status == 'DELETED') {
                    $deleted_value += $total_SGD;
                    ++$deleted_count;
                } elseif ($values->status == 'REJECTED') {
                    $rejected_value += $total_SGD;
                    ++$rejected_count;
                } elseif ($values->status == 'SUBMITTED') {
                    $submitted_value += $total_SGD;
                    ++$submitted_count;
                } elseif ($values->status == 'SUCCESSFUL') {
                    $successful_value += $total_SGD;
                    ++$successful_count;
                }
                ++$total_quotation_by_staff;
            }

            $total_value_by_staff = $deleted_value + $rejected_value + $submitted_value + $successful_value;
            $total_precentage_by_staff = $total_quotation_by_staff * 100 / $grand_total_quotation;

            $html .= '<tr>';
            $html .= '<td valign="top">'.$staff_name_code.'</td>';
            $html .= '<td valign="top">'.$total_quotation_by_staff.'</td>';
            $html .= '<td valign="top">'.number_format($total_precentage_by_staff, 2).'</td>';
            $html .= '<td valign="top">'.number_format($total_value_by_staff, 2).'</td>';
            $html .= '<td valign="top">'.number_format($successful_value, 2).'</td>';
            $html .= '<td valign="top">'.number_format($rejected_value, 2).'</td>';
            $html .= '<td valign="top">'.number_format($deleted_value, 2).'</td>';
            $html .= '<td valign="top">'.number_format($submitted_value, 2).'</td>';
            $html .= '</tr>';

            $grand_total_percentage += $total_precentage_by_staff;
            $grand_total_value += $total_value_by_staff;
            $grand_total_deleted += $deleted_value;
            $grand_total_rejected += $rejected_value;
            $grand_total_submitted += $submitted_value;
            $grand_total_successful += $successful_value;
        }

        $html .= '<tr>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold; text-align: right">Total</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.$grand_total_quotation.'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_percentage, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_value, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_successful, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_rejected, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_deleted, 2).'</td>';
        $html .= '<td valign="top" style="border-top: 2px solid #ccc; font-weight: bold">'.number_format($grand_total_submitted, 2).'</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $file = 'report_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_summary_analysis()
    {
        is_logged_in('admin');
        has_permission();
        $from = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $to = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $tbl = $this->qt_model->get_summary_analysis();

        $employee = isset($_GET['employee_id']) ? $_GET['employee_id'] : 'Company';

        if ($employee != 'Company') {
            $header_text = '<h3>Summary Analysis By Employee</h3> <h4 style="color: dimgray">'.$this->qt_model->get_employee($employee, 'name').' ( '.$this->qt_model->get_employee($employee, 'code').' )</h4> ';
        } else {
            $header_text = '<h2>Summary Analysis By Company</h2>';
        }

        $html = '<style type="text/css">
        table { border-collapse: collapse;}
        table th {
            background: gainsboro;
            padding: 10px; text-align: left;
        }
        table td {
            border: 1px solid gainsboro;
            padding: 10px; text-align: left;
        }
        .special {
            border: none;
            border-top: 2px solid dimgray;
            color: #000;
        }
        </style>';

        $html .= '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="text-align: center; border: none">';
        $html .= $header_text;
        $html .= '</td>';
        $html .= '<tr>';
        $html .= '<td style="border: none;" height="50"><strong>Period:</strong> <i>'.$from.'</i> <strong>to</strong> <i>'.$to.'</i></td>';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date: </strong>'.date('d-m-Y').'</td>';
        $html .= '</tr><tr><td colspan="2" style="border: none; border-top: 2px solid brown"></td></tr></table><br />';

        $html .= '
      <table style="width: 100%;">
        <thead>
          <tr>
            <th>Category</th>
            <th>Nos</th>
            <th>%</th>
            <th>Value $</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="color: green">Success</td>
            <td>'.$tbl['Success']['total'].'</td>
            <td>'.$tbl['Success']['rate'].'</td>
            <td>'.number_format($tbl['Success']['sum'], 2).'</td>
            <td>'.$tbl['Success']['rate_amount'].'</td>
          </tr>
          <tr>
            <td style="color: brown">Rejected</td>
            <td>'.$tbl['Rejected']['total'].'</td>
            <td>'.$tbl['Rejected']['rate'].'</td>
            <td>'.number_format($tbl['Rejected']['sum'], 2).'</td>
            <td>'.$tbl['Rejected']['rate_amount'].'</td>
          </tr>
          <tr>
            <td style="color: red">Deleted</td>
            <td>'.$tbl['Deleted']['total'].'</td>
            <td>'.$tbl['Deleted']['rate'].'</td>
            <td>'.number_format($tbl['Deleted']['sum'], 2).'</td>
            <td>'.$tbl['Deleted']['rate_amount'].'</td>
          </tr>
          <tr>
            <td style="color: blue">Pending</td>
             <td>'.$tbl['Pending']['total'].'</td>
            <td>'.$tbl['Pending']['rate'].'</td>
            <td>'.number_format($tbl['Pending']['sum'], 2).'</td>
            <td>'.$tbl['Pending']['rate_amount'].'</td>
          </tr>
          <tr>
            <td style="color: #000; text-align: right; font-weight: bold">Total</strong></td>
            <td style="color: #000; font-weight: bold">'.$tbl['Total']['total'].'</td>
            <td style="color: #000; font-weight: bold">'.$tbl['Total']['rate'].'</td>
            <td style="color: #000; font-weight: bold">'.$tbl['Total']['sum'].'</td>
            <td style="color: #000; font-weight: bold">'.$tbl['Total']['rate_amount'].'</td>
          </tr>
        </tbody>
      </table>';

        $file = 'report_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function rpt_detailed_analysis()
    {
        is_logged_in('admin');
        has_permission();
        $tbl = $this->qt_model->get_detailed_analysis();
        echo $tbl;
        exit;
    }

    public function print_all() // Print All from Listing Page
    {
        $html = '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th {background: gainsboro; }
            table th, table td {
            border: 1px solid gainsboro;
            padding: 10px; text-align: left;
            }
         </style>';

        $html .= '<div style="width: 100%; margin: auto;text-align: center;"><h3>'.$qt_type.' QUOTATIONS</h3></div>';

        $html .= '<table>
         <tr>
            <th style="width: 120px">DATE</th>
            <th style="width: 120px">REFERENCE</th>
            <th style="width: 280px">CUSTOMER</th>
            <th style="width: 130px">SUBTOTAL</th>
            <th style="width: 160px">DISCOUNT</th>
            <th style="width: 130px">NET TOTAL</th>
         </tr>';

        $qt_type = strtoupper($this->uri->segment(3));

        $table = 'quotation_master';
        $columns = ['quotation_id', 'modified_on', 'quotation_ref_no', 'customer_id', 'sub_total', 'lsd_code', 'lsd_percentage', 'lsd_value', 'net_after_lsd', 'net_total'];
        $where = ['status' => $qt_type];
        $join_table = null;
        $join_condition = null;
        $table_id = 'quotation_id';

        $list = $this->qt_model->get_quotations($table, $columns, $join_table, $join_condition, $where, $table_id);

        $i = 1;
        foreach ($list as $record) {
            $customer_data = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['customer_id' => $record->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td valign="top">'.strtoupper(date('M j, Y', strtotime($record->modified_on))).'</td>';
            $html .= '<td valign="top">'.$record->quotation_ref_no.'</td>';
            $html .= '<td valign="top">'.strtoupper($customer_data->name).'<br />'.strtoupper($customer_data->code).' | <span style="color: brown">'.$currency.'</span></td>';
            $html .= '<td valign="top">'.number_format($record->sub_total, 2).'</td>';
            if ($record->lsd_code == 'P') { // discount percentage applied
                $html .= '<td valign="top">$'.$record->lsd_value.' | '.number_format($record->lsd_percentage, 2).' %</td>';
            } elseif ($record->lsd_code == 'V') { // disount amount applied
                $html .= '<td valign="top">$'.$record->lsd_value.'</td>';
            } else {
                $html .= '<td valign="top">0.00</td>';
            }

            $html .= '<td valign="top">'.number_format($record->net_total, 2).'</td>';
            $html .= '</tr>';
            ++$i;
        }

        $html .= '</table>';

        $file = 'quotation_list_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    // Print - New & Edit Quotation Function
    public function print_stage_1()
    {
        $data = $this->input->post();
        $this->data['data'] = $data;

        // company details
        $this->data['company_details'] = $this->custom->populateCompanyHeader();
        $this->data['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

        // Customer & Currency Details
        $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $data['customer_id']]);
        $this->data['customer_address'] = $this->custom->populateCustomerAddress($customer_data);
        $this->data['customer_name'] = $customer_data->name;
        $this->data['customer_code'] = $customer_data->code;
        $this->data['customer_gst_number'] = $customer_data->customer_gst_number;

        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer_data->currency_id]);
        $this->data['customer_currency'] = $currency_data->code;
        $this->data['currency_rate'] = $currency_data->rate;

        // Employee Details
        $employee_where = ['e_id' => $data['employee_id']];
        $this->data['employee_data'] = $this->custom->getSingleRow('master_employee', $employee_where);

        // this value needs check and display gst registration number in the customer details as well as in the footer content
        $special_gst_srcas_exist = false;
        $special_gst_srcas_amount = 0;
        $total_items = count($data['billing_id']);
        $default_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);
        for ($i = 0; $i <= $total_items - 1; ++$i) {
            if ($data['gst_code'][$i] == 'SRCA-S') {
                $special_gst_srcas_exist = true;
                $special_gst_srcas_amount += $data['amount'][$i] * $default_gst_rate / 100;
            }
        }
        $this->data['special_gst_srcas_exist'] = $special_gst_srcas_exist;
        $this->data['special_gst_srcas_amount'] = $special_gst_srcas_amount;

        $file = 'quotation_'.date('YmdHis').'.pdf';
        $document = $this->load->view('quotation/print_stage_1.php', $this->data, true);
        $this->custom->printMPDF($file, $document);
    }

    // this quotation will be printed after submitted
    public function print_stage_2()
    {
        if (isset($_GET['rowID'])) {
            $row_id = $_GET['rowID'];
            $action = 'print';
        } else {
            $row_id = $this->input->post('rowID');
            $action = 'email';
        }

        // quotation master details
        $this->data['mt_data'] = $mt_data = $this->custom->getSingleRow('quotation_master', ['quotation_id' => $row_id]);

        if ($mt_data) {
            // company details
            $this->data['company_details'] = $this->custom->populateCompanyHeader();
            $this->data['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

            // quotation item (product / service) details
            $this->data['pr_data'] = $pr_data = $this->qt_model->get_quotation_items($row_id);

            // customer and employee details
            $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $mt_data->customer_id]);
            $this->data['customer_address'] = $this->custom->populateCustomerAddress($customer_data);
            $this->data['customer_name'] = $customer_data->name;
            $this->data['customer_code'] = $customer_data->code;
            $this->data['customer_gst_number'] = $customer_data->customer_gst_number;

            // currency details
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer_data->currency_id]);
            $this->data['customer_currency'] = $currency_data->code;
            $this->data['currency_rate'] = $currency_data->rate;

            // Employee Details
            $employee_where = ['e_id' => $mt_data->employee_id];
            $this->data['employee_data'] = $this->custom->getSingleRow('master_employee', $employee_where);

            // this value needs check and display gst registration number in the customer details as well as in the footer content
            $special_gst_srcas_exist = false;
            $special_gst_srcas_amount = 0;
            $default_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);
            foreach ($pr_data as $value) {
                if ($value->gst_category == 'SRCA-S') {
                    $special_gst_srcas_exist = true;
                    $special_gst_srcas_amount += $value->amount * $default_gst_rate / 100;
                }
            }
            $this->data['special_gst_srcas_exist'] = $special_gst_srcas_exist;
            $this->data['special_gst_srcas_amount'] = $special_gst_srcas_amount;

            $file = 'quotation_'.date('YmdHis').'.pdf';
            $document = $this->load->view('quotation/print_stage_2.php', $this->data, true);
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
            $mpdf->WriteHTML($document);

            if ($action == 'print') {
                //$this->custom->printMPDF($file, $document);
                $mpdf->Output($file, 'I');
            }

            if ($action == 'email') {
                $path = BASEPATH.'upload/quotation/';

                if (is_dir($path)) {
                    $mpdf->Output(realpath($path).'/'.$file, 'F');
                } else {
                    echo 'error'.$path;
                }

                $pdfFilePath = realpath($path).'/'.$file;

                // Email settings
                $to_address = $customer_data->email;
                if ($to_address !== null && $to_address !== '') {

                    $mail = $this->custom->populateEmailHeaders();
                    
                    // to address
                    $mail->addAddress($to_address);

                    // subject
                    $mail->Subject = 'Information About Quotation';

                    // body
                    $mail->Body = $document;

                    // attachments
                    $mail->AddAttachment($pdfFilePath);

                    // Send
                    if (!$mail->send()) {
                        print_r('Email could not be sent.');
                        print_r('Mailer Error: '.$mail->ErrorInfo);
                        $message = 'error';
                    } else {
                        print_r('Email has been sent');
                        $message = 'success';
                    }
                } else {
                    $message = 'email address empty';
                }
                echo $message;
            }
        } else {
            redirect('quotation/listing/submitted', 'refresh');
        }
    }
    
    public function email() {
        
        $mail = $this->custom->populateEmailHeaders();

        // Add a recipient
        $mail->addAddress('jegaonline@gmail.com');

        // Add cc or bcc
        // $mail->addCC();
        // $mail->addBCC(');

        // Email subject
        $mail->Subject = 'Test Email on Headers from Custom.Model';

        // Set email format to HTML
        $mail->isHTML(true);

        // Email body content
        $mailContent = "TEST EMAIL";
        $mail->Body = $mailContent;

        // email attachement - quotation
        //$mail->AddAttachment($pdfFilePath);

        // Send email
        if (!$mail->send()) {
            echo 'Email could not be sent.';
            echo 'Mailer Error: '.$mail->ErrorInfo;
            $message = 'error';
        } else {
            echo 'Mailer Error: '.$mail->ErrorInfo;
            echo 'Email has been sent';
            $message = 'success';
        }
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'quotation_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['quotation_setting', 'quotation_master', 'quotation_product_master'],
            'format' => 'sql',           // sql, txt
            'filename' => $file_name,      // File name
            'add_drop' => true,            // Whether to add DROP TABLE statements to backup file
            'add_insert' => true,            // Whether to add INSERT data to backup file
            'newline' => "\n",             // Newline character used in backup file
        ];

        $backup = $CI->dbutil->backup($prefs);
        // Backup your entire database and assign it to a variable

        // Load the file helper and write the file to your server
        $CI->load->helper('file');
        write_file(FCPATH.'/assets/database_backups/'.$file_name, $backup);

        // Load the download helper and send the file to your desktop
        $CI->load->helper('download');
        force_download($file_name, $backup);
    }

    public function df_restore()
    {
        is_logged_in('admin');
        $data = file_upload(date('YmdHis'), 'db_file', 'database_restore_files');
        $this->load->helper('file');
        if ($data['status']) {
            $sql_file = $data['upload_data']['full_path'];

            $search_str = [' ; ', 'com;', 'sg;'];
            $replace_str = [' : ', 'com:', 'sg:'];

            $query_list = explode(';', str_replace($search_str, $replace_str, read_file($sql_file)));

            // This foreign key check was disabled for 1 table referred by 2 tables
            // Cannot delete or update a parent row: a foreign key constraint fails # # TABLE STRUCTURE FOR: groups # DROP TABLE IF EXISTS `groups`
            $this->db->query('SET foreign_key_checks = 0');

            foreach ($query_list as $query) {
                $query = trim($query);
                if ($query != '') {
                    $this->db->query($query);
                }
            }

            $this->db->query('SET foreign_key_checks = 1');
            set_flash_message('message', 'success', 'Quotation Datafiles Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('quotation/', 'refresh');
        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'quotation/blank.php';
        zapQuotation();
        redirect('quotation/', 'refresh');
    }
}
