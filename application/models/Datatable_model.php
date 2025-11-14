<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datatable_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($table, $columns, $join_table, $join_condition, $where)
    {
        $this->db->select($columns)->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($join_table) && !is_null($join_condition)) {
            for ($i = 0; $i < count($join_table); ++$i) {
                $this->db->join($join_table[$i], $join_condition[$i]);
            }
        }
    }

    public function get_datatables($table, $columns, $join_table = null, $join_condition = null, $where = null, $group_by = null, $order_by = null, $order = null)
    {
        $this->_get_datatables_query($table, $columns, $join_table, $join_condition, $where);        
        
        if (!is_null($group_by)) {
            $this->db->group_by($group_by);
        }

        if (!is_null($order_by)) {
            $this->db->order_by($order_by, $order);
        } else {
            $this->db->order_by($columns[1], 'ASC');
        }

        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->result();
        
    }

    public function count_filtered($table, $columns, $join_table = null, $join_condition = null, $where = null)
    {
        $this->_get_datatables_query($table, $columns, $join_table, $join_condition, $where);
        
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($table)
    {        
        $this->db->from($table);
        return $this->db->count_all_results();
    }

}
