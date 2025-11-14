<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datatable extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function ajax_list($data_check)
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        if ($data_check == 'batch_sales') {
            $table = 'ez_sales';
            $columns = ['sb_id', 'doc_date', 'ref_no', 'customer_id', 'exchange_rate'];
            $group_by = 'ref_no';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'batch_purchase') {
            $table = 'ez_purchase';
            $columns = ['pb_id', 'doc_date', 'ref_no', 'supplier_id', 'exchange_rate'];
            $group_by = 'ref_no, supplier_id';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'batch_receipt') {
            $table = 'ez_receipt';
            $columns = ['rb_id', 'doc_date', 'ref_no', 'customer_id', 'exchange_rate', 'foreign_amount', 'local_amount', 'remarks'];
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'batch_settlement') {
            $table = 'ez_settlement';
            $columns = ['ap_id', 'doc_date', 'ref_no', 'supplier_id', 'exchange_rate', 'foreign_amount', 'local_amount', 'remarks'];
            $order_by = 'doc_date';
            $order = 'DESC';
        } elseif ($data_check == 'batch_payment') {
            $table = 'ez_payment';
            $columns = ['batch_id', 'doc_date', 'ref_no', 'remarks'];
            $group_by = 'doc_date, ref_no';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'batch_adjustment') {
            $table = 'ez_adjustment';
            $columns = ['batch_id', 'doc_date', 'ref_no', 'remarks'];
            $group_by = 'doc_date, ref_no';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'debtor') {
            $table = 'ez_debtor';
            $columns = ['batch_id', 'customer_code', 'currency'];
            $group_by = 'customer_code';
            $order_by = 'doc_date';
            $order = 'DESC';

        } elseif ($data_check == 'creditor') {
            $table = 'ez_creditor';
            $columns = ['batch_id', 'supplier_code', 'currency'];
            $group_by = 'supplier_code';
            $order_by = 'doc_date';
            $order = 'DESC';
        }

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        $data = [];

        $no = $this->input->post('start');

        foreach ($list as $record) {
            ++$no;
            $row = [];

            if ($data_check == 'batch_sales') {
                $row[] = $record->sb_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
						<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
						<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;

                $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $record->customer_id]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);
                $row[] = '('.$customer->code.') '.$customer->name;

                $row[] = $currency.' | <u>Exchange Rate:</u> '.$record->exchange_rate;

            } elseif ($data_check == 'batch_purchase') {
                $row[] = $record->pb_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
						<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
						<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;

                $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $record->supplier_id]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);
                $row[] = '('.$supplier->code.') '.$supplier->name;

                $row[] = $currency.' | <u>Exchange Rate:</u> '.$record->exchange_rate;            

            }  elseif ($data_check == 'batch_receipt') {
                $row[] = $record->rb_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
                        <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                        <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;

                $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $record->customer_id]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer->currency_id]);
                $row[] = '('.$customer->code.') '.$customer->name;

                if($currency == 'SGD') {
                    $row[] = $currency;
                } else {
                    $row[] = $currency.' (<u>Rate:</u> '.$record->exchange_rate.')';
                }

                $row[] = number_format($record->foreign_amount, 2);

            } elseif ($data_check == 'batch_settlement') {
                $row[] = $record->ap_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
                        <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                        <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;

                $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $record->supplier_id]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $supplier->currency_id]);
                $row[] = '('.$supplier->code.') '.$supplier->name;

                if($currency == 'SGD') {
                    $row[] = $currency;
                } else {
                    $row[] = $currency.' ('.$record->exchange_rate.')';
                }

                $row[] = number_format($record->foreign_amount, 2);

            } elseif ($data_check == 'batch_payment') {
                $row[] = $record->batch_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
                        <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                        <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;
                $row[] = $record->remarks;

            } elseif ($data_check == 'batch_adjustment') {
                $row[] = $record->batch_id;
                $row[] = '<a class="dt-btn dt_view" style="display: none"><i class="fa fa-eye"></i><span>View</span></a>
                        <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                        <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                $row[] = date('M j, Y', strtotime($record->doc_date));
                $row[] = $record->ref_no;
                $row[] = $record->remarks;

            } elseif ($data_check == 'debtor') {
                $row[] = $record->batch_id;
                $row[] = '<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                
                $name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->customer_code]);
                $row[] = '('.$record->customer_code.') '.$name;
                $row[] = $record->currency;

            } elseif ($data_check == 'creditor') {
                $row[] = $record->batch_id;
                $row[] = '<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                        <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';
                
                $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $record->supplier_code]);
                $row[] = '('.$record->supplier_code.') '.$name;
                $row[] = $record->currency;
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