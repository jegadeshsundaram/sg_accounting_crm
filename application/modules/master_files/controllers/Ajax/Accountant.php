<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Accountant extends CI_Controller {

	public $table;
	public $logged_id;
	public function __construct() {
		parent::__construct();

		$this->table = "master_accountant";
		$this->logged_id = $this->session->user_id;
	}

	public function save() {
		$post = $post = $this->input->post();
		if($post) {
			$id = $this->custom->insertRow($this->table, $post);
			if($id != "error") {
				set_flash_message('message', 'success', "RECORD SAVED");
			} else {
				set_flash_message('message', 'danger', "RECORD SAVE ERROR");
			}
			redirect('master_files/accountant');
		} else {
			show_404();
		}
	}

	public function update() {
		$post = $this->input->post();
		if($post) {
			$id = $post['id'];
			unset($post['id']);
			$where = array('ac_id' => $id);
			$result = $this->custom->updateRow($this->table, $post, $where);
			if($result) {
				set_flash_message('message','success',"RECORD UPDATED");
			} else {
				set_flash_message('message','danger', "RECORD UPDATE ERROR");
			}
			redirect('master_files/accountant');

		} else {
			show_404();
		}
	}

	public function delete() {
		is_ajax();
		$id = $this->input->post('rowID');
		$ac_delete_data['active'] = 0;
		$accountant_deleted = $this->custom->updateRow($this->table, $ac_delete_data, array('ac_id' => $id));
		if($accountant_deleted) {
			set_flash_message('message', 'success', "RECORD DELETED");
		} else {
			set_flash_message('message', 'danger', "RECORD DELETE ERROR");
		}
		redirect('master_files/accountant');
	}

	public function print() {

		if(isset($_GET['rowID'])) {
			$id = $_GET['rowID'];
		} else {
			$id = $this->input->post('rowID');
		}
		$row = $this->custom->getSingleRow($this->table, array('ac_id' => $id));
		$html = "";
		if($row) {

			$html .= $this->custom->populateMPDFStyle();

			$html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
			$html .= '<tr>';
			$html .= '<td style="text-align: center; border: none;"><h3>ACCOUNTANT INFORMATION</h3></td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
			$html .= '</tr>';
			$html .= '</table><br /><br />';

			$html .= '<table align="center">';
			$html .= '<tr>';
			$html .= '<td style="width: 180px"><strong>Code</strong></td>';
			$html .= '<td>'.$row->code.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td><strong>Name</strong></td>';
			$html .= '<td>'.$row->name.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td><strong>Email</strong></td>';
			$html .= '<td>'.$row->email.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td><strong>Category</strong></td>';
			$html .= '<td>'.$row->category.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td><strong>Basic Salary</strong></td>';
			$html .= '<td>'.$row->basic_salary.'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<td><strong>Incentives (%)</strong></td>';
			$html .= '<td>'.$row->incentives.'</td>';
			$html .= '</tr>';
	    	$html .= '</table>';

			$file = 'accountant_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);

  		} else {
			redirect("/master_files/accountant", "refresh");
		}
	}

}
