<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Receipt_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_latest_reference($text_prefix)
    {
        $query = $this->db->query("SELECT receipt_ref_no FROM `receipt_master` WHERE `receipt_ref_no` LIKE '$text_prefix%' ORDER BY receipt_ref_no DESC LIMIT 1");
        // print_r($this->db->last_query());
        $tbls = $query->result();

        return $tbls[0]->receipt_ref_no;
    }

    public function get_customer_credits($customer_code)
    {
        // extract only Credit references
        $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(f_amt) AS total_foreign_amount FROM accounts_receivable WHERE customer_code = "'.$customer_code.'" AND sign = "-" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $query = $this->db->query($sql);
        // print_r($this->db->last_query());

        return $query->result();
    }

    public function get_customer_debits($customer_code)
    {
        // extract only Debit references
        $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(f_amt) AS total_foreign_amount FROM accounts_receivable WHERE customer_code = "'.$customer_code.'" AND sign = "+" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $query = $this->db->query($sql);
        // print_r($this->db->last_query());

        return $query->result();
    }

    public function rec_inv_amount($ar_id)
    {
        $sql = 'SELECT rec_inv_amount FROM receipt_invoice_master, receipt_master WHERE receipt_invoice_master.receipt_id = receipt_master.receipt_id AND receipt_invoice_master.invoice_id = "'.$ar_id.'" AND receipt_master.receipt_status = "C" ORDER BY r_i_id DESC limit 1';
        $query = $this->db->query($sql);

        return $query->result();
    }

    public function get_rec_ar_entries($receipt_id)
    {
        $sql = 'SELECT * FROM receipt_invoice_master, accounts_receivable WHERE receipt_invoice_master.invoice_id = accounts_receivable.ar_id AND receipt_id = '.$receipt_id.' ORDER BY receipt_invoice_master.r_i_id ASC';
        $query = $this->db->query($sql);

        return $query->result();
    }

    public function get_product_details($where)
    {
        return $result = $this->custom->getSingleRow('master_billing', $where);
    }

    public function getSingleRow($table, $where = [])
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();

        // d($this->db->last_query());
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

            // echo $this->db->last_query();
            return $query->result();
        }
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
                // d(count($join_table));
                $this->db->order_by($column_search[1], 'ASC');
                $this->db->select("*, $table.$table_id AS table_id")->from($table);

                for ($i = 0; $i < count($join_table); ++$i) {
                    $this->db->join($join_table[$i], $join_condition[$i]);
                }
            } elseif (!is_null($where)) {
                $this->db->select("*, $table.$table_id AS table_id")->from($table)->where($where);
                $this->db->order_by($column_search[1], 'ASC');
            } else {
                $this->db->select("*, $table.$table_id AS table_id")->from($table);

                $this->db->order_by($column_search[0], 'ASC');
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
