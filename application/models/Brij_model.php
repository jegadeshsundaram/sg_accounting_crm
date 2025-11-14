<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Brij_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url_helper');
        $this->load->helper(['form', 'url']);
    }

}
