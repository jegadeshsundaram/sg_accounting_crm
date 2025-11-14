<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'foreign_bank';
        $this->logged_id = $this->session->user_id;
        $this->load->model('foreign_bank/Foreign_bank_model', 'fb_model');
    }

    public function get_bank()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $post['code']]);
        $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
        $data['currency'] = $currency->code;
        $data['currency_rate'] = $currency->rate;

        echo json_encode($data);
    }

    public function populate_ob()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $data = [];
        $no = $this->input->post('start');

        $table = 'fb_open';
        $columns = ['fb_ob_id', 'fb_code', 'document_date', 'document_reference', 'remarks', 'foreign_amount', 'local_amount', 'sign'];
        $where = ['status' => 'C'];
        $group_by = null;
        $order_by = 'fb_ob_id';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->fb_ob_id;

            $row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                    <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                    <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';

            $row[] = date('d-m-Y', strtotime($record->document_date));
            $row[] = $record->document_reference;

            $fb_data = $this->custom->getMultiValues('master_foreign_bank', 'fb_name, currency_id', ['fb_code' => $record->fb_code]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);

            $row[] = '('.$record->fb_code.') '.$fb_data->fb_name.' | '.$currency;

            $row[] = $record->foreign_amount;
            $row[] = $record->local_amount;
            $row[] = $record->remarks;

            $row[] = $record->sign;

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

    public function double_ref()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('fb_open', ['document_reference' => $post['ref_no'], 'status != ' => 'D']);
        echo $ref;
    }

    function get_ob() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob = $this->custom->getSingleRow('fb_open', ['fb_ob_id' => $post['rowID']]);
            $data['ob'] = $ob;

            $currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $ob->fb_code]);
            $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);

            $data['currency'] = $currency->code;
            $data['currency_rate'] = $currency->rate;

            echo json_encode($data);
        }
    }

    // save ob entry
    public function save_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob_id = $post['entry_id'];
            $ob_data['fb_code'] = $post['bank'];
            $ob_data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $ob_data['document_reference'] = $post['ref_no'];
            $ob_data['foreign_amount'] = $post['foreign_amount'];
            $ob_data['local_amount'] = $post['local_amount'];
            $ob_data['sign'] = $post['sign'];

            if ($post['remarks'] != '') {
                $ob_data['remarks'] = $post['remarks'];
            } else {
                $ob_data['remarks'] = 'Opening Balance B/F';
            }

            if ($ob_id == '') {
                $res = $this->custom->insertData('fb_open', $ob_data);
            } else {
                $res = $this->custom->updateRow('fb_open', $ob_data, ['fb_ob_id' => $ob_id]);
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

    // listing page
    public function delete_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');
        $where = ['fb_ob_id' => $ob_id];
        $status = $this->custom->updateRow('fb_open', ['status' => 'D'], $where);
        echo $status;
    }

    public function post_ob()
    {
        is_ajax();

        $ob_id = $this->input->post('rowID');
        $where = ['fb_ob_id' => $ob_id];

        // get details from fb opening balance
        $ob_data = $this->custom->getSingleRow('fb_open', $where);

        // get currency details from ct_currency
        $currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $ob_data->fb_code]);
        $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);

        $insert_data['doc_ref_no'] = $ob_data->document_reference;
        $insert_data['fb_code'] = $ob_data->fb_code;
        $insert_data['doc_date'] = $ob_data->document_date;
        $insert_data['currency'] = $currency;
        $insert_data['local_amt'] = $ob_data->local_amount;
        $insert_data['fa_amt'] = $ob_data->foreign_amount;
        $insert_data['sign'] = $ob_data->sign;
        $insert_data['tran_type'] = 'OPBAL';
        $insert_data['remarks'] = $ob_data->remarks;
        $ob_id = $this->custom->insertRow('foreign_bank', $insert_data);

        if ($ob_id > 0) {
            // Update transaction status to POSTED
            $status = $this->custom->updateRow('fb_open', ['status' => 'P'], $where);
        }

        echo $status;
    }

}
