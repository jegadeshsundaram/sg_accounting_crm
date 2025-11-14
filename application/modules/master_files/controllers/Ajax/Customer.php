<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Customer extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'master_customer';
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

            if ($page == 'invoice') {
                redirect('/invoice');
            } elseif ($page == 'quotation') {
                redirect('/quotation');
            } else {
                redirect('/master_files/customer');
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

            $where = ['customer_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);

            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'RECORD UPDATE ERROR');
            }
            redirect('master_files/customer');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['customer_id' => $id];
        $code = $this->custom->getSingleValue($this->table, 'code', $where);
        $wherecode = ['customer_code' => $code];
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
            } elseif ($i == 2) {
                ++$i;
                $checkOpenResult = $this->custom->checkTableValues('ar_open', $where);
                if ($checkOpenResult) {
                    echo 'errors';
                    $flag = 1;
                }
            } elseif ($i == 3) {
                ++$i;
                $receipt_master = $this->custom->checkTableValues('receipt_master', $where);
                if ($receipt_master) {
                    echo 'errors';
                    $flag = 1;
                }
            } elseif ($i == 5) {
                ++$i;
                $checkAcountsReceivable = $this->custom->checkTableValues('acounts_receivable', $wherecode);
                if ($checkAcountsReceivable) {
                    echo 'errors';
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        if ($flag == 0) {
            $result = $this->custom->deleteRow($this->table, $where);
            $result1 = $this->custom->deleteRow('customer_price', $wherecode);
            echo $result.'---'.$result1;
        }
    }

    public function print()
    {
        if (isset($_GET['rowID'])) {
            $id = $_GET['rowID'];
        } else {
            $id = $this->input->post('rowID');
        }
        $row = $this->custom->getSingleRow($this->table, ['customer_id' => $id]);
        $html = '';
        if ($row) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            $country = $this->custom->getSingleValue('ct_country', 'country_name', ['country_id' => $row->country_id]);

            $html .= $this->custom->populateMPDFStyle();

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>CUSTOMER INFORMATION</h3></td>';
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
            $html .= '<td><strong>Credit Limit</strong></td>';
            $html .= '<td>'.$row->credit_limit.'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>Credit Term Days</strong></td>';
            $html .= '<td>'.$row->credit_term_days.'</td>';
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

            $file = 'customer_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);
        } else {
            redirect('/master_files/customer', 'refresh');
        }
    }
}
