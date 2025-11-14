<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_latest_reference($text_prefix)
    {
        $query = $this->db->query("SELECT payment_ref_no FROM `payment_master` WHERE `payment_ref_no` LIKE '$text_prefix%' ORDER BY payment_ref_no DESC LIMIT 1");
        // print_r($this->db->last_query());
        $tbls = $query->result();

        return $tbls[0]->payment_ref_no;
    }

    public function get_product_details($where)
    {
        return $result = $this->custom->getSingleRow('master_billing', $where);
    }

    public function getSingleRow($table, $where = [])
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();

        return $query->row();
    }

    public function insertRow($table, $data)
    {
        $result = $this->db->insert($table, $data);
        if ($result) {
            return $this->db->insert_id();
        } else {
            return 'error';
        }
    }

    public function get_datatables($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
        $this->_get_datatables_query($table, $columns, $join_table, $join_condition, $where, $table_id);

        if (is_array($table) && array_key_exists('SQL', $table)) {
            $sql = $table['SQL'];
            $sql .= $limit;
            $query = $this->db->query($sql);

            return $query->result();
        } else {
            $query = $this->db->get();

            return $query->result();
        }
    }

    private function _get_datatables_query($table, $column_order, $join_table, $join_condition, $where, $table_id)
    {
        $column_search = $column_order;
        if (is_array($table) && array_key_exists('SQL', $table)) {
            $sql = $table['SQL'];
        } else {
            if (!is_null($join_table) && !is_null($join_condition) && !is_null($where)) {
                $this->db->select("*, $table.$table_id AS table_id")->from($table)->where($where);
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

    /* update existing row in a table */
    public function updateRow($table, $data, $where, $or_where = [])
    {
        $this->db->where($where);
        $this->db->or_where($or_where);
        $result = $this->db->update($table, $data);
        if ($result) {
            return 'updated';
        } else {
            return 'error';
        }
    }
}
