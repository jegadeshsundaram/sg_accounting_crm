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
        $this->logged_id = $this->session->user_id;
    }

    public function get_supplier()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $post['supplier_id']]);

        $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
        $data['supplier_currency'] = $currency->code;
        $data['currency_rate'] = $currency->rate;

        echo json_encode($data);
    }

    public function double_ref()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('accounts_payable', ['supplier_code' => $post['supplier_code'], 'doc_ref_no' => $post['ref_no']]);
        echo $ref;
        exit;
    }

    public function double_ob_ref()
    {
        is_ajax();
        $post = $this->input->post();
        $supplier_id = $post['supplier_id'];
        $ref_no = $post['ref_no'];

        $ob_ref = $this->custom->getCount('ap_open', ['document_reference' => $ref_no, 'supplier_id' => $supplier_id, 'status != ' => 'D']);
        echo $ob_ref;

        exit;
    }

    // page : data patch
    public function get_refs()
    {
        is_ajax();

        $post = $this->input->post();

        $entries = 0;
        $opts = '<option value="">-- Select --</option>';

        $this->db->select('doc_ref_no');
        $this->db->from('accounts_payable');
        $this->db->where(['supplier_code' => $post['supplier_code'], 'tran_type' => 'OPBAL']);
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
            $ob_data['supplier_id'] = $post['supplier_id'];

            if ($ob_id == '') {
                $ob_id = $this->custom->insertRow('ap_open', $ob_data);
            } else {
                $updated = $this->custom->updateRow('ap_open', $ob_data, ['ap_ob_id' => $ob_id]);
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
            $deleted = $this->custom->deleteRow('ap_open', ['ap_ob_id' => $post['entry_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function delete_ap_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('accounts_payable', ['supplier_code' => $post['supplier_code'], 'ap_id' => $post['ap_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    // this will populate opening balance transactions from ap_open.TBL for do changes if any before post to Accounts_Payable.TBL
    public function populate_batch_ob()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;

        $table = 'ap_open';
        $columns = ['ap_ob_id', 'supplier_id'];
        $where = ['status' => 'C'];
        $group_by = 'supplier_id';
        $order_by = 'ap_ob_id';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);

        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->supplier_id;

            $supplier = $this->custom->getMultiValues('master_supplier', 'currency_id, code, name', ['supplier_id' => $record->supplier_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);

            $row[] = '('.$supplier->code.') '.$supplier->name;
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
        $supplier_id = $this->input->post('rowID');
        $where = ['supplier_id' => $supplier_id];
        $result = $this->custom->updateRow('ap_open', ['status' => 'D'], $where);
        echo $result;
    }

    public function post()
    {
        is_ajax();
        $supplier_id = $this->input->post('rowID');

        $supplier = $this->custom->getMultiValues('master_supplier', 'currency_id, code', ['supplier_id' => $supplier_id]);
        $currency_code = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);

        $this->db->select('*');
        $this->db->from('ap_open');
        $this->db->where(['status' => 'C', 'supplier_id' => $supplier_id]);
        $this->db->order_by('document_date', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $record) {
            $data['doc_ref_no'] = $record->document_reference;
            $data['supplier_code'] = $supplier->code;
            $data['doc_date'] = $record->document_date;
            $data['currency'] = $currency_code;
            $data['fa_amt'] = $record->foreign_amount;
            $data['total_amt'] = $record->local_amount;
            $data['sign'] = $record->sign;
            $data['tran_type'] = 'OPBAL';
            $data['remarks'] = $record->remarks;

            $inserted = $this->custom->insertRow('accounts_payable', $data);

            // Update to POSTED in ap_open.TBL
            $posted = $this->custom->updateRow('ap_open', ['status' => 'P'], ['ap_ob_id' => $record->ap_ob_id]);
        }

        echo $inserted;
    }
}
