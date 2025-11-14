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

        $table = 'receipt_master';
        $columns = ['receipt_id', 'modified_on', 'receipt_ref_no', 'customer_id', 'bank', 'cheque', 'amount', 'other_reference'];
        $where = ['receipt_status' => strtoupper($this->uri->segment(4))];
        $table_id = 'receipt_id';
        $order_by = 'modified_on';
        $order = 'DESC';

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);

        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->receipt_id;

            $document_date = date('M j, Y', strtotime($record->modified_on));
            $row[] = strtoupper($document_date);
            $row[] = $record->receipt_ref_no;

            $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $record->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);
            $row[] = $customer->name.'<br />'.$customer->code.' | '.$currency;

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
