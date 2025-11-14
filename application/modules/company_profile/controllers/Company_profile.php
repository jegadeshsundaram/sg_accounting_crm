<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    class Company_profile extends MY_Controller
    {
        public function index()
        {
            is_logged_in('admin');
            has_permission();

            $where = ['code' => 'CP'];
            $company = $this->custom->getCount('company_profile', $where);
            $company_details = '';
            if ($company > 0) {
                $company_details = $this->custom->getSingleRow('company_profile', $where);

                $this->body_vars['company_name'] = $company_details->company_name;
                $this->body_vars['company_logo'] = $company_details->company_logo;
                $this->body_vars['company_address'] = $company_details->company_address;
                $this->body_vars['gst_reg_no'] = $company_details->gst_reg_no;
                $this->body_vars['uen_no'] = $company_details->uen_no;
                $this->body_vars['phone'] = $company_details->phone;
                $this->body_vars['fax'] = $company_details->fax;
                $this->body_vars['company_email'] = $company_details->company_email;
                $this->body_vars['currency_options'] = $this->custom->createDropdownSelect('currency', ['iso', 'iso', 'name'], 'Currency', ['-', ' '], [], [$company_details->default_currency]);
            } else {
                $this->body_vars['company_name'] = '';
                $this->body_vars['company_logo'] = '';
                $this->body_vars['company_address'] = '';
                $this->body_vars['gst_reg_no'] = '';
                $this->body_vars['uen_no'] = '';
                $this->body_vars['phone'] = '';
                $this->body_vars['fax'] = '';
                $this->body_vars['company_email'] = '';
                $this->body_vars['currency_options'] = $this->custom->createDropdownSelect('currency', ['iso', 'iso', 'name'], 'Currency', ['-', ' '], [], []);
            }

            // gst standard rate
            $gst_rate_data = $this->custom->getLastInsertedRow('gst_std_rate', 'gsr_id');
            $gst_std_rate = '';
            if ($company_details->gst_reg_no != '') {
                $gst_std_rate = $gst_rate_data->rate;
            }
            $this->body_vars['gst_std_rate'] = $gst_std_rate;

            $readonly = 'readonly';
            $user = 'admin';
            if ($this->ion_auth->is_admin() && $this->session->group_id == 1) { // superuser
                $readonly = '';
                $user = 'superadmin';
            }
            $this->body_vars['readonly'] = $readonly;
            $this->body_vars['user'] = $user;

            $this->body_vars['save_url'] = '/company_profile/manage';

            $this->body_file = 'index.php';
        }

        public function manage()
        {
            $post = $this->input->post();

            // Logo - starts
            $config['upload_path'] = './assets/uploads/site/';
            $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
            $config['max_size'] = 1000;
            $config['max_width'] = 5000;
            $config['max_height'] = 3000;

            $this->load->library('upload', $config);

            $this->upload->do_upload('company_logo');
            // Logo - Ends

            // If GST Standard Rate is updated then System will insert a Entry into DB with New GST Rate
            $gst_std_rate = $post['gst_std_rate'];
            if ($gst_std_rate > 0) {
                $gst_rate_data = $this->custom->getLastInsertedRow('gst_std_rate', 'gsr_id');
                if ($gst_std_rate != $gst_rate_data->rate) {
                    $gsr_data['rate'] = $gst_std_rate;
                    $gsr_data['updated_on'] = date('Y-m-d');
                    $res = $this->custom->insertRow('gst_std_rate', $gsr_data);

                    // Update GST Report Table
                    // Update all GST Type's Rate Value except 0 Rated Values
                    $gst_data['gst_rate'] = $gst_std_rate;
                    $gst_where = ['gst_rate != ' => 0];
                    $gst_update = $this->custom->updateRow('ct_gst', $gst_data, $gst_where);
                }
            }
            unset($post['gst_std_rate']);

            $where = ['code' => 'CP'];
            $company = $this->custom->getCount('company_profile', $where);
            if ($company > 0) {
                $res = $this->custom->updateRow('company_profile', $post, $where);
            } else {
                $post['code'] = 'CP';
                $res = $this->custom->insertRow('company_profile', $post);
            }

            if ($res != 'error') {
                set_flash_message('message', 'success', 'Profile submitted');
            } else {
                set_flash_message('message', 'danger', 'Profile error');
            }

            redirect('company_profile', 'refresh');
        }
    }
