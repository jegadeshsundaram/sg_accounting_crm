<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'quotation_master';
        $this->logged_id = $this->session->user_id;
        $this->load->model('quotation/quotation_model', 'qt_model');
    }

    public function quotation_new_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $quotations = $this->custom->getCount('quotation_master', ['quotation_ref_no' => $ref_no]);
        echo $quotations;
    }

    public function double_quotation()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $quotations = $this->custom->getCount('quotation_master', ['quotation_ref_no' => $ref_no]);
        echo $quotations;
    }

    function get_settings() {
        is_ajax();
        // get last inserted row
        $settings = $this->custom->getLastInsertedRow('quotation_setting', 'updated_on');
        $data['settings'] = $settings;
        echo json_encode($data);
    }

    function save_settings() {

        is_ajax();
        $post = $this->input->post();
        $post['user_id'] = $this->session->user_id;

        // checks current text prefix from settings page is already exists or not
        $prefix = $this->custom->getCount('quotation_setting', ['text_prefix' => $post['text_prefix']]);
        if ($prefix > 0) { // Exists, Update Entry
            
            $this->custom->updateRow('quotation_setting', $post, ['text_prefix' => $post['text_prefix']]);
            echo 'Settings saved';

        } else { // Not Exists, Insert Entry
            
            $this->custom->insertRow('quotation_setting', $post);
            echo 'Settings Updated';

        }
    }

    public function get_gst_categories()
    {
        is_ajax();

        $table = 'ct_gst';
        $where = ['gst_type' => 'supply'];
        $order_by = 'gst_code';
        $query = $this->qt_model->get_tbl_data($table, $where, $order_by);
        $list = $query->result();

        $items = "<option value=''>-- Select --</option>";
        foreach ($list as $record) {
            if ($record->gst_code == 'SR') {
                $items .= "<option value='".$record->gst_code."' selected='selected'>";
            } else {
                $items .= "<option value='".$record->gst_code."'>";
            }
            $items .= $record->gst_code.' : '.$record->gst_description.' (Rate: '.$record->gst_rate.')';
            $items .= '</option>';
        }

        $data['gst_categories'] = $items;
        $data['gst_rate'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'SR']);

        echo json_encode($data);
    }

    public function get_billing_details()
    {
        is_ajax();
        $post = $this->input->post();

        $table = 'master_billing';
        $where = ['billing_id' => $post['billing_id']];
        $query = $this->qt_model->get_tbl_data($table, $where, null);
        $row = $query->row();

        $data['billing_type'] = $row->billing_type;
        $data['billing_code'] = $row->stock_code;
        $data['billing_description'] = $row->billing_description;
        $data['billing_uom'] = $row->billing_uom;

        echo json_encode($data);
    }
    
    public function get_billing()
    {
        is_ajax();
        $post = $this->input->post();

        $customer_code = $post['customer_code'];
        $billing_id = $post['billing_id'];
        $billing = $this->custom->getMultiValues('master_billing', 'stock_code, billing_type, billing_uom, billing_description', ['billing_id' => $billing_id]);
        $data['billing_type'] = $billing->billing_type;
        $data['billing_uom'] = $billing->billing_uom;
        $data['billing_details'] = '('.$billing->stock_code.') '.substr($billing->billing_description, 0, 40).'...';

        // get currency code to display unit price is editable if it is Foreign Currency
        $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $customer_code]);
        $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);

        $price = 0;

        // Step 1 :: Checking Price in Spcial_Price.TBL
        $this->db->select('billing_price_per_uom');
        $this->db->from('customer_price');
        $this->db->where(['customer_code' => $customer_code, 'stock_code' => $billing->stock_code]);
        $this->db->order_by('pt_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $price = $query->row()->billing_price_per_uom;
        
        // Step 2 :: If Special Price NOT EXISTS, then check in master_billing.TBL
        if ($price == 0) {
            $price = $this->custom->getSingleValue('master_billing', 'billing_price_per_uom', ['stock_code' => $billing->stock_code]);
        }

        $final_price = $currency_rate * $price;
        $data['unit_price'] = number_format($final_price, 2, '.', '');

        echo json_encode($data);
    }

    public function set_unit_price()
    {
        is_ajax();
        $post = $this->input->post();

        $tbl = $post['tbl'];

        $customer_id = $post['customer_id'];
        $customer_code = $this->custom->getSingleValue('master_customer', 'code', ['customer_id' => $customer_id]);

        $billing_id = $post['billing_id'];
        $billing_code = $this->custom->getSingleValue('master_billing', 'stock_code', ['billing_id' => $billing_id]);
        $billing_price_per_uom = $post['billing_price_per_uom'];

        // If user has selected to update the price in "CUSTOMER_PRICE.TBL"
        // First, check there is already entry for this customer and the same product in CUSTOMER_PRICE.TBL
        // If "YES", then UPDATE
        // if "NO", then INSERT
        if ($tbl == 'customer_price') {
            $data['billing_price_per_uom'] = $billing_price_per_uom;
            $data['modified_on'] = date('Y-m-d');

            $where = ['customer_code' => $customer_code, 'stock_code' => $billing_code];
            $cnt = $this->custom->getCount('customer_price', $where);
            if ($cnt > 0) { // Update
                $status = $this->custom->updateRow('customer_price', $data, $where);
            } else { // Insert
                $data['customer_code'] = $customer_code;
                $data['stock_code'] = $billing_code;
                $data['created_on'] = date('Y-m-d');
                $status = $this->custom->insertRow('customer_price', $data);
            }
        }

        // If user has selected to update the price in "master_billing.TBL"
        // then, UPDATE the Price in "master_billing.TBL"
        if ($tbl == 'master_billing') {
            $where = ['billing_id' => $billing_id, 'stock_code' => $billing_code];
            $status = $this->custom->updateRow('master_billing', ['billing_price_per_uom' => $billing_price_per_uom], $where);
        }

        echo $status;
    }

    public function get_gst_details()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $data['gst_percentage'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gst_code']]);
            echo json_encode($data);
        } else {
            echo '0';
        }
    }

    public function get_customer_gst_number()
    {
        is_ajax();
        $post = $this->input->post();
        $customer_id = $post['customer_id'];
        $customer_gst_number = $this->custom->getSingleValue('master_customer', 'gst_number', ['customer_id' => $customer_id]);
        if ($customer_gst_number !== null && $customer_gst_number !== '') {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function get_customer_details()
    {
        is_ajax();
        $post = $this->input->post();

        $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $post['customer_id']]);
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer_data->currency_id]);

        $data['customer_address'] = $this->custom->populateCustomerAddress($customer_data);
        $data['customer_code'] = $customer_data->code;
        $data['customer_currency'] = $currency_data->code;
        $data['currency_rate'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['quotation_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'DELETED'], $where);
        echo $result;
    }

    public function success()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['quotation_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'SUCCESSFUL'], $where);
        echo $result;
    }

    public function reject()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['quotation_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'REJECTED'], $where);
        echo $result;
    }
}
