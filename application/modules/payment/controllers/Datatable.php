<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datatable extends CI_Controller
{
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->logged_id = $this->session->user_id;
    }

    public function ajax_list()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $table = 'payment_master';
        $columns = ['payment_id', 'modified_on', 'payment_ref_no', 'supplier_id', 'currency', 'bank', 'cheque', 'amount', 'other_reference'];
        $where = ['payment_status' => strtoupper($this->uri->segment(4))];
    
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);

        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->payment_id;

            $document_date = date('M j, Y', strtotime($record->modified_on));
            $row[] = strtoupper($document_date);
            $row[] = $record->payment_ref_no;

            $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $record->supplier_id]);
            $row[] = $supplier->name.'<br />'.$supplier->code.' | '.$row->currency;

            $row[] = $record->bank;
            $row[] = $record->cheque;
            $row[] = number_format($record->amount, 2);
            $row[] = $record->other_reference;
            $row[] = date('d-m-Y', strtotime($record->modified_on));

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
}
