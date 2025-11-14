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

        $table = 'quotation_master';
        $columns = ['quotation_id', 'modified_on', 'quotation_ref_no', 'customer_id', 'sub_total', 'lsd_code', 'lsd_percentage', 'lsd_value', 'net_after_lsd', 'f_net_total'];
        $where = ['status' => strtoupper($this->uri->segment(4))];
        $order_by = 'modified_on';
        $order = 'DESC';

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);

        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->quotation_id;

            $document_date = date('M j, Y', strtotime($record->modified_on));
            $row[] = strtoupper($document_date);
            $row[] = $record->quotation_ref_no;

            $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $record->customer_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer_data->currency_id]);
            $row[] = $customer_data->name.'<br />'.$customer_data->code.' | '.$currency;

            $row[] = number_format($record->sub_total, 2);

            if ($record->lsd_code == 'P') {
                $row[] = '$'.number_format($record->lsd_value, 2).' | '.number_format($record->lsd_percentage, 2);
            } elseif ($record->lsd_code == 'V') { // disount amount applied
                $row[] = '$'.number_format($record->lsd_value, 2);
            } else {
                $row[] = '0.00';
            }
            $row[] = number_format($record->net_after_lsd, 2);
            $row[] = number_format($record->f_net_total, 2);

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
