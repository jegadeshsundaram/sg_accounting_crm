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
    }

    public function populate_admins()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $data = [];
        $no = $this->input->post('start');

        $table = 'users';
        $columns = ['id', 'group_id', 'username', 'email', 'created_on', 'last_login', 'emp_name'];
        $where = ['level' => 'admin'];
        $order_by = 'emp_name';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->id;

            $row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
					<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a></a>';

            $row[] = $record->emp_name;
            $row[] = $record->username;
            $row[] = $record->email;
            $row[] = date('m-d-Y', $record->created_on);

            if($record->last_login !== null) {
                $row[] = date('m-d-Y', $record->last_login);
            } else {
                $row[] = '';
            }
            

            $data[] = $row;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->dt_model->count_all($table),
            'recordsFiltered' => $this->dt_model->count_filtered($table, $columns, $join_table, $join_condition, $where),
            'data' => $data,
        ];

        echo json_encode($output);
    }

    function get_user() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $user = $this->custom->getSingleRow('users', ['id' => $post['user_id']]);
            $data['user'] = $user;
            echo json_encode($data);
        }
    }
    
    public function double_user()
    {
        is_ajax();
        $post = $this->input->post();

        $user = $this->custom->getCount('users', ['username' => $post['username']]);

        echo $user;
    }

    public function delete_user()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('users', ['id' => $post['user_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

    public function save_user() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $ip_address = $this->input->ip_address();

            $user_data['level'] = 'admin';
            $user_data['active'] = 1;

            $user_data['emp_name'] = $post['emp_name'];
            $user_data['username'] = $post['username'];

            $user_data['email'] = $post['email'];
            $user_data['group_id'] = 3;
            $user_data['ip_address'] = $ip_address;
            $user_data['created_on'] = time();

            if ($post['user_id'] == '') {
                $salt = $this->ion_auth_model->store_salt ? $this->ion_auth_model->salt() : false;
                $password = $this->ion_auth_model->hash_password($post['password'], $salt);
                $user_data['password'] = $password;

                $res = $this->custom->insertData('users', $user_data);

            } else {
                $res = $this->custom->updateRow('users', $user_data, ['id' => $post['user_id']]);
            }

            if($res == 'updated' || $res) {
                echo $res;
            } else {
                echo 'error';
            }

        } else {
            echo 'post error';
        }
    }


    public function update_password() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $salt = $this->ion_auth_model->store_salt ? $this->ion_auth_model->salt() : false;
            $password = $this->ion_auth_model->hash_password($post['password'], $salt);

            $res = $this->custom->updateRow('users', ['password' => $password], ['id' => $post['user_id']]);

            echo $res;
        } else {
            echo 'post error';
        }
    }

    
}
