<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Currency extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ct_currency';
        $this->logged_id = $this->session->user_id;
        $this->view_path = 'combo_tables/currency';
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
            redirect('combo_tables/currency');
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
            $where = ['currency_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UDPATE ERROR');
            }
            redirect('combo_tables/currency');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['currency_id' => $id];
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkCustomerResult = $this->custom->checkTableValues('master_customer', $where);
                if ($checkCustomerResult) {
                    echo 'errors';
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        if ($flag == 0) {
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
        $row = $this->custom->getSingleRow($this->table, ['currency_id' => $id]);
        $html = '';
        if ($row) {
            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>CURRENCY</h3></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table align="center" style="width: 500px">';
            $html .= '<tr>';
            $html .= '<td style="width: 130px"><strong>Currency</strong></td>';
            $html .= '<td>'.$row->code.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Description</strong></td>';
            $html .= '<td>'.$row->description.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Rate</strong></td>';
            $html .= '<td>'.$row->rate.'</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'currency_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/combo_tables/currency', 'refresh');
        }
    }
}
