<?php

// defined('BASEPATH') OR exit('No direct script access allowed');
class Invoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice/invoice_model', 'inv_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'invoice/options.php';
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

        $update = $this->custom->updateRow('invoice_setting', ['number_suffix' => $number_suffix], ['text_prefix' => $setting->text_prefix]);

        return $ref_no;
    }

    public function create()
    {
        is_logged_in('admin');
        has_permission();

        // invoice settings - reference, header and footer texts
        $setting = $this->custom->getLastInsertedRow('invoice_setting', 'updated_on');
        if (is_null($setting)) {
            set_flash_message('message', 'warning', 'Define a invoice Settings First !');
            redirect('invoice/');
        }
        
        $this->body_vars['invoice_ref_no'] = $this->generate_ref_no($setting);
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

        $this->body_file = 'invoice/create.php';
    }

    public function listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function other_listing()
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
            // Insert values into invoice_master.TBL
            $mt_data['invoice_ref_no'] = $data['invoice_ref_no'];
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
            $mt_data['payment_terms'] = $data['payment_terms'];
            $mt_data['order_notes'] = $data['order_notes'];
            $mt_data['footer_notes'] = $data['footer_notes'];
            $mt_data['created_on'] = date('Y-m-d', strtotime($data['created_on']));
            $mt_data['modified_on'] = date('Y-m-d');
            $mt_data['status'] = 'CONFIRMED';
            $mt_data['user_id'] = $this->session->user_id;

            $mt_data['quotation_ref_no'] = $data['quotation_ref_no'];

            if ($action == 'new') {
                $invoice_id = $this->custom->insertRow('invoice_master', $mt_data);
                $status = 'Created';
            } elseif ($action == 'edit') {
                $invoice_id = $data['invoice_id'];
                $where = ['invoice_id' => $invoice_id];

                // Update values in invoice_master.TBL
                $updated = $this->custom->updateRow('invoice_master', $mt_data, $where);

                // delete all the items from invoice_product_master.TBL
                $res[] = $this->custom->deleteRow('invoice_product_master', $where);

                $status = 'Updated';
            }

            // Insert into quotation_product_master.TBL
            $total_items = count($data['billing_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $pr_data['invoice_id'] = $invoice_id;
                $pr_data['billing_id'] = $data['billing_id'][$i];
                $pr_data['quantity'] = $data['quantity'][$i];
                $pr_data['discount'] = $data['discount'][$i];
                $pr_data['unit_price'] = $data['unit_price'][$i];
                $pr_data['amount'] = $data['amount'][$i];
                $pr_data['gst_category'] = $data['gst_code'][$i];
                $pr_data['gst_amount'] = $data['gst_amount'][$i];
                $pr_data['details'] = $data['item_details'][$i];
                
                $inserted[] = $this->custom->insertRow('invoice_product_master', $pr_data);
            }

            if ($this->db->trans_status() === false || in_array('error', $inserted)) {
                set_flash_message('message', 'danger', 'Error in creating Invoice');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'Invoice '.$status);
                $this->db->trans_commit();
            }

            redirect('invoice/listing/');

        }
        exit;
    }

    public function manage($mode, $row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $this->body_vars['mt_data'] = $mt_data = $this->custom->getSingleRow('invoice_master', ['invoice_id' => $row_id]);
            if ($mt_data) {
                $this->body_vars['invoice_id'] = $row_id;

                $this->body_vars['pr_data'] = $this->inv_model->get_invoice_items($row_id);

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
                    $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], [], [$mt_data->customer_id]);
                    $this->body_vars['employee_options'] = $this->custom->createDropdownSelect('master_employee', ['e_id', 'name', 'code'], 'Staff-in-charge', ['( ', ') '], [], [$mt_data->employee_id]);

                    $this->body_file = 'invoice/edit.php';
                }

                if ($mode == 'view') {
                    $this->body_vars['mode'] = 'view';
                    $this->body_file = 'invoice/view.php';
                }
            } else {
                redirect('invoice/listing/', 'refresh');
            }
        }
    }

    public function extract_quotation($row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $this->body_vars['mt_data'] = $mt_data = $this->custom->getSingleRow('quotation_master', ['quotation_id' => $row_id]);
            if ($mt_data) {

                $setting = $this->custom->getLastInsertedRow('invoice_setting', 'updated_on');
                if (is_null($setting)) {
                    set_flash_message('message', 'warning', 'Define a invoice Settings First !');
                    redirect('invoice/');
                }
                
                $this->body_vars['invoice_ref_no'] = $this->generate_ref_no($setting);
                $this->body_vars['header_notes'] = $setting->header_notes;
                $this->body_vars['footer_notes'] = $setting->footer_notes;

                $this->body_vars['quotation_id'] = $row_id;
                $this->body_vars['quotation_ref_no'] = $mt_data->quotation_ref_no;
                $this->body_vars['pr_data'] = $this->inv_model->get_quotation_items($row_id);

                $this->body_vars['customer_data'] = $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $mt_data->customer_id]);
                $this->body_vars['customer_address'] = $this->custom->populateCustomerAddress($customer_data);

                $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer_data->currency_id]);
                $this->body_vars['customer_currency'] = $currency_data->code;
                $this->body_vars['customer_currency_rate'] = $currency_data->rate;

                $this->body_vars['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);
                $this->body_vars['gst_rate'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);

                // employee details
                $employee_data = $this->custom->getSingleRow('master_employee', ['e_id' => $mt_data->employee_id]);
                $edata['name'] = $employee_data->name;
                $edata['code'] = $employee_data->code;
                $edata['email'] = $employee_data->email;
                $department = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $employee_data->department_id]);
                $edata['department'] = $department;
                $this->body_vars['employee_data'] = $edata;

                $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], [], [$mt_data->customer_id]);
                $this->body_vars['employee_options'] = $this->custom->createDropdownSelect('master_employee', ['e_id', 'name', 'code'], 'Staff-in-charge', ['( ', ') '], [], [$mt_data->employee_id]);

                $this->body_file = 'invoice/extract_quotation.php';
            } else {
                redirect('invoice/', 'refresh');
            }
        }
    }

    public function print_other_listing()
    {
        is_logged_in('admin');
        has_permission();

        $listing_type = strtoupper($this->uri->segment(3));

        $html = '';

        $html .= '<style type="text/css">
  			table { border-collapse: collapse; }
  			table th { background: gainsboro; }
  			table th, table td {
  				border: 1px solid gainsboro;
  				padding: 11px; text-align: left;
  			}
  			</style>';

        if ($listing_type == 'AR') {
            $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") as original_doc_ref, sum(total_amt) as total_local_amount, sum(f_amt) as total_foreign_amount FROM accounts_receivable GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC, doc_ref_no ASC';
            $query = $this->db->query($sql);
            $ar_data = $query->result();

            $i = 0;
            foreach ($ar_data as $key => $value) {
                if ($i == 0) {
                    $html .= '<table style="width: 100%">';
                    $html .= '<tr><td style="border: none; text-align: center"><h3>AR LISTING</h3></td></tr>';
                    $html .= '<tr><td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i><hr /></td></tr>';
                    $html .= '</table><br />';
                    $html .= '<table style="width: 100%;">
        				<thead>
        					<tr>
        						<th valign="bottom">DATE</th>
        						<th valign="bottom">REFERENCE</th>
        						<th valign="bottom">CUSTOMER</th>
                                <th valign="bottom">CURRENCY</th>
        						<th valign="bottom">FAMT $</th>
        						<th valign="bottom">SGD $</th>        						
                                <th valign="bottom">REMARKS</th>
                                <th valign="bottom">TRAN</th>
        					</tr>
        				</thead>
        				<tbody>';
                    ++$i;
                }

                $foreign_amount = $value->total_foreign_amount;
                $local_amount = $value->total_local_amount;

                $customer_data = $this->custom->getSingleRow('master_customer', ['code' => $value->customer_code]);

                $new_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$new_date.'</td>';
                $html .= '<td style="width: 100px;">'.$value->original_doc_ref.'</td>';
                $html .= '<td style="width: 200px;">'.$customer_data->name.' ('.$customer_data->code.')</td>';
                $html .= '<td style="width: 60px; text-align: center">'.$value->currency.'</td>';
                $html .= '<td style="width: 120px;">'.number_format($foreign_amount, 2).'</td>';
                $html .= '<td style="width: 120px;">'.number_format($local_amount, 2).'</td>';
                $html .= '<td style="width: 150px;">'.$value->remarks.'</td>';
                $html .= '<td style="width: 100px;">'.$value->tran_type.'</td>';

                $html .= '</tr>';
            }
        } elseif ($listing_type == 'GL') {
            $html .= '<table style="width: 100%">';
            $html .= '<tr><td style="border: none; text-align: center"><h3>GL LISTING</h3></td></tr>';
            $html .= '<tr><td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i><hr /></td></tr>';
            $html .= '</table><br />';
            $html .= '<table style="width: 100%;">
                <thead>
                <tr>
                  <th>Date</th>
                  <th>Reference</th>
                  <th>Remarks</th>
                  <th>Customer</th>
                  <th>Account</th>
                  <th>Debit</th>
                  <th>Credit</th>                  
                </tr>
              </thead>
              <tbody>';

            $sql = 'SELECT doc_date, ref_no, name, code, total_amount, accn, sign, remarks FROM gl, invoice_master, master_customer WHERE gl.ref_no = invoice_master.invoice_ref_no AND invoice_master.customer_id = master_customer.customer_id AND gl.tran_type = "INVOICE" ORDER BY doc_date ASC, ref_no ASC';
            $query = $this->db->query($sql);
            $gl_data = $query->result();

            $total_debit = 0;
            $total_credit = 0;
            foreach ($gl_data as $key => $value) {
                $doc_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                $html .= '<td style="width: 70px;">'.$doc_date.'</td>';
                $html .= '<td style="width: 100px;">'.$value->ref_no.'</td>';
                $html .= '<td style="width: 200px;">'.$value->remarks.'</td>';
                $html .= '<td style="width: 150px;">'.$value->name.' ('.$value->code.')</td>';
                $html .= '<td style="width: 80px;">'.$value->accn.'</td>';

                if ($value->sign == '+') {
                    $html .= '<td style="width: 130px; text-align: right">'.$value->total_amount.'</td>';
                    $html .= '<td style="width: 130px;"></td>';
                    $total_debit += $value->total_amount;
                } else {
                    $html .= '<td style="width: 130px;"></td>';
                    $html .= '<td style="width: 130px; text-align: right">'.$value->total_amount.'</td>';
                    $total_credit += $value->total_amount;
                }

                $html .= '</tr>';
            }

            $html .= '<tr>';
            $html .= '<td colspan="5"><strong>*** Total (SGD) ***</strong></td>';
            $html .= '<td style="text-align: right"><strong>'.number_format($total_debit, 2).'</strong></td>';
            $html .= '<td style="text-align: right"><strong>'.number_format($total_credit, 2).'</strong></td>';
            $html .= '</tr>';
        } elseif ($listing_type == 'GST') {
            $html .= '<table style="width: 100%">';
            $html .= '<tr><td style="border: none; text-align: center"><h3>GST LISTING</h3></td></tr>';
            $html .= '<tr><td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i><hr /></td></tr>';
            $html .= '</table><br />';
            $html .= '<table style="width: 100%;">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Reference</th>
                  <th>Customer</th>
                  <th>Amount</th>
                  <th>GST Category</th>
                  <th>GST Amount</th>
                  <th>GST Type</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>';

            $sql = 'SELECT * FROM gst WHERE tran_type = "INVOICE" ORDER BY dref ASC';
            $query = $this->db->query($sql);
            $gst_data = $query->result();

            foreach ($gst_data as $key => $value) {
                $doc_date = implode('/', array_reverse(explode('-', $value->date)));
                $html .= '<tr>';
                $html .= '<td style="width: 70px;">'.$doc_date.'</td>';
                $html .= '<td style="width: 100px;">'.$value->dref.'</td>';
                $name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $value->iden]);
                $html .= '<td style="width: 150px;">'.$name.' ('.$value->iden.')</td>';
                $html .= '<td style="width: 100px;">'.$value->amou.'</td>';
                $html .= '<td style="width: 130px;">'.$value->gstcate.'</td>';
                $html .= '<td style="width: 70px;">'.$value->gstamou.'</td>';

                if ($value->gsttype == 'O') {
                    $html .= '<td style="width: 70px;">OUTPUT</td>';
                } elseif ($value->gsttype == 'I') {
                    $html .= '<td style="width: 70px;">INPUT</td>';
                }

                $html .= '<td style="width: 200px;">'.$value->rema.'</td>';

                $html .= '</tr>';
            }
        } elseif ($listing_type == 'stock') {
            $html .= '<table style="width: 100%">';
            $html .= '<tr><td style="border: none; text-align: center"><h3>STOCK LISTING</h3></td></tr>';
            $html .= '<tr><td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i><hr /></td></tr>';
            $html .= '</table><br />';
            $html .= '<table style="width: 100%;">
              <thead>
                  <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Stock</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Amount</th>
                    <th>Sign</th>
                  </tr>
                </thead>
                <tbody>';

            $sql = 'SELECT * FROM stock WHERE stock_type = "Invoice" ';
            $query = $this->db->query($sql);
            $stock_data = $query->result();
            foreach ($stock_data as $key => $value) {
                $doc_date = implode('/', array_reverse(explode('-', $value->created_on)));
                $html .= '<tr>';
                $html .= '<td style="width: 80px;">'.$doc_date.'</td>';
                $html .= '<td style="width: 100px;">'.$value->ref_no.'</td>';

                $name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $value->iden]);
                $html .= '<td style="width: 200px;">'.$name.' ('.$value->iden.')</td>';

                $billing_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $value->product_id]);
                $html .= '<td style="width: 300px;">'.$billing_data->billing_description.' ('.$billing_data->stock_code.')</td>';
                $html .= '<td style="width: 100px;">'.$value->quantity.'</td>';
                $html .= '<td style="width: 80px;">'.$value->unit_cost.'</td>';
                $stock_amount = $value->quantity * $value->unit_cost;
                $html .= '<td style="width: 80px;">'.$stock_amount.'</td>';
                $html .= '<td style="width: 80px;">'.$value->sign.'</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';

        $file = $listing_type.'_listing_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    public function print_listing() // Print All from Listing Page
    {

        $type = strtoupper($this->uri->segment(3));

        $html = '';
        $html .= '<style type="text/css">
            table { width: 100%; }
            table { border-collapse: collapse; }
            table th {background: gainsboro; }
            table th, table td {
            border: 1px solid gainsboro;
            padding: 10px; text-align: left;
            }
         </style>';

        $html .= '<div style="width: 100%; margin: auto;text-align: center;"><h3>'.$type.' INVOICES</h3></div>';

        $html .= '<table>
         <tr>
            <th style="width: 120px">DATE</th>
            <th style="width: 120px">REFERENCE</th>
            <th style="width: 280px">CUSTOMER</th>
            <th style="width: 130px">SUBTOTAL</th>
            <th style="width: 160px">DISCOUNT</th>
            <th style="width: 130px">NET TOTAL</th>
         </tr>';

        

        $table = 'invoice_master';
        $columns = ['invoice_id', 'modified_on', 'invoice_ref_no', 'customer_id', 'sub_total', 'lsd_code', 'lsd_percentage', 'lsd_value', 'net_after_lsd', 'net_total'];
        $where = ['status' => $type];
        $join_table = null;
        $join_condition = null;
        $table_id = 'invoice_id';

        $list = $this->inv_model->get_invoices($table, $columns, $join_table, $join_condition, $where, $table_id);

        $i = 1;
        foreach ($list as $record) {
            $customer_data = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['customer_id' => $record->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td valign="top">'.strtoupper(date('M j, Y', strtotime($record->modified_on))).'</td>';
            $html .= '<td valign="top">'.$record->invoice_ref_no.'</td>';
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

        $file = 'invoices_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
    }

    // Print - New & Edit Invoice Function
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
        $this->data['customer_gst_number'] = $customer_data->gst_number;

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
            if ($data['gst_category'][$i] == 'SRCA-S') {
                $special_gst_srcas_exist = true;
                $special_gst_srcas_amount += $data['amount'][$i] * $default_gst_rate / 100;
            }
        }
        $this->data['special_gst_srcas_exist'] = $special_gst_srcas_exist;
        $this->data['special_gst_srcas_amount'] = $special_gst_srcas_amount;

        $file = 'invoice_'.date('YmdHis').'.pdf';
        $document = $this->load->view('invoice/print_stage_1.php', $this->data, true);
        $this->custom->printMPDF($file, $document);
    }

    // this invoice will be printed after submitted
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
        $this->data['mt_data'] = $mt_data = $this->custom->getSingleRow('invoice_master', ['invoice_id' => $row_id]);

        if ($mt_data) {
            // company details
            $this->data['company_details'] = $this->custom->populateCompanyHeader();
            $this->data['system_currency'] = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

            // invoice item (product / service) details
            $this->data['pr_data'] = $pr_data = $this->inv_model->get_invoice_items($row_id);

            // customer and employee details
            $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $mt_data->customer_id]);
            $this->data['customer_address'] = $this->custom->populateCustomerAddress($customer_data);
            $this->data['customer_name'] = $customer_data->name;
            $this->data['customer_code'] = $customer_data->code;
            $this->data['customer_gst_number'] = $customer_data->gst_number;

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

            $file = 'invoice_'.date('YmdHis').'.pdf';
            $document = $this->load->view('invoice/print_stage_2.php', $this->data, true);
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
                $mpdf->Output($file, 'I');
            }

            if ($action == 'email') {
                $path = BASEPATH.'upload/invoice/';

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
                    $mail->Subject = 'Information About Invoice';

                    // body
                    $mail->Body = $document;

                    // attachements
                    $mail->AddAttachment($pdfFilePath);

                    // Send
                    if (!$mail->send()) {
                        // echo 'Email could not be sent.';
                        // echo 'Mailer Error: '.$mail->ErrorInfo;
                        $message = 'error';
                    } else {
                        // echo 'Email has been sent';
                        $message = 'success';
                    }
                } else {
                    $message = 'error';
                }
                echo $message;
            }
        } else {
            redirect('invoice/listing/', 'refresh');
        }
    }

    public function customer_price()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'invoice/customer_special_price/listing.php';
    }

    public function add_customer_price()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ' '], ['active' => 1]);
        $this->body_file = 'invoice/customer_special_price/add.php';
    }

    public function edit_customer_price($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $customer_code = $this->custom->getSingleValue('customer_price', 'customer_code', ['pt_id' => $row_id]);
            $customer_id = $this->custom->getSingleValue('master_customer', 'customer_id', ['code' => $customer_code]);
            $this->body_vars['customer_code'] = $customer_code;
            $this->body_vars['customer_options'] = $customer_options = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ' '], ['active' => 1], ['customer_id' => $customer_id]);
            $this->body_file = 'invoice/customer_special_price/edit.php';
        }
    }

    public function view_customer_price($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $customer_code = $this->custom->getSingleValue('customer_price', 'customer_code', ['pt_id' => $row_id]);
            $customer_id = $this->custom->getSingleValue('master_customer', 'customer_id', ['code' => $customer_code]);
            $this->body_vars['customer_code'] = $customer_code;
            $this->body_vars['customer_options'] = $customer_options = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ' '], ['active' => 1], ['customer_id' => $customer_id]);
            $this->body_file = 'invoice/customer_special_price/edit.php';
        }
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'invoice_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
        'tables' => ['invoice_setting', 'invoice_master', 'invoice_product_master'],
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

    public function df_backup_cstmr_price($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'cust_spcl_price_'.date('Y-m-d_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['customer_price'],
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
            set_flash_message('message', 'success', 'Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('invoice/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'invoice/blank.php';
        zapinvoice();
        redirect('invoice/', 'refresh');
    }

    public function df_zap_cstmr_price()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'invoice/blank.php';
        zapCustomer_price();
        redirect('invoice/', 'refresh');
    }
}
