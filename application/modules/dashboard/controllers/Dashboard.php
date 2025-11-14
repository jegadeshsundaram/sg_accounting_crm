<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function index()
    {
        is_logged_in('admin');

        $where = ['process' => 'modules'];
        $this->body_vars['modules_permission'] = json_decode($this->custom->getSingleValue('configuration_master', 'permissions', $where));

        $this->body_file = 'dashboard.php';
    }

    public function buttonURLs($class)
    {
        $this->body_vars['new_url'] = "dashboard/Ajax/' . $class . '/add";
        $this->body_vars['save_url'] = "dashboard/Ajax/' . $class . '/save";
        $this->body_vars['edit_url'] = "dashboard/Ajax/' . $class . '/edit";
        $this->body_vars['update_url'] = "dashboard/Ajax/' . $class . '/update";
        $this->body_vars['view_url'] = "dashboard/Ajax/' . $class . '/view";
        $this->body_vars['delete_url'] = "dashboard/Ajax/' . $class . '/delete";
    }
}
