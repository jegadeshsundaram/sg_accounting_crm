<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Foreign_bank extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'master_foreign_bank';
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
                set_flash_message('message', 'danger', 'RECORD SAVE ERROR');
            }
            redirect('master_files/foreign_bank');
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

            $where = ['fb_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);

            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UPDATE ERROR');
            }
            redirect('master_files/foreign_bank');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $code = $this->custom->getSingleValue($this->table, 'fb_code', ['fb_id' => $id]);
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkFBResult = $this->custom->checkTableValues('foreign_bank', ['fb_code' => $code]);
                if ($checkFBResult) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);

        if ($flag == 0) {
            $result = $this->custom->deleteRow($this->table, ['fb_id' => $id]);
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
        $row = $this->custom->getSingleRow($this->table, ['fb_id' => $id]);
        $html = '';
        if ($row) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);

            $html .= $this->custom->populateMPDFStyle();

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>FOREIGN BANK INFORMATION</h3></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table align="center">';
            $html .= '<tr>';
            $html .= '<td style="width: 220px"><strong>Code</strong></td>';
            $html .= '<td>'.$row->fb_code.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Name</strong></td>';
            $html .= '<td>'.$row->fb_name.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Contact Person</strong></td>';
            $html .= '<td>'.$row->contact_person.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>BLDG NO</strong></td>';
            $html .= '<td>'.$row->bldg_number.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Street Name &amp; Unit No</strong></td>';
            $html .= '<td>'.$row->street_name.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Address Line 2</strong></td>';
            $html .= '<td>'.$row->address_line_2.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Postal code</strong></td>';
            $html .= '<td>'.$row->postal_code.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Fax</strong></td>';
            $html .= '<td>'.$row->fax.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Phone</strong></td>';
            $html .= '<td>'.$row->phone.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Email</strong></td>';
            $html .= '<td>'.$row->email.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Currency</strong></td>';
            $html .= '<td>'.$currency.'</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $file = 'fb_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);
        } else {
            redirect('/master_files/foreign_bank', 'refresh');
        }
    }
}
