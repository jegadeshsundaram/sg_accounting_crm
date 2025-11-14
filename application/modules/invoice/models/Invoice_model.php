<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
error_reporting(0);
class Invoice_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }    

    public function get_billing_items($table, $where = null)
    {
        $this->db->select('*, LEFT(billing_description, 40) as stock_description')->from($table);
        if (!is_null($where)) {
            $this->db->where($where);
        }
        $this->db->order_by('stock_code', 'ASC');

        return $this->db->get();
    }

    public function get_latest_reference($text_prefix)
    {
        $query = $this->db->query("SELECT invoice_ref_no FROM `invoice_master` WHERE `invoice_ref_no` LIKE '$text_prefix%' ORDER BY invoice_ref_no DESC LIMIT 1");
        // print_r($this->db->last_query());
        $tbls = $query->result();

        return $tbls[0]->invoice_ref_no;
    }

    public function get_tbl_data($table, $where, $order_by)
    {
        $this->db->select('*')->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($order_by)) {
            $this->db->order_by($order_by, 'ASC');
        }

        return $this->db->get();
    }

    public function get_invoices($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
        $this->db->select('*')->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($join_table) && !is_null($join_condition)) {
            for ($i = 0; $i < count($join_table); ++$i) {
                $this->db->join($join_table[$i], $join_condition[$i]);
            }
        }

        $this->db->order_by($columns[1], 'DESC');

        $query = $this->db->get();

        return $query->result();

        echo $this->db->last_query();
    }

    public function get_invoice_items($row_id)
    {
        $this->db->select('*');
        $this->db->order_by('i_p_id');
        $query = $this->db->get_where('invoice_product_master', ['invoice_id' => $row_id]);

        return $query->result();
    }

    public function get_quotation_items($row_id)
    {
        $this->db->select('*');
        $this->db->order_by('q_p_id');
        $query = $this->db->get_where('quotation_product_master', ['quotation_id' => $row_id]);

        return $query->result();
    }

    public function updateUnitPrice($billing_id, $billing_price_per_uom)
    {
        $this->db->where('billing_id', $billing_id);
        $this->db->update('master_billing', ['billing_price_per_uom' => $billing_price_per_uom]);
        echo 'updated';
    }

    private function _get_datatables_query($table, $column_order, $join_table, $join_condition, $where, $table_id)
    {
        // d($table_id);
        $column_search = $column_order;
        if (is_array($table) && array_key_exists('SQL', $table)) {
            $sql = $table['SQL'];
            // $this->db->query($sql);
        } else {
            if (!is_null($join_table) && !is_null($join_condition) && !is_null($where)) {
                $this->db->select(" *  , $table.$table_id AS table_id")->from($table)->where($where);
                if ($table == 'quotation_master') {
                    $this->db->order_by($column_search[0], 'DESC');
                } elseif ($table == 'invoice_master') {
                    $this->db->order_by($column_search[0], 'DESC');
                } else {
                    $this->db->order_by($column_search[1], 'ASC');
                }
                for ($i = 0; $i < count($join_table); ++$i) {
                    $this->db->join($join_table[$i], $join_condition[$i]);
                }
            } elseif (!is_null($join_table) && !is_null($join_condition)) {
                // d(count($join_table));
                $this->db->order_by($column_search[1], 'ASC');
                $this->db->select(" *  , $table.$table_id AS table_id")->from($table);
                for ($i = 0; $i < count($join_table); ++$i) {
                    $this->db->join($join_table[$i], $join_condition[$i]);
                }
            } elseif (!is_null($where)) {
                $this->db->select(" *  , $table.$table_id AS table_id")->from($table)->where($where);
                $this->db->order_by($column_search[1], 'ASC');
            } else {
                $this->db->select(" *  , $table.$table_id AS table_id")->from($table);

                $this->db->order_by($column_search[0], 'ASC'); // this line was added for ordering
            }
        }
        $i = 0;
        foreach ($column_search as $item) { // loop column
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($column_search) - 1 == $i) { // last loop
                    $this->db->group_end();
                } // close bracket
            }
            ++$i;
        }
    }

    public function count_all($table)
    {
        if (is_array($table) && array_key_exists('SQL', $table)) {
            $this->db->from($table['TABLE']);
        } else {
            $this->db->from($table);
        }

        return $this->db->count_all_results();
    }

    public function count_filtered($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
        $this->_get_datatables_query($table, $columns, $join_table, $join_condition, $where, $table_id);
        if (is_array($table) && array_key_exists('SQL', $table)) {
            $sql = $table['SQL'];
            $query = $this->db->query($sql);

            return $query->num_rows();
        } else {
            $query = $this->db->get();

            return $query->num_rows();
        }
    }

    public function get_datatables($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
        $this->_get_datatables_query($table, $columns, $join_table, $join_condition, $where, $table_id);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
            $limit = ' LIMIT '.$_POST['start'].','.$_POST['length'];
        }

        if (is_array($table) && array_key_exists('SQL', $table)) {
            $sql = $table['SQL'];
            $sql .= $limit;
            $query = $this->db->query($sql);

            return $query->result();
        } else {
            $query = $this->db->get();

            // echo $this->db->last_query();
            return $query->result();
        }
    }

    public function get_amount_utilized($customer_code)
    {
        $this->db->select('*');
        $this->db->from('accounts_receivable');
        $this->db->where(['customer_code' => $customer_code, 'offset !=' => 'o', 'offset !=' => 'y', 'settled !=' => 'y']);
        $query = $this->db->get();
        $list = $query->result();

        $current_amount = 0;
        $balance_amount = 0;

        foreach ($list as $key => $value) {
            $current_amount = $value->total_amt;

            if ($value->sign == '+') {
                $balance_amount += $current_amount;
            } elseif ($value->sign == '-') {
                $balance_amount -= $current_amount;
            }
        }

        if ($balance_amount < 0) {
            $balance_amount *= (-1);
        }

        return round($balance_amount, 2);
    }

    public function get_product_details($where)
    {
        return $result = $this->custom->getSingleRow('master_billing', $where);
    }

    public function get_product_details_row($bid)
    {
        return $result = $this->custom->getSingleRow('master_billing', ['billing_id' => $bid]);
    }

    public function getEmployee()
    {
        $query = $this->db->query('SELECT * FROM `master_employee`');
        $tbls = $query->result();
        foreach ($tbls as $tbl) {
            $arr[] = ['id' => $tbl->e_id, 'name' => $tbl->name];
        }

        // echo json_encode($arr);
        return $arr;
    }

    public function getEmployeeTbl($id, $col)
    {
        $query = $this->db->query("SELECT * FROM `master_employee` WHERE `e_id` = $id LIMIT 1 ");
        $tbls = $query->result();

        // echo json_encode($arr);
        return $tbls[0]->$col;
    }

    public function getUsers($id, $col)
    {
        $query = $this->db->query("SELECT $col FROM `master_customer` WHERE `customer_id` = $id LIMIT 1");
        $tbl = $query->result();
        if (count($tbl) == 0) {
            return $id;
        } else {
            return $tbl[0]->$col;
        }
    }
}
