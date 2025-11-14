<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $data;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->logged_id = $this->session->user_id;
        $this->load->model('stock/stock_model', 'stock_model');
    }

    public function double_ob()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('stock_open', ['document_reference' => $post['ref_no']]);
        echo $ref;
    }

    // Datapatch opening balance in Stock.TBL
    public function double_stock_ob()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('stock', ['ref_no' => $post['ref_no'], 'stock_type' => 'OPBAL']);
        echo $ref;
    }

    public function double_purchase()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('stock_purchase', ['document_reference' => $post['ref_no'], 'supplier_id' => $post['supplier_id']]);
        echo $ref;
    }

    public function double_adj()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('stock_adjustment', ['document_reference' => $post['ref_no']]);       
        echo $ref;
    }    

    // this will populate opening balance transactions from stock_open.TBL for do changes if any before post to STOCK.TBL
    public function populate_ob()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $ob_status = 'C';
        $data = [];
        $no = $this->input->post('start');

        $table = 'stock_open';
        $columns = ['stock_ob_id', 'document_date', 'document_reference'];
        $where = ['status' => $ob_status];
        $group_by = 'document_reference';
        $order_by = 'document_date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->stock_ob_id;

            $row[] = date('d-m-Y', strtotime($record->document_date));
            $row[] = $record->document_reference;

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

    public function save_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob_id = $post['ob_id'];            
            $data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $data['document_reference'] = $post['ref_no'];
            $data['product_id'] = $post['product_id'];
            $data['quantity'] = $post['quantity'];
            $data['unit_cost'] = $post['unit_cost'];
            $data['remarks'] = $post['remarks'];

            if ($ob_id == '') {
                $ob_id = $this->custom->insertRow('stock_open', $data);
            } else {
                $updated = $this->custom->updateRow('stock_open', $data, ['stock_ob_id' => $ob_id]);
            }

            echo $ob_id;
        } else {
            echo 'post error';
        }
    }

    public function save_purchase_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $purchase_id = $post['purchase_id'];
            $data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $data['document_reference'] = $post['ref_no'];
            $data['supplier_id'] = $post['supplier_id'];
            $data['product_id'] = $post['product_id'];
            $data['quantity'] = $post['quantity'];
            $data['unit_cost'] = $post['unit_cost'];
            $data['remarks'] = $post['remarks'];

            if ($purchase_id == '') {
                $purchase_id = $this->custom->insertRow('stock_purchase', $data);
            } else {
                $updated = $this->custom->updateRow('stock_purchase', $data, ['purchase_id' => $purchase_id]);
            }

            echo $purchase_id;
        } else {
            echo 'post error';
        }
    }

    public function save_adj_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $adj_id = $post['adj_id'];
            $data['product_id'] = $post['product_id'];
            $data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $data['document_reference'] = $post['ref_no'];
            $data['quantity'] = $post['quantity'];
            $data['sign'] = $post['sign'];
            $data['remarks'] = $post['remarks'];

            if ($adj_id == '') {
                $adj_id = $this->custom->insertRow('stock_adjustment', $data);
            } else {
                $updated = $this->custom->updateRow('stock_adjustment', $data, ['adj_id' => $adj_id]);
            }

            echo $adj_id;
        } else {
            echo 'post error';
        }
    }

    public function delete_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('stock_open', ['stock_ob_id' => $post['ob_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    public function delete_purchase_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('stock_purchase', ['purchase_id' => $post['purchase_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    public function delete_adj_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('stock_adjustment', ['adj_id' => $post['adj_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    // delete in datapatch from stock.tbl
    public function delete_stock_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('stock', ['stock_id' => $post['ob_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    public function delete_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');
        $ob_data = $this->custom->getMultiValues('stock_open', 'document_date, document_reference', ['stock_ob_id' => $ob_id]);
        $where = ['document_date' => $ob_data->document_date, 'document_reference' => $ob_data->document_reference];
        $result = $this->custom->updateRow('stock_open', ['status' => 'D'], $where);
        echo $result;
    }

    public function post_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');
        $ob_data = $this->custom->getMultiValues('stock_open', 'document_date, document_reference', ['stock_ob_id' => $ob_id]);

        $this->db->select('*');
        $this->db->from('stock_open');
        $this->db->where('document_date = "'.$ob_data->document_date.'" AND document_reference = "'.$ob_data->document_reference.'" AND status = "C"');
        $this->db->order_by('stock_ob_id', 'ASC');
        $query = $this->db->get();
        $record_list = $query->result();
        foreach ($record_list as $record) {
            $ob_id = $record->stock_ob_id;
            $product_id = $record->product_id;

            // get balance stock in hand for specific product_id for WAC Computation - start
            $i = 0;
            $product_balance_in_stock = 0;
            $sql = 'SELECT sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity FROM stock WHERE product_id = "'.$product_id.'" ';
            $query = $this->db->query($sql);
            $stock_status = $query->result();
            foreach ($stock_status as $key => $value) {
                $product_balance_in_stock = $value->balance_quantity;
            }
            // get balance stock in hand for specific product_id for WAC Computation - end

            $quantity = $record->quantity;
            $unit_cost = $record->unit_cost;
            $document_date = $record->document_date;

            $data['product_id'] = $product_id;
            $data['quantity'] = $quantity;
            $data['unit_cost'] = $unit_cost;
            $data['created_on'] = $document_date;
            $data['ref_no'] = $record->document_reference;
            $data['stock_type'] = 'OPBAL';
            $data['iden'] = 'OPBAL';
            $data['remark'] = $record->remarks;
            $stock_inserted_id = $this->custom->insertRow('stock', $data);

            // WAC - Stock Matrix Changes - Start
            $stock_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $product_id]);

            // $stock_cost_wac = total wac value from stock_cost.tbl
            $stock_cost_wac = 0;

            $i = 0;
            $this->db->select('*');
            $this->db->from('stock_cost');
            $this->db->where(['stock_code' => $stock_code]);
            $this->db->order_by('created_on', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            $wac_data = $query->result();
            foreach ($wac_data as $key => $value) {
                $stock_cost_wac += $value->wac;
                ++$i;
            }

            if ($i > 0) {
                $last_cumulative_cost = $product_balance_in_stock * $stock_cost_wac;
                $cumulative_cost = $last_cumulative_cost + ($quantity * $unit_cost);

                $cumulative_quantity = $product_balance_in_stock + $quantity;
                $wac_cost = $cumulative_cost / $cumulative_quantity;
            } else {
                $cumulative_quantity = $quantity;
                $cumulative_cost = $quantity * $unit_cost;
                $wac_cost = $cumulative_cost / $cumulative_quantity;
            }

            $insert_data['created_on'] = $document_date;
            $insert_data['stock_code'] = $stock_code;
            $insert_data['wac'] = number_format($wac_cost, 2, '.', '');
            $insert_data['quantity'] = $quantity;
            $insert_data['unit_cost'] = $unit_cost;
            $insert_data['stock_id'] = $stock_inserted_id;
            $ct_true = $this->db->insert('stock_cost', $insert_data);
            // WAC - Stock Matrix Changes - End

            // Update each record as "POSTED"
            $where = ['stock_ob_id' => $ob_id];
            $result = $this->custom->updateRow('stock_open', ['status' => 'P'], $where);
        }
    }

    public function post_purchase()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        // get specific product data from stock purchase master TBL
        $purchase_data = $this->custom->getMultiValues('stock_purchase', 'document_date, document_reference, supplier_id', ['purchase_id' => $id]);

        $this->db->select('*');
        $this->db->from('stock_purchase');
        $this->db->where('document_date = "'.$purchase_data->document_date.'" AND document_reference = "'.$purchase_data->document_reference.'" AND supplier_id = '.$purchase_data->supplier_id.' AND status = "C"');
        $this->db->order_by('purchase_id', 'ASC');
        $query = $this->db->get();
        $record_list = $query->result();
        foreach ($record_list as $record) {
            $purchase_id = $record->purchase_id;
            $supplier_id = $record->supplier_id;
            $product_id = $record->product_id;
            $document_date = $record->document_date;
            $document_reference = $record->document_reference;

            // STEP 1: get balance stock in hand for specific product for WAC Computation - start
            $sql = 'SELECT sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity FROM stock WHERE product_id = '.$product_id;
            $query = $this->db->query($sql);
            $stock_status = $query->result();
            $product_balance_in_stock = 0;
            foreach ($stock_status as $key => $value) {
                $product_balance_in_stock = $value->balance_quantity;
            }

            // STEP 2: inserting into stock table - start
            $quantity = $record->quantity;
            $unit_cost = $record->unit_cost;

            $data['product_id'] = $product_id;
            $data['quantity'] = $quantity;
            $data['unit_cost'] = $unit_cost;
            $data['created_on'] = date('Y-m-d', strtotime($document_date));
            $data['ref_no'] = $document_reference;
            $data['stock_type'] = 'Purchase';
            $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $supplier_id]);
            $data['iden'] = $supplier_code;

            $stock_inserted_id = $this->custom->insertRow('stock', $data);

            // STEP 3: WAC - Stock Matrix Changes - Start
            $stock_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $product_id]);

            // $stock_cost_wac = total wac value from stock_cost.tbl
            $stock_cost_wac = 0;

            $i = 0;
            $this->db->select('*');
            $this->db->from('stock_cost');
            $this->db->where(['stock_code' => $stock_code]);
            $this->db->order_by('created_on', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            $wac_data = $query->result();
            foreach ($wac_data as $key => $value) {
                $stock_cost_wac += $value->wac;
                ++$i;
            }

            if ($i > 0) {
                $last_cumulative_cost = $product_balance_in_stock * $stock_cost_wac;
                $cumulative_cost = $last_cumulative_cost + ($quantity * $unit_cost);

                $cumulative_quantity = $product_balance_in_stock + $quantity;
                $wac_cost = $cumulative_cost / $cumulative_quantity;
            } else {
                $cumulative_quantity = $quantity;
                $cumulative_cost = $quantity * $unit_cost;
                $wac_cost = $cumulative_cost / $cumulative_quantity;
            }

            $insert_data['created_on'] = date('Y-m-d', strtotime($document_date));
            $insert_data['stock_code'] = $stock_code;
            $insert_data['wac'] = number_format($wac_cost, 2, '.', '');
            $insert_data['quantity'] = $quantity;
            $insert_data['unit_cost'] = $unit_cost;
            $insert_data['stock_id'] = $stock_inserted_id;
            $ct_inserted = $this->db->insert('stock_cost', $insert_data);

            // STEP 4: updating status to POSTED in stock_purchase.TBL
            $where = ['purchase_id' => $purchase_id];
            $result = $this->custom->updateRow('stock_purchase', ['status' => 'P'], $where);
            echo $result;
        }
    }

    public function post_adjustment()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        $adj_data = $this->custom->getSingleRow('stock_adjustment', ['adj_id' => $id]);

        $this->db->select('*');
        $this->db->from('stock_adjustment');
        $this->db->where('document_date = "'.$adj_data->document_date.'" AND document_reference = "'.$adj_data->document_reference.'" AND status = "C"');
        $this->db->order_by('adj_id', 'ASC');
        $query = $this->db->get();
        $record_list = $query->result();
        foreach ($record_list as $record) {
            // STEP 1: inserting into stock table - start
            $data['product_id'] = $record->product_id;
            $data['quantity'] = $record->quantity;
            $data['created_on'] = date('Y-m-d', strtotime($record->document_date));
            $data['ref_no'] = $record->document_reference;
            $data['stock_type'] = 'Adjustment';
            $data['iden'] = 'ADJ';
            $data['sign'] = $record->sign;
            $data['remark'] = $record->remarks;
            $open_id[] = $this->custom->insertRow('stock', $data);

            // STEP 2: updating status to POSTED in stock adjustment master TBL
            $where = ['adj_id' => $record->adj_id];
            $result = $this->custom->updateRow('stock_adjustment', ['status' => 'P'], $where);
            echo $result;
        }
    }

    // this will populate purchase transactions from stock_purchase.TBL for do changes if any before post to STOCK.TBL
    public function populate_purchase()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $status = 'C';
        $data = [];
        $no = $this->input->post('start');

        $table = 'stock_purchase';
        $columns = ['purchase_id', 'document_date', 'document_reference', 'supplier_id'];
        $where = ['status' => $status];
        $group_by = 'document_date, document_reference';
        $order_by = 'document_date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->purchase_id;

            $row[] = date('d-m-Y', strtotime($record->document_date));
            $row[] = $record->document_reference;

            $supplier_data = $this->custom->getMultiValues('master_supplier', 'code, name', ['supplier_id' => $record->supplier_id]);
            $row[] = '('.$supplier_data->code.') '.$supplier_data->name;

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

    public function delete_purchase()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $purchase_data = $this->custom->getSingleRow('stock_purchase', ['purchase_id' => $id]);
        if ($purchase_data->supplier_id != '') {
            $status = $this->custom->updateRow('stock_purchase', ['status' => 'D'], ['supplier_id' => $purchase_data->supplier_id, 'document_reference' => $purchase_data->document_reference, 'document_date' => $purchase_data->document_date]);
        }
        echo $status;
    }

    // this will populate opening balance transactions from stock_open.TBL for do changes if any before post to STOCK.TBL
    public function populate_adjustment()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;

        $status = 'C';
        $data = [];
        $no = $this->input->post('start');

        $table = 'stock_adjustment';
        $columns = ['adj_id', 'document_date', 'document_reference'];
        $where = ['status' => $status];
        $group_by = 'document_reference';
        $order_by = 'document_date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->adj_id;

            $row[] = date('d-m-Y', strtotime($record->document_date));
            $row[] = $record->document_reference;

            $this->db->select('*');
            $this->db->from('stock_adjustment');
            $this->db->where(['document_date' => $record->document_date, 'document_reference' => $record->document_reference, 'status' => $status]);
            $query = $this->db->get();
            // print_r($this->db->last_query());
            $list = $query->result();
            $html = '';
            $html .= '<div class="row">';
            $i = 0;
            foreach ($list as $value) {
                if ($i == 0) {
                    $html .= '<div class="col-12">';
                    $html .= '<div style="float: left; width: 300px; padding: 5px; font-weight: bold">Product</div>';
                    $html .= '<div style="float: left; width: 100px; padding: 5px; font-weight: bold">UOM</div>';
                    $html .= '<div style="float: left; width: 100px; padding: 5px; font-weight: bold">Quantity</div>';
                    $html .= '<div style="float: left; width: 50px; padding: 5px; font-weight: bold; text-align: center">Sign</div>';
                    $html .= '<div style="float: left; width: 300px; padding: 5px; font-weight: bold">Remarks</div>';
                    $html .= '</div>';
                }
                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $value->product_id]);
                $html .= '<div class="col-12">';
                $html .= '<div style="float: left; width: 300px; padding: 5px;">('.$product_data->stock_code.') '.substr($product_data->billing_description, 0, 60).'</div>';
                $html .= '<div style="float: left; width: 100px; padding: 5px;">'.$product_data->billing_uom.'</div>';
                $html .= '<div style="float: left; width: 100px; padding: 5px;">'.$value->quantity.'</div>';
                $html .= '<div style="float: left; width: 50px; padding: 5px;  text-align: center">'.$value->sign.'</div>';
                $html .= '<div style="float: left; width: 300px; padding: 5px;">'.$value->remarks.'</div>';
                $html .= '</div>';

                ++$i;
            }
            $html .= '</div>';

            $row[] = $html;

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

    public function delete_adjustment()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $data = $this->custom->getMultiValues('stock_adjustment', 'document_date, document_reference', ['adj_id' => $id]);
        $where = ['document_date' => $data->document_date, 'document_reference' => $data->document_reference];
        $result = $this->custom->updateRow('stock_adjustment', ['status' => 'D'], $where);
        echo $result;
    }    

    // this will populate opening balance transactions from STOCK.TBL for do datapatch
    public function populate_stock_ob()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;

        $data = [];
        $no = $this->input->post('start');

        $table = 'stock';
        $columns = ['stock_id', 'created_on', 'ref_no'];
        $where = ['stock_type' => 'OPBAL'];
        $group_by = 'ref_no';
        $order_by = 'created_on';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->stock_id;

            $row[] = date('d-m-Y', strtotime($record->created_on));
            $row[] = $record->ref_no;

            $html = '';
            $html .= '<div class="row">';
            $i = 0;

            $this->db->select('*');
            $this->db->from('stock');
            $this->db->where(['created_on' => $record->created_on, 'ref_no' => $record->ref_no, 'stock_type' => 'OPBAL']);
            $query = $this->db->get();
            $ob_data = $query->result();
            foreach ($ob_data as $value) {
                if ($i == 0) {
                    $html .= '<div class="col-12">';
                    $html .= '<div style="float: left; width: 400px; padding: 5px; font-weight: bold">Product</div>';
                    $html .= '<div style="float: left; width: 100px; padding: 5px; font-weight: bold">UOM</div>';
                    $html .= '<div style="float: left; width: 100px; padding: 5px; font-weight: bold">Quantity</div>';
                    $html .= '<div style="float: left; width: 200px; padding: 5px; font-weight: bold; text-align: right">Unit Cost</div>';
                    $html .= '<div style="float: left; width: 20px; padding: 5px;"></div>';
                    $html .= '<div style="float: left; width: 250px; padding: 5px; font-weight: bold">Remarks</div>';
                    $html .= '</div>';
                }
                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $value->product_id]);
                $html .= '<div class="col-12">';
                $html .= '<div style="float: left; width: 400px; padding: 5px;">('.$product_data->stock_code.') '.$product_data->billing_description.'</div>';
                $html .= '<div style="float: left; width: 100px; padding: 5px;">'.$product_data->billing_uom.'</div>';
                $html .= '<div style="float: left; width: 100px; padding: 5px;">'.$value->quantity.'</div>';
                $html .= '<div style="float: left; width: 200px; padding: 5px;  text-align: right">'.$value->unit_cost.'</div>';
                $html .= '<div style="float: left; width: 20px; padding: 5px;"></div>';
                $html .= '<div style="float: left; width: 250px; padding: 5px;">'.$value->remark.'</div>';
                $html .= '</div>';

                ++$i;
            }
            $html .= '</div>';

            $row[] = $html;

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

    public function get_product_data()
    {
        is_ajax();
        $post = $this->input->post();

        $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $post['product_id']]);

        echo json_encode($product_data);
    }

    public function get_product_uom() {
        is_ajax();
        $post = $this->input->post();

        $uom = $this->custom->getSingleValue('master_billing', 'billing_uom', ['billing_id' => $post['product_id']]);

        echo $uom;
    }
}
