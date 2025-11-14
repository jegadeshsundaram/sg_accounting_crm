<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Billing extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'master_billing';
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
            redirect('master_files/billing');
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
            $where = ['billing_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'RECORD UPDATED');
            } else {
                set_flash_message('message', 'danger', 'UPDATE ERROR');
            }
            redirect('master_files/billing');
        } else {
            show_404();
        }
    }

    public function reset_stock()
    {
        is_ajax();

        // Set all record update stock value to "no" where UOM = blank
        $this->db->set('billing_type', 'Service');
        $this->db->set('billing_update_stock', 'NO');
        $this->db->where('billing_uom IS NULL');
        $this->db->update('master_billing');

        $this->db->set('billing_type', 'Service');
        $this->db->set('billing_update_stock', 'NO');
        $this->db->where('billing_uom', '');
        $this->db->update('master_billing');

        return 'complete';
    }

    public function delete()
    {
        is_ajax();
        $billing_id = $this->input->post('rowID');

        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkInvoiceProductMaster = $this->custom->checkTableValues('invoice_product_master', ['billing_id' => $billing_id]);
                if ($checkInvoiceProductMaster) {
                    $flag = 1;
                }
            } elseif ($i == 1) {
                ++$i;
                $checkQuotationProductMaster = $this->custom->checkTableValues('quotation_product_master', ['billing_id' => $billing_id]);
                if ($checkQuotationProductMaster) {
                    $flag = 1;
                }
            } elseif ($i == 2) {
                ++$i;
                $where = ['product_id' => $billing_id];
                $checkOpenStockTable = $this->custom->checkTableValues('stock_open', $where);
                if ($checkOpenStockTable) {
                    $flag = 1;
                }
            } elseif ($i == 3) {
                ++$i;
                $where = ['product_id' => $billing_id];
                $checkStockAdjusmtent = $this->custom->checkTableValues('stock_adjustment', $where);
                if ($checkStockAdjusmtent) {
                    $flag = 1;
                }
            } elseif ($i == 4) {
                ++$i;
                $where = ['product_id' => $billing_id];
                $checkStockPurchaseMaster = $this->custom->checkTableValues('stock_purchase', $where);
                if ($checkStockPurchaseMaster) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);

        if ($flag == 0) {
            echo $flag;
            $where = ['billing_id' => $billing_id];
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
        $row = $this->custom->getSingleRow($this->table, ['billing_id' => $id]);
        $html = '';
        if ($row) {
            $html .= $this->custom->populateMPDFStyle();

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h3>BILLING INFORMATION</h3></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table align="center">';
            $html .= '<tr>';
            $html .= '<td style="width: 180"><strong>Code</strong></td>';
            $html .= '<td>'.$row->stock_code.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Description</strong></td>';
            $html .= '<td>'.$row->billing_description.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>UOM</strong></td>';
            $html .= '<td>'.$row->billing_uom.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Price per UOM</strong></td>';
            $html .= '<td>'.$row->billing_price_per_uom.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td><strong>Billing Type</strong></td>';
            $html .= '<td>'.$row->billing_type.'</td>';
            $html .= '</tr>';
            $html .= '</table>';            

            $file = 'billing_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);

        } else {
            redirect('/master_files/billing', 'refresh');
        }
    }
}
