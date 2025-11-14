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

    public function ajax_list($data_check)
    {
        $table = '';
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        if ($data_check == 'invoice') {
            $table = 'invoice_master';
            $columns = ['invoice_id', 'modified_on', 'invoice_ref_no', 'customer_id', 'sub_total', 'lsd_code', 'lsd_percentage', 'lsd_value', 'net_after_lsd', 'f_net_total'];
            $where = ['status' => strtoupper($this->uri->segment(5))];
            $table_id = 'invoice_id';
            $order_by = 'modified_on';
            $order = 'DESC';

        } elseif ($data_check == 'ar') {
            $table = 'accounts_receivable';
            $columns = ['doc_date', 'doc_ref_no', 'customer_code', 'f_amt', 'total_amt', 'currency', 'tran_type', 'remarks', 'sign'];
            $where = ['accounts_receivable.tran_type' => 'INVOICE'];
            $table_id = 'ar_id';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'gl') {
            $table = 'gl';
            $columns = ['doc_date', 'ref_no', 'accn', 'sign', 'total_amount', 'remarks'];
            $where = ['gl.tran_type' => 'INVOICE'];
            $table_id = 'gl_id';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'stock') {
            $in_status = 'Product';
            $table = 'stock';
            $columns = ['iden', 'created_on', 'ref_no', 'product_id', 'quantity', 'unit_cost', 'sign'];
            $where = ['stock_type' => 'Invoice'];
            $table_id = 'stock_id';
            $order_by = 'created_on';
            $order = 'DESC';

        } elseif ($data_check == 'gst') {
            $table = 'gst';
            $columns = ['iden', 'date', 'dref', 'rema', 'gsttype', 'gstcate', 'amou', 'gstamou'];
            $where = ['tran_type' => 'INVOICE'];
            $table_id = 'gst_id';
            $order_by = 'date';
            $order = 'DESC';
        
        } elseif ($data_check == 'cstmr_price') {
            $table = 'customer_price';
            $columns = ['customer_code', 'modified_on'];
            $table_id = 'pt_id';
            $order_by = 'modified_on';
            $order = 'DESC';
        }

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        
        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];
            if ($data_check == 'invoice') {
                $row[] = $record->invoice_id;

                $document_date = date('M j, Y', strtotime($record->modified_on));
                $row[] = strtoupper($document_date);
                $row[] = $record->invoice_ref_no;

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

            } elseif ($data_check == 'ar') {
                $row[] = $table_id;
                $doc_date = implode('/', array_reverse(explode('-', $record->doc_date)));
                $row[] = $doc_date;
                $row[] = $record->doc_ref_no;
                $customer_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->customer_code]);
                $row[] = $customer_name.' ('.$record->customer_code.')';
                $row[] = $record->currency;
                $row[] = number_format($record->f_amt, 2, '.', '');
                $row[] = number_format($record->total_amt, 2, '.', '');
                $row[] = $record->remarks;

            } elseif ($data_check == 'gl') {
                $row[] = $table_id;
                $doc_date = implode('/', array_reverse(explode('-', $record->doc_date)));
                $row[] = $doc_date;
                $row[] = $record->ref_no;

                $customer_id = $this->custom->getSingleValue('invoice_master', 'customer_id', ['invoice_ref_no' => $record->ref_no]);
                $customer_name = $this->custom->getSingleValue('master_customer', 'name', ['customer_id' => $customer_id]);
                $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $customer_id]);

                $row[] = $customer_name.' ('.$customer_code.')';
                $row[] = $record->accn;

                if ($record->sign == '+') {
                    $row[] = $record->total_amount;
                    $row[] = '';
                } else {
                    $row[] = '';
                    $row[] = $record->total_amount;
                }
                $row[] = $record->remarks;

            } elseif ($data_check == 'stock') {
                $row[] = $table_id;
                $doc_date = implode('/', array_reverse(explode('-', $record->created_on)));
                $row[] = $doc_date;
                $row[] = $record->ref_no;

                $customer_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->iden]);
                $row[] = $customer_name.' ('.$record->iden.')';

                $billing_description = $this->custom->getSingleValue('master_billing', 'billing_description', ['billing_id' => $record->product_id]);
                $row[] = $billing_description.' ('.$record->product_id.')';

                $row[] = $record->quantity;
                $row[] = $record->unit_cost;

                $stock_amount = $value->quantity * $value->unit_cost;
                $row[] = number_format($stock_amount, 2);

                $row[] = $record->sign;

            } elseif ($data_check == 'gst') {
                $row[] = $table_id;

                $doc_date = implode('/', array_reverse(explode('-', $record->date)));
                $row[] = $doc_date;
                $row[] = $record->dref;

                $customer_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->iden]);
                $row[] = $customer_name.' ('.$record->iden.')';

                $row[] = $record->amou;
                $row[] = $record->gstcate;
                $row[] = $record->gstamou;
                if ($record->gsttype == 'O') {
                    $row[] = 'OUTPUT';
                } elseif ($record->gsttype == 'I') {
                    $row[] = 'INPUT';
                }

                $row[] = $record->rema;
                
            } elseif ($data_check == 'cstmr_price') {
                $row[] = $table_id;
                $customer_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->customer_code]);
                $row[] = date('d-m-Y', strtotime($record->modified_on));
                $row[] = $customer_name.' ('.$record->customer_code.')';
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