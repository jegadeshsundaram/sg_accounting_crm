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

        if ($data_check == 'customer') {
            $table = 'master_customer';
            $columns = ['customer_id', 'name', 'code', 'contact_person', 'email', 'phone', 'currency_id'];
            $where = ['active' => 1];
            $order_by = 'name';
        }

        if ($data_check == 'supplier') {
            $table = 'master_supplier';
            $columns = ['supplier_id', 'name', 'code', 'contact_person', 'email', 'phone', 'currency_id'];
            $where = ['active' => 1];
            $order_by = 'name';
        }

        if ($data_check == 'billing') {
            $table = 'master_billing';
            $columns = ['billing_id', 'stock_code', 'billing_description', 'billing_uom', 'billing_price_per_uom', 'billing_type'];
            $order_by = 'stock_code';
        }

        if ($data_check == 'foreign_bank') {
            $table = 'master_foreign_bank';
            $columns = [
                'fb_id',
                'fb_name',
                'fb_code',
                'phone',
                'email',
                'currency_id',
            ];
            $order_by = 'fb_name';
        }

        if ($data_check == 'accountant') {
            $table = 'master_accountant';
            $columns = [
                'ac_id',
                'name',
                'code',
                'category',
                'basic_salary',
                'incentives',
                'email',
            ];
            $where = ['active' => 1];
            $order_by = 'name';
        }

        if ($data_check == 'employee') {
            $table = 'master_employee';
            $columns = [
                'e_id',
                'name',
                'code',
                'email',
                'department_id',
                'note',
            ];
            $order_by = 'name';
        }

        if ($data_check == 'department') {
            $table = 'master_department';
            $columns = [
                'd_id',
                'name',
                'code',
            ];
            $order_by = 'name';
        }

        $data = [];
        $no = $this->input->post('start');

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            if ($data_check == 'customer') {
                $row[] = $record->customer_id;
                $row[] = $record->code;
                $row[] = $record->name;
                $row[] = $record->contact_person;
                $row[] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $record->currency_id]);
                $row[] = $record->phone;
                $row[] = $record->email;
            }

            if ($data_check == 'supplier') {
                $row[] = $record->supplier_id;
                $row[] = $record->code;
                $row[] = $record->name;
                $row[] = $record->contact_person;
                $row[] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $record->currency_id]);
                $row[] = $record->phone;
                $row[] = $record->email;
            }

            if ($data_check == 'billing') {
                $row[] = $record->billing_id;
                $row[] = $record->stock_code;
                $row[] = $record->billing_description;
                $row[] = $record->billing_uom;
                $row[] = $record->billing_price_per_uom;
                $row[] = $record->billing_type;
            }

            if ($data_check == 'foreign_bank') {
                $row[] = $record->fb_id;
                $row[] = $record->fb_code;
                $row[] = $record->fb_name;
                $row[] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $record->currency_id]);
                $row[] = $record->phone;
                $row[] = $record->email;
            }

            if ($data_check == 'accountant') {
                $row[] = $record->ac_id;
                $row[] = $record->code;
                $row[] = $record->name;
                $row[] = $record->category;
                $row[] = $record->basic_salary;
                $row[] = $record->incentives;
                $row[] = $record->email;
            }

            if ($data_check == 'employee') {
                $row[] = $record->e_id;
                $row[] = $record->code;
                $row[] = $record->name;
                $row[] = $record->email;
                $department_name = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $record->department_id]);
                $row[] = $department_name;
                $row[] = $record->note;
            }

            if ($data_check == 'department') {
                $row[] = $record->d_id;
                $row[] = $record->code;
                $row[] = $record->name;
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
