<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Employee extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'master_employee';
        $this->logged_id = $this->session->user_id;
    }

    public function save()
    {
        $post = $post = $this->input->post();
        if ($post) {
            $id = $this->custom->insertRow($this->table, $post);
            if ($id != 'error') {
                set_flash_message('message', 'success', 'RECORD SAVED');
            } else {
                set_flash_message('message', 'danger', 'SAVE ERROR');
            }
            redirect('master_files/employee');
        } else {
            show_404();
        }
    }

    public function update()
    {
        $post = $this->input->post();
        if ($post) {
            $id = $post['id'];
            unset($post['id']);
            $where = ['e_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'UPDATE ERROR');
            }
            redirect('master_files/employee');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['employee_id' => $id]; // In quotation master and invoice master, is used employee_id
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkQuatationResult = $this->custom->checkTableValues('quotation_master', $where);
                if ($checkQuatationResult) {
                    echo 'errors';
                    $flag = 1;
                }
            } elseif ($i == 1) {
                ++$i;
                $checkInvoiceResult = $this->custom->checkTableValues('invoice_master', $where);
                if ($checkInvoiceResult) {
                    echo 'errors';
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);

        if ($flag == 0) {
            $where = ['e_id' => $id]; // In master_employee, is used e_id
            $result = $this->custom->deleteRow($this->table, $where);
            echo $result;
        }
    }

    public function print()
    {
        if (isset($_GET['rowID'])) {
            $id = $_GET['rowID'];
        } else {
            $id = $this->input->post('rowID');
        }
        $row = $this->custom->getSingleRow($this->table, ['e_id' => $id]);
        $department = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $row->department_id]);
        $html = '';
        if ($row) {
            $html .= $this->custom->populateMPDFStyle();

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>EMPLOYEE INFORMATION</h3></td>';
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
            $html .= '<td><strong>Department</strong></td>';
            $html .= '<td>'.$department.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Note</strong></td>';
            $html .= '<td>'.$row->note.'</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $file = 'emp_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);
        } else {
            redirect('/master_files/employee', 'refresh');
        }
    }
}
