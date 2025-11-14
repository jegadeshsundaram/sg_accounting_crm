<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->logged_id = $this->session->user_id;
        $this->load->model('ez_entry/ez_entry_model', 'ez_model');
    }

    public function get_gst_details()
    {
        is_ajax();
        $post = $this->input->post();
        $data['gst_percentage'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gst_code']]);
        echo json_encode($data);
    }

    public function double_sales()
    {
        is_ajax();
        $post = $this->input->post();

        $ref = $this->custom->getCount('gl', ['ref_no' => $post['ref_no'], 'tran_type' => 'BTHSALE']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_sales', ['ref_no' => $post['ref_no']]);
        }

        echo $ref;
    }

    public function double_purchase()
    {
        is_ajax();
        $post = $this->input->post();

        $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $post['supplier_id']]);

        $ref = $this->custom->getCount('accounts_payable', ['doc_ref_no' => $post['ref_no'], 'supplier_code' => $supplier_code, 'tran_type' => 'BTHPURC']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_purchase', ['ref_no' => $post['ref_no'], 'supplier_id' => $post['supplier_id']]);
        }

        echo $ref;
    }

    public function double_receipt()
    {
        is_ajax();
        $post = $this->input->post();

        $ref = $this->custom->getCount('gl', ['ref_no' => $post['ref_no'], 'tran_type' => 'BTHREC']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_receipt', ['ref_no' => $post['ref_no']]);
        }

        echo $ref;
    }

    public function double_settlement()
    {
        is_ajax();
        $post = $this->input->post();
        $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $post['supplier_id']]);

        $ref = $this->custom->getCount('accounts_payable', ['doc_ref_no' => $post['ref_no'], 'supplier_code' => $supplier_code, 'tran_type' => 'BTHSET']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_settlement', ['ref_no' => $post['ref_no'], 'supplier_id' => $post['supplier_id']]);
        }

        echo $ref;
    }

    public function double_payment()
    {
        is_ajax();
        $post = $this->input->post();

        $ref = $this->custom->getCount('gl', ['ref_no' => $post['ref_no'], 'tran_type' => 'EZPAY']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_payment', ['ref_no' => $post['ref_no']]);
        }

        echo $ref;
    }

    public function double_adjustment()
    {
        is_ajax();
        $post = $this->input->post();

        $ref = $this->custom->getCount('gl', ['ref_no' => $post['ref_no'], 'tran_type' => 'EZADJ']);
        if ($ref == 0) {
            $ref = $this->custom->getCount('ez_adjustment', ['ref_no' => $post['ref_no']]);
        }

        echo $ref;
    }

    public function update_exchange_rate()
    {
        is_ajax();
        $post = $this->input->post();

        $updated = $this->custom->updateRow($post['tbl'], ['exchange_rate' => $post['exchange_rate']], ['ref_no' => $post['ref_no']]);

        echo $updated;
    }
    
    public function get_sales_view()
    {
        is_ajax();
        $post = $this->input->post();

        $sales_data = $this->custom->getMultiValues('ez_sales', 'doc_date, ref_no', ['sb_id' => $post['entry_id']]);

        $html = '<table class="tbl-h">';

        $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount');
        $this->db->from('ez_sales');
        $this->db->where(['ref_no' => $sales_data->ref_no, 'doc_date' => $sales_data->doc_date]);
        $this->db->group_by('ref_no, customer_id, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_data = $query->result();
        foreach ($batch_data as $value) {
            $doc_date = $value->doc_date;
            $ref_no = $value->ref_no;
            $customer_id = $value->customer_id;
            $remarks = $value->remarks;
            $customer = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['customer_id' => $value->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);

            $local_amount = $value->batch_local_amount;
            $local_gst_amount = $value->batch_local_gst_amount;

            $sales_accn = $value->sales_accn;
        }

        $html .= '<tr>';
        $html .= '<td><label>Date</label> : '.date('d-m-Y', strtotime($doc_date)).'</td>';
        $html .= '<td style="text-align: right"><label>Reference</label> : '.$ref_no.'</td>';
        $html .= '</tr>';

        $customer = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['customer_id' => $customer_id]);
        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);

        $html .= '<tr>';
        $html .= '<td colspan="2"><label>Customer</label> <br />('.$customer->code.') '.$customer->name.' | '.$currency.'</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $html .= '<table class="tbl-b">';
        $html .= '<tr>';
        $html .= '<th>Account</th>';
        $html .= '<th style="text-align: right">Debit</th>';
        $html .= '<th style="text-align: right">Credit</th>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>CA001</td>';
        $html .= '<td style="text-align: right">'.number_format($local_amount + $local_gst_amount, 2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>CL300</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align: right">'.number_format($local_gst_amount, 2).'</td>';
        $html .= '</tr>';

        $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $sales_accn]);
        $html .= '<tr>';
        $html .= '<td>'.$sales_accn.'</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align: right">'.number_format($local_amount, 2).'</td>';
        $html .= '</tr>';

        $html .= '</table>';

        $data['sales_data'] = $html;

        echo json_encode($data);
    }

    public function save_sales()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['customer_id'] = $post['customer'];
            $batch_data['exchange_rate'] = $post['exchange_rate'];
            $batch_data['sales_accn'] = $post['sales_accn'];
            $batch_data['gst_code'] = $post['gst_category'];
            $batch_data['local_amount'] = $post['local_amount'];
            $batch_data['local_gst_amount'] = $post['local_gst_amount'];
            $batch_data['foreign_amount'] = $post['foreign_amount'];
            $batch_data['foreign_gst_amount'] = $post['foreign_gst_amount'];

            if ($post['local_gst_amount'] > 0) {
                $batch_data['gst'] = 1;
            } else {
                $batch_data['gst'] = 0;
            }

            if ($entry_id == '') {
                $entry_id = $this->custom->insertRow('ez_sales', $batch_data);
            } else {
                $updated = $this->custom->updateRow('ez_sales', $batch_data, ['sb_id' => $entry_id]);
            }

            echo $entry_id;
        } else {
            echo 'post error';
        }
    }

    public function save_purchase()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['supplier_id'] = $post['supplier'];
            $batch_data['exchange_rate'] = $post['exchange_rate'];
            $batch_data['purchase_accn'] = $post['purchase_accn'];
            $batch_data['gst_code'] = $post['gst_category'];
            $batch_data['local_amount'] = $post['local_amount'];
            $batch_data['local_gst_amount'] = $post['local_gst_amount'];
            $batch_data['foreign_amount'] = $post['foreign_amount'];
            $batch_data['foreign_gst_amount'] = $post['foreign_gst_amount'];

            if ($post['local_gst_amount'] > 0) {
                $batch_data['gst'] = 1;
            } else {
                $batch_data['gst'] = 0;
            }

            if ($entry_id == '') {
                $entry_id = $this->custom->insertRow('ez_purchase', $batch_data);
            } else {
                $updated = $this->custom->updateRow('ez_purchase', $batch_data, ['pb_id' => $entry_id]);
            }

            echo $entry_id;
        } else {
            echo 'post error';
        }
    }

    public function save_receipt()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['customer_id'] = $post['customer_id'];
            $batch_data['foreign_amount'] = $post['foreign_amount'];
            $batch_data['exchange_rate'] = $post['exchange_rate'];
            $batch_data['local_amount'] = $post['local_amount'];            
            $batch_data['bank_accn'] = $post['bank'];
            $batch_data['fb_accn'] = $post['foreign_bank'];
            $batch_data['remarks'] = $post['remarks'];

            if ($entry_id == '') {
                $res = $this->custom->insertData('ez_receipt', $batch_data);                

            } else {
                $res = $this->custom->updateRow('ez_receipt', $batch_data, ['rb_id' => $entry_id]);
            }

            // update bank accn 
            if ($post['bank'] == 'CA110') {
                $bank_update_data['fb_accn'] = $post['foreign_bank'];
            } else {
                $bank_update_data['accn'] = $post['bank'];
            }
            $bank_update = $this->custom->updateRow('bank', $bank_update_data, ['accn_type' => 'CA']);


            if($res == 'updated' || $res) {
                echo $res;
            } else {
                echo 'error';
            }
            
        } else {
            echo 'post error';
        }
    }

    public function save_settlement()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['supplier_id'] = $post['supplier_id'];
            $batch_data['foreign_amount'] = $post['foreign_amount'];
            $batch_data['exchange_rate'] = $post['exchange_rate'];
            $batch_data['local_amount'] = $post['local_amount'];
            $batch_data['bank_accn'] = $post['bank'];
            $batch_data['fb_accn'] = $post['foreign_bank'];
            $batch_data['remarks'] = $post['remarks'];

            if ($entry_id == '') {
                $res = $this->custom->insertData('ez_settlement', $batch_data);

                // update bank accn 
                $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
                if ($bank_data->accn != $post['bank'] || $bank_data->fb_accn != $post['foreign_bank']) {
                    $bank_update_data['accn'] = $post['bank'];
                    $bank_update_data['fb_accn'] = '';
                    if ($post['bank'] == 'CA110') {
                        $bank_update_data['fb_accn'] = $post['foreign_bank'];
                    }
                    $bank_update = $this->custom->updateRow('bank', $bank_update_data, ['accn_type' => 'CA']);
                }

            } else {
                $res = $this->custom->updateRow('ez_settlement', $batch_data, ['ap_id' => $entry_id]);
            }

            if($res == 'updated' || $res) {
                echo $res;
            } else {
                echo 'error';
            }
            
        } else {
            echo 'post error';
        }
    }

    public function save_payment()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['remarks'] = $post['remarks'];

            $batch_data['accn'] = $post['accn'];
            $batch_data['sign'] = '+';
            $batch_data['total_amount'] = $post['amount'];
            $batch_data['gst_type'] = $post['gst_type'];
            $batch_data['gst_category'] = $post['gst_category'];
            $batch_data['net_amount'] = $post['net_amount'];
            $batch_data['gst_amount'] = $post['gst_amount'];

            $batch_data['bank_accn'] = $post['bank'];
            
            if ($entry_id == '') {
                $entry_id = $this->custom->insertRow('ez_payment', $batch_data);

                // update bank accn 
                $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
                if ($bank_data->accn != $post['bank'] || $bank_data->fb_accn != $post['foreign_bank']) {
                    $bank_update_data['accn'] = $post['bank'];
                    $bank_update_data['fb_accn'] = '';
                    if ($post['bank'] == 'CA110') {
                        $bank_update_data['fb_accn'] = $post['foreign_bank'];
                    }
                    $bank_update = $this->custom->updateRow('bank', $bank_update_data, ['accn_type' => 'CA']);
                }

            } else {
                $res = $this->custom->updateRow('ez_payment', $batch_data, ['batch_id' => $entry_id]);
            }

            echo $entry_id;
            
        } else {
            echo 'post error';
        }
    }

    public function save_adjustment()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['remarks'] = $post['remarks'];
            $batch_data['accn'] = $post['accn'];
            $batch_data['control_account'] = $post['control_account'];
            $batch_data['exchange_rate'] = $post['exchange_rate'];
            $batch_data['foreign_amount'] = $post['foreign_amount'];
            $batch_data['local_amount'] = $post['local_amount'];
            $batch_data['sign'] = $post['sign'];
            $batch_data['gst_type'] = $post['gst_type'];
            $batch_data['gst_category'] = $post['gst_category'];
            $batch_data['net_amount'] = $post['net_amount'];
            $batch_data['gst_amount'] = $post['gst_amount'];

            if ($entry_id == '') {
                $entry_id = $this->custom->insertRow('ez_adjustment', $batch_data);
            } else {
                $res = $this->custom->updateRow('ez_adjustment', $batch_data, ['batch_id' => $entry_id]);
            }

            echo $entry_id;
            
        } else {
            echo 'post error';
        }
    }

    function get_receipt() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $receipt = $this->custom->getSingleRow('ez_receipt', ['rb_id' => $post['rowID']]);
            $data['receipt'] = $receipt;

            $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $receipt->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $data['currency'] = $currency;
            echo json_encode($data);
        }
    }

    function get_settlement() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $settlement = $this->custom->getSingleRow('ez_settlement', ['ap_id' => $post['rowID']]);
            $data['settlement'] = $settlement;

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $settlement->supplier_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $data['currency'] = $currency;
            echo json_encode($data);
        }
    }

    public function post_sales()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $sales_data = $this->custom->getMultiValues('ez_sales', 'doc_date, ref_no, customer_id', ['sb_id' => $post['rowID']]);

            $html = '';

            // Extract ALL the records from ez_sales TBL GROUP BY DATE, REFERENCE, CUSTOMER is SAME
            $this->db->select('*, sum(local_amount) as batch_local_amount, sum(local_gst_amount) as batch_local_gst_amount, sum(foreign_amount) as batch_foreign_amount, sum(foreign_gst_amount) as batch_foreign_gst_amount');
            $this->db->where('doc_date = "'.$sales_data->doc_date.'" AND ref_no = "'.$sales_data->ref_no.'" AND customer_id = "'.$sales_data->customer_id.'"');
            $this->db->from('ez_sales');
            $this->db->group_by('ref_no, customer_id, doc_date');
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $batch_data = $query->result();
            foreach ($batch_data as $key => $value) {
                // Special GST - Start
                $sales_specific_entries = $this->custom->findRow('ez_sales', ['doc_date' => $value->doc_date, 'ref_no' => $value->ref_no, 'customer_id' => $value->customer_id]);
                $count = count($sales_specific_entries);
                $special_gst = false;
                for ($i = 0; $i < $count; ++$i) {
                    if ($sales_specific_entries[$i]->gst_code == 'SROVR-RS' || $sales_specific_entries[$i]->gst_code == 'SROVR-LVG' || $sales_specific_entries[$i]->gst_code == 'SRLVG') { // Special GST Category = SROVR-RS | SROVR-LVG | SRLVG
                        $special_gst = true;
                    }
                }
                // Special GST - End

                if ($special_gst) { // transaction with entries used Special GST Categories
                    $this->post_special_gst_sales($value->doc_date, $value->ref_no, $value->customer_id);
                } else {
                    // Customer Information
                    $customer_id = $value->customer_id;
                    $customer = $this->custom->getMultiValues('master_customer', 'code, currency_id', ['customer_id' => $customer_id]);
                    $customer_code = $customer->code;
                    $currency_id = $customer->currency_id;

                    // Currency Information
                    $currency = $this->custom->getMultiValues('ct_currency', 'rate, code', ['currency_id' => $currency_id]);
                    $currency_rate = $currency->rate;
                    $currency_code = $currency->code;

                    $latest_currency_rate = $value->exchange_rate;

                    // Document Information
                    $document_date = $value->doc_date;
                    $document_reference = $value->ref_no;
                    $document_remarks = $value->remarks;

                    // Amount Information
                    $batch_foreign_amount = $value->batch_foreign_amount;
                    $batch_foreign_gst_amount = $value->batch_foreign_gst_amount;
                    $batch_local_amount = $value->batch_local_amount;
                    $batch_local_gst_amount = $value->batch_local_gst_amount;

                    $batch_local_amount_with_gst = $batch_local_amount + $batch_local_gst_amount;
                    $batch_foreign_amount_with_gst = $batch_foreign_amount + $batch_foreign_gst_amount;

                    // 1. Start - Post to Accounts Receivable
                    $ar_data['doc_ref_no'] = $document_reference;
                    $ar_data['customer_code'] = $customer_code;
                    $ar_data['doc_date'] = $document_date;
                    $ar_data['currency'] = $currency_code;
                    $ar_data['total_amt'] = number_format($batch_local_amount_with_gst, 2, '.', '');
                    $ar_data['f_amt'] = number_format($batch_foreign_amount_with_gst, 2, '.', '');
                    $ar_data['fa_amt'] = 0.00;
                    $ar_data['sign'] = '+';
                    $ar_data['tran_type'] = 'BTHSALE';
                    $ar_data['remarks'] = $document_remarks;
                    $ar_data['invoice_id'] = 0;

                    $ar_insert = $this->db->insert('accounts_receivable', $ar_data);
                    // End - Post to Accounts Receivable

                    // 2. Start - Post to GL TABLE
                    $gl_data['doc_date'] = $document_date;
                    $gl_data['ref_no'] = $document_reference;
                    $gl_data['remarks'] = $document_remarks;
                    $gl_data['accn'] = 'CA001';
                    $gl_data['sign'] = '+';
                    $gl_data['tran_type'] = 'BTHSALE';
                    $gl_data['total_amount'] = number_format($batch_local_amount_with_gst, 2, '.', '');
                    $gl_data['iden'] = $customer_code;
                    $gl_insert = $this->db->insert('gl', $gl_data);

                    if ($batch_local_gst_amount > 0) {
                        $gl_data['accn'] = 'CL300';
                        $gl_data['sign'] = '-';
                        $gl_data['total_amount'] = number_format($batch_local_gst_amount, 2, '.', '');
                        $gl_insert = $this->db->insert('gl', $gl_data);
                    }

                    if ($latest_currency_rate != $currency_rate) {
                        $er_update = $this->custom->updateRow('ct_currency', ['rate' => $latest_currency_rate], ['currency_id' => $currency_id, 'code' => $currency_code]);
                    }

                    // Extract ALL the records from ez_sales TBL where DATE, REFERENCE, CUSTOMER and GROUP BY SALES ACCOUNT
                    $this->db->select('*, sum(local_amount) as batch_local_amount');
                    $this->db->from('ez_sales');
                    $this->db->where('doc_date = "'.$document_date.'" AND ref_no = "'.$document_reference.'" AND customer_id = "'.$customer_id.'"');
                    $this->db->group_by('sales_accn');
                    $query = $this->db->get();
                    $batch_sales_account_specific_records = $query->result();
                    foreach ($batch_sales_account_specific_records as $record) {
                        // Start - Post to GL
                        $gl_data['accn'] = $record->sales_accn;
                        $gl_data['sign'] = '-';
                        $gl_data['total_amount'] = number_format($record->batch_local_amount, 2, '.', '');
                        $gl_insert = $this->db->insert('gl', $gl_data);
                        // End - Post to GL
                    }
                }
            }

            // Inserting each and every record from batch table into GST REPORT
            $this->db->select('*');
            $this->db->from('ez_sales');
            $this->db->where('doc_date = "'.$sales_data->doc_date.'" AND ref_no = "'.$sales_data->ref_no.'" AND customer_id = "'.$sales_data->customer_id.'"');
            $query2 = $this->db->get();
            $batch_sales_data_gst = $query2->result();
            foreach ($batch_sales_data_gst as $key => $value) {
                // Customer Information
                $customer_id = $value->customer_id;
                $customer_data = $this->custom->getMultiValues('master_customer', 'code, currency_id', ['customer_id' => $customer_id]);
                $customer_code = $customer_data->code;
                $currency_id = $customer_data->currency_id;

                // Sales Account Information
                $sales_accn = $value->sales_accn;

                // Currency Information
                $currency_data = $this->custom->getMultiValues('ct_currency', 'rate, code', ['currency_id' => $currency_id]);
                $currency_rate = $currency_data->rate;
                $currency_code = $currency_data->code;

                // GST Information
                $gst_code = $value->gst_code;
                $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $gst_code]);

                // Document Information
                $document_date = $value->doc_date;
                $document_reference = $value->ref_no;
                $document_remarks = $value->remarks;

                // Amount Information
                $individual_local_amount = $value->local_amount;
                $individual_local_gst_amount = $value->local_gst_amount;

                $individual_foreign_amount = $value->foreign_amount;
                $individual_foreign_gst_amount = $value->foreign_gst_amount;

                // End - Common Variables

                // 2. Start - Post to ***gst*** (Each Item)
                $gst_data['date'] = $document_date;
                $gst_data['dref'] = $document_reference;
                $gst_data['iden'] = $customer_code;
                $gst_data['rema'] = $document_remarks;
                $gst_data['gsttype'] = 'O';
                $gst_data['gstcate'] = $gst_code;
                $gst_data['gstperc'] = $gst_rate;
                $gst_data['amou'] = $individual_local_amount;
                $gst_data['gstamou'] = $individual_local_gst_amount;
                $gst_data['tran_type'] = 'BTHSALE';

                if ($this->ion_auth->isGSTMerchant()) {
                    $gst_insert = $this->db->insert('gst', $gst_data);
                } else {
                    $gst_insert = true;
                }
                // End - Post to ***gst*** (Each Item)

                /*** POSTING SINGLE MAIN ENTRY TO GL_SINGLE_ENTRY TABLE FOR AUDIT LISTING REPORT - START ***/
                $gl_single_entry_data['doc_date'] = $document_date;
                $gl_single_entry_data['ref_no'] = $document_reference;
                $gl_single_entry_data['remarks'] = $document_remarks;
                $gl_single_entry_data['iden'] = $customer_code;
                $gl_single_entry_data['accn'] = $sales_accn;

                $gl_single_entry_data['foreign_amount'] = $individual_foreign_amount;
                $gl_single_entry_data['local_amount'] = $individual_local_amount;

                if ($this->ion_auth->isGSTMerchant()) {
                    $gl_single_entry_data['gst_category'] = $gst_code;
                    $gl_single_entry_data['gst_rate'] = $gst_rate;
                }

                $gl_single_entry_data['foreign_gst_amount'] = $individual_foreign_gst_amount;
                $gl_single_entry_data['local_gst_amount'] = $individual_local_gst_amount;

                $gl_single_entry_data['sign'] = '-';
                $gl_single_entry_data['tran_type'] = 'BTHSALE';

                $gl_single_entry_insert = $this->db->insert('gl_single_entry', $gl_single_entry_data);
                /*** POSTING SINGLE MAIN ENTRY TO GL_SINGLE_ENTRY TABLE FOR AUDIT LISTING REPORT - END ***/
            }

            if ($special_gst) {
                $ar_insert = true;
                $gl_insert = true;
            } else {
                unset($ar_data);
                unset($gl_data);
            }

            unset($gst_data);
            unset($gl_single_entry_data);

            if ($ar_insert && $gl_insert && $gst_insert && $gl_single_entry_insert) {
                // 3. Start - Delete from ***Sales Batch*** Table
                $delete = $this->custom->deleteRow('ez_sales', ['doc_date' => $sales_data->doc_date, 'ref_no' => $sales_data->ref_no, 'customer_id' => $sales_data->customer_id]);
                // End - Delete from ***Sales Batch*** Table

                echo 'posted';
            } else {
                echo 'insert error';
                // echo $ar_insert.' >>> '.$gl_insert.' >>> '.$gst_insert.' >>> '.$gl_single_entry_insert;
            }
        } else {
            echo 'post error';
        }
    }

    public function post_special_gst_sales($doc_date, $ref_no, $customer_id)
    {
        // Customer Information
        $customer = $this->custom->getMultiValues('master_customer', 'code, currency_id', ['customer_id' => $customer_id]);
        $customer_code = $customer->code;
        $currency_id = $customer->currency_id;

        // Currency Information
        $currency = $this->custom->getMultiValues('ct_currency', 'rate, code', ['currency_id' => $currency_id]);
        $currency_code = $currency->code;
        $currency_rate = $currency->rate;

        $html = '';
        $other_gst_used_entry = 0;

        $this->db->select('*');
        $this->db->from('ez_sales');
        $this->db->where(['doc_date' => $doc_date, 'ref_no' => $ref_no, 'customer_id' => $customer_id]);
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $batch_data = $query->result();
        foreach ($batch_data as $key => $value) {
            $latest_currency_rate = $value->exchange_rate;

            $remarks .= $value->remarks.' ';

            // Amount Information
            if ($value->gst_code == 'SROVR-RS' || $value->gst_code == 'SROVR-LVG' || $value->gst_code == 'SRLVG') {
                $batch_foreign_amount += $value->foreign_gst_amount;
                $batch_local_amount += $value->local_gst_amount;
            } else {
                ++$other_gst_used_entry;
                $batch_foreign_amount += $value->foreign_amount + $value->foreign_gst_amount;
                $batch_local_amount += $value->local_amount + $value->local_gst_amount;
            }

            // Sum of all the entries GST Amount
            $gst_local_amount += $value->local_gst_amount;
        }

        // 1. Start - Post to Accounts Receivable
        $ar_data['doc_ref_no'] = $ref_no;
        $ar_data['customer_code'] = $customer_code;
        $ar_data['doc_date'] = $doc_date;
        $ar_data['currency'] = $currency_code;
        $ar_data['total_amt'] = number_format($batch_local_amount, 2, '.', '');
        $ar_data['f_amt'] = number_format($batch_foreign_amount, 2, '.', '');
        $ar_data['fa_amt'] = 0.00;
        $ar_data['sign'] = '+';
        $ar_data['tran_type'] = 'BTHSALE';
        $ar_data['remarks'] = $remarks;
        $ar_data['invoice_id'] = 0;
        $ar_insert = $this->db->insert('accounts_receivable', $ar_data);
        // End - Post to Accounts Receivable

        // 2. Start - Post to GL TABLE
        // 2.a. Debit = CA001 (Debtor Control Account)
        $gl_data['doc_date'] = $doc_date;
        $gl_data['ref_no'] = $ref_no;
        $gl_data['remarks'] = $remarks;
        $gl_data['accn'] = 'CA001';
        $gl_data['sign'] = '+';
        $gl_data['tran_type'] = 'BTHSALE';
        $gl_data['total_amount'] = number_format($batch_local_amount, 2, '.', '');
        $gl_data['iden'] = $customer_code;
        $gl_insert = $this->db->insert('gl', $gl_data);

        // 2.b. Credit = CL300 (Goods & Services Account)
        $gl_data['accn'] = 'CL300';
        $gl_data['sign'] = '-';
        $gl_data['total_amount'] = number_format($gst_local_amount, 2, '.', '');
        $gl_insert = $this->db->insert('gl', $gl_data);

        if ($latest_currency_rate != $currency_rate) {
            $er_data['rate'] = $latest_currency_rate;
            $er_update = $this->custom->updateRow('ct_currency', $er_data, ['currency_id' => $currency_id, 'code' => $currency_code]);
        }

        // Extract ALL the records from ez_sales TBL where DATE, REFERENCE, CUSTOMER and GROUP BY SALES ACCOUNT
        $this->db->select('*, sum(local_amount) as batch_local_amount');
        $this->db->from('ez_sales');
        $this->db->where(['gst_code !=' => 'SROVR-RS', 'gst_code !=' => 'SROVR-LVG', 'gst_code !=' => 'SRLVG', 'doc_date' => $doc_date, 'ref_no' => $ref_no, 'customer_id' => $customer_id]);
        $this->db->group_by('sales_accn');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_data = $query->result();
        foreach ($batch_data as $key => $value) {
            // Start - Post to GL
            $gl_data['accn'] = $value->sales_accn;
            $gl_data['sign'] = '-';
            $gl_data['total_amount'] = number_format($value->batch_local_amount, 2, '.', '');
            $gl_insert = $this->db->insert('gl', $gl_data);
            // End - Post to GL
        }
    }

    public function post_purchase() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $html = '';

            $purchase_data = $this->custom->getSingleRow('ez_purchase', ['pb_id' => $post['rowID']]);

            $special_gst = false;
            $local_amount = 0;
            $foreign_amount = 0;
            $local_gst_amount = 0;

            $this->db->select('*');
            $this->db->where('doc_date = "'.$purchase_data->doc_date.'" AND ref_no = "'.$purchase_data->ref_no.'" AND supplier_id = "'.$purchase_data->supplier_id.'"');
            $this->db->from('ez_purchase');
            $query = $this->db->get();
            $tran_entries = $query->result();
            foreach ($tran_entries as $value) {
                $gst_code = $value->gst_code;

                // Special GST Category (TXCA / TXRC-TS)
                if ($gst_code == 'TXCA' || $gst_code == 'TXRC-TS') {
                    $local_amount += $value->local_amount;
                    $local_gst_amount += $value->local_gst_amount;
                    $foreign_amount += $value->foreign_amount;
                    $special_gst = true;
                } else {
                    $local_amount += $value->local_amount + $value->local_gst_amount;
                    $local_gst_amount += $value->local_gst_amount;
                    $foreign_amount += $value->foreign_amount + $value->foreign_gst_amount;
                }

                // 1. Inserting each and every record from ez_purchase TBL into GST REPORT TBL
                $gst_data['date'] = $value->doc_date;
                $gst_data['dref'] = $value->ref_no;

                $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $value->supplier_id]);
                $gst_data['iden'] = $supplier_code;
                
                $gst_data['rema'] = $value->remarks;
                $gst_data['gsttype'] = 'I';
                $gst_data['gstcate'] = $value->gst_code;
                
                $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_code]);
                $gst_data['gstperc'] = $gst_rate;
                
                $gst_data['amou'] = $value->local_amount;
                $gst_data['gstamou'] = $value->local_gst_amount;
                $gst_data['tran_type'] = 'BTHPURC';

                if ($this->ion_auth->isGSTMerchant()) {
                    $gst_insert = $this->db->insert('gst', $gst_data);
                } else {
                    $gst_insert = true;
                }

                // Special GST - Insert
                $gst_data['gsttype'] = 'O';
                if ($gst_code == 'TXCA') {
                    $gst_data['gstcate'] = 'SRCA-C';
                } elseif ($gst_code == 'TXRC-TS') {
                    $gst_data['gstcate'] = 'SRRC';
                }
                if (($gst_code == 'TXCA' || $gst_code == 'TXRC-TS') && $this->ion_auth->isGSTMerchant()) {
                    $gst_special_insert = $this->db->insert('gst', $gst_data);
                }

                // 2. POSTING SINGLE MAIN ENTRY TO GL_SINGLE_ENTRY TABLE FOR AUDIT LISTING REPORT
                $gl_single_entry_data['doc_date'] = $value->doc_date;
                $gl_single_entry_data['ref_no'] = $value->ref_no;
                $gl_single_entry_data['remarks'] = $value->remarks;
                $gl_single_entry_data['iden'] = $supplier_code;
                $gl_single_entry_data['accn'] = $value->purchase_accn;

                $gl_single_entry_data['foreign_amount'] = $value->foreign_amount;
                $gl_single_entry_data['local_amount'] = $value->local_amount;

                if ($this->ion_auth->isGSTMerchant()) {
                    $gl_single_entry_data['gst_category'] = $value->gst_code;
                    $gl_single_entry_data['gst_rate'] = $gst_rate;
                }

                $gl_single_entry_data['foreign_gst_amount'] = $value->foreign_gst_amount;
                $gl_single_entry_data['local_gst_amount'] = $value->local_gst_amount;

                $gl_single_entry_data['sign'] = '+';
                $gl_single_entry_data['tran_type'] = 'BTHPURC';

                $gl_single_entry_insert = $this->db->insert('gl_single_entry', $gl_single_entry_data);

            } // loop ends

            $supplier_data = $this->custom->getMultiValues('master_supplier', 'code, currency_id', ['supplier_id' => $purchase_data->supplier_id]);
            $supplier_code = $supplier_data->code;
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier_data->currency_id]);

            // 1. Post to Accounts Payable
            $ap_data['doc_ref_no'] = $purchase_data->ref_no;
            $ap_data['supplier_code'] = $supplier_code;
            $ap_data['doc_date'] = $purchase_data->doc_date;
            $ap_data['currency'] = $currency_code;
            $ap_data['total_amt'] = number_format($local_amount, 2, '.', '');
            $ap_data['fa_amt'] = number_format($foreign_amount, 2, '.', '');
            $ap_data['sign'] = '-';
            $ap_data['tran_type'] = 'BTHPURC';
            $ap_data['remarks'] = $purchase_data->remarks;
            $ap_data['purchase_id'] = 0;
            $ap_insert = $this->db->insert('accounts_payable', $ap_data);

            // 2. Post to GL TABLE - CREDIT CL001
            $gl_data['doc_date'] = $purchase_data->doc_date;
            $gl_data['ref_no'] = $purchase_data->ref_no;
            $gl_data['remarks'] = $purchase_data->remarks;
            $gl_data['accn'] = 'CL001';
            $gl_data['sign'] = '-';
            $gl_data['tran_type'] = 'BTHPURC';
            $gl_data['total_amount'] = number_format($local_amount, 2, '.', '');
            $gl_data['iden'] = $supplier_code;
            $gl_insert = $this->db->insert('gl', $gl_data);

            // 3. Post to GL TABLE
            if ($local_gst_amount > 0) {
                if ($special_gst) {
                    // Debit GST for Special GST
                    $gl_data['accn'] = 'CL300';
                    $gl_data['sign'] = '+';
                    $gl_data['total_amount'] = number_format($local_gst_amount, 2, '.', '');
                    $gl_insert = $this->db->insert('gl', $gl_data);

                    // Credit GST for Special GST
                    $gl_data['accn'] = 'CL300';
                    $gl_data['sign'] = '-';
                    $gl_data['total_amount'] = number_format($local_gst_amount, 2, '.', '');
                    $gl_insert = $this->db->insert('gl', $gl_data);
                } else {
                    $gl_data['accn'] = 'CL300';
                    $gl_data['sign'] = '+';
                    $gl_data['total_amount'] = number_format($local_gst_amount, 2, '.', '');
                    $gl_insert = $this->db->insert('gl', $gl_data);
                }
            }

            // Exchange Rate updated in Currency Master if changed in any transaction
            $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $supplier_data->currency_id]);
            if ($purchase_data->exchange_rate != $currency_rate) {
                $er_data['rate'] = $purchase_data->exchange_rate;
                $er_update = $this->custom->updateRow('ct_currency', $er_data, ['currency_id' => $supplier_data->currency_id, 'code' => $currency_code]);
            }

            // 4. Post to GL TABLE - DEBIT PURCHASE ACCOUNT
            // Extract ALL the records from ez_purchase.TBL where DATE, REFERENCE, CUSTOMER and GROUP BY SALES ACCOUNT
            $this->db->select('*, sum(local_amount) as batch_local_amount');
            $this->db->from('ez_purchase');
            $this->db->where('doc_date = "'.$purchase_data->doc_date.'" AND ref_no = "'.$purchase_data->ref_no.'" AND supplier_id = "'.$purchase_data->supplier_id.'"');
            $this->db->group_by('purchase_accn');
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $tran_accn_entries = $query->result();
            foreach ($tran_accn_entries as $value) {
                $gl_data['accn'] = $value->purchase_accn;
                $gl_data['sign'] = '+';
                $gl_data['total_amount'] = number_format($value->batch_local_amount, 2, '.', '');

                $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $value->supplier_id]);
                $gl_data['iden'] = $supplier_code;

                $gl_inserted = $this->db->insert('gl', $gl_data);
            }

            unset($gst_data);
            unset($gl_single_entry_data);
            unset($ap_data);
            unset($gl_data);

            if ($ap_insert && $gl_insert && $gst_insert) {
                // 3. Start - Delete from ***Purchases Batch*** Table
                $delete = $this->custom->deleteRow('ez_purchase', ['doc_date' => $purchase_data->doc_date, 'ref_no' => $purchase_data->ref_no, 'supplier_id' => $purchase_data->supplier_id]);
                // End - Delete from ***Sales Batch*** Table

                echo 'posted';
            } else {
                echo 'Post Error';
            }
    
        } else {
            echo 'post error';
        }
    }

    public function post_receipt() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $html = '';

            $receipt_data = $this->custom->getSingleRow('ez_receipt', ['rb_id' => $post['rowID']]);
            $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $receipt_data->customer_id]);
            $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $receipt_data->customer_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

            $gl_data['doc_date'] = $receipt_data->doc_date;
            $gl_data['ref_no'] = $receipt_data->ref_no;
            $gl_data['remarks'] = $receipt_data->remarks;
            $gl_data['accn'] = 'CA001';
            $gl_data['sign'] = '-';
            $gl_data['tran_type'] = 'BTHREC';
            $gl_data['total_amount'] = $receipt_data->local_amount;
            $gl_data['iden'] = $customer_code;
            $gl_insert = $this->db->insert('gl', $gl_data);

            $gl_data['accn'] = $receipt_data->bank_accn;
            $gl_data['sign'] = '+';
            $gl_data['total_amount'] = $receipt_data->local_amount;
            $gl_insert = $this->db->insert('gl', $gl_data);

            // insert into accounts_receivable
            $ar_data['doc_date'] = $receipt_data->doc_date;
            $ar_data['doc_ref_no'] = $receipt_data->ref_no;
            $ar_data['customer_code'] = $customer_code;
            $ar_data['currency'] = $currency_code;
            $ar_data['total_amt'] = $receipt_data->local_amount;
            $ar_data['f_amt'] = $receipt_data->foreign_amount;
            $ar_data['fa_amt'] = 0.00;
            $ar_data['sign'] = '-';
            $ar_data['tran_type'] = 'BTHREC';
            $ar_data['remarks'] = $receipt_data->remarks;
            $ar_data['invoice_id'] = 0;
            $ar_insert = $this->db->insert('accounts_receivable', $ar_data);

            // insert into foreign bank ledger if foreign bank (CA110) selected
            if ($receipt_data->bank_accn == 'CA110' && $receipt_data->fb_accn != '') {

                $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $receipt_data->fb_accn]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_currency_id]);

                $fb_data['doc_ref_no'] = $receipt_data->ref_no;                
                $fb_data['fb_code '] = $receipt_data->fb_accn;
                $fb_data['doc_date'] = $receipt_data->doc_date;
                $fb_data['currency'] = $currency;
                $fb_data['local_amt'] = $receipt_data->local_amount;
                $fb_data['fa_amt'] = $receipt_data->foreign_amount;
                $fb_data['sign'] = '+';
                $fb_data['remarks'] = $receipt_data->remarks;
                $fb_data['tran_type'] = 'BTHREC';
                $fb_insert = $this->custom->insertRow('foreign_bank', $fb_data);
            }

            // insert gl single entry
            $gl_single_data['doc_date'] = $receipt_data->doc_date;
            $gl_single_data['ref_no'] = $receipt_data->ref_no;
            $gl_single_data['remarks'] = $receipt_data->remarks;
            $gl_single_data['iden'] = $customer_code;
            $gl_single_data['accn'] = $receipt_data->bank_accn;
            $gl_single_data['foreign_amount'] = $receipt_data->foreign_amount;
            $gl_single_data['local_amount'] = $receipt_data->local_amount;
            $gl_single_data['sign'] = '+';
            $gl_single_data['tran_type'] = 'BTHREC';
            $gl_single_insert = $this->db->insert('gl_single_entry', $gl_single_data);

            // update exchange rate in currency_master.tbl
            if ($receipt_data->exchange_rate != $currency_rate) {
                $er_data['rate'] = $receipt_data->exchange_rate;
                $er_update = $this->custom->updateRow('ct_currency', $er_data, ['currency_id' => $currency_id, 'code' => $currency_code]);
            }

            unset($gl_data);
            unset($gl_single_data);
            unset($ar_data);
            unset($fb_data);

            if ($ar_insert && $gl_insert) {
                $delete = $this->custom->deleteRow('ez_receipt', ['doc_date' => $receipt_data->doc_date, 'ref_no' => $receipt_data->ref_no, 'customer_id' => $receipt_data->customer_id]);

                echo 'Receipt Posted';
            } else {
                echo 'Post Error';
            }

        } else {
            echo 'Post Error';
        }
    }

    public function post_settlement() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $html = '';

            $settlement_data = $this->custom->getSingleRow('ez_settlement', ['ap_id' => $post['rowID']]);
            $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $settlement_data->supplier_id]);
            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $settlement_data->supplier_id]);
            $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

            $gl_data['doc_date'] = $settlement_data->doc_date;
            $gl_data['ref_no'] = $settlement_data->ref_no;
            $gl_data['remarks'] = $settlement_data->remarks;
            $gl_data['accn'] = 'CL001';
            $gl_data['sign'] = '+';
            $gl_data['tran_type'] = 'BTHSET';
            $gl_data['total_amount'] = $settlement_data->local_amount;
            $gl_data['iden'] = $supplier_code;
            $gl_insert = $this->db->insert('gl', $gl_data);

            $gl_data['accn'] = $settlement_data->bank_accn;
            $gl_data['sign'] = '-';
            $gl_data['total_amount'] = $settlement_data->local_amount;
            $gl_insert = $this->db->insert('gl', $gl_data);

            // insert into accounts_payable
            $ap_data['doc_date'] = $settlement_data->doc_date;
            $ap_data['doc_ref_no'] = $settlement_data->ref_no;
            $ap_data['supplier_code'] = $supplier_code;
            $ap_data['currency'] = $currency_code;
            $ap_data['total_amt'] = $settlement_data->local_amount;
            $ap_data['fa_amt'] = $settlement_data->foreign_amount;
            $ap_data['sign'] = '+';
            $ap_data['tran_type'] = 'BTHSET';
            $ap_data['remarks'] = $settlement_data->remarks;
            $ap_data['purchase_id'] = 0;
            $ap_insert = $this->db->insert('accounts_payable', $ap_data);

            // insert into foreign bank ledger if foreign bank (CA110) selected
            if ($settlement_data->bank_accn == 'CA110' && $settlement_data->fb_accn != '') {

                $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $settlement_data->fb_accn]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_currency_id]);

                $fb_data['doc_ref_no'] = $settlement_data->ref_no;                
                $fb_data['fb_code '] = $settlement_data->fb_accn;
                $fb_data['doc_date'] = $settlement_data->doc_date;
                $fb_data['currency'] = $currency;
                $fb_data['local_amt'] = $settlement_data->local_amount;
                $fb_data['fa_amt'] = $settlement_data->foreign_amount;
                $fb_data['sign'] = '+';
                $fb_data['remarks'] = $settlement_data->remarks;
                $fb_data['tran_type'] = 'BTHREC';
                $fb_insert = $this->custom->insertRow('foreign_bank', $fb_data);
            }

            // insert gl single entry
            $gl_single_data['doc_date'] = $settlement_data->doc_date;
            $gl_single_data['ref_no'] = $settlement_data->ref_no;
            $gl_single_data['remarks'] = $settlement_data->remarks;
            $gl_single_data['iden'] = $supplier_code;
            $gl_single_data['accn'] = $settlement_data->bank_accn;
            $gl_single_data['foreign_amount'] = $settlement_data->foreign_amount;
            $gl_single_data['local_amount'] = $settlement_data->local_amount;
            $gl_single_data['sign'] = '-';
            $gl_single_data['tran_type'] = 'BTHSET';
            $gl_single_insert = $this->db->insert('gl_single_entry', $gl_single_data);

            // update exchange rate in currency_master.tbl
            if ($settlement_data->exchange_rate != $currency_rate) {
                $er_data['rate'] = $settlement_data->exchange_rate;
                $er_update = $this->custom->updateRow('ct_currency', $er_data, ['currency_id' => $currency_id, 'code' => $currency_code]);
            }

            unset($gl_data);
            unset($gl_single_data);
            unset($ap_data);
            unset($fb_data);

            if ($ap_insert && $gl_insert) {
                $delete = $this->custom->deleteRow('ez_settlement', ['doc_date' => $settlement_data->doc_date, 'ref_no' => $settlement_data->ref_no, 'supplier_id' => $settlement_data->supplier_id]);

                echo 'Settlement Posted';
            } else {
                echo 'Post Error';
            }

        } else {
            echo 'Post Error';
        }
    }

    public function post_payment() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $payment_data = $this->custom->getSingleRow('ez_payment', ['batch_id' => $post['rowID']]);

            $credit_total_amount = 0;

            $this->db->select('*');
            $this->db->where('doc_date = "'.$payment_data->doc_date.'" AND ref_no = "'.$payment_data->ref_no.'"');
            $this->db->from('ez_payment');
            $query = $this->db->get();
            $payment_entries = $query->result();
            foreach ($payment_entries as $value) {

                $credit_total_amount += $value->total_amount;

                // insert all the entries into *** gl *** table
                $gl_data['doc_date'] = $value->doc_date;
                $gl_data['ref_no'] = $value->ref_no;
                $gl_data['remarks'] = $value->remarks;
                $gl_data['accn'] = $value->accn;
                $gl_data['sign'] = $value->sign;
                $gl_data['tran_type'] = 'EZPAY';
                $gl_data['total_amount'] = $value->total_amount;

                $gl_insert = $this->db->insert('gl', $gl_data);

                // GST - Settlement / Input Tax Changes
                if ($value->accn == 'CL300') {
                    $gst_data['date'] = $value->doc_date;
                    $gst_data['dref'] = $value->ref_no;
                    $gst_data['rema'] = $value->remarks;

                    if ($value->gst_type == 'S') {
                        $gst_data['iden'] = 'Settlement';
                        $gst_data['gsttype'] = 'S';
                        $gst_data['gstcate'] = '';
                        $gst_data['gstperc'] = 0;
                        $gst_data['amou'] = 0;
                        $gst_data['gstamou'] = $value->total_amount;

                    } elseif ($value->gst_type == 'I') {
                        $gst_data['iden'] = 'Input Tax';
                        $gst_data['gsttype'] = 'I';                        
                        $gst_data['gstcate'] = $value->gst_category;
                        $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                        $gst_data['gstperc'] = $gst_rate;

                        $gst_data['amou'] = $value->net_amount;
                        $gst_data['gstamou'] = $value->gst_amount;
                    }

                    $gst_data['tran_type'] = 'EZPAY';

                    $gst_insert = $this->db->insert('gst', $gst_data);
                }

            } // for loop ends

            // Credit Bank - Inserting One Lum Sum Entry to GL.TBL
            $gl_data['accn'] = $payment_data->bank_accn;
            $gl_data['sign'] = '-';
            $gl_data['total_amount'] = $credit_total_amount;
            $gl_insert = $this->db->insert('gl', $gl_data);

            // insert into foreign bank ledger if foreign bank (CA110) selected
            if ($payment_data->bank_accn == 'CA110' && $payment_data->fb_accn != '') {

                $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $payment_data->fb_accn]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_currency_id]);

                $fb_ledger_data['doc_ref_no'] = $payment_data->ref_no;                
                $fb_ledger_data['fb_code '] = $payment_data->fb_accn;
                $fb_ledger_data['doc_date'] = $payment_data->doc_date;
                $fb_ledger_data['currency'] = $currency;
                $fb_ledger_data['local_amt'] = $credit_total_amount;
                $fb_ledger_data['fa_amt'] = $credit_total_amount;
                $fb_ledger_data['sign'] = '+';
                $fb_ledger_data['remarks'] = $payment_data->remarks;
                $fb_ledger_data['tran_type'] = 'EZPAY';

                $fb_insert = $this->custom->insertRow('foreign_bank', $fb_ledger_data);
            }           

            unset($gl_data);
            unset($gst_data);
            unset($fb_ledger_data);

            if ($gl_insert) {
                $delete = $this->custom->deleteRow('ez_payment', ['doc_date' => $payment_data->doc_date, 'ref_no' => $payment_data->ref_no]);

                echo 'posted';
            } else {
                echo 'Post Error';
            }

        } else {
            echo 'post error';
        }
    }

    public function post_adjustment() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $unbalanced = 0;
            $posted = 0;
            
            $adjustment_data = $this->custom->getMultiValues('ez_adjustment', 'doc_date, ref_no', ['batch_id' => $post['rowID']]);

            // debit and credit todal - equals check
            $dr_cr_diff = 0;
            $sql = 'SELECT sum(CASE WHEN sign = "+" THEN local_amount WHEN sign = "-" THEN -local_amount END) AS sum_of_debit_and_credit FROM ez_adjustment WHERE ref_no = "'.$adjustment_data->ref_no.'"';
            $query = $this->db->query($sql);
            $dr_cr_entries = $query->result();
            foreach ($dr_cr_entries as $record) {
                $dr_cr_diff = $record->sum_of_debit_and_credit;
            }

            // System will POST Tranasctions whose debit total and credit total should be same for double entry
            if ($dr_cr_diff == 0.00) {

                $this->db->select('*');
                $this->db->from('ez_adjustment');
                $this->db->where('doc_date = "'.$adjustment_data->doc_date.'" AND ref_no = "'.$adjustment_data->ref_no.'"');
                $query = $this->db->get();
                $entries = $query->result();
                foreach ($entries as $value) {

                    $accn = $value->accn;
                    $iden = '';

                    // insert CA001 entries into *** accounts_receivable *** table
                    if ($accn == 'CA001') {
                        $ar_data['doc_date'] = $value->doc_date;
                        $ar_data['doc_ref_no'] = $value->ref_no;
                        $ar_data['remarks'] = $value->remarks;

                        $customer = $this->custom->getMultiValues('master_customer', 'code, currency_id', ['customer_id' => $value->control_account]);
                        $ar_data['customer_code'] = $customer->code;
                        $iden = $customer->code;

                        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);
                        $ar_data['currency'] = $currency;

                        $ar_data['total_amt'] = $value->local_amount;
                        $ar_data['f_amt'] = $value->foreign_amount;
                        $ar_data['fa_amt'] = 0.00;
                        $ar_data['sign'] = $value->sign;
                        $ar_data['tran_type'] = 'EZADJ';
                        $ar_data['invoice_id'] = 0;

                        $ar_post = $this->db->insert('accounts_receivable', $ar_data);                    

                    // insert CL001 entries into *** accounts_payable *** table
                    } elseif ($accn == 'CL001') {
                        $ap_data['doc_date'] = $value->doc_date;
                        $ap_data['doc_ref_no'] = $value->ref_no;
                        $ap_data['remarks'] = $value->remarks;

                        $supplier = $this->custom->getMultiValues('master_supplier', 'code, currency_id', ['supplier_id' => $value->control_account]);
                        $ap_data['supplier_code'] = $supplier->code;
                        $iden = $supplier->code;

                        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);
                        $ap_data['currency'] = $currency;

                        $ap_data['total_amt'] = $value->local_amount;
                        $ap_data['fa_amt'] = $value->foreign_amount;
                        $ap_data['sign'] = $value->sign;
                        $ap_data['tran_type'] = 'EZADJ';
                        $ap_data['purchase_id'] = 0;

                        $ap_post = $this->db->insert('accounts_payable', $ap_data);

                    // insert CA110 entries into *** foreign_bank ledger *** table
                    } elseif ($accn == 'CA110') {
                        $fb_data['doc_date'] = $value->doc_date;
                        $fb_data['doc_ref_no'] = $value->ref_no;
                        $fb_data['remarks'] = $value->remarks;

                        $fbank = $this->custom->getMultiValues('master_foreign_bank', 'fb_code, currency_id', ['fb_id' => $value->control_account]);
                        $fb_data['fb_code'] = $fbank->fb_code;
                        $iden = $fbank->fb_code;

                        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fbank->currency_id]);
                        $fb_data['currency'] = $currency;

                        $fb_data['local_amt'] = $value->local_amount;
                        $fb_data['fa_amt'] = $value->foreign_amount;

                        $fb_data['sign'] = $value->sign;
                        $fb_data['tran_type'] = 'EZADJ';

                        $fb_post = $this->db->insert('foreign_bank', $fb_data);
                    
                    // ACCN = CL300 (GST), then show options 1. Input Tax 2. Reverse Output Tax 3. Settlement
                    } elseif ($accn == 'CL300') {
                        $gst_data['date'] = $value->doc_date;
                        $gst_data['dref'] = $value->ref_no;
                        $gst_data['rema'] = $value->remarks;

                        if ($value->gst_type == 'I') { // Debit GST Account with INPUT TAX
                            $gst_data['iden'] = 'Input Tax';
                            $gst_data['gsttype'] = 'I';

                            $gst_data['gstcate'] = $value->gst_category;
                            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                            $gst_data['gstperc'] = $gst_rate;

                            $gst_data['amou'] = $value->net_amount;
                            $gst_data['gstamou'] = $value->gst_amount;

                        } elseif ($value->gst_type == 'OR') { // Debit GST Account with REVERESE OUTPUT TAX
                            $gst_data['iden'] = 'Reverse Output';
                            $gst_data['gsttype'] = 'OR';

                            $gst_data['gstcate'] = $value->gst_category;
                            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                            $gst_data['gstperc'] = $gst_rate;

                            $gst_data['amou'] = $value->net_amount;
                            $gst_data['gstamou'] = $value->gst_amount;
                            
                        } elseif ($value->gst_type == 'S') { // Debit GST Account with SETTLEMENT
                            $gst_data['iden'] = 'Settlement';
                            $gst_data['gsttype'] = 'S';

                            $gst_data['gstcate'] = '';
                            $gst_data['gstperc'] = 0;
                            $gst_data['amou'] = 0;
                            $gst_data['gstamou'] = $value->local_amount;
                        }
                        $gst_data['tran_type'] = 'EZADJ';

                        $gst_post = $this->db->insert('gst', $gst_data);
                    }

                    // insert all the entries into *** gl *** table
                    $gl_data['doc_date'] = $value->doc_date;
                    $gl_data['ref_no'] = $value->ref_no;
                    $gl_data['remarks'] = $value->remarks;
                    $gl_data['accn'] = $accn;
                    $gl_data['sign'] = $value->sign;
                    $gl_data['tran_type'] = 'EZADJ';
                    $gl_data['total_amount'] = $value->local_amount;
                    $gl_data['iden'] = $iden;

                    $gl_post = $this->db->insert('gl', $gl_data);

                } // loop ends


                unset($gl_data);
                unset($gst_data);

                if ($gl_post) {
                    $delete = $this->custom->deleteRow('ez_adjustment', ['doc_date' => $adjustment_data->doc_date, 'ref_no' => $adjustment_data->ref_no]);
                    ++$posted;
                }

            } else { // sum of debit and credit NOT EQUALS 0
                ++$unbalanced;
            }

            $data['posted'] = $posted;
            $data['unbalanced'] = $unbalanced;
            echo json_encode($data);           

        } else {
            redirect("/ez_entry/other_adjustment?error=post");
        }
    }

    public function delete_sales_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_sales', ['sb_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_purchase_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_purchase', ['pb_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_receipt()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_receipt', ['rb_id' => $post['rowID']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_settlement()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_settlement', ['ap_id' => $post['rowID']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_payment_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_payment', ['batch_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_adjustment_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ez_adjustment', ['batch_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_sales()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $data = $this->custom->getMultiValues('ez_sales', 'doc_date, ref_no', ['sb_id' => $post['rowID']]);

            $deleted = $this->custom->deleteRow('ez_sales', ['doc_date' => $data->doc_date, 'ref_no' => $data->ref_no]);

            echo $deleted;
        }
    }

    public function delete_purchase()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $data = $this->custom->getMultiValues('ez_purchase', 'doc_date, ref_no, supplier_id', ['pb_id' => $post['rowID']]);

            $deleted = $this->custom->deleteRow('ez_purchase', ['doc_date' => $data->doc_date, 'ref_no' => $data->ref_no, 'supplier_id' => $data->supplier_id]);

            echo $deleted;
        }
    }

    public function delete_payment()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $data = $this->custom->getMultiValues('ez_payment', 'doc_date, ref_no', ['batch_id' => $post['rowID']]);

            $deleted = $this->custom->deleteRow('ez_payment', ['doc_date' => $data->doc_date, 'ref_no' => $data->ref_no]);

            echo $deleted;
        }
    }

    public function delete_adjustment()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $data = $this->custom->getMultiValues('ez_adjustment', 'doc_date, ref_no', ['batch_id' => $post['rowID']]);

            $deleted = $this->custom->deleteRow('ez_adjustment', ['doc_date' => $data->doc_date, 'ref_no' => $data->ref_no]);

            echo $deleted;
        }
    }

    public function delete_debtor_contra()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $customer_code = $this->custom->getSingleValue('ez_debtor', 'customer_code', ['batch_id' => $post['rowID']]);
            $deleted = $this->custom->deleteRow('ez_debtor', ['customer_code' => $customer_code]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_creditor_contra()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $supplier_code = $this->custom->getSingleValue('ez_creditor', 'supplier_code', ['batch_id' => $post['rowID']]);
            $deleted = $this->custom->deleteRow('ez_creditor', ['supplier_code' => $supplier_code]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }
    
    public function get_fbank_details()
    {
        is_ajax();
        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';
        $post = $this->input->post();
        $result = $this->custom->getSingleRow('master_foreign_bank', $post);
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $result->currency_id]);
        $data['currency_code'] = $currency_data->code;
        $data['currency_rate'] = $currency_data->rate;
        echo json_encode($data);
    }

    public function get_customer_details()
    {
        is_ajax();

        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';

        $post = $this->input->post();
        $customer = $this->custom->getSingleRow('master_customer', $post);
        $data['name'] = $customer->name;
        $data['code'] = $customer->code;
        $data['gst_number'] = $customer->gst_number;
        $data['address'] = $customer->bldg_number.' , <br>'.$customer->street_name.' , <br>'.$customer->postal_code;
        $data['phone'] = $customer->phone;
        $data['email'] = $customer->email;

        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer->currency_id]);
        $data['currency'] = $currency_data->code;
        $data['currency_amount'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function get_supplier_details()
    {
        is_ajax();

        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';

        $post = $this->input->post();
        $supplier = $this->custom->getSingleRow('master_supplier', $post);
        $data['name'] = $supplier->name;
        $data['code'] = $supplier->code;
        $data['gst_number'] = $supplier->gst_number;
        $data['address'] = $supplier->bldg_number.' , <br>'.$supplier->street_name.' , <br>'.$supplier->postal_code;
        $data['phone'] = $supplier->phone;
        $data['email'] = $supplier->email;

        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $supplier->currency_id]);
        $data['currency'] = $currency_data->code;
        $data['currency_amount'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function get_customer_debits_credits() {

        is_ajax();
        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';
        $post = $this->input->post();

        $customer = $this->custom->getMultiValues('master_customer', 'code, name, currency_id', ['customer_id' => $post['customer_id']]);
        $data['name'] = $customer->name;
        $data['code'] = $customer->code;
        
        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);
        $data['currency'] = $currency;

        // Credit Entries
        $html = '';
        $credits = 0;
        $entries = $this->ez_model->get_customer_credits($customer->code);
        foreach ($entries as $key => $value) {

            $ref = $this->custom->getCount('ez_debtor', ['doc_ref_no' => $value->doc_ref_no]);
            if($ref > 0) {
                continue;
            }

            $doc_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $html .= '<tr id="'.$value->ar_id.'">';
            $html .= '<td class="entry d-none">CR</td>';
            $html .= '<td class="doc_date">'.$doc_date.'</td>';
            $html .= '<td class="ref_no">'.$value->original_doc_ref.'</td>';
            $html .= '<td class="amount">'.number_format($value->total_foreign_amount, 2).'</td>';
            $html .= '<td style="text-align: center;">';
            $html .= '<div style="position: relative">
                            <label class="check-container">
                                <input class="entry_check" type="checkbox" name="contra_'.$value->ar_id.'" id="contra_'.$value->ar_id.'" />
                                <span class="checkmark"></span>
                            </label>
                        </div>';
            $html .= '</td>';

            $html .= '</tr>';
            ++$credits;
        }

        if ($credits == 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4">Not found</td>';
            $html .= '<tr>';
        }

        $data['credit_entries'] = $html;
        $data['credits'] = $credits;

        // extract only DEBIT REFERENCES
        $html = '';
        $debits = 0;
        $entries = $this->ez_model->get_customer_debits($customer->code);
        foreach ($entries as $key => $value) {

            $ref = $this->custom->getCount('ez_debtor', ['doc_ref_no' => $value->doc_ref_no]);
            if($ref > 0) {
                continue;
            }

            $doc_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $html .= '<tr id="'.$value->ar_id.'">';
            $html .= '<td class="entry d-none">DR</td>';
            $html .= '<td class="doc_date">'.$doc_date.'</td>';
            $html .= '<td class="ref_no">'.$value->original_doc_ref.'</td>';
            $html .= '<td class="amount">'.number_format($value->total_foreign_amount, 2).'</td>';
            $html .= '<td style="text-align: center;">';
            $html .= '<div style="position: relative">
							    <label class="check-container">
								    <input class="entry_check" type="checkbox" name="contra_'.$value->ar_id.'" id="contra_'.$value->ar_id.'" />
								    <span class="checkmark"></span>
							    </label>
                            </div>';
            $html .= '</td>';

            $html .= '</tr>';
            ++$debits;
        }

        if ($debits == 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4">Not found</td>';
            $html .= '<tr>';
        }

        $data['debit_entries'] = $html;
        $data['debits'] = $debits;

        echo json_encode($data);
        exit;
    }

    public function post_debtor_contra()
    {
        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';
        $post = $this->input->post();

        if ($post) {
            
            $customer_code = $this->custom->getSingleValue('ez_debtor', 'customer_code', ['batch_id' => $post['rowID']]);

            $this->db->select('*');
            $this->db->from('ez_debtor');
            $this->db->where(['customer_code' => $customer_code]);
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $debtor_entries = $query->result();
            foreach ($debtor_entries as $key => $value) {
                $customer_code = $value->customer_code;
                $currency_code = $value->currency;
                $ref_no = $value->doc_ref_no;
   
                if($value->settled == 'n') {
                    
                    // Step 1 :: Update unsettled entry in AR.Tbl (settled = 'n' entry from ez_debtor)
                    $ar_update['total_amt'] = $value->total_amt;
                    $ar_update['f_amt'] = $value->f_amt;
                    $update = $this->custom->updateRow('accounts_receivable', $ar_update, ['customer_code' => $customer_code, 'doc_ref_no' => $value->doc_ref_no]);
                    

                } else { // settled entries

                    // Step 2 :: Insert SPLITTED Settled entry into AR.Tbl (settled = 'y' entry from ez_debtor)
                    if (strpos($value->doc_ref_no, '_sp_1') !== false) {
                        $ar_insert['doc_ref_no'] = $value->doc_ref_no;
                        $ar_insert['customer_code'] = $customer_code;
                        $ar_insert['doc_date'] = $value->doc_date;
                        $ar_insert['currency'] = $currency_code;
                        $ar_insert['total_amt'] = $value->total_amt;
                        $ar_insert['f_amt'] = $value->f_amt;
                        $ar_insert['sign'] = $value->sign;
                        $ar_insert['remarks'] = $value->remarks;
                        $ar_insert['settled'] = 'y';
                        $ar_insert['tran_type'] = 'BTHCONTRA';
                        $insert = $this->custom->insertRow('accounts_receivable', $ar_insert);
                    } else {
                        // update other entries as settled ('y')
                        $update = $this->custom->updateRow('accounts_receivable', ['settled' => 'y'], ['customer_code' => $customer_code, 'doc_ref_no' => $value->doc_ref_no]);
                    }
                
                }

                // delete every entry after update / insert in AR.TBL
                $delete = $this->custom->deleteRow('ez_debtor', ['doc_ref_no' => $ref_no]);
            }
        } else {
            echo 'post error';
        }
    }

    public function get_supplier_debits_credits() {

        is_ajax();
        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';
        $post = $this->input->post();

        $supplier = $this->custom->getMultiValues('master_supplier', 'code, name, currency_id', ['supplier_id' => $post['supplier_id']]);
        $data['name'] = $supplier->name;
        $data['code'] = $supplier->code;
        
        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);
        $data['currency'] = $currency;

        // Credit Entries
        $html = '';
        $credits = 0;
        $entries = $this->ez_model->get_supplier_credits($supplier->code);
        foreach ($entries as $key => $value) {

            $ref = $this->custom->getCount('ez_creditor', ['doc_ref_no' => $value->doc_ref_no]);
            if($ref > 0) {
                continue;
            }

            $doc_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $html .= '<tr id="'.$value->ap_id.'">';
            $html .= '<td class="entry d-none">CR</td>';
            $html .= '<td class="doc_date">'.$doc_date.'</td>';
            $html .= '<td class="ref_no">'.$value->original_doc_ref.'</td>';
            $html .= '<td class="amount">'.number_format($value->total_foreign_amount, 2).'</td>';
            $html .= '<td style="text-align: center;">';
            $html .= '<div style="position: relative">
                            <label class="check-container">
                                <input class="entry_check" type="checkbox" name="contra_'.$value->ap_id.'" id="contra_'.$value->ap_id.'" />
                                <span class="checkmark"></span>
                            </label>
                        </div>';
            $html .= '</td>';

            $html .= '</tr>';
            ++$credits;
        }

        if ($credits == 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4">Not found</td>';
            $html .= '<tr>';
        }

        $data['credit_entries'] = $html;
        $data['credits'] = $credits;

        // extract only DEBIT REFERENCES
        $html = '';
        $debits = 0;
        $entries = $this->ez_model->get_supplier_debits($supplier->code);
        foreach ($entries as $key => $value) {

            $ref = $this->custom->getCount('ez_creditor', ['doc_ref_no' => $value->doc_ref_no]);
            if($ref > 0) {
                continue;
            }

            $doc_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $html .= '<tr id="'.$value->ap_id.'">';
            $html .= '<td class="entry d-none">DR</td>';
            $html .= '<td class="doc_date">'.$doc_date.'</td>';
            $html .= '<td class="ref_no">'.$value->original_doc_ref.'</td>';
            $html .= '<td class="amount">'.number_format($value->total_foreign_amount, 2).'</td>';
            $html .= '<td style="text-align: center;">';
            $html .= '<div style="position: relative">
							    <label class="check-container">
								    <input class="entry_check" type="checkbox" name="contra_'.$value->ap_id.'" id="contra_'.$value->ap_id.'" />
								    <span class="checkmark"></span>
							    </label>
                            </div>';
            $html .= '</td>';

            $html .= '</tr>';
            ++$debits;
        }

        if ($debits == 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4">Not found</td>';
            $html .= '<tr>';
        }

        $data['debit_entries'] = $html;
        $data['debits'] = $debits;

        echo json_encode($data);
        exit;
    }

    public function post_creditor_contra()
    {
        $this->body_file = 'ez_entry/blank.php';
        $this->header_file = 'ez_entry/blank.php';
        $this->footer_file = 'ez_entry/blank.php';
        $post = $this->input->post();

        if ($post) {
            
            $supplier_code = $this->custom->getSingleValue('ez_creditor', 'supplier_code', ['batch_id' => $post['rowID']]);

            $this->db->select('*');
            $this->db->from('ez_creditor');
            $this->db->where(['supplier_code' => $supplier_code]);
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $creditor_entries = $query->result();
            foreach ($creditor_entries as $key => $value) {
                $supplier_code = $value->supplier_code;
                $currency_code = $value->currency;
                $ref_no = $value->doc_ref_no;
   
                if($value->settled == 'n') {
                    
                    // Step 1 :: Update unsettled entry in AP.Tbl (settled = 'n' entry from ez_creditor)
                    $ap_update['total_amt'] = $value->total_amt;
                    $ap_update['fa_amt'] = $value->fa_amt;
                    $update = $this->custom->updateRow('accounts_payable', $ap_update, ['supplier_code' => $supplier_code, 'doc_ref_no' => $value->doc_ref_no]);
                    
                } else { // settled entries

                    // Step 2 :: Insert SPLITTED Settled entry into AP.Tbl (settled = 'y' entry from ez_creditor)
                    if (strpos($value->doc_ref_no, '_sp_1') !== false) {
                        $ap_insert['doc_ref_no'] = $value->doc_ref_no;
                        $ap_insert['supplier_code'] = $supplier_code;
                        $ap_insert['doc_date'] = $value->doc_date;
                        $ap_insert['currency'] = $currency_code;
                        $ap_insert['total_amt'] = $value->total_amt;
                        $ap_insert['fa_amt'] = $value->fa_amt;
                        $ap_insert['sign'] = $value->sign;
                        $ap_insert['remarks'] = $value->remarks;
                        $ap_insert['settled'] = 'y';
                        $ap_insert['tran_type'] = 'BTHCONTRA';
                        $insert = $this->custom->insertRow('accounts_payable', $ap_insert);
                    } else {
                        // update other entries as settled ('y')
                        $update = $this->custom->updateRow('accounts_payable', ['settled' => 'y'], ['supplier_code' => $supplier_code, 'doc_ref_no' => $value->doc_ref_no]);
                    }
                
                }

                // delete every entry after update / insert in AR.TBL
                $delete = $this->custom->deleteRow('ez_creditor', ['doc_ref_no' => $ref_no]);
            }
        } else {
            echo 'post error';
        }
    }
}