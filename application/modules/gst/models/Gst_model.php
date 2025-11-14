<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

  class Gst_model extends CI_Model {

    public function __construct() {
      parent::__construct();
    }

    public function get_COA_details($where) {
      return $result = $this->custom->getSingleRow("chart_of_account", $where);
    }

  }
?>
