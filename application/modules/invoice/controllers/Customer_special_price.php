<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Customer_special_price extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'customer_price';
        $this->logged_id = $this->session->user_id;
        $this->view_path = 'invoice/customer_special_price/';

        $this->load->model('invoice/invoice_model', 'inv_model');
    }

    public function list()
    {
        is_ajax();
        $sql = 'SELECT pt_id, master_customer.name, customer_price.customer_code, customer_price.modified_on FROM customer_price, master_customer WHERE customer_price.customer_code = master_customer.code GROUP BY customer_price.customer_code ORDER BY master_customer.name ASC, master_customer.code ASC';
        $query = $this->db->query($sql);
        $list = $query->result();

        $data = [];
        $i = 0;
        foreach ($list as $record) {
            $row = [];
            $row[] = $record->pt_id;
            $row[] = date('M j, Y', strtotime($record->modified_on));
            $row[] = strtoupper($record->name).' ('.strtoupper($record->customer_code).' )';

            $sql_cust_entries = 'SELECT customer_price.stock_code, customer_price.billing_price_per_uom, LEFT(billing_description , 40) as stock_description FROM customer_price, master_billing WHERE customer_price.stock_code = master_billing.stock_code AND customer_code = "'.$record->customer_code.'" ORDER BY customer_price.stock_code ASC';
            $query_cust_entries = $this->db->query($sql_cust_entries);
            $cust_entries = $query_cust_entries->result();
            $html = '';
            foreach ($cust_entries as $entries) {
                $html .= '<div style="padding: 5px 0;"></div><div style="width: 500px; margin">';
                $html .= '<div style="width: 400px; float: left">'.$entries->stock_code.' : '.$entries->stock_description.'</div>';
                $html .= '<div style="width: 100px; float: left; text-align: right">'.$entries->billing_price_per_uom.'</div>';
                $html .= '</div><div style="clear: both; padding: 5px;"></div>';
            }
            $row[] = $html;
            $data[] = $row;
            ++$i;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->custom->getCount('customer_price', null),
            'recordsFiltered' => $i,
            'data' => $data,
        ];
        echo json_encode($output);
    }

    public function get_product_details()
    {
        is_ajax();
        $post = $this->input->post();
        $billing_id = $post['billing_id'];
        $customer_id = $post['cust_id'];

        $product_detail = $this->custom->getSingleRow('master_billing', ['billing_id' => $billing_id]);

        $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $customer_id]);
        $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $customer_data->currency_id]);
        $product_detail->billing_price_per_uom = round($product_detail->billing_price_per_uom * $currency_rate, 2);

        $special_price = $this->custom->getSingleRow('customer_price', ['stock_code' => $product_detail->stock_code, 'customer_code' => $customer_data->code]);
        if ($special_price != null) {
            $product_detail->billing_price_per_uom = $special_price->billing_price_per_uom;
            $pro = json_encode($product_detail);
            echo $pro;
        } else {
            $pro = json_encode($product_detail);
            echo $pro;
        }
    }

    public function get_billings()
    {
        is_ajax();
        $post = $this->input->post();

        $product_options = '';
        if ($post['product'] == 'Y') {
            $sql_product_list = 'SELECT billing_id, stock_code, billing_uom, LEFT(billing_description, 40) as stock_description FROM master_billing WHERE billing_type = "Product" ORDER BY stock_code ASC';
            $query_product_list = $this->db->query($sql_product_list);
            $product_list = $query_product_list->result();

            foreach ($product_list as $key => $value) {
                $product_options .= "<option value='".$value->billing_id."'>";
                $product_options .= $value->stock_code.' : '.$value->stock_description.' ('.$value->billing_uom.')';
                $product_options .= '</option>';
            }
        }

        $service_options = '';
        if ($post['service'] == 'Y') {
            $sql_service_list = 'SELECT * FROM master_billing WHERE billing_type = "Service"  ORDER BY stock_code ASC';
            $query_service_list = $this->db->query($sql_service_list);
            $service_list = $query_service_list->result();

            foreach ($service_list as $key => $value) {
                $service_options .= "<option label='service' value='".$value->billing_id."'>";
                $service_options .= $value->stock_code.' : '.$value->billing_description;
                $service_options .= '</option>';
            }
        }

        $data['product_service_options'] = $product_options.$service_options;

        echo json_encode($data);
    }

    public function save()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $post['customer_id']]);

            if ($post['process'] == 'update') {
                // delete all the items from cust_price.TBL
                $where = ['customer_code' => $customer_code];
                $res[] = $this->custom->deleteRow('customer_price', $where);
            }

            $total_records = count($post['product_id']);

            for ($i = 0; $i <= $total_records - 1; ++$i) {
                // echo $post['product_id'][$i].' :: '.$post['unit_cost'][$i];

                $batch_data['customer_code'] = $customer_code;

                $stock_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $post['product_id'][$i]]);
                $batch_data['stock_code'] = $stock_code;

                $batch_data['billing_price_per_uom'] = $post['unit_cost'][$i];
                $batch_data['created_on'] = date('Y-m-d');
                $batch_data['modified_on'] = date('Y-m-d');
                $insert[] = $this->custom->insertRow('customer_price', $batch_data);
                set_flash_message('message', 'success', 'Customer special price saved');
            }
            redirect('invoice/customer_price');
        }
    }

    public function save1()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $post['customer_id']]);
            for ($i = 1; i <= $len; ++$i) {
                if ($post['data']['product_id'][$i] != null) {
                    $stock_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $post['data']['product_id'][$i]]);
                    $batch_data['customer_code'] = $customer_code;
                    $batch_data['stock_code'] = $stock_code;
                    $batch_data['billing_price_per_uom'] = $post['data']['unit_cost'][$i];
                    $batch_data['created_on'] = date('Y-m-d');
                    $batch_data['modified_on'] = date('Y-m-d');

                    $inserted[] = $this->custom->insertRow('customer_price', $batch_data);
                } else {
                    unset($batch_data);

                    if (in_array('error', $inserted)) {
                        set_flash_message('message', 'danger', 'BATCH SAVE ERROR');
                    } else {
                        set_flash_message('message', 'success', 'SPECIAL PRICE SAVED');
                    }

                    redirect('invoice/customer_price');
                }
            }
        } else {
            set_flash_message('message', 'danger', 'POST ERROR');
            redirect('/invoice/customer_price');
        }
    }

    public function update()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $post['customer_id']]);
            for ($i = 1; i <= $len; ++$i) {
                if ($post['data']['product_id'][$i] != null) {
                    $stock_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $post['data']['product_id'][$i]]);
                    $batch_data['customer_code'] = $customer_code;
                    $batch_data['stock_code'] = $stock_code;
                    $batch_data['billing_price_per_uom'] = $post['data']['unit_cost'][$i];
                    $batch_data['modified_on'] = date('Y-m-d');

                    $pt_id = $post['data']['pt_id'][$i];

                    if ($pt_id !== '') {
                        $updated[] = $this->custom->updateRow('customer_price', $batch_data, ['pt_id' => $post['data']['pt_id'][$i]]);
                    } else {
                        $inserted[] = $this->custom->insertRow('customer_price', $batch_data);
                    }
                } else {
                    unset($batch_data);

                    if (in_array('error', $updated) || in_array('error', $inserted)) {
                        set_flash_message('message', 'danger', 'BATCH SAVE ERROR');
                    } else {
                        set_flash_message('message', 'success', 'SPECIAL PRICE SAVED');
                    }

                    redirect('invoice/customer_price');
                }
            }
        } else {
            set_flash_message('message', 'danger', 'POST ERROR');
            redirect('/invoice/customer_price');
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $customer_code = $this->custom->getSingleValue('customer_price', 'customer_code', ['pt_id' => $id]);
        $where = ['customer_code' => $customer_code];
        $result = $this->custom->deleteRow($this->table, $where);
        echo $result;
    }

    public function delete_item()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['pt_id' => $id];
        $result = $this->custom->deleteRow($this->table, $where);
        echo $result;
    }

    public function print()
    {
        if (isset($_GET['rowID'])) {
            $id = $_GET['rowID'];
        } else {
            $id = $this->input->post('rowID');
        }

        $html = '';

        $html .= '<style type="text/css">
		table { width: 100%; }
		table { border-collapse: collapse; }
		table th {background: gainsboro }
		table th, table td {
			border: 1px solid gainsboro;
			padding: 10px; text-align: left;
		}
		</style>';

        $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>CUSTOMER SPECIAL PRICE</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br /><table>';

        if ($id != 0) {
            $sql = 'SELECT customer_code FROM customer_price WHERE pt_id = '.$id;
        } else {
            $sql = 'SELECT customer_code FROM customer_price, master_customer WHERE customer_price.customer_code = master_customer.code GROUP BY customer_price.customer_code ORDER BY master_customer.code ASC';
        }
        $query = $this->db->query($sql);
        $customer_list = $query->result();

        foreach ($customer_list as $key => $value) {
            $customer_data = $this->custom->getSingleRow('master_customer', ['code' => $value->customer_code]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $customer_data->currency_id]);

            $stock_description = $this->custom->getSingleValue('master_billing', 'billing_description', ['stock_code' => $value->stock_code]);
            $html .= '<tr>';
            $html .= '<th colspan="4" style="border: none">'.$customer_data->name.' ('.$value->customer_code.') | Currency: '.$currency.'</th>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td><strong>STOCK</strong></td>';
            $html .= '<td><strong>UOM</strong></td>';
            $html .= '<td><strong>PRICE</strong></td>';
            $html .= '<td><strong>DATE</strong></td>';
            $html .= '</tr>';

            $sql_customer_entries = 'SELECT customer_price.*, master_billing.billing_description, master_billing.billing_uom FROM customer_price, master_billing WHERE customer_price.stock_code = master_billing.stock_code AND customer_price.customer_code = "'.$value->customer_code.'" ORDER BY master_billing.billing_description ASC, customer_price.stock_code ASC';
            $query_customer_entries = $this->db->query($sql_customer_entries);
            $customer_entries = $query_customer_entries->result();
            foreach ($customer_entries as $key => $values) {
                $html .= '<tr>';
                $html .= '<td>'.$values->billing_description.' ('.$values->stock_code.')</td>';
                $html .= '<td>'.$values->billing_uom.'</td>';
                $html .= '<td>'.$values->billing_price_per_uom.'</td>';
                $html .= '<td>'.date('d-m-Y', strtotime($values->modified_on)).'</td>';
                $html .= '</tr>';
            }
            $html .= '<tr><td colspan="3" height="20" style="border: none"></td></tr>';
        }

        $html .= '</table>';

        $file = 'cust_price_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }
}
