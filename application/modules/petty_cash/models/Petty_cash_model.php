<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Petty_cash_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_latest_reference($text_prefix)
    {
        $query = $this->db->query("SELECT ref_no FROM `petty_cash_batch` WHERE `ref_no` LIKE '$text_prefix%' ORDER BY ref_no DESC LIMIT 1");
        // print_r($this->db->last_query());
        $tbls = $query->result();

        return $tbls[0]->ref_no;
    }

    public function get_tbl_data($table, $columns = null, $where, $group_by = null, $order_by = null, $order_by_many = null)
    {
        $this->db->select($columns)->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($group_by)) {
            $this->db->group_by($group_by);
        }

        if (!is_null($order_by)) {
            $this->db->order_by($order_by, 'ASC');
        }

        if (!is_null($order_by_many)) {
            $this->db->order_by($order_by_many);
        }

        return $this->db->get();
    }
}
