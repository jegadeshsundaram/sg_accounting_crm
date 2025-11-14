<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Foreign_bank_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_fbank_details($where)
    {
        return $result = $this->custom->getSingleRow('master_foreign_bank', $where);
    }
}
