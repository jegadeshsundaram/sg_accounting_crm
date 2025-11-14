<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stock_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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
