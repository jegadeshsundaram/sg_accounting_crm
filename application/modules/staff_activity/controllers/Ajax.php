<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ajax extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function tasks() {
		$join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

		$table = 'staff_activity';
		$columns = ['sa_id', 'employee_id', 'activity_date', 'task_description', 'start_time', 'end_time', 'minutes', 'remarks', 'supervisor_comments'];
		$order_by = 'activity_date';
		$order = 'DESC';

		$list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        $data = [];

        $no = $this->input->post('start');

		foreach ($list as $record) {
            ++$no;
            $row = [];
			$row[] = $record->sa_id;
			$row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
					<a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>';
			
			$row[] = date('M j, Y', strtotime($record->activity_date));
			
			$employee = $this->custom->getMultiValues('master_employee', 'name, code', ['e_id' => $record->employee_id]);
			$row[] = $employee->name.' ('.$employee->code.')';
			$row[] = $record->task_description;
			$row[] = $record->start_time.' to '.$record->end_time;
			$row[] = $record->minutes;
			
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

	public function save_task()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $sa_id = $post['sa_id'];
			$batch_data['employee_id'] = $post['employee_id'];
            $batch_data['activity_date'] = date('Y-m-d', strtotime($post['activity_date']));            
            $batch_data['task_description'] = $post['task_description'];
            $batch_data['start_time'] = $post['start_time'];
            $batch_data['end_time'] = $post['end_time'];
            $batch_data['minutes'] = $post['minutes'];
            $batch_data['remarks'] = $post['remarks'];
			$batch_data['supervisor_comments'] = $post['supervisor_comments'];

            if ($sa_id == '') {
                $res = $this->custom->insertData('staff_activity', $batch_data);                

            } else {
                $res = $this->custom->updateRow('staff_activity', $batch_data, ['sa_id' => $sa_id]);
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

	function get_task() {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $task = $this->custom->getSingleRow('staff_activity', ['sa_id' => $post['sa_id']]);
            $data['task'] = $task;
            $data['activity_date'] = date('d-m-Y', strtotime($task->activity_date));
            echo json_encode($data);
        }
    }

	public function delete_task()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $deleted = $this->custom->deleteRow('staff_activity', ['sa_id' => $post['sa_id']]);
            echo $deleted;
        } else {
            echo 'post error';
        }
    }

}
?>
