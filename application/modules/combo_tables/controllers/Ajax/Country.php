<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Country extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ct_country';
        $this->logged_id = $this->session->user_id;
        $this->view_path = 'combo_tables/country';
    }

    public function save()
    {
        $post = $this->input->post();
        if ($post) {
            $id = $this->custom->insertRow($this->table, $post);
            if ($id != 'error') {
                set_flash_message('message', 'success', 'RECORD SAVED');
            } else {
                set_flash_message('message', 'danger', 'RECORD SAVE ERROR');
            }
            redirect('combo_tables/country');
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
            $where = ['country_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UDPATE ERROR');
            }
            redirect('combo_tables/country');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $wherecode = ['country_id' => $id];
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkCustomerResult = $this->custom->checkTableValues('master_customer', $wherecode);
                if ($checkCustomerResult) {
                    echo 'errors';
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        if ($flag == 0) {
            $result = $this->custom->deleteRow($this->table, $wherecode);
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
        $row = $this->custom->getSingleRow($this->table, ['country_id' => $id]);
        $html = '';
        if ($row) {
            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>COUNTRY</h3></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table align="center" style="width: 500px">';
            $html .= '<tr>';
            $html .= '<td style="width: 130px"><strong>Code</strong></td>';
            $html .= '<td>'.$row->country_code.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Name</strong></td>';
            $html .= '<td>'.$row->country_name.'</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;
            
            $file = 'country_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/combo_tables/country', 'refresh');
        }
    }
}
