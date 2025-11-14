<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Master_files_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_datatables($table, $columns, $join_table = null, $join_condition = null, $where = null, $table_id = 'id')
    {
    }
}
