<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Supplier extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'master_supplier';
        $this->logged_id = $this->session->user_id;
    }

    public function save()
    {
        $post = $post = $this->input->post();
        if ($post) {
            $page = $post['page'];
            unset($post['page']);

            if ($post['currency_id'] == '') {
                $post['currency_id'] = $this->custom->getSingleValue('ct_currency', 'currency_id', ['code' => 'SGD']);
            }
            if ($post['country_id'] == '') {
                $post['country_id'] = $this->custom->getSingleValue('ct_country', 'country_id', ['country_name' => 'SINGAPORE']);
            }
            $id = $this->custom->insertRow($this->table, $post);
            if ($id != 'error') {
                set_flash_message('message', 'success', 'RECORD SAVED');
            } else {
                set_flash_message('message', 'danger', 'RECORD SAVE ERROR');
            }

            if ($page == 'stock_purchase') {
                redirect('/stock/enter_stock_purchases');
            } else {
                redirect('/master_files/supplier');
            }
        } else {
            show_404();
        }
    }

    public function update()
    {
        $post = $this->input->post();
        if ($post) {
            if ($post['currency_id'] == '') {
                $post['currency_id'] = $this->custom->getSingleValue('ct_currency', 'currency_id', ['code' => 'SGD']);
            }

            if ($post['country_id'] == '') {
                $post['country_id'] = $this->custom->getSingleValue('ct_country', 'country_id', ['country_name' => 'SINGAPORE']);
            }

            $id = $post['id'];
            unset($post['id']);

            $where = ['supplier_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);

            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UPDATE ERROR');
            }
            redirect('master_files/supplier');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        $where = ['supplier_id' => $id];
        $code = $this->custom->getSingleValue($this->table, 'code', $where);
        $wherecode = ['supplier_code' => $code];

        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkQuatationResult = $this->custom->checkTableValues('accounts_payable', $wherecode);
                if ($checkQuatationResult) {
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
        $row = $this->custom->getSingleRow($this->table, ['supplier_id' => $id]);
        $html = '';
        if ($row) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            $country = $this->custom->getSingleValue('ct_country', 'country_name', ['country_id' => $row->country_id]);

            $html .= $this->custom->populateMPDFStyle();

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>SUPPLIER INFORMATION</h3></td>';
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
            $html .= '<td><strong>Address</strong></td>';
            $html .= '<td>';
            if ($row->bldg_number !== '') {
                $html .= $row->bldg_number;
            }
            if ($row->street_name !== '') {
                $html .= '<br />'.$row->street_name;
            }
            if ($row->address_line_2 !== '') {
                $html .= '<br />'.$row->address_line_2;
            }
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Postal Code</strong></td>';
            $html .= '<td>'.$row->postal_code.'</td>';
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

            $html .= '<tr>';
            $html .= '<td><strong>UEN No</strong></td>';
            $html .= '<td>'.$row->uen_no.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>GST No</strong></td>';
            $html .= '<td>'.$row->gst_number.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Country</strong></td>';
            $html .= '<td>'.$country.'</td>';
            $html .= '</tr>';

            $html .= '</table>';

            $file = 'supplier_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);

        } else {
            redirect('/master_files/supplier', 'refresh');
        }
    }
}
