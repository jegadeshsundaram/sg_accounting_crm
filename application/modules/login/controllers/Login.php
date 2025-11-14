<?php

    if (!defined('BASEPATH')) {
        exit('No direct script access allowed');
    }

    class Login extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function index()
        {
            $this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
            $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
            $this->output->set_header('Pragma: no-cache');

            if ($this->ion_auth->logged_in()) {
                if ($this->ion_auth->is_admin()) {
                    redirect('dashboard');
                } elseif ($this->session->level) {
                    redirect('dashboard');
                } else {
                    $this->load->view('index');
                }
            } else {
                $this->load->view('index');
            }
        }

        public function login_check()
        {
            $data = $this->input->post();
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->config->set_item('identity', 'email');
            if ($this->ion_auth->login($data['username'], $data['password'])) {
                $data = $this->ion_auth->user()->result()[0];
                $this->session->set_userdata('level', $data->level);
                $this->session->set_userdata('group_id', $data->group_id);
                $this->session->set_userdata('conf_id', $data->conf_id);
                if ($this->ion_auth->is_admin()) {
                    echo 'Admin';
                } else {
                    echo $data->level;
                }
            } else {
                echo 'Fail';
            }
        }

        public function signout()
        {
            $this->session->unset_userdata('level');
            $this->session->unset_userdata('group_id');
            $this->session->unset_userdata('conf_id');

            $unset_session = ['fields_that_session_contains' => ''];
            $unset_session = [
            'identity' => '',
            'email' => '',
            'user_id' => '',
            'old_last_login' => '',
            'last_check' => '',
            ];
            $this->session->unset_userdata($unset_session);

            $logged_out = $this->ion_auth->logout();

            $this->load->driver('cache');
            $this->session->sess_destroy();
            $this->cache->clean();
            redirect('/login');
        }

        public function forgot_password()
        {
            $email = $this->input->post('username');
            if ($email != '') {
                $identity = $this->ion_auth->get_user_by_email($email)->result()[0]->username;
                if (!$this->ion_auth_model->email_check($email)) {
                    echo 'Email Address Not Registered';
                    set_flash_message('message', 'danger', 'Email Address Not Registered');
                } else {
                    $forgotten = $this->ion_auth->forgotten_password($identity);
                    if ($forgotten) { //if there were no errors
                        echo $this->ion_auth->messages();
                        $this->session->set_flashdata('message', '');
                    } else {
                        echo $this->ion_auth->messages().' '.$this->ion_auth->errors();
                        set_flash_message('message', 'info', $this->ion_auth->messages());
                    }
                }
            } else {
                echo 'Email is required';
                set_flash_message('message', 'danger', 'Email is required');
            }
        }
    }
