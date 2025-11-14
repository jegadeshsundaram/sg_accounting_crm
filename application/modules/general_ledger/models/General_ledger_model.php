<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class General_ledger_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_COA_details($where)
    {
        return $result = $this->custom->getSingleRow('chart_of_account', $where);
    }

    public function get_acc_des($where)
    {
        return $result = $this->custom->getSingleRow('chart_of_account', $where);
    }

    public function get_product_details($where)
    {
        return $result = $this->custom->getSingleRow('master_billing', $where);
    }

    public function get_product_details_row($bid)
    {
        return $result = $this->custom->getSingleRow('master_billing', ['billing_id' => $bid]);
    }
}
