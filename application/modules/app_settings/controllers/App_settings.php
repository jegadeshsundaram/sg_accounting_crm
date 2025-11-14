<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App_settings extends MY_Controller
{
    public function index()
    {
        is_logged_in('admin');
        $this->body_file = 'app_settings/options.php';
    }

    public function admin() {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['admins'] = $this->custom->createDropdownSelect('users', ['id', 'username'], 'Admin Idendity', [''], ['level' => 'admin']);
    }

    public function change_password()
    {
        is_logged_in('admin');
        
        $data = $this->input->post();

        if ($this->ion_auth->update_admin_credentials($data['identity'], $data['username'], $data['new_password'])) {
            set_flash_message('message', 'success', 'Password Updated');
        } else {
            set_flash_message('message', 'danger', 'Password Update Error');
        }
        redirect('dashboard/change_password');
        
    }

    public function configuration()
    {
        is_logged_in('admin');
        has_permission();
        $where = ['process' => 'modules'];
        $this->body_vars['modules_permission'] = json_decode($this->custom->getSingleValue('configuration_master', 'permissions', $where));
        $this->body_file = 'app_settings/configuration.php';
    }

    public function save_configuration()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $modules = $this->custom->getSingleValue('configuration_master', 'process', ['process' => 'modules']);
            if ($modules == null || $modules == '') {
                $permission_data['process'] = 'modules';
                $permission_data['permissions'] = $post['modules_permission'];
                $inserted = $this->custom->insertRow('configuration_master', $permission_data);
            } else {
                $this->custom->updateRow('configuration_master', ['permissions' => $post['modules_permission']], ['process' => 'modules']);
            }

            if ($this->db->trans_status() === false) {
                set_flash_message('message', 'danger', 'Configuration Save Error');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'Configuration Saved');
                $this->db->trans_commit();
            }
        } else {
            set_flash_message('message', 'danger', 'Error in request');
        }

        redirect('app_settings/configuration');
    }
}
