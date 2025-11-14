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
        $this->table = 'sac_job';
        $this->logged_id = $this->session->user_id;
    }

    public function list() {
		$join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

		$table = 'sac_job';
		$columns = ['job_id', 'accountant_id', 'job_code', 'job_value', 'customer_id', 'fy_start_date', 'fy_end_date', 'accountant_remarks', 'manager_remarks'];
		$order_by = 'job_code';
		$order = 'DESC';

		$list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        $data = [];

        $no = $this->input->post('start');

		foreach ($list as $record) {
            ++$no;
            $row = [];
			$row[] = $record->job_id;
			$row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
					<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>';
				
			$accountant = $this->custom->getMultiValues('master_accountant', 'name, code', ['ac_id' => $record->accountant_id]);
			$row[] = $accountant->name.' ('.$accountant->code.')';
			$row[] = $record->job_code;
			$row[] = number_format($record->job_value, 2);
			$row[] = date('M j, Y', strtotime($record->fy_start_date)).' <i>to</i> '.date('M j, Y', strtotime($record->fy_end_date));

            $remarks = '';
            if($record->accountant_remarks !== "") {
                $remarks .= $record->accountant_remarks;
            } else if($record->manager_remarks !== "") {
                $remarks .= $record->manager_remarks;
            }
            $row[] = $remarks;
			
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

    public function duplicate()
    {
        is_ajax();
        $post = $this->input->post();

        $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $post['customer_id']]);
        $job_code = $customer_code.$post['financial_year'];

        $ref = $this->custom->getCount('sac_job', ['job_code' => $job_code]);
        echo $ref;
    }

    public function save()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $job_id = $post['job_id'];

            $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $post['customer_id']]);
            $job_code = $customer_code.$post['financial_year'];
			$data['job_code'] = $job_code;

            $data['customer_id'] = $post['customer_id'];
            $data['financial_year'] = $post['financial_year'];
            $data['fy_start_date'] = date('Y-m-d', strtotime($post['fy_start_date']));
            $data['fy_end_date'] = date('Y-m-d', strtotime($post['fy_end_date']));

            $data['job_confirmed_date'] = date('Y-m-d', strtotime($post['job_confirmed_date']));
            $data['promised_delivery_date'] = date('Y-m-d', strtotime($post['promised_delivery_date']));
            $data['job_value'] = $post['job_value'];
            $data['payment_collected'] = $post['payment_collected'];

            $data['accountant_id'] = $post['accountant_id'];
            $data['assignment_date'] = null;
            if($post['assignment_date'] !== "") {
                $data['assignment_date'] = date('Y-m-d', strtotime($post['assignment_date']));
            }
            $data['agreed_completion_date'] = null;
            if($post['agreed_completion_date'] !== "") {
                $data['agreed_completion_date'] = date('Y-m-d', strtotime($post['agreed_completion_date']));
            }
            $data['actual_completion_date'] = null;
            if($post['actual_completion_date'] !== "") {
                $data['actual_completion_date'] = date('Y-m-d', strtotime($post['actual_completion_date']));
            }           
            
            $data['sales_input_target_date'] = null;
            if($post['sales_input_target_date'] !== "") {
                $data['sales_input_target_date'] = date('Y-m-d', strtotime($post['sales_input_target_date']));
            }
            $data['receipt_input_target_date'] = null;
            if($post['receipt_input_target_date'] !== "") {
                $data['receipt_input_target_date'] = date('Y-m-d', strtotime($post['receipt_input_target_date']));
            }
            $data['purchase_input_target_date'] = null;
            if($post['purchase_input_target_date'] !== "") {
                $data['purchase_input_target_date'] = date('Y-m-d', strtotime($post['purchase_input_target_date']));
            }
            $data['payment_input_target_date'] = null;
            if($post['payment_input_target_date'] !== "") {
                $data['payment_input_target_date'] = date('Y-m-d', strtotime($post['payment_input_target_date']));
            }

            $data['draft_accounts_completion_date'] = null;
            if($post['draft_accounts_completion_date'] !== "") {
                $data['draft_accounts_completion_date'] = date('Y-m-d', strtotime($post['draft_accounts_completion_date']));
            }
            $data['final_accounts_completion_date'] = null;
            if($post['final_accounts_completion_date'] !== "") {
                $data['final_accounts_completion_date'] = date('Y-m-d', strtotime($post['final_accounts_completion_date']));
            }
            $data['bank_reckon_completion_date'] = null;
            if($post['bank_reckon_completion_date'] !== "") {
                $data['bank_reckon_completion_date'] = date('Y-m-d', strtotime($post['bank_reckon_completion_date']));
            }

            $data['tax_compilation'] = $post['tax_compilation'];
            $data['estimated_completion_date'] = null;
            if($post['estimated_completion_date'] !== "") {
                $data['estimated_completion_date'] = date('Y-m-d', strtotime($post['estimated_completion_date']));
            } 
            $data['tax_completion_date'] = null;
            if($post['tax_completion_date'] !== "") {
                $data['tax_completion_date'] = date('Y-m-d', strtotime($post['tax_completion_date']));
            }           
            $data['tax_compilation_fees'] = $post['tax_compilation_fees'];

            $data['job_closed'] = $post['job_closed'];
            $data['accountant_remarks'] = $post['accountant_remarks'];
            $data['manager_remarks'] = $post['manager_remarks'];

            if ($job_id == '') {
                $res = $this->custom->insertData('sac_job', $data);

            } else {
                $res = $this->custom->updateRow('sac_job', $data, ['job_id' => $job_id]);
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

    function details() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $job = $this->custom->getSingleRow('sac_job', ['job_id' => $post['job_id']]);
            $data['job'] = $job;
            echo json_encode($data);
        }
    }

    public function delete()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('sac_job', ['job_id' => $post['job_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }
}
