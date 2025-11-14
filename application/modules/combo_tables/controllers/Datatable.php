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

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('person_view');
    }

    public function ajax_list($data_check)
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        if ($data_check == 'ye_revision') {
            $table = 'ye_revision';
            $columns = ['r_id', 'currency', 'rate', 'description', 'cutoff_date'];
        }

        if ($data_check == 'currency') {
            $table = 'ct_currency';
            $columns = ['currency_id', 'code', 'rate', 'description'];
        }

        if ($data_check == 'country') {
            $table = 'ct_country';
            $columns = ['country_id', 'country_name', 'country_code'];
        }

        if ($data_check == 'gst') {
            $table = 'ct_gst';
            $columns = ['gst_id', 'gst_code', 'gst_rate', 'gst_type', 'gst_description'];
        }

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);

        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            if ($data_check == 'ye_revision') {
                $row[] = $record->r_id;
                $row[] = $record->currency;
                $row[] = $record->description;
                $row[] = $record->rate;
                $row[] = date('d-m-Y', strtotime($record->cutoff_date));
            }

            if ($data_check == 'currency') {
                $row[] = $record->currency_id;
                $row[] = $record->code;
                $row[] = $record->description;
                $row[] = number_format($record->rate, 5);
            }

            if ($data_check == 'country') {
                $row[] = $record->country_id;
                $row[] = $record->country_code;
                $row[] = $record->country_name;
            }

            if ($data_check == 'gst') {
                $row[] = $record->gst_id;
                $row[] = $record->gst_code;
                $row[] = $record->gst_rate;
                $row[] = $record->gst_type;
                $row[] = $record->gst_description;
            }

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
