<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->logged_id = $this->session->user_id;
    }

    public function double_currency()
    {
        is_ajax();
        $post = $this->input->post();
        $currency = $this->custom->getCount('ct_currency', ['code' => $post['code']]);
        echo $currency;
    }

    public function double_gst()
    {
        is_ajax();
        $post = $this->input->post();
        $gst = $this->custom->getCount('ct_gst', ['gst_code' => $post['gst_code']]);
        echo $gst;
    }

    public function double_country()
    {
        is_ajax();
        $post = $this->input->post();
        $country = $this->custom->getCount('ct_country', ['country_code' => $post['country_code']]);
        echo $country;
    }
}
