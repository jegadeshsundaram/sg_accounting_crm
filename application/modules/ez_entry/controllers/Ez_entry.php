<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ez_entry extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ez_entry/ez_entry_model', 'ez_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'ez_entry/options.php';
    }

    public function batch_sales()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_sales($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $page = 'edit';

            $sales_data = $this->custom->getMultiValues('ez_sales', 'doc_date, ref_no, customer_id, exchange_rate', ['sb_id' => $row_id]);

            $this->body_vars['doc_date'] = $sales_data->doc_date;
            $this->body_vars['ref_no'] = $sales_data->ref_no;
            $this->body_vars['customer_id'] = $sales_data->customer_id;

            $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1], ['customer_id' => $sales_data->customer_id]);

            $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $sales_data->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $this->body_vars['currency'] = $currency;

            $this->body_vars['exchange_rate'] = $sales_data->exchange_rate;

        } else {

            $page = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['customer_id'] = '';

            $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);

            $this->body_vars['currency'] = '';
            $this->body_vars['exchange_rate'] = '';
        }

        $this->body_vars['page'] = $page;

        $this->body_vars['sales_accns'] = $this->custom->populateCOASalesList();

        $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);
        $this->body_vars['std_gst_rate'] = $std_gst_rate;

        $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply'], ['SR']);
    }
    
    public function batch_purchase()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_purchase_batch($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $page = 'edit';

            $purchase_data = $this->custom->getMultiValues('ez_purchase', 'doc_date, ref_no, supplier_id, exchange_rate', ['pb_id' => $row_id]);

            $this->body_vars['doc_date'] = $purchase_data->doc_date;
            $this->body_vars['ref_no'] = $purchase_data->ref_no;
            $this->body_vars['supplier_id'] = $purchase_data->supplier_id;

            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['supplier_id' => $purchase_data->supplier_id]);

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $purchase_data->supplier_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $this->body_vars['currency'] = $currency;

            $this->body_vars['exchange_rate'] = $purchase_data->exchange_rate;
        } else {
            $page = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['supplier_id'] = '';

            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);

            $this->body_vars['currency'] = '';
            $this->body_vars['exchange_rate'] = '';
        }

        $this->body_vars['page'] = $page;

        $this->body_vars['purchase_accns'] = $this->custom->populateCOAPurchasesList();

        $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'TX']);
        $this->body_vars['std_gst_rate'] = $std_gst_rate;

        $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase'], ['TX']);
    }

    public function batch_receipt() {
        is_logged_in('admin');
        has_permission();

        $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
        $this->body_vars['bank'] = $bank_data->accn;
        $this->body_vars['foreign_bank'] = $bank_data->fb_accn;
           
        $this->body_vars['banks'] = $this->custom->populateCOABankListWithFB();
        $this->body_vars['foreign_banks'] = $this->custom->populateFBAccounts();

        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
    }

    public function batch_settlement() {
        is_logged_in('admin');
        has_permission();

        $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
        $this->body_vars['bank'] = $bank_data->accn;
        $this->body_vars['foreign_bank'] = $bank_data->fb_accn;
           
        $this->body_vars['banks'] = $this->custom->populateCOABankListWithFB($bank_data->accn);
        $this->body_vars['foreign_banks'] = $this->custom->populateFBAccounts($bank_data->fb_accn);

        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
    }

    public function other_payment() {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_other_payment($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $page = 'edit';

            $payment_data = $this->custom->getMultiValues('ez_payment', 'doc_date, ref_no, remarks', ['batch_id' => $row_id]);

            $this->body_vars['doc_date'] = $payment_data->doc_date;
            $this->body_vars['ref_no'] = $payment_data->ref_no;
            $this->body_vars['remarks'] = $payment_data->remarks;
            
        } else {
            $page = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['remarks'] = '';
        }

        $this->body_vars['page'] = $page;

        $this->body_vars['co_accns'] = $this->custom->populateCOAByCode();

        $bank_accn = $this->custom->getSingleValue('bank', 'accn', ['accn_type' => 'CA']);
        $this->body_vars['bank'] = $bank_accn;
        $this->body_vars['banks'] = $this->custom->populateCOABankList($bank_accn);

        $this->body_vars['input_gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], '', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
    }
    
    public function other_adjustment() {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_other_adjustment($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $page = 'edit';

            $data = $this->custom->getMultiValues('ez_adjustment', 'doc_date, ref_no, remarks', ['batch_id' => $row_id]);

            $this->body_vars['doc_date'] = $data->doc_date;
            $this->body_vars['ref_no'] = $data->ref_no;
            $this->body_vars['remarks'] = $data->remarks;

            $ca_field = 0;
            $er_field = 0;
            $this->db->select('accn, control_account');
            $this->db->from('ez_adjustment');
            $this->db->where('doc_date = "'.$data->doc_date.'" AND ref_no = "'.$data->ref_no.'"');
            $query = $this->db->get();
            $entries = $query->result();
            foreach ($entries as $value) {

                $currency_id = 0;

                if($value->accn == "CA001") {
                    $ca_field = 1;
                    
                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $value->control_account]);                    

                } elseif($value->accn == "CL001") {
                    $ca_field = 1;

                    $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $value->control_account]);

                } elseif($value->accn == "CA110") {
                    $ca_field = 1;

                    $currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_id' => $value->control_account]);                    
                }

                // accn is CA001 | CL001 | CA110
                if($currency_id > 0) {
                    $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
                    if($currency !== "SGD") {
                        $er_field = 1;
                    }
                }
            }

            $this->body_vars['ca_field'] = $ca_field;
            $this->body_vars['er_field'] = $er_field;
            
        } else {
            $page = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['remarks'] = '';

            $this->body_vars['ca_field'] = '';
            $this->body_vars['er_field'] = '';
        }

        $this->body_vars['page'] = $page;

        $this->body_vars['co_accns'] = $this->custom->populateCOAByCode();
        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
        $this->body_vars['foreign_banks'] = $this->custom->createDropdownSelect('master_foreign_bank', ['fb_id', 'fb_name', 'fb_code', 'currency_id'], 'Foreign Bank', ['( ', ') ', '']);
        $this->body_vars['gst_input_categories'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
        $this->body_vars['gst_output_categories'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);
    }

    public function debtor() {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], '-- Select --', ['( ', ') ', ''], ['active' => 1]);
    }

    public function debtor_contra() {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], '-- Select --', ['( ', ') ', ''], ['active' => 1]);
    }

    public function save_debtor_contra()
    {
        $post = $this->input->post();

        if ($post) {
            $ids = $post['ar_ids'];
            $final_balance_entry_id = $post['final_balance_entry_id'];
            $final_balance_entry_reference = $post['final_balance_entry_reference'];
            $final_balance_total = $post['final_balance_amount'];

            $splitted_ids = explode(',', $ids);

            foreach ($splitted_ids as $value) {
                $ar_id = $value;
                $data = $this->custom->getSingleRow('accounts_receivable', ['ar_id' => $ar_id]);

                // insert partially settled & un-settled entries
                if ($final_balance_entry_id !== '' && $ar_id == $final_balance_entry_id) {

                    $currency_code = $data->currency;
                    $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['code' => $currency_code]);

                    // Original reference with Unsettled amount
                    $un_settled_amount = $final_balance_total;
                    $un_settled_amount_local = $un_settled_amount / $currency_rate;
                    
                    $entry['doc_ref_no'] = $data->doc_ref_no;
                    $entry['customer_code'] = $data->customer_code;
                    $entry['doc_date'] = $data->doc_date;
                    $entry['currency'] = $data->currency;
                    $entry['total_amt'] = number_format($un_settled_amount_local, 2, '.', '');
                    $entry['f_amt'] = number_format($un_settled_amount, 2, '.', '');
                    $entry['sign'] = $data->sign;
                    $entry['remarks'] = $data->remarks;
                    $entry['settled'] = 'n';
                    $entry['tran_type'] = 'BTHCONTRA';
                    $insert = $this->custom->insertRow('ez_debtor', $entry);

                    // Original Reference adding "_sp_1" with settled amount
                    $full_amount = $data->f_amt;
                    $settled_amount = $full_amount - $final_balance_total;
                    $settled_amount_local = $settled_amount / $currency_rate;

                    $entry['doc_ref_no'] = $data->doc_ref_no.'_sp_1';
                    $entry['total_amt'] = number_format($settled_amount_local, 2, '.', '');
                    $entry['f_amt'] = number_format($settled_amount, 2, '.', '');
                    $entry['settled'] = 'y';
                    $insert = $this->custom->insertRow('ez_debtor', $entry);
                    
                } else { // insert other entries
                    $entry['doc_ref_no'] = $data->doc_ref_no;
                    $entry['customer_code'] = $data->customer_code;
                    $entry['doc_date'] = $data->doc_date;
                    $entry['currency'] = $data->currency;
                    $entry['total_amt'] = $data->total_amt;
                    $entry['f_amt'] = $data->f_amt;
                    $entry['sign'] = $data->sign;
                    $entry['remarks'] = $data->remarks;
                    $entry['settled'] = 'y';
                    $entry['tran_type'] = 'BTHCONTRA';
                    $insert = $this->custom->insertRow('ez_debtor', $entry);
                }
            }

            if ($insert) {
                set_flash_message('message', 'success', 'Contra Saved!');
            } else {
                set_flash_message('message', 'danger', 'Contra Error!');
            }

            redirect('ez_entry/debtor');
        } else {
            set_flash_message('message', 'danger', 'Request Error!');
            redirect('ez_entry/debtor');
        }
    }

    public function creditor() {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '-- Select --', ['( ', ') ', ''], ['active' => 1]);
    }

    public function creditor_contra() {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '-- Select --', ['( ', ') ', ''], ['active' => 1]);
    }

    public function save_creditor_contra()
    {
        $post = $this->input->post();

        if ($post) {
            $ids = $post['ap_ids'];
            $final_balance_entry_id = $post['final_balance_entry_id'];
            $final_balance_entry_reference = $post['final_balance_entry_reference'];
            $final_balance_total = $post['final_balance_amount'];

            $splitted_ids = explode(',', $ids);

            foreach ($splitted_ids as $value) {
                $ap_id = $value;
                $data = $this->custom->getSingleRow('accounts_payable', ['ap_id' => $ap_id]);

                // insert partially settled & un-settled entries
                if ($final_balance_entry_id !== '' && $ap_id == $final_balance_entry_id) {

                    $currency_code = $data->currency;
                    $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['code' => $currency_code]);

                    // Original reference with Unsettled amount
                    $un_settled_amount = $final_balance_total;                                       
                    $un_settled_amount_local = $un_settled_amount / $currency_rate;

                    $entry['doc_ref_no'] = $data->doc_ref_no;
                    $entry['supplier_code'] = $data->supplier_code;
                    $entry['doc_date'] = $data->doc_date;
                    $entry['currency'] = $data->currency;
                    $entry['total_amt'] = number_format($un_settled_amount_local, 2, '.', '');
                    $entry['fa_amt'] = number_format($un_settled_amount, 2, '.', '');
                    $entry['sign'] = $data->sign;
                    $entry['remarks'] = $data->remarks;
                    $entry['settled'] = 'n';
                    $entry['tran_type'] = 'BTHCONTRA';
                    $insert = $this->custom->insertRow('ez_creditor', $entry);

                    // Original Reference adding "_sp_1" with settled amount
                    $full_amount = $data->fa_amt;
                    $settled_amount = $full_amount - $final_balance_total;
                    $settled_amount_local = $settled_amount / $currency_rate;

                    $entry['doc_ref_no'] = $data->doc_ref_no.'_sp_1';
                    $entry['total_amt'] = number_format($settled_amount_local, 2, '.', '');
                    $entry['fa_amt'] = number_format($settled_amount, 2, '.', '');
                    $entry['settled'] = 'y';
                    $insert = $this->custom->insertRow('ez_creditor', $entry);
                    
                } else { // insert other entries
                    $entry['doc_ref_no'] = $data->doc_ref_no;
                    $entry['supplier_code'] = $data->supplier_code;
                    $entry['doc_date'] = $data->doc_date;
                    $entry['currency'] = $data->currency;
                    $entry['total_amt'] = $data->total_amt;
                    $entry['fa_amt'] = $data->fa_amt;
                    $entry['sign'] = $data->sign;
                    $entry['remarks'] = $data->remarks;
                    $entry['settled'] = 'y';
                    $entry['tran_type'] = 'BTHCONTRA';
                    $insert = $this->custom->insertRow('ez_creditor', $entry);
                }
            }

            if ($insert) {
                set_flash_message('message', 'success', 'Contra Saved!');
            } else {
                set_flash_message('message', 'danger', 'Contra Error!');
            }

            redirect('ez_entry/creditor');
        } else {
            set_flash_message('message', 'danger', 'Request Error!');
            redirect('ez_entry/creditor');
        }
    }

    public function print_sales_audit_detailed()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>SALES AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        // Extract ALL the records from ez_sales TBL GROUP BY DATE, REFERENCE, CUSTOMER is SAME
        $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
        $this->db->from('ez_sales');
        $this->db->group_by('ref_no, customer_id, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_sales_all_records = $query->result();
        $debit_grand_total = 0;
        $credit_grand_total = 0;
        $i = 0;

        $html .= '<table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>REFERENCE</th>
                            <th>REMARKS</th>
                            <th>CUSTOMER</th>
                            <th>ACCOUNT</th>
                            <th style="text-align: right">DEBIT</th>
                            <th style="text-align: right">CREDIT</th>
                        </tr>
                    </thead>
                    <tbody>
						<tr><td colspan="7" height="5" style="border: none"></td></tr>';

        foreach ($batch_sales_all_records as $key => $value) {
            // Document Information
            $document_date = $value->doc_date;
            $document_reference = $value->ref_no;
            $document_remarks = $value->remarks;

            // Amount Information
            $batch_local_amount_same_reference = $value->batch_local_amount;
            $batch_local_gst_amount_same_reference = $value->batch_local_gst_amount;
            $batch_foreign_amount_same_reference = $value->batch_foreign_amount;
            $batch_foreign_gst_amount_same_reference = $value->batch_foreign_gst_amount;

            $debit_grand_total += $value->batch_local_amount + $value->batch_local_gst_amount;

            $iden_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $value->customer_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden_data->currency_id]);
            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_code]);

            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));

            // Debit Entry - Start
            $html .= '<tr>';
            $html .= '<td style="width: 90px;">'.$document_date.'</td>';
            $html .= '<td style="width: 110px;">'.$value->ref_no.'</td>';
            $html .= '<td style="width: 140px;">'.$document_remarks.'</td>';
            $html .= '<td style="width: 190px;">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 190px;">CA001 : DEBTORS CONTROL ACCOUNT</td>';

            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 150px; text-align: right"><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_reference + $batch_local_gst_amount_same_reference, 2).'</td>';
            } else {
                $html .= '<td style="width: 150px; text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($batch_foreign_amount_same_reference + $batch_foreign_gst_amount_same_reference, 2).'<hr style="border: 0; border-bottom: 1px dotted #f5f5f5;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_reference + $batch_local_gst_amount_same_reference, 2).'</td>';
            }

            $html .= '<td style="width: 170px; text-align: right"></td>';
            $html .= '</tr>';

            // Debit Entry - End

            // GST Entry - Start
            if ($batch_local_gst_amount_same_reference > 0) {
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$value->ref_no.'</td>';
                $html .= '<td>'.$document_remarks.'</td>';
                $html .= '<td>'.$iden_data->name.' ('.$iden_data->code.')</td>';
                $html .= '<td>CL300 : GOODS & SERVICES TAX</td>';
                $html .= '<td></td>';
                if ($currency_code == 'SGD') {
                    $html .= '<td style="text-align: right"><span style="color: red;">SGD</span> '.number_format($batch_local_gst_amount_same_reference, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right"><span style="color: red; text-align: right">'.$currency_code.'</span> '.number_format($batch_foreign_gst_amount_same_reference, 2).'<hr style="border: 0; border-bottom: 1px dotted #f5f5f5;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_gst_amount_same_reference, 2).'</td>';
                }
                $html .= '</tr>';
                $credit_grand_total += $batch_local_gst_amount_same_reference;
            }
            // GST Entry - End

            // Credit Entry
            // Extract ALL the records from ez_sales TBL where DATE, REFERENCE, CUSTOMER and GROUP BY SALES ACCOUNT
            $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
            $this->db->from('ez_sales');
            $this->db->where('doc_date = "'.$value->doc_date.'" AND ref_no = "'.$value->ref_no.'" AND customer_id = "'.$value->customer_id.'"');
            $this->db->group_by('sales_accn');
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $batch_sales_account_specific_records = $query->result();

            $row_number = 0;
            foreach ($batch_sales_account_specific_records as $key => $value) {
                ++$row_number;

                // Sales Account Information
                $coa_data = $this->custom->getSingleRow('chart_of_account', ['accn' => $value->sales_accn]);

                // Amount Information
                $batch_local_amount_same_account = $value->batch_local_amount;
                $batch_foreign_amount_same_account = $value->batch_foreign_amount;

                // Start - Display Sales Account Record

                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$value->ref_no.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                $html .= '<td>'.$iden_data->name.' ('.$iden_data->code.')</td>';
                $html .= '<td>'.$coa_data->accn.' : '.$coa_data->description.'</td>';
                $html .= '<td></td>';

                if ($currency_code == 'SGD') {
                    $html .= '<td style="text-align: right"><span style="color: red;">SGD</span> '.number_format($batch_local_amount_same_account, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right"><span style="color: red;">'.$currency_code.'</span> '.number_format($batch_foreign_amount_same_account, 2).' <hr style="border: 0; border-bottom: 1px dotted #f5f5f5;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_account, 2).' </td>';
                }
                $html .= '</tr>';

                $credit_grand_total += $batch_local_amount_same_account;
                // End - Post to GL
            } // Second Loop end

            $html .= '<tr><td colspan="7" height="8" style="border: none"></td></tr>';

            ++$i;
        } // First Loop End

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="5" style="padding-top: 10px; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'ez_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_sales_audit_summary()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>SALES AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        // Extract ALL the records from ez_sales TBL GROUP BY DATE, REFERENCE, CUSTOMER is SAME
        $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
        $this->db->from('ez_sales');
        $this->db->group_by('ref_no, customer_id, doc_date');
        $this->db->order_by('doc_date, ref_no', 'ASC, ASC');
        $query = $this->db->get();
        $batch_entries = $query->result();

        $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
								<th>CUSTOMER</th>
								<th>CURRENCY</th>
								<th style="text-align: right">TOTAL FAMT $</th>
								<th style="text-align: right">X-RATE</th>
								<th style="text-align: right">TOTAL SGD $</th>
							</tr>
						</thead>
						<tbody>';

        $i = 0;
        $foreign_total = 0;
        $local_total = 0;
        foreach ($batch_entries as $key => $value) {
            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $document_reference = $value->ref_no;

            // Amount Information
            $total_sgd = $value->batch_local_amount + $value->batch_local_gst_amount;
            $total_famt = $value->batch_foreign_amount + $value->batch_foreign_gst_amount;

            $iden_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $value->customer_id]);
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $iden_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td style="width: 90px;">'.$document_date.'</td>';
            $html .= '<td style="width: 110px;">'.$document_reference.'</td>';
            $html .= '<td style="width: 200px;">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 70px;">'.$currency_data->code.'</td>';
            $html .= '<td style="width: 150px; text-align: right">'.number_format($total_famt, 2).'</td>';
            $html .= '<td style="width: 100px;">'.$value->exchange_rate.'</td>';
            $html .= '<td style="width: 150px; text-align: right">'.number_format($total_sgd, 2).'</td>';
            $html .= '</tr>';

            $foreign_total += $total_famt;
            $local_total += $total_sgd;

            ++$i;
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" style="padding-top: 10px; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($foreign_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold"></td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($local_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'sales_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_purchase_audit_detailed()
    {
        is_logged_in('admin');
        has_permission();       

        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>PURCHASE AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';        

        $debit_grand_total = 0;
        $credit_grand_total = 0;
        $i = 0;

        $html .= '<table style="width: 100%;">
		        <thead>
		          <tr>
		            <th>DATE</th>
		            <th>REFERENCE</th>
                    <th>REMARKS</th>
		            <th>SUPPLIER</th>
		            <th>ACCOUNT</th>
		            <th style="text-align: right">DEBIT</th>
                    <th style="text-align: right">CREDIT</th>           
		          </tr>
		        </thead>
		        <tbody>
				    <tr>
                        <td colspan="7" height="5" style="border: none"></td>
                    </tr>';

        // Extract ALL the records from ez_purchase TBL GROUP BY DATE, REFERENCE, SUPPLIER is SAME
        $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
        $this->db->from('ez_purchase');
        $this->db->group_by('ref_no, supplier_id, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_purchases_all_records = $query->result();
        foreach ($batch_purchases_all_records as $keys => $values) {
            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $values->doc_date)));
            $document_reference = $values->ref_no;
            $document_remarks = $values->remarks;

            // Amount Information
            $batch_local_amount_same_reference = $values->batch_local_amount;
            $batch_local_gst_amount_same_reference = $values->batch_local_gst_amount;
            $batch_foreign_amount_same_reference = $values->batch_foreign_amount;
            $batch_foreign_gst_amount_same_reference = $values->batch_foreign_gst_amount;

            $credit_grand_total += $values->batch_local_amount + $values->batch_local_gst_amount;

            // Supplier Information
            $iden_data = $this->custom->getMultiValues('master_supplier', 'name, code, currency_id', ['supplier_id' => $values->supplier_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden_data->currency_id]);
            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $values->gst_code]);

            // Credit Entry - Start
            $html .= '<tr>';
            $html .= '<td style="width: 90px;">'.$document_date.'</td>';
            $html .= '<td style="width: 110px;">'.$document_reference.'</td>';
            $html .= '<td style="width: 140px;">'.$document_remarks.'</td>';
            $html .= '<td style="width: 180px;">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 220px;">CL001 : TRADE CREDITORS CONTROL ACCOUNT</td>';
            $html .= '<td style="width: 150px; text-align: right"></td>';
            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 150px; text-align: right"><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_reference + $batch_local_gst_amount_same_reference, 2).'</td>';
            } else {
                $html .= '<td style="width: 150px; text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($batch_foreign_amount_same_reference + $batch_foreign_gst_amount_same_reference, 2).'<hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_reference + $batch_local_gst_amount_same_reference, 2).'</td>';
            }
            $html .= '</tr>';
            // Credit Entry - End

            // GST Entry - Start
            if ($batch_local_gst_amount_same_reference > 0) {
                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td>'.$iden_data->name.' ('.$iden_data->code.')</td>';
                $html .= '<td>CL300 : GOODS & SERVICES TAX</td>';
                if ($currency_code == 'SGD') {
                    $html .= '<td style="text-align: right"><span style="color: red">SGD</span> '.number_format($batch_local_gst_amount_same_reference, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($batch_foreign_gst_amount_same_reference, 2).'<hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_gst_amount_same_reference, 2).'</td>';
                }

                $html .= '<td></td>';
                $html .= '</tr>';
                $debit_grand_total += $batch_local_gst_amount_same_reference;
            }
            // GST Entry - End

            // Extract ALL the records from ez_purchase TBL where DATE, REFERENCE, SUPPLIER and GROUP BY PURCHASE ACCOUNT
            $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
            $this->db->from('ez_purchase');
            $this->db->where('doc_date = "'.$values->doc_date.'" AND ref_no = "'.$values->ref_no.'" AND supplier_id = "'.$values->supplier_id.'"');
            $this->db->group_by('purchase_accn');
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $batch_purchase_account_specific_records = $query->result();

            $row_number = 0;
            foreach ($batch_purchase_account_specific_records as $key => $value) {
                ++$row_number;

                // Purchase Account Information
                $coa_data = $this->custom->getSingleRow('chart_of_account', ['accn' => $value->purchase_accn]);

                // Amount Information
                $batch_local_amount_same_account = $value->batch_local_amount;
                $batch_foreign_amount_same_account = $value->batch_foreign_amount;

                $html .= '<tr>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td>'.$value->remarks.'</td>';
                $html .= '<td>'.$iden_data->name.' ('.$iden_data->code.')</td>';
                $html .= '<td>'.$coa_data->accn.' : '.$coa_data->description.'</td>';
                if ($currency_code == 'SGD') {
                    $html .= '<td style="text-align: right"><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_account, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right"><span style="color: red">'.$currency_code.'</span>'.number_format($batch_foreign_amount_same_account, 2).'<hr style="border: 0; border-bottom: 1px dotted #f5f5f5;" /><br /><span style="color: red">SGD</span> '.number_format($batch_local_amount_same_account, 2).'</td>';
                }
                $html .= '<td></td>';
                $html .= '</tr>';

                $debit_grand_total += $batch_local_amount_same_account;
            } // Second Loop end

            $html .= '<tr><td colspan="7" height="8" style="border: none"></td></tr>';

            ++$i;
        } // First Loop End

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="5" style="padding-top: 10px; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; border-top: 1px solid gray; border-bottom: 1px solid gray; font-weight: bold">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'purc_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_purchase_audit_summary()
    {
        is_logged_in('admin');
        has_permission();
       
        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>PURCHASE AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';        

        $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
								<th>SUPPLIER</th>
								<th>CURRENCY</th>
								<th style="text-align: right">TOTAL FAMT $</th>
								<th style="text-align: right">X-RATE</th>
								<th style="text-align: right">TOTAL SGD $</th>
							</tr>
						</thead>
						<tbody>';

        $i = 0;
        $foreign_total = 0;
        $local_total = 0;

        // Extract ALL the records from ez_sales TBL GROUP BY DATE, REFERENCE, CUSTOMER is SAME
        $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
        $this->db->from('ez_purchase');
        $this->db->group_by('ref_no, supplier_id, doc_date');
        $this->db->order_by('doc_date, ref_no', 'ASC, ASC');
        $query = $this->db->get();
        $batch_entries = $query->result();
        foreach ($batch_entries as $key => $value) {
            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $document_reference = $value->ref_no;

            // Amount Information
            $total_sgd = $value->batch_local_amount + $value->batch_local_gst_amount;
            $total_famt = $value->batch_foreign_amount + $value->batch_foreign_gst_amount;

            $iden_data = $this->custom->getMultiValues('master_supplier', 'name, code, currency_id', ['supplier_id' => $value->supplier_id]);
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $iden_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td style="width: 90px;">'.$document_date.'</td>';
            $html .= '<td style="width: 110px;">'.$document_reference.'</td>';
            $html .= '<td style="width: 200px;">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 70px;">'.$currency_data->code.'</td>';
            $html .= '<td style="width: 150px; text-align: right">'.number_format($total_famt, 2).'</td>';
            $html .= '<td style="width: 100px; text-align: right">'.$value->exchange_rate.'</td>';
            $html .= '<td style="width: 150px; text-align: right">'.number_format($total_sgd, 2).'</td>';
            $html .= '</tr>';

            $foreign_total += $total_famt;
            $local_total += $total_sgd;

            ++$i;
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" style="padding-top: 10px; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($foreign_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold"></td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($local_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'purc_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_receipt_audit_trail()
    {    
        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>RECEIPT AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        $i = 0;
        $debit_grand_total = 0;
        $credit_grand_total = 0;

        $this->db->select('*');
        $this->db->from('ez_receipt');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $batch_tbl_data = $query->result();
        foreach ($batch_tbl_data as $key => $value) {
            if ($i == 0) {
                $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
                                <th>REMARKS</th>
								<th>CUSTOMER</th>
								<th>ACCOUNT</th>
								<th style="text-align: right">DEBIT</th>
								<th style="text-align: right">CREDIT</th>
							</tr>
						</thead>
						<tbody>';
            }

            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $document_reference = $value->ref_no;
            $document_remarks = $value->remarks;

            // Amount Information
            $local_amount = $value->local_amount;
            $foreign_amount = $value->foreign_amount;

            $debit_grand_total += $value->local_amount;
            $credit_grand_total += $value->local_amount;

            // Credit Entry - CA001
            $iden_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $value->customer_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td style="width: 90px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_date.'</td>';
            $html .= '<td style="width: 110px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_reference.'</td>';
            $html .= '<td style="width: 120px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_remarks.'</td>';
            $html .= '<td style="width: 180px; border-bottom: 1px solid dimgray;" rowspan="2">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 180px;">CA001 : DEBTORS CONTROL ACCOUNT</td>';
            $html .= '<td style="width: 100px;"></td>';

            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 170px; text-align: right"><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            } else {
                $html .= '<td style="width: 170px; text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($foreign_amount, 2).'<hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br /><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            }

            $html .= '</tr>';

            // Debit Entry - Selected BANK ACCOUNT
            $accn = $value->bank_accn;
            $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $accn]);

            $html .= '<tr>';
            $html .= '<td style="width: 150px; border-bottom: 1px solid dimgray;">'.$accn.' : '.$accn_description.'</td>';
            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 170px; border-bottom: 1px solid dimgray; text-align: right"><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            } else {
                $html .= '<td style="width: 170px; border-bottom: 1px solid dimgray; text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($foreign_amount, 2).'<hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br /><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            }
            $html .= '<td style="width: 100px; border-bottom: 1px solid dimgray;"></td>';
            $html .= '</tr>';

            ++$i;
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="5" style="padding-top: 10px; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'rec_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_settlement_audit_trail()
    {
        is_logged_in('admin');
        has_permission();

        $html = '';
  
        $html .= '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>AP SETTLEMENT AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        $i = 0;
        $debit_grand_total = 0;
        $credit_grand_total = 0;

        $this->db->select('*');
        $this->db->from('ez_settlement');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $batch_tbl_data = $query->result();
        foreach ($batch_tbl_data as $key => $value) {
            if ($i == 0) {
                $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
                                <th>REMARKS</th>
								<th>SUPPLIER</th>
								<th>ACCOUNT</th>
								<th style="text-align: right">DEBIT</th>
								<th style="text-align: right">CREDIT</th>
							</tr>
						</thead>
						<tbody>';
            }

            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $document_reference = $value->ref_no;
            $document_remarks = $value->remarks;

            // Amount Information
            $local_amount = $value->local_amount;
            $foreign_amount = $value->foreign_amount;

            $debit_grand_total += $value->local_amount;
            $credit_grand_total += $value->local_amount;

            // Debit Entry - CL001

            $iden_data = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $value->supplier_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden_data->currency_id]);

            $html .= '<tr>';
            $html .= '<td style="width: 90px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_date.'</td>';
            $html .= '<td style="width: 110px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_reference.'</td>';
            $html .= '<td style="width: 120px; border-bottom: 1px solid dimgray;" rowspan="2">'.$document_remarks.'</td>';
            $html .= '<td style="width: 150px; border-bottom: 1px solid dimgray;" rowspan="2">'.$iden_data->name.' ('.$iden_data->code.')</td>';
            $html .= '<td style="width: 200px;">CL001 : TRADE CREDITORS CONTROL ACCOUNT</td>';

            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 200px; text-align: right"><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            } else {
                $html .= '<td style="width: 200px; text-align: right"><span style="color: red">'.$currency_code.'</span> '.number_format($foreign_amount, 2).'<hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br /><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            }

            $html .= '<td style="width: 100px;"></td>';
            $html .= '</tr>';

            // Debit Entry - Selected BANK ACCOUNT
            $accn = $value->bank_accn;
            $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $accn]);

            $html .= '<tr>';
            $html .= '<td style="width: 150px; border-bottom: 1px solid dimgray;">'.$accn.' : '.$accn_description.'</td>';
            $html .= '<td style="width: 100px; border-bottom: 1px solid dimgray;"></td>';
            if ($currency_code == 'SGD') {
                $html .= '<td style="width: 170px; border-bottom: 1px solid dimgray; text-align: right"><span style="color: red">SGD</span> '.number_format($local_amount, 2).'</td>';
            } else {
                $html .= '<td style="width: 170px; border-bottom: 1px solid dimgray; text-align: right">'.number_format($foreign_amount, 2).' <span style="color: red">'.$currency_code.'</span><hr style="border: 0; border-bottom: 1px dotted lightgray;" /><br />'.number_format($local_amount, 2).' <span style="color: red">SGD</span></td>';
            }
            $html .= '</tr>';

            ++$i;
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="5" style="padding-top: 10px; font-weight: bold">*** Total (SGD) ***</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="padding-top: 10px; text-align: right; font-weight: bold">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'set_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_payment()
    {
        is_logged_in('admin');
        has_permission();

        $html = '';

        $html .= '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>PAYMENT AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        $i = 0;
        $debit_grand_total = 0;
        $credit_grand_total = 0;

        // Extract ALL the records from OTHER_PAYMENT_BATCH TBL GROUP BY DATE, REFERENCE is SAME
        $this->db->select('*');
        $this->db->from('ez_payment');
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_data = $query->result();
        foreach ($batch_data as $key => $value) {
            if ($i == 0) {
                $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
                                <th>REMARKS</th>
								<th>ACCOUNT</th>
								<th>DEBIT</th>
								<th>CREDIT</th>								
							</tr>
						</thead>
						<tbody>

						<tr><td colspan="6" height="5" style="border: none"></td></tr>';

                ++$i;
            }

            // Document Information
            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $document_reference = $value->ref_no;
            $document_remarks = $value->remarks;

            // Debit Entry - Selected Accounts
            $batch_entry = $this->custom->getRows('ez_payment', ['ref_no' => $document_reference]);
            $grand_total_by_reference = 0;
            foreach ($batch_entry as $value) {
                $accn = $value->accn;
                $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $accn]);

                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$document_date.'</td>';
                $html .= '<td style="width: 110px;">'.$document_reference.'</td>';
                $html .= '<td style="width: 170px;">'.$document_remarks.'</td>';
                $html .= '<td style="width: 220px;">'.$accn.' : '.$accn_description.'</td>';
                $html .= '<td style="width: 150px; text-align: right">'.number_format($value->total_amount, 2).'</td>';
                $html .= '<td style="width: 150px;"></td>';
                $html .= '</tr>';

                $grand_total_by_reference += $value->total_amount;
                $debit_grand_total += $value->total_amount;
            }

            // Credit Entry - Bank Account
            $accn = $value->bank_accn;
            $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $accn]);

            $html .= '<tr>';
            $html .= '<td style="width: 90px;">'.$document_date.'</td>';
            $html .= '<td style="width: 110px;">'.$document_reference.'</td>';
            $html .= '<td style="width: 140px;">'.$document_remarks.'</td>';
            $html .= '<td style="width: 220px;">'.$accn.' : '.$accn_description.'</td>';
            $html .= '<td style="width: 170px;"></td>';
            $html .= '<td style="width: 170px; text-align: right">'.number_format($grand_total_by_reference, 2).'</td>';
            $html .= '</tr>';

            $credit_grand_total += $grand_total_by_reference;

            $html .= '<tr><td colspan="6" height="5" style="border: none"></td></tr>';
        } // First Loop End

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" style="font-weight: bold; border-left: none; border-right: none">*** Total (SGD) ***</td>';
            $html .= '<td style="text-align: right; font-weight: bold; border-left: none; border-right: none">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="text-align: right; font-weight: bold; border-left: none; border-right: none">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'payment_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);        
    }

    public function print_adjustment()
    {
        is_logged_in('admin');
        has_permission();

        $html = '';
        $html .= '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>OTHER ADJUSTMENT AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        // Extract ALL the records from OTHER_ADJUSTMENT_BATCH TBL GROUP BY DATE, REFERENCE is SAME
        $i = 0;
        $debit_grand_total = 0;
        $credit_grand_total = 0;

        $this->db->select('doc_date, ref_no, remarks');
        $this->db->from('ez_adjustment');
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date desc, ref_no asc');
        $query = $this->db->get();
        $batch_data = $query->result();
        foreach ($batch_data as $val) {
            if ($i == 0) {
                $html .= '<table style="width: 100%;">
						<thead>
							<tr>
								<th>DATE</th>
								<th>REFERENCE</th>
                                <th>REMARKS</th>
								<th>ACCOUNT</th>
								<th style="text-align: right">DEBIT</th>
								<th style="text-align: right">CREDIT</th>
							</tr>
						</thead>
						<tbody>

						<tr><td colspan="6" height="5" style="border: none"></td></tr>';
            }

            // Document Information
            $doc_date = implode('/', array_reverse(explode('-', $val->doc_date)));
            $ref_no = $val->ref_no;
            $remarks = $val->remarks;

            $batch_entry = $this->custom->getRows('ez_adjustment', ['ref_no' => $ref_no]);
            foreach ($batch_entry as $value) {
                $accn = $value->accn;
                $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $accn]);

                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$doc_date.'</td>';
                $html .= '<td style="width: 110px;">'.$ref_no.'</td>';
                $html .= '<td style="width: 180px;">'.$remarks.'</td>';
                $html .= '<td style="width: 200px;">'.$accn.' : '.$accn_description.'</td>';

                if ($value->sign == '+') {
                    $html .= '<td style="width: 150px; text-align: right;">'.number_format($value->local_amount, 2).'</td>';
                    $html .= '<td style="width: 150px;"></td>';
                    $debit_grand_total += $value->local_amount;
                } elseif ($value->sign == '-') {
                    $html .= '<td style="width: 150px;"></td>';
                    $html .= '<td style="width: 150px; text-align: right;">'.number_format($value->local_amount, 2).'</td>';
                    $credit_grand_total += $value->local_amount;
                }

                $html .= '</tr>';
            }

            $html .= '<tr><td colspan="6" height="25" style="border: none"></td></tr>';

            ++$i;
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" style="font-weight: bold; border-left: none; border-right: none">*** Total (SGD) ***</td>';
            $html .= '<td style="text-align: right; font-weight: bold; border-left: none; border-right: none">'.number_format($debit_grand_total, 2).'</td>';
            $html .= '<td style="text-align: right; font-weight: bold; border-left: none; border-right: none">'.number_format($credit_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'adjustment_audit_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_debtor() {
   
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['customer'])) {
            $customer_id = $_GET['customer'];

            $html = '';

            $company_where = ['profile_id' => 1];
            $company_details = $this->custom->getSingleRow('company_profile', $company_where);

            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none">';

            $html .= $this->custom->populateCompanyHeader();

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none; border-bottom: 1px solid brown">';
            $html .= '<h4>DEBTOR STATEMENT</h4>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $customer_id]);
            $html .= '<br /><table width="100%">';
            $html .= '<tr><td style="text-align: left; border: none"><strong>To,</strong>';
            $html .= '<br />'.$customer->name.' ('.$customer->code.')';
            $html .= '<br />'.$this->custom->populateCustomerAddress($customer);
            $html .= '</td></tr></table>';

            $html .= '<br /><table style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 85px;">DATE</th>
							<th style="width: 110px;">REFERENCE</th>
							<th style="width: 220px;">REMARKS</th>
							<th style="width: 120px; text-align: right">DEBIT</th>
							<th style="width: 120px; text-align: right">CREDIT</th>
							<th style="width: 150px; text-align: right">BALANCE</th>
						</tr>
					</thead>
					<tbody>';

            $current_amount = 0;
            $balance_amount = 0;

            $this->db->select('*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, SUM(total_amt) AS total_local_amount, SUM(f_amt) AS total_foreign_amount');
            $this->db->from('accounts_receivable');
            $this->db->where('customer_code = "'.$customer->code.'" AND offset = "n"');
            $this->db->group_by('REPLACE(doc_ref_no, "_sp_1", "")');
            $this->db->order_by('doc_date ASC, doc_ref_no ASC');
            $query = $this->db->get();
            $debtor_data = $query->result();
            foreach ($debtor_data as $key => $value) {
                $doc_date = date('d-m-Y', strtotime($value->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$doc_date.'</td>';
                $html .= '<td>'.$value->original_doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $balance_amount += $value->total_foreign_amount;
                    $html .= '<td style="text-align: right">'.number_format($value->total_foreign_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($value->sign == '-') {
                    $balance_amount -= $value->total_foreign_amount;
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($value->total_foreign_amount, 2).'</td>';
                }

                if ($balance_amount >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($balance_amount, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format(abs($balance_amount), 2).' (CR)</td>';
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'debtor_stmt_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        } else {
            redirect('/ez_entry/debtor');
        }
    }

    public function print_creditor() {
   
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['supplier'])) {
            $supplier_id = $_GET['supplier'];

            $html = '';

            $company_where = ['profile_id' => 1];
            $company_details = $this->custom->getSingleRow('company_profile', $company_where);

            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none">';

            $html .= $this->custom->populateCompanyHeader();

            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none; border-bottom: 1px solid brown">';
            $html .= '<h4>CREDITOR STATEMENT</h4>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $supplier_id]);
            $html .= '<br /><table width="100%">';
            $html .= '<tr><td style="text-align: left; border: none"><strong>To,</strong>';
            $html .= '<br />'.$supplier->name.' ('.$supplier->code.')';
            $html .= '<br />'.$this->custom->populateSupplierAddress($supplier);
            $html .= '</td></tr></table>';

            $html .= '<br /><table style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 85px;">DATE</th>
							<th style="width: 110px;">REFERENCE</th>
							<th style="width: 220px;">REMARKS</th>
							<th style="width: 120px; text-align: right">DEBIT</th>
							<th style="width: 120px; text-align: right">CREDIT</th>
							<th style="width: 150px; text-align: right">BALANCE</th>
						</tr>
					</thead>
					<tbody>';

            $current_amount = 0;
            $balance_amount = 0;

            $this->db->select('*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, SUM(total_amt) AS total_local_amount, SUM(fa_amt) AS total_foreign_amount');
            $this->db->from('accounts_payable');
            $this->db->where('supplier_code = "'.$supplier->code.'" AND offset = "n"');
            $this->db->group_by('REPLACE(doc_ref_no, "_sp_1", "")');
            $this->db->order_by('doc_date ASC, doc_ref_no ASC');
            $query = $this->db->get();
            $creditor_data = $query->result();
            foreach ($creditor_data as $key => $value) {
                $doc_date = date('d-m-Y', strtotime($value->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$doc_date.'</td>';
                $html .= '<td>'.$value->original_doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $balance_amount += $value->total_foreign_amount;
                    $html .= '<td style="text-align: right">'.number_format($value->total_foreign_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($value->sign == '-') {
                    $balance_amount -= $value->total_foreign_amount;
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($value->total_foreign_amount, 2).'</td>';
                }

                if ($balance_amount >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($balance_amount, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format(abs($balance_amount), 2).' (CR)</td>';
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'creditor_stmt_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        } else {
            redirect('/ez_entry/creditor');
        }
    }

    public function df_options() {
        is_logged_in('admin');
        has_permission();
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'ezentry_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['ez_sales', 'ez_purchase', 'ez_receipt', 'ez_settlement', 'ez_payment', 'ez_adjustment', 'ez_debtor', 'ez_creditor'],
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

    public function df_restore($action = 'form')
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
            set_flash_message('message', 'success', 'EZ ENTRY RESTORED');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('ez_entry/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'ez_entry/blank.php';
        zapEzentry();
        redirect('ez_entry/', 'refresh');
    }
}
