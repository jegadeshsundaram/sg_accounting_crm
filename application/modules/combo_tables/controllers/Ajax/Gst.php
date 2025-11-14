<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Gst extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ct_gst';
        $this->logged_id = $this->session->user_id;
        $this->view_path = 'combo_tables/gst';
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
            redirect('combo_tables/gst');
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
            $where = ['gst_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UDPATE ERROR');
            }
            redirect('combo_tables/gst');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        $where = ['gst_id' => $id];
        $gst_code = $this->custom->getSingleValue($this->table, 'gst_code', ['gst_id' => $id]);

        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkCustomerResult = $this->custom->checkTableValues('gst', ['gstcate' => $gst_code]);
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

        $row = $this->custom->getSingleRow($this->table, ['gst_id' => $id]);
        $html = '';
        if ($row) {
            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>GST</h3></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table align="center" style="width: 500px">';
            $html .= '<tr>';
            $html .= '<td style="width: 130px"><strong>Code</strong></td>';
            $html .= '<td>'.$row->gst_code.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Rate</strong></td>';
            $html .= '<td>'.$row->gst_rate.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Description</strong></td>';
            $html .= '<td>'.$row->gst_description.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Type</strong></td>';
            $html .= '<td>'.$row->gst_type.'</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'currency_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/combo_tables/gst', 'refresh');
        }
    }
}
