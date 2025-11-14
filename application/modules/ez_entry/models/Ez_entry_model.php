<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

  class Ez_entry_model extends CI_Model {

    public function __construct() {
      parent::__construct();
    }

    public function get_COA_details($where) {
      return $result = $this->custom->getSingleRow("chart_of_account", $where);
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

    public function get_supplier_credits($supplier_code)
    {
        // extract only Credit references
        $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount FROM accounts_payable WHERE supplier_code = "'.$supplier_code.'" AND sign = "-" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $query = $this->db->query($sql);
        // print_r($this->db->last_query());

        return $query->result();
    }

    public function get_supplier_debits($supplier_code)
    {
        // extract only Debit references
        $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount FROM accounts_payable WHERE supplier_code = "'.$supplier_code.'" AND sign = "+" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $query = $this->db->query($sql);
        // print_r($this->db->last_query());

        return $query->result();
    }

  }
?>
