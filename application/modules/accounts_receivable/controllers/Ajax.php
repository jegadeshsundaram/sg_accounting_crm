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

        $this->table = 'receipt_master';
        $this->logged_id = $this->session->user_id;
        $this->load->model('accounts_receivable/accounts_receivable_model', 'ar_model');
    }

    // page : data patch
    public function get_refs()
    {
        is_ajax();

        $post = $this->input->post();

        $entries = 0;
        $opts = '<option value="">-- Select --</option>';

        $this->db->select('doc_ref_no');
        $this->db->from('accounts_receivable');
        $this->db->where(['customer_code' => $post['customer_code'], 'tran_type' => 'OPBAL']);
        $this->db->group_by('doc_ref_no');
        $this->db->order_by('doc_ref_no', 'ASC');
        $query = $this->db->get();
        $refs = $query->result();
        foreach ($refs as $value) {
            $opts .= '<option value='.$value->doc_ref_no.'>'.$value->doc_ref_no.'</option>';
            ++$entries;
        }

        $data['entries'] = $entries;
        $data['options'] = $opts;

        echo json_encode($data);
    }

    public function save_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob_id = $post['ob_id'];
            $ob_data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $ob_data['document_reference'] = $post['ref_no'];
            $ob_data['foreign_amount'] = $post['foreign_amount'];
            $ob_data['local_amount'] = $post['local_amount'];
            $ob_data['sign'] = $post['sign'];
            $ob_data['remarks'] = $post['remarks'];
            $ob_data['customer_id'] = $post['customer_id'];

            if ($ob_id == '') {
                $ob_id = $this->custom->insertRow('ar_open', $ob_data);
            } else {
                $updated = $this->custom->updateRow('ar_open', $ob_data, ['ar_ob_id' => $ob_id]);
            }

            echo $ob_id;
        } else {
            echo 'post error';
        }
    }

    public function delete_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('ar_open', ['ar_ob_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_ar_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('accounts_receivable', ['customer_code' => $post['customer_code'], 'ar_id' => $post['ar_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function get_customer()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $post['customer_id']]);

        $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
        $data['customer_currency'] = $currency->code;
        $data['currency_rate'] = $currency->rate;

        echo json_encode($data);
    }

    public function double_ref()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('accounts_receivable', ['customer_code' => $post['customer_code'], 'doc_ref_no' => $post['ref_no']]);
        echo $ref;
        exit;
    }

    public function double_ob_ref()
    {
        is_ajax();
        $post = $this->input->post();
        $customer_id = $post['customer_id'];
        $ref_no = $post['ref_no'];

        $ob_ref = $this->custom->getCount('ar_open', ['document_reference' => $ref_no, 'customer_id' => $customer_id, 'status != ' => 'D']);
        echo $ob_ref;

        exit;
    }

    // this will populate opening balance transactions from ar_open.TBL for do changes if any before post to Accounts_Receivable.TBL
    public function populate_batch_ob()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $data = [];
        $no = $this->input->post('start');

        $table = 'ar_open';
        $columns = ['ar_ob_id', 'customer_id'];
        $where = ['status' => 'C'];
        $group_by = 'customer_id';
        $order_by = 'ar_ob_id';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->customer_id;

            $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $record->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer_data->currency_id]);

            $row[] = '('.$customer_data->code.') '.$customer_data->name;
            $row[] = $currency;           

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

    public function delete()
    {
        is_ajax();
        $customer_id = $this->input->post('rowID');
        $where = ['customer_id' => $customer_id];
        $result = $this->custom->updateRow('ar_open', ['status' => 'D'], $where);
        echo $result;
    }

    public function post()
    {
        is_ajax();
        $customer_id = $this->input->post('rowID');

        $customer = $this->custom->getMultiValues('master_customer', 'currency_id, code', ['customer_id' => $customer_id]);
        $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);

        $this->db->select('*');
        $this->db->from('ar_open');
        $this->db->where(['status' => 'C', 'customer_id' => $customer_id]);
        $this->db->order_by('document_date', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $key => $value) {
            $insert_data['doc_ref_no'] = $value->document_reference;
            $insert_data['customer_code'] = $customer->code;
            $insert_data['doc_date'] = $value->document_date;
            $insert_data['currency'] = $currency_code;
            $insert_data['f_amt'] = $value->foreign_amount;
            $insert_data['total_amt'] = $value->local_amount;
            $insert_data['sign'] = $value->sign;
            $insert_data['tran_type'] = 'OPBAL';
            $insert_data['remarks'] = $value->remarks;

            $inserted = $this->custom->insertRow('accounts_receivable', $insert_data);

            // Update to POSTED in ar_open.TBL
            $posted = $this->custom->updateRow('ar_open', ['status' => 'P'], ['ar_ob_id' => $value->ar_ob_id]);
        }

        echo $inserted;
    }
}
