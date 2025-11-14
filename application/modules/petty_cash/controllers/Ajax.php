<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'petty_cash';
        $this->logged_id = $this->session->user_id;
        $this->load->model('Petty_cash/Petty_cash_model', 'pt_model');
    }

    function get_settings() {
        is_ajax();
        // get last inserted row
        $settings = $this->custom->getLastInsertedRow('petty_cash_setting', 'updated_on');
        $data['settings'] = $settings;
        echo json_encode($data);
    }

    public function duplicate()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $pc = $this->custom->getCount('petty_cash_batch', ['ref_no' => $ref_no]);
        echo $pc;
    }

    function save_settings() {

        is_ajax();
        $post = $this->input->post();
        $post['user_id'] = $this->session->user_id;

        // checks current text prefix from settings page is already exists or not
        $prefix = $this->custom->getCount('petty_cash_setting', ['text_prefix' => $post['text_prefix']]);
        if ($prefix > 0) { // Exists, Update Entry
            
            $this->custom->updateRow('petty_cash_setting', $post, ['text_prefix' => $post['text_prefix']]);
            echo 'Settings saved';

        } else { // Not Exists, Insert Entry
            
            $this->custom->insertRow('petty_cash_setting', $post);
            echo 'Settings Updated';

        }
    }

    public function get_gst_rate()
    {
        is_ajax();
        $post = $this->input->post();
        $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gst_code']]);
        echo $gst_rate;
    }

    public function populate_voucher_number()
    {
        is_ajax();
        $post = $this->input->post();
        $number = $post['number'] + 1;
        $ref = $post['text'].'.'.$number;
        $nextVoucher = $this->custom->getSingleRow('petty_cash_batch', ['ref_no' => $ref]);
        if (count($nextVoucher)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function double_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $document_reference = $post['document_reference'];

        $gl_reference = $this->custom->getSingleRow('gl', ['ref_no' => $document_reference, 'tran_type' => 'PTCASH']);
        if (count($gl_reference)) {
            echo '1';
        } else {
            $petty_cash_reference = $this->custom->getSingleRow('petty_cash_batch', ['ref_no' => $document_reference]);
            if (count($petty_cash_reference)) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }

    public function get_COA_details()
    {
        is_ajax();
        $post = $this->input->post();
        $coa_data = $this->custom->getSingleRow('chart_of_account', $post);
        $data['coa_code'] = $coa_data->accn;
        $data['coa_description'] = $coa_data->description;
        echo json_encode($data);
    }

    public function save_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $entry_id = $post['entry_id'];
            $batch_data['pay_to'] = $post['pay_to'];
            $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $batch_data['ref_no'] = $post['ref_no'];
            $batch_data['accn'] = $post['coa'];
            $batch_data['amount'] = $post['amount'];
            $batch_data['gst_type'] = $post['gst_type'];
            $batch_data['gst_category'] = $post['gst_category'];
            $batch_data['net_amount'] = $post['net_amount'];
            $batch_data['gst_amount'] = $post['gst_amount'];
            $batch_data['iden'] = $post['iden'];
            $batch_data['remarks'] = $post['remarks'];
            $batch_data['received_by'] = $post['received_by'];
            $batch_data['approved_by'] = $post['approved_by'];

            if ($entry_id == '') {
                $entry_id = $this->custom->insertRow('petty_cash_batch', $batch_data);
            } else {
                $updated = $this->custom->updateRow('petty_cash_batch', $batch_data, ['pcb_id' => $entry_id]);
            }

            echo $entry_id;
        } else {
            echo 'post error';
        }
    }

    // this will populate opening balance transactions from Accounts_Receivable.TBL for Datapatch if any
    public function populate_vouchers()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $data = [];
        $no = $this->input->post('start');

        $table = 'petty_cash_batch';
        $columns = ['pcb_id', 'pay_to', 'doc_date', 'ref_no', 'received_by', 'approved_by', 'status'];
        $where = ['status' => 'C'];
        $group_by = 'doc_date, ref_no';
        $order_by = 'doc_date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->pcb_id;
            $row[] = $record->pay_to;
            $row[] = $record->doc_date;
            $row[] = $record->ref_no;
            $row[] = $record->received_by;
            $row[] = $record->approved_by;
            $data[] = $row;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->dt_model->count_all($table),
            'recordsFiltered' => $this->dt_model->count_filtered($table, $columns, $join_table, $join_condition, $where),
            'data' => $data,
        ];

        echo json_encode($output);
    }

    public function list_petty_cash()
    {
        is_ajax();

        $this->db->select('*');
        $this->db->from('petty_cash_batch');
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $batch_data = $query->result();

        $html = '';

        foreach ($batch_data as $key => $value) {
            $document_date = strtoupper(date('M j, Y', strtotime($value->doc_date)));
            $html .= '<tr id="'.$value->pcb_id.'">';
            $html .= '<td width="85">'.$document_date.'</td>';
            $html .= '<td width="100">'.$value->ref_no.'</td>';
            $html .= '<td width="220">'.$value->pay_to.'</td>';
            $html .= '<td width="220">'.$value->received_by.'</td>';
            $html .= '<td width="220">'.$value->approved_by.'</td>';
            $html .= '</tr>';
            ++$i;
        }

        $data['table_html'] = $html;

        echo json_encode($data);
    }

    public function delete_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('petty_cash_batch', ['pcb_id' => $post['entry_id']]);
            
            echo $status;
        } else {
            echo 'post error';
        }
    }

    // delete petty cash in listing page
    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        $pc_data = $this->custom->getMultiValues('petty_cash_batch', 'doc_date, ref_no', ['pcb_id' => $id]);

        $where = ['doc_date' => $pc_data->doc_date, 'ref_no' => $pc_data->ref_no];
        $result = $this->custom->updateRow('petty_cash_batch', ['status' => 'D'], $where);

        echo $result;
    }

    public function post()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $pc_data = $this->custom->getMultiValues('petty_cash_batch', 'doc_date, ref_no', ['pcb_id' => $id]);
       
        $credits_total = 0;

        $this->db->select('*');
        $this->db->from('petty_cash_batch');
        $this->db->where(['ref_no' => $pc_data->ref_no]);
        $query = $this->db->get();
        $entries = $query->result();
        foreach ($entries as $value) {
            $credits_total += $value->amount;

            // insert all the entries into *** GL.TBL ***
            $gl_data['doc_date'] = $value->doc_date;
            $gl_data['ref_no'] = $value->ref_no;
            $gl_data['remarks'] = $value->remarks;
            $gl_data['accn'] = $value->accn;
            $gl_data['sign'] = $value->sign;
            $gl_data['tran_type'] = 'PTCASH';
            $gl_data['total_amount'] = $value->amount;
            $gl_insert = $this->db->insert('gl', $gl_data);

            // insert CA001 entries into *** accounts_receivable *** table
            if ($value->accn == 'CA001') {
                $ar_data['doc_date'] = $value->doc_date;
                $ar_data['doc_ref_no'] = $value->ref_no;
                $ar_data['remarks'] = $value->remarks;
                $ar_data['customer_code'] = $value->iden;

                $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $value->iden]);
                $currency_data = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
                $ar_data['currency'] = $currency_data->code;

                $local_amount = $value->amount / $currency_data->rate;
                $ar_data['total_amt'] = round($local_amount, 2);
                $ar_data['f_amt'] = $value->amount;

                $ar_data['fa_amt'] = 0.00;

                $ar_data['sign'] = $value->sign;
                $ar_data['tran_type'] = 'PTCASH';

                $ar_data['invoice_id'] = 0;
                $ar_insert = $this->db->insert('accounts_receivable', $ar_data);

            } elseif ($value->accn == 'CL001') {
                // insert CL001 entries into *** accounts_payable *** table
                $ap_data['doc_date'] = $value->doc_date;
                $ap_data['doc_ref_no'] = $value->ref_no;

                $ap_data['remarks'] = $value->remarks;
                $ap_data['supplier_code'] = $value->iden;

                $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['code' => $value->iden]);
                $currency_data = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
                $ap_data['currency'] = $currency_data->code;

                $local_amount = $value->amount / $currency_data->rate;
                $ap_data['total_amt'] = round($local_amount, 2);
                $ap_data['fa_amt'] = $value->amount;

                $ap_data['sign'] = $value->sign;
                $ap_data['tran_type'] = 'PTCASH';

                $ap_data['purchase_id'] = 0;

                $ap_insert = $this->db->insert('accounts_payable', $ap_data);

            } elseif ($value->accn == 'CL300') { // Insert "CL300" entries into *** GST.TBL ***
                $gst_data['date'] = $value->doc_date;
                $gst_data['dref'] = $value->ref_no;
                $gst_data['rema'] = $value->remarks;

                if ($value->gst_type == 'I') {
                    $gst_data['iden'] = 'Input Tax';
                    $gst_data['gsttype'] = 'I';
                    $gst_data['gstcate'] = $value->gst_category;
                    $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                    $gst_data['gstperc'] = $gst_rate;

                    $gst_data['amou'] = $value->net_amount;
                    $gst_data['gstamou'] = $value->gst_amount;
                }
                $gst_data['tran_type'] = 'PTCASH';

                $gst_insert = $this->db->insert('gst', $gst_data);
            }
        } // entry loop ends

        // Credit Default Bank - Inserting One Lum Sum Entry to GL.TBL
        $gl_data['doc_date'] = $pc_data->doc_date;
        $gl_data['ref_no'] = $pc_data->ref_no;
        $gl_data['remarks'] = 'Credit to Petty Cash Account';
        $gl_data['accn'] = 'CA100';
        $gl_data['sign'] = '-';
        $gl_data['tran_type'] = 'PTCASH';
        $gl_data['total_amount'] = $credits_total;

        $gl_insert = $this->db->insert('gl', $gl_data);

        if ($gl_insert) {
            $delete = $this->custom->updateRow('petty_cash_batch', ['status' => 'P'], ['ref_no' => $pc_data->ref_no]);
        }
        

        echo $gl_insert;
    }
}
