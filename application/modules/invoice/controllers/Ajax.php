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
        $this->table = 'invoice_master';
        $this->logged_id = $this->session->user_id;
        $this->load->model('invoice/invoice_model', 'inv_model');
    }

    public function invoice_new_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $invoices = $this->custom->getCount('invoice_master', ['invoice_ref_no' => $ref_no]);
        echo $invoices;
    }

    public function double_invoice()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $invoices = $this->custom->getCount('invoice_master', ['invoice_ref_no' => $ref_no]);
        echo $invoices;
    }

    function get_settings() {
        is_ajax();
        // get last inserted row
        $settings = $this->custom->getLastInsertedRow('invoice_setting', 'updated_on');
        $data['settings'] = $settings;
        echo json_encode($data);
    }

    function save_settings() {

        is_ajax();
        $post = $this->input->post();
        $post['user_id'] = $this->session->user_id;

        // checks current text prefix from settings page is already exists or not
        $prefix = $this->custom->getCount('invoice_setting', ['text_prefix' => $post['text_prefix']]);
        if ($prefix > 0) { // Exists, Update Entry
            
            $this->custom->updateRow('invoice_setting', $post, ['text_prefix' => $post['text_prefix']]);
            echo 'Settings saved';

        } else { // Not Exists, Insert Entry
            
            $this->custom->insertRow('invoice_setting', $post);
            echo 'Settings Updated';

        }
    }

    public function get_gst_categories()
    {
        is_ajax();

        $table = 'ct_gst';
        $where = ['gst_type' => 'supply'];
        $order_by = 'gst_code';
        $query = $this->inv_model->get_tbl_data($table, $where, $order_by);
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
        $query = $this->inv_model->get_tbl_data($table, $where, null);
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
        if ($post['gst_code']) {
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

        $data['credit_terms'] = $customer_data->credit_term_days;
        $data['credit_limit'] = $customer_data->credit_limit;

        $data['amount_utilized'] = $this->inv_model->get_amount_utilized($customer_data->code);

        echo json_encode($data);
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['invoice_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'DELETED'], $where);
        echo $result;
    }

    public function success()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['invoice_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'SUCCESSFUL'], $where);
        echo $result;
    }

    public function reject()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['invoice_id' => $id];
        $result = $this->custom->updateRow($this->table, ['status' => 'REJECTED'], $where);
        echo $result;
    }

    public function post_invoice_with_special_gst($invoice_data, $customer_code, $currency_code)
    {
        // Itemized Data
        $f_net_total = 0;
        $net_total = 0;
        $all_items_gst_total = 0;
        $other_gst_entries = 0;
        $other_items_gst_total = 0;

        $invoice_product_data = $this->custom->findRow('invoice_product_master', ['invoice_id' => $invoice_data->invoice_id]);
        $count = count($invoice_product_data);
        for ($i = 0; $i < $count; ++$i) {
            $all_items_gst_total += $invoice_product_data[$i]->gst_amount / $invoice_data->currency_rate;

            if ($invoice_product_data[$i]->gst_category == 'SROVR-RS' || $invoice_product_data[$i]->gst_category == 'SROVR-LVG' || $invoice_product_data[$i]->gst_category == 'SRLVG') { // Special GST Category = SROVR-RS, SROVR-LVG, SRLVG
                $f_net_total += $invoice_product_data[$i]->gst_amount;
                $net_total += $invoice_product_data[$i]->gst_amount / $invoice_data->currency_rate;
            } else {
                $other_items_gst_total += $invoice_product_data[$i]->amount / $invoice_data->currency_rate;
                $f_net_total += $invoice_product_data[$i]->amount + $invoice_product_data[$i]->gst_amount;
                $net_total += ($invoice_product_data[$i]->amount + $invoice_product_data[$i]->gst_amount) / $invoice_data->currency_rate;
                ++$other_gst_entries;
            }
        }

        // AR Entry - Debtor Ledger
        $ar_data['doc_ref_no'] = $invoice_data->invoice_ref_no;
        $ar_data['customer_code'] = $customer_code;
        $ar_data['doc_date'] = $invoice_data->modified_on;
        $ar_data['currency'] = $currency_code;
        $ar_data['total_amt'] = number_format($net_total, 2, '.', '');
        $ar_data['f_amt'] = number_format($f_net_total, 2, '.', '');
        $ar_data['sign'] = '+';
        $ar_data['tran_type'] = 'INVOICE';
        $ar_data['invoice_id'] = $invoice_data->invoice_id;
        $ar_data['remarks'] = 'sales invoice. Reference : '.$invoice_data->invoice_ref_no;
        $this->custom->insertRow('accounts_receivable', $ar_data);

        // GL Entry - Double Entry
        // 1. Debit = CA001 (DEBTOR CONTRA ACCOUNT)
        $gl_data['doc_date'] = $invoice_data->modified_on;
        $gl_data['ref_no'] = $invoice_data->invoice_ref_no;
        $gl_data['remarks'] = 'Sales invoice. Reference: '.$invoice_data->invoice_ref_no;
        $gl_data['accn'] = 'CA001';
        $gl_data['sign'] = '+';
        $gl_data['gstcat'] = '';
        $gl_data['tran_type'] = 'INVOICE';
        $gl_data['total_amount'] = number_format($net_total, 2, '.', '');
        $gl_data['sman'] = $invoice_data->employee_id;
        $gl_data['iden'] = $customer_code;
        $this->custom->insertRow('gl', $gl_data);

        // 2. Credit = CL300 (GST ACCOUNT)
        $gl_data['accn'] = 'CL300';
        $gl_data['sign'] = '-';
        $gl_data['total_amount'] = number_format($all_items_gst_total, 2, '.', '');
        $this->custom->insertRow('gl', $gl_data);

        // 3. Credit = S0001 (SALES ACCOUNT) // Only need to be insert if any item used gst category which is other than SROVR-RS, SROVR-LVG, SRLVG
        if ($other_gst_entries > 0) {
            $gl_data['accn'] = 'S0001';
            $gl_data['sign'] = '-';
            $gl_data['total_amount'] = number_format($other_items_gst_total, 2, '.', '');
            $this->custom->insertRow('gl', $gl_data);
        }
    }

    public function isGSTSpecial($id)
    {
        $special_gst = false;
        $invoice_product_data = $this->custom->findRow('invoice_product_master', ['invoice_id' => $id]);
        $count = count($invoice_product_data);
        for ($i = 0; $i < $count; ++$i) {
            if ($invoice_product_data[$i]->gst_category == 'SROVR-RS' || $invoice_product_data[$i]->gst_category == 'SROVR-LVG' || $invoice_product_data[$i]->gst_category == 'SRLVG') { // Special GST Category = SROVR-RS, SROVR-LVG, SRLVG
                $special_gst = true;
            }
        }

        return $special_gst;
    }

    public function postInv()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        // invoice data
        $invoice_data = $this->custom->getSingleRow('invoice_master', ['invoice_id' => $id]);

        // customer data
        $customer_data = $this->custom->getSingleRow('master_customer', ['customer_id' => $invoice_data->customer_id]);

        // currency data
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer_data->currency_id]);

        $special_gst = $this->isGSTSpecial($id);
        if ($special_gst) {
            $this->post_invoice_with_special_gst($invoice_data, $customer_data->code, $currency_data->code);
        } else {
            // inserting into ACCOUNTS RECEIVABLE TBL
            $ar_data['doc_ref_no'] = $invoice_data->invoice_ref_no;
            $ar_data['customer_code'] = $customer_data->code;
            $ar_data['doc_date'] = $invoice_data->modified_on;
            $ar_data['currency'] = $currency_data->code;
            $ar_data['total_amt'] = $invoice_data->net_total;
            $ar_data['f_amt'] = $invoice_data->f_net_total;
            $ar_data['sign'] = '+';
            $ar_data['tran_type'] = 'INVOICE';
            $ar_data['invoice_id'] = $id;
            $ar_data['remarks'] = 'Sales invoice. Reference : '.$invoice_data->invoice_ref_no;
            $this->custom->insertRow('accounts_receivable', $ar_data);

            // inserting into GL TBL
            // 1. Debit = CA001 (DEBTOR CONTRA ACCOUNT)
            $gl_data['doc_date'] = $invoice_data->modified_on;
            $gl_data['ref_no'] = $invoice_data->invoice_ref_no;
            $gl_data['remarks'] = 'Sales invoice. Reference : '.$invoice_data->invoice_ref_no;
            $gl_data['accn'] = 'CA001';
            $gl_data['sign'] = '+';
            $gl_data['gstcat'] = '';
            $gl_data['tran_type'] = 'INVOICE';
            $gl_data['total_amount'] = $invoice_data->net_total;
            $gl_data['sman'] = $invoice_data->employee_id;
            $gl_data['iden'] = $customer_data->code;
            $this->custom->insertRow('gl', $gl_data);

            if ($invoice_data->gst_total > 0) {
                // credit 'GST ACCOUNT (CL300)'
                $gl_data['accn'] = 'CL300';
                $gl_data['sign'] = '-';
                $gl_data['total_amount'] = $invoice_data->gst_total;
                $this->custom->insertRow('gl', $gl_data);
            }

            // credit 'SALES ACCOUNT (S0001)'
            $gl_data['accn'] = 'S0001';
            $gl_data['sign'] = '-';
            $net_total_without_gst = $invoice_data->net_total - $invoice_data->gst_total;
            $gl_data['total_amount'] = number_format($net_total_without_gst, 2, '.', '');
            $this->custom->insertRow('gl', $gl_data);
        }

        // inserting every item into stock table (only product items) and gst table
        echo $result;
        $invoice_product_data = $this->custom->findRow('invoice_product_master', ['invoice_id' => $id]);
        $count = count($invoice_product_data);
        for ($i = 0; $i < $count; ++$i) {
            $billing_type = $this->custom->getSingleValue('master_billing', 'billing_type', ['billing_id' => $invoice_product_data[$i]->billing_id]);

            if ($billing_type == 'Product') {
                $st_data['product_id'] = $invoice_product_data[$i]->billing_id;
                $st_data['quantity'] = $invoice_product_data[$i]->quantity;
                $st_data['created_on'] = $invoice_data->modified_on;
                $st_data['ref_no'] = $invoice_data->invoice_ref_no;
                $st_data['stock_type'] = 'Invoice';
                $st_data['iden'] = $customer_data->code;
                $st_data['sign'] = '-';
                $st_id[] = $this->custom->insertRow('stock', $st_data);
            }

            // Insert invoice details into gst Table
            $gst_data['date'] = $invoice_data->modified_on;
            $gst_data['dref'] = $invoice_data->invoice_ref_no;
            $gst_data['iden'] = $customer_data->code;
            $gst_data['rema'] = 'Sales invoice. Reference : '.$invoice_data->invoice_ref_no;
            $gst_data['gsttype'] = 'O';

            $gst_data['gstcate'] = $invoice_product_data[$i]->gst_category;
            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $invoice_product_data[$i]->gst_category]);
            $gst_data['gstperc'] = $gst_rate;

            $local_amount = $invoice_product_data[$i]->amount / $invoice_data->currency_rate;
            $local_gst_amount = $invoice_product_data[$i]->gst_amount / $invoice_data->currency_rate;
            $gst_data['amou'] = number_format($local_amount, 2, '.', '');
            $gst_data['gstamou'] = number_format($local_gst_amount, 2, '.', '');
            $gst_data['tran_type'] = 'INVOICE';

            $gst[] = $this->custom->insertRow('gst', $gst_data);
        }

        // invoice status set to POSTED
        $where = ['invoice_id' => $id];
        $result = $this->custom->updateRow('invoice_master', ['status' => 'POSTED'], $where);

        echo $result;
    }

    // extract invoice
    public function get_quotations()
    {
        $draw = intval($this->input->get('draw'));
        $start = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));

        $data = [];
        $table = 'quotation_master';
        $this->db->select('quotation_id, quotation_ref_no, customer_id')->from($table);
        $this->db->where(['extract_in_invoice' => 0, 'status' => 'SUCCESSFUL']);
        $this->db->order_by('quotation_ref_no', 'DESC');
        $query = $this->db->get();
        foreach ($query->result() as $r) {
            $customer_data = $this->custom->getMultiValues('master_customer', 'code, name', ['customer_id' => $r->customer_id]);
            $data[] = [
                 $r->quotation_id,
                 $r->quotation_ref_no,
                 '('.$customer_data->code.') '.$customer_data->name,
            ];
        }

        $result = [
                'draw' => $draw,
                'recordsTotal' => $query->num_rows(),
                'recordsFiltered' => $query->num_rows(),
                'data' => $data,
              ];

        echo json_encode($result);

        exit;
    }

    public function get_quotation_details()
    {
        is_ajax();
        $post = $this->input->post();
        $quotation['quotation_details'] = $this->custom->getSingleRow('quotation_master', ['quotation_id' => $post['quotation_id']]);
        $quotation['quotation_product_edit_data'] = $quotation_product_edit_data = $this->custom->getRows('quotation_product_master', ['quotation_id' => $post['quotation_id']]);
        foreach ($quotation_product_edit_data as $value) {
            $product_array[] = $value->product_id;
        }
        $quotation['product_array'] = $product_array;
        $quotation['total_quotation'] = $this->custom->getTotalCount('quotation_master');
        echo json_encode($quotation);
    }
}
