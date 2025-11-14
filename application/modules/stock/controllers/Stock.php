<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stock extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock/stock_model', 'stock_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        // data patch - ref list
        $this->db->select('ref_no, created_on');
        $this->db->from('stock');
        $this->db->where(['stock_type' => 'OPBAL']);
        $this->db->group_by('ref_no');
        $this->db->order_by('ref_no', 'ASC');
        $query = $this->db->get();
        $refs = $query->result();
        $options = "<option value=''>-- Select --</option>";
        foreach ($refs as $value) {
            $options .= "<option value='".$value->ref_no."'>";
            $options .= date('d-m-Y', strtotime($value->created_on)).' | '.$value->ref_no;
            $options .= '</option>';
        }
        $this->body_vars['refs'] = $options;

        $this->body_file = 'stock/options.php';
    }

    public function opening_balance()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function purchase()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function adjustment()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_ob($row_id = '') {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['page'] = 'edit';

            $ob_data = $this->custom->getMultiValues('stock_open', 'document_date, document_reference', ['stock_ob_id' => $row_id]);
            $this->body_vars['doc_date'] = $ob_data->document_date;
            $this->body_vars['ref_no'] = $ob_data->document_reference;

        } else {
            $this->body_vars['page'] = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
        }

        $this->body_vars['products'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], 'Product', [' : ', ' '], ['billing_type' => 'product']);

        $this->body_vars['save_url'] = '/stock/save_ob';
    }

    public function manage_purchase($row_id = '') {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['page'] = 'edit';

            $purchase_data = $this->custom->getMultiValues('stock_purchase', 'document_date, document_reference, supplier_id', ['purchase_id' => $row_id]);
            $this->body_vars['doc_date'] = $purchase_data->document_date;
            $this->body_vars['ref_no'] = $purchase_data->document_reference;
            $this->body_vars['supplier_id'] = $purchase_data->supplier_id;
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ''], ['active' => 1], ['supplier_id' => $purchase_data->supplier_id]);

        } else {
            $this->body_vars['page'] = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ' '], ['active' => 1]);
        }

        
        $this->body_vars['products'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], 'Product', [' : ', ' '], ['billing_type' => 'product']);

        $this->body_vars['save_url'] = '/stock/save_purchase';
    }

    public function manage_adjustment($row_id = '') {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['page'] = 'edit';

            $adj_data = $this->custom->getMultiValues('stock_adjustment', 'document_date, document_reference', ['adj_id' => $row_id]);
            $this->body_vars['doc_date'] = $adj_data->document_date;
            $this->body_vars['ref_no'] = $adj_data->document_reference;

        } else {
            $this->body_vars['page'] = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
        }

        $this->body_vars['products'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], 'Product', [' : ', ' '], ['billing_type' => 'product']);

        $this->body_vars['save_url'] = '/stock/save_adjustment';
    }

    public function save_ob()
    {
        $post = $this->input->post();

        if ($post) {
            $total_items = count($post['ob_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['product_id'] = $post['product_id'][$i];
                $batch_data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['document_reference'] = $post['ref_no'];
                $batch_data['quantity'] = $post['quantity'][$i];
                $batch_data['unit_cost'] = $post['unit_cost'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];

                $updated[] = $this->custom->updateRow('stock_open', $batch_data, ['stock_ob_id' => $post['ob_id'][$i]]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Saved!');
            }
        } else {
            set_flash_message('message', 'danger', 'Post Error');
        }

        redirect('stock/opening_balance/');
    }
    
    public function save_purchase()
    {
        $post = $this->input->post();

        if ($post) {
            $total_items = count($post['purchase_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['document_reference'] = $post['ref_no'];
                $batch_data['supplier_id'] = $post['supplier'];
                $batch_data['product_id'] = $post['product_id'][$i];
                $batch_data['quantity'] = $post['quantity'][$i];
                $batch_data['unit_cost'] = $post['unit_cost'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];

                $purchase_id = $post['purchase_id'][$i];
                $updated[] = $this->custom->updateRow('stock_purchase', $batch_data, ['purchase_id' => $purchase_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Saved!');
            }
            
        } else {
            set_flash_message('message', 'danger', 'Post Error');
        }

        redirect('stock/purchase');
    }

    public function save_adjustment()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $total_items = count($post['adj_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['product_id'] = $post['product_id'][$i];
                $batch_data['document_date'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['document_reference'] = $post['ref_no'];
                $batch_data['quantity'] = $post['quantity'][$i];
                $batch_data['sign'] = $post['sign'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];

                $adj_id = $post['adj_id'][$i];
                $updated[] = $this->custom->updateRow('stock_adjustment', $batch_data, ['adj_id' => $adj_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Batch Saved');
            }            
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('stock/adjustment');
    }

    public function reports()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['product_options'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], 'Product', [' : ', ' '], ['billing_type' => 'product']);
    }   

    public function data_patch()
    {
        is_logged_in('admin');
        has_permission();

        $post = $this->input->post();
        if ($post) {
            $doc_date = $this->custom->getSingleValue('stock', 'created_on', ['ref_no' => $post['ref_no']]);
            $this->body_vars['doc_date'] = $doc_date;
            $this->body_vars['ref_no'] = $post['ref_no'];

            $this->body_vars['products'] = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], 'Product', [' : ', ' '], ['billing_type' => 'product']);

            $this->body_vars['save_url'] = '/stock/save_patched_data';
        }

        $this->body_file = 'stock/data_patch.php';
    }

    public function save_patched_data()
    {
        $post = $this->input->post();

        if ($post) {
            $total_items = count($post['ob_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['product_id'] = $post['product_id'][$i];
                $batch_data['created_on'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['ref_no'] = $post['ref_no'];
                $batch_data['quantity'] = $post['quantity'][$i];
                $batch_data['unit_cost'] = $post['unit_cost'][$i];
                $batch_data['remark'] = $post['remarks'][$i];

                $updated[] = $this->custom->updateRow('stock', $batch_data, ['stock_id' => $post['ob_id'][$i]]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Data Patched');
            }
            
        } else {
            set_flash_message('message', 'danger', 'Post Error');
        }

        redirect('stock/');
    }

    public function print_audit()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $transaction_type = $post['transaction'];
            $transaction_text = $post['transaction_desc'];
            $transaction_order = $post['order'];
            $cut_off_date = date('d-m-Y');

            $html = '';

            $header = '<table style="width: 100%; border: none">';
            $header .= '<tr>';
            $header .= '<td style="border: none; padding-left: 0;">';
            $header .= $this->custom->populateCompanyHeader();
            $header .= '</td>';
            $header .= '<td style="border: none; text-align: right; padding-right: 0;"><h3>STOCK AUDIT LISTING</h3></td>';
            $header .= '</tr>';
            $header .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $header .= '<td style="border: none; color: blue; padding-left: 0;"><h4>'.$transaction_text.'</h4></td>';
            $header .= '<td style="border: none; text-align: right; padding-right: 0;"><strong>Date:</strong> <i>'.$cut_off_date.'</i></td>';
            $header .= '</tr>';

            $header .= '</table><br /><br />';

            $tbl_header = '<thead>
							<tr>
								<th style="width: 90px;">Date</th>
								<th style="width: 110px;">Reference</th>
								<th style="width: 120px;">Iden</th>
                                <th style="width: 250px;">Stock</th>
                                <th>Sign</th>
                                <th style="width: 50px;">Quantity</th>
                                <th style="width: 150px; text-align: right">Unit Cost</th>
                                <th style="width: 150px; text-align: right">Amount</th>								
							</tr>
						</thead>';
            $tbl_body = '<tbody>';

            $i = 0;
            $bf_total_per_subledger = 0;

            $table = 'stock';
            $columns = '*';
            $where = ['stock_type' => $transaction_type];
            $group_by = null;
            $order_by = null;
            $order_by_many = 'created_on '.$transaction_order.', ref_no ASC';
            $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $sql = $this->db->last_query();
            $list = $query->result();
            foreach ($list as $record) {
                $document_date = implode('/', array_reverse(explode('-', $record->created_on)));
                $tbl_body .= '<tr>';
                $tbl_body .= '<td>'.$document_date.'</td>';
                $tbl_body .= '<td>'.$record->ref_no.'</td>';
                if ($record->stock_type == 'OPBAL') {
                    $tbl_body .= '<td>Balance B/F</td>';
                } else {
                    $iden = $this->custom->getSingleRow('master_customer', ['code' => $record->iden]);
                    $tbl_body .= '<td>'.$iden->name.' ('.$iden->code.')</td>';
                }
                $stock_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $record->product_id]);
                $stock_name_desc = $stock_data->billing_description.' ('.$stock_data->stock_code.' )';
                $tbl_body .= '<td>'.$stock_name_desc.'</td>';

                $tbl_body .= '<td style="text-align: center">'.$record->sign.'</td>';
                $tbl_body .= '<td style="text-align: center">'.$record->quantity.'</td>';
                $tbl_body .= '<td style="text-align: right">'.number_format($record->unit_cost, 2).'</td>';

                $stock_amount = $record->quantity * $record->unit_cost;
                $tbl_body .= '<td style="text-align: right">'.number_format($stock_amount, 2).'</td>';
                $tbl_body .= '</tr>';

                if ($record->sign == '+') {
                    $bf_total_per_subledger += $stock_amount;
                } else {
                    $bf_total_per_subledger -= $stock_amount;
                }
                ++$i;
            }

            $tbl_body .= '</tbody>';

            $html_document = '';
            if ($i == 0) {
                $html_document .= '<table style="width: 100%;">';
                $html_document .= '<tr>';
                $html_document .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html_document .= '</tr>';
                $html_document .= '</table>';
            } else {
                $tbl_footer = '<table style="width: 100%; page-break-inside: avoid;">';

                // dummy row to fix font and cols size
                $tbl_footer .= '<thead>
                    <tr class="dummy-row">
                        <th style="width: 90px;">Date</th>
                        <th style="width: 110px;">Reference</th>
                        <th style="width: 120px;">Iden</th>
                        <th style="width: 250px;">Stock</th>
                        <th>Sign</th>
                        <th style="width: 50px;">Quantity</th>
                        <th style="width: 150px;">Unit Cost</th>
                        <th style="width: 150px;">Amount</th>                        
                    </tr></thead>';

                $tbl_footer .= '<tbody>';
                $tbl_footer .= '<tr>';
                $tbl_footer .= '<td colspan="7" style="border: none; text-align: right">GRAND TOTAL <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';
                $tbl_footer .= '<td style="border: none; text-align: right">';
                if ($bf_total_per_subledger < 0) {
                    $tbl_footer .= number_format((-1) * $bf_total_per_subledger, 2).' CR';
                } else {
                    $tbl_footer .= number_format($bf_total_per_subledger, 2).' DR';
                }
                $tbl_footer .= '</td>';
                $tbl_footer .= '</tr>';

                $bf_total_per_gl = 0;
                $table = 'gl';
                $columns = 'sign, total_amount';
                $where = ['accn' => 'CA001', 'tran_type' => $transaction_type];
                $group_by = null;
                $order_by = null;
                $order_by_many = null;
                $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
                $list = $query->result();
                foreach ($list as $record) {
                    if ($record->sign == '+') {
                        $bf_total_per_gl += $record->total_amount;
                    } else {
                        $bf_total_per_gl -= $record->total_amount;
                    }
                }

                $tbl_footer .= '<tr>';
                $tbl_footer .= '<td colspan="7" style="border: none; text-align: right">';
                if ($transaction_type == 'OPBAL') {
                    $tbl_footer .= 'Balance b/f per GL';
                } else {
                    $tbl_footer .= 'Total per CA001';
                }
                $tbl_footer .= ' <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';

                $tbl_footer .= '<td style="border: none; text-align: right">';
                if ($bf_total_per_gl < 0) {
                    $tbl_footer .= number_format((-1) * $bf_total_per_gl, 2).' CR';
                } else {
                    $tbl_footer .= number_format($bf_total_per_gl, 2).' DR';
                }
                $tbl_footer .= '</td>';
                $tbl_footer .= '</tr>';

                $diff_amount = $this->abs_diff($bf_total_per_subledger < 0 ? $bf_total_per_subledger : (-1) * $bf_total_per_subledger, $bf_total_per_gl < 0 ? $bf_total_per_gl : (-1) * $bf_total_per_gl);
                $tbl_footer .= '<tr>';
                $tbl_footer .= '<td colspan="7" style="border: none; text-align: right">DIFFERENCE <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';
                $tbl_footer .= '<td style="border: none; text-align: right">';
                if ($diff_amount < 0) {
                    $tbl_footer .= number_format((-1) * $diff_amount, 2).' CR';
                } else {
                    $tbl_footer .= number_format($diff_amount, 2).' DR';
                }
                $tbl_footer .= '</td>';
                $tbl_footer .= '</tr>';
                $tbl_footer .= '<tbody>';
                $tbl_footer .= '</table>';

                $html_document .= '<table style="width: 100%;">';
                $html_document .= $tbl_header.$tbl_body;
                $html_document .= '</table>';
                $html_document .= $tbl_footer;
            }

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$header.$html_document;

            $file = 'st_audit_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
            
        } else {
            redirect('/stock/');
        }
    }

    public function abs_diff($v1, $v2)
    {
        $diff = $v1 - $v2;

        return $diff < 0 ? (-1) * $diff : $diff;
    }

    public function print_stock_card()
    {
        is_logged_in('admin');
        has_permission();

        $post = $this->input->post();
        if ($post) {
            $from = date('Y-m-d', strtotime($post['from']));
            $to = date('Y-m-d', strtotime($post['to']));
            $product_id = $post['product_id'];

            $html = '<table>';
            $html .= '<tr>';
            $html .= '<td colspan="6" align="center" style="border: none"><h3>STOCK CARD</h3></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6" align="right" style="border: none;"><strong>Period : </strong><span style="color: blue;">'.$post['from'].'</span> <i>to</i> <span style="color: blue;">'.$post['to'].'</span> <hr /></td>';
            $html .= '</tr>';

            $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $product_id]);
            $stock_name_desc = $product_data->billing_description.' ('.$product_data->stock_code.')';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; padding-left: 0;">';
            $html .= '<strong>Stock: </strong><span style="color: tomato;">'.$stock_name_desc;
            $html .= '</span></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<th style="width: 90px">Date</th>';
            $html .= '<th>Reference</th>';
            $html .= '<th>Transaction</th>';
            $html .= '<th>In</th>';
            $html .= '<th>Out</th>';
            $html .= '<th>Balance</th>';
            $html .= '</tr>';

            // Balance B/f lum sum quantity - start
            $balance_bf_qty_lum_sum = 0;
            $this->db->select('quantity, sign');
            $this->db->from('stock');
            $this->db->where('created_on < "'.$from.'" AND product_id = '.$product_id);
            $this->db->order_by('created_on', 'ASC');
            $query = $this->db->get();
            // print_r($this->db->last_query());
            $list = $query->result();
            foreach ($list as $record) {
                if ($record->sign == '+') {
                    $balance_bf_qty_lum_sum += $record->quantity;
                } elseif ($record->sign == '-') {
                    $balance_bf_qty_lum_sum -= $record->quantity;
                }
            }

            if ($balance_bf_qty_lum_sum !== 0) {
                $html .= '<tr>';
                $html .= '<td valign="top">'.date('d-m-Y', strtotime($from)).'</td>';
                $html .= '<td valign="top">Balance B/F</td>';
                $html .= '<td valign="top">Balance B/F</td>';

                if ($balance_bf_qty_lum_sum > 0) {
                    $html .= '<td valign="top">'.$balance_bf_qty_lum_sum.'</td>';
                    $html .= '<td valign="top"></td>';
                } elseif ($balance_bf_qty_lum_sum < 0) {
                    $html .= '<td valign="top"></td>';
                    $html .= '<td valign="top">'.$balance_bf_qty_lum_sum.'</td>';
                } else {
                    $html .= '<td valign="top">0</td>';
                    $html .= '<td valign="top">0</td>';
                }

                $html .= '<td valign="top">'.$balance_bf_qty_lum_sum.'</td>';
                $html .= '</tr>';
            }
            // Balance B/f lum sum quantity - end

            $quantity_in_out_balance = 0;
            $i = 0;

            $this->db->select('*');
            $this->db->from('stock');
            $this->db->where('created_on BETWEEN "'.$from.'" and "'.$to.'" AND product_id = '.$product_id);
            $this->db->order_by('created_on', 'ASC');
            $query = $this->db->get();
            $stock_card = $query->result();
            foreach ($stock_card as $value) {
                $html .= '<tr>';
                $html .= '<td valign="top">'.date('d-m-Y', strtotime($value->created_on)).'</td>';
                $html .= '<td valign="top">'.$value->ref_no.'</td>';
                $html .= '<td valign="top">'.$value->stock_type.'</td>';

                if ($value->sign == '+') {
                    $quantity_in_out_balance += $value->quantity;
                    $html .= '<td valign="top">'.$value->quantity.'</td>';
                    $html .= '<td valign="top"></td>';
                } elseif ($value->sign == '-') {
                    $quantity_in_out_balance -= $value->quantity;
                    $html .= '<td valign="top"></td>';
                    $html .= '<td valign="top">'.$value->quantity.'</td>';
                }

                if ($i == 0) {
                    if ($balance_bf_qty_lum_sum >= 0) {
                        $quantity_in_out_balance += $balance_bf_qty_lum_sum;
                    } else {
                        $quantity_in_out_balance -= (-1) * $quantity_in_out_balance;
                    }
                }

                $html .= '<td valign="top">'.$quantity_in_out_balance.'</td>';
                $html .= '</tr>';

                ++$i;
            }

            if ($i == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="5" style="border: none; color: tomato; text-align: center;">';
                $html .= 'No records available for this product';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'st_card_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('stock/reports');
        }
    }

    public function print_stock_status()
    {
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['cut_off_date'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['cut_off_date']));

            $html = '<table>';

            $html .= '<tr>';
            $html .= '<td colspan="4" align="center" style="border: none"><h4>STOCK STATUS AS AT <span style="color: tomato">'.$_GET['cut_off_date'].'</span></h4></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="border: none;"><strong>Date : </strong><span style="color: blue;">'.date('d-m-Y').'</span><hr /></td>';
            $html .= '</tr>';

            $i = 0;

            $this->db->select('st.product_id, sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity');
            $this->db->from('stock as st, master_billing as bm');
            $this->db->where('st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'"');
            $this->db->group_by('st.product_id');
            $this->db->order_by('bm.stock_code', 'ASC');
            $query = $this->db->get();
            $stock_list = $query->result();
            foreach ($stock_list as $value) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Stock Code</th>';
                    $html .= '<th>Stock Description</th>';
                    $html .= '<th>Quantity</th>';
                    $html .= '<th>UOM</th>';
                    $html .= '</tr>';
                }

                $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $value->product_id]);

                $html .= '<tr>';
                $html .= '<td valign="top">'.$product_data->stock_code.'</td>';
                $html .= '<td valign="top">'.$product_data->billing_description.'</td>';
                $html .= '<td valign="top" style="text-align: center">'.$value->balance_quantity.'</td>';
                $html .= '<td valign="top" style="text-align: center">'.$product_data->billing_uom.'</td>';
                $html .= '</tr>';

                ++$i;
            }

            if ($i == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="4" style="border: none; color: tomato; text-align: center;">';
                $html .= 'No stocks available';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'st_status_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('stock/reports');
        }
    }

    public function print_wac()
    {
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['wac_cutoff'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['wac_cutoff']));

            $html = '<table>';

            $html .= '<tr>';
            $html .= '<td colspan="5" align="center" style="border: none; color: blue"><h4>WAC STOCK VALUATION AS AT <span style="color: tomato">'.date('d-m-Y', strtotime($_GET['wac_cutoff'])).'</span></h4></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="5" align="right" style="border: none;"><strong>Report Date : </strong><span style="font-weight: normal">'.date('d-m-Y').'</span> <hr /></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<th>Stock Code</th>';
            $html .= '<th>Description</th>';
            $html .= '<th style="text-align: center">Quantity</th>';
            $html .= '<th style="text-align: right">WAC Cost</th>';
            $html .= '<th style="text-align: right">Amount</th>';
            $html .= '</tr>';

            $wac_stock_grand_total = 0;
            $i = 0;

            // $sql = 'SELECT st.product_id FROM stock as st, master_billing as bm WHERE st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'" GROUP BY st.product_id ORDER BY bm.stock_code ASC';
            $this->db->select('st.product_id');
            $this->db->from('stock as st, master_billing as bm');
            $this->db->where('st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'"');
            $this->db->group_by('st.product_id');
            $this->db->order_by('bm.stock_code', 'ASC');
            $query = $this->db->get();
            $stock_list = $query->result();
            foreach ($stock_list as $value) {
                $product_id = $value->product_id;
                $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $product_id]);

                $specific_product_stock_balance = 0;

                // get balance stock in hand for specific product_id for WAC Computation - start
                // $sql = 'SELECT sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity FROM stock WHERE created_on <= "'.$cut_off_date.'" AND product_id = "'.$product_id.'" ';
                $this->db->select('sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity');
                $this->db->from('stock');
                $this->db->where('created_on <= "'.$cut_off_date.'" AND product_id = "'.$product_id.'"');
                $query = $this->db->get();
                $st_balance_list = $query->result();
                foreach ($st_balance_list as $st_record) {
                    $specific_product_stock_balance = $st_record->balance_quantity;
                }
                // get balance stock in hand for specific product_id for WAC Computation - end

                // get WAC cost for specific product_id from cost matrix - start
                // $sql_cost_matrix_latest_row = 'SELECT wac FROM stock_cost WHERE created_on <= "'.$cut_off_date.'" AND stock_code = "'.$product_data->stock_code.'" ORDER BY created_on DESC, cost_id DESC limit 1';
                $this->db->select('wac');
                $this->db->from('stock_cost');
                $this->db->where('created_on <= "'.$cut_off_date.'" AND stock_code = "'.$product_data->stock_code.'"');
                $this->db->order_by('created_on DESC, cost_id DESC');
                $this->db->limit(1);
                $query = $this->db->get();
                $wac_data = $query->row();
                $stock_cost_wac = $wac_data->wac;
                // get WAC cost for specific product_id from cost matrix - end

                $html .= '<tr>';
                $html .= '<td valign="top">'.$product_data->stock_code.'</td>';
                $html .= '<td valign="top">'.$product_data->billing_description.'</td>';
                $html .= '<td valign="top" style="text-align: center">'.$specific_product_stock_balance.'</td>';
                $html .= '<td valign="top" style="text-align: right">SGD '.number_format($stock_cost_wac, 2).'</td>';
                $cumulative_cost = $stock_cost_wac * $specific_product_stock_balance;
                $html .= '<td valign="top" style="text-align: right">SGD '.number_format($cumulative_cost, 2).'</td>';
                $html .= '</tr>';

                $wac_stock_grand_total += $cumulative_cost;
                ++$i;
            }

            $html .= '<tr><td colspan="5" height="30" style="border: none;"></td></tr>';

            if (isset($_GET['process'])) {
                $ye_status = $this->referesh_ye_closing_tbl($wac_stock_grand_total);
            }

            if ($i > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="border: none; border-top: 2px dotted dimgray; border-bottom: 2px dotted dimgray; text-align: right; font-weight: bold; color: blue;">GRAND TOTAL</td>';
                $html .= '<td colspan="2" style="border: none; border-top: 2px dotted dimgray; border-bottom: 2px dotted dimgray; font-weight: bold; text-align: right">SGD '.number_format($wac_stock_grand_total, 2).'</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="5" style="border: none; color: tomato; text-align: center;">';
                $html .= 'No stock available';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'st_wac_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('stock/reports');
        }
    }

    public function referesh_ye_closing_tbl($closing_stock)
    {
        $ye_data['closing_stock'] = $closing_stock;
        $ye_data['ye_closing_status'] = 'backup';
        $db_status = $this->custom->updateRow('ye_closing', $ye_data, ['process' => 'YE']);

        return $db_status;
    }

    public function get_fifo_valuation($cut_off_date, $process)
    {
        is_logged_in('admin');
        has_permission();

        $html = '';

        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td colspan="4" align="center" style="border: none; color: blue"><h4>FIFO STOCK VALUATION AS AT <span style="color: tomato">'.date('d-m-Y', strtotime($cut_off_date)).'</span></h4></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td colspan="4" align="right" style="border: none;"><strong>Report Date : </strong><span style="font-weight: normal">'.date('d-m-Y').'</span> <hr /></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th>Stock Code</th>';
        $html .= '<th>Description</th>';
        $html .= '<th>Quantity</th>';
        $html .= '<th style="text-align: right">Amount</th>';
        $html .= '</tr>';

        $fifo_stock_grand_total = 0;
        $i = 0;

        $this->db->select('st.product_id');
        $this->db->from('stock as st, master_billing as bm');
        $this->db->where('st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'"');
        $this->db->group_by('st.product_id');
        $this->db->order_by('bm.stock_code', 'ASC');
        $query = $this->db->get();
        $st_list = $query->result();
        foreach ($st_list as $st_record) {
            $product_id = $st_record->product_id;

            // Get product details from billing master
            $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $product_id]);

            // get balance stock in hand for specific product_id for WAC Computation - start
            // $sql = 'SELECT sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity FROM stock WHERE created_on <= "'.$cut_off_date.'" AND product_id = "'.$product_id.'" ';
            $this->db->select('sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity');
            $this->db->from('stock');
            $this->db->where('created_on <= "'.$cut_off_date.'" AND product_id = '.$product_id);
            $query = $this->db->get();
            $st_balance_list = $query->result();
            foreach ($st_balance_list as $st_balance) {
                $specific_product_stock_balance = $st_balance->balance_quantity;
            }
            $stock_balance = $specific_product_stock_balance;
            // get balance stock in hand for specific product_id for WAC Computation - end

            // get details for specific product from cost matrix - start
            $latest_quantity = 0;
            $latest_total_cost = 0;
            if ($stock_balance > 0) {
                // $sql_cost_matrix_latest_row = 'SELECT * FROM stock_cost WHERE created_on <= "'.$cut_off_date.'" AND stock_code = "'.$product_data->stock_code.'" ORDER BY created_on DESC';
                $this->db->select('*');
                $this->db->from('stock_cost');
                $this->db->where('created_on <= "'.$cut_off_date.'" AND stock_code = "'.$product_data->stock_code.'"');
                $this->db->order_by('created_on', 'DESC');
                $query = $this->db->get();
                $st_cost_list = $query->result();
                foreach ($st_cost_list as $st_cost_record) {
                    $latest_quantity = $st_cost_record->quantity;
                    if ($stock_balance <= $st_cost_record) {
                        $latest_total_cost += $stock_balance * $st_cost_record->unit_cost;
                        $stock_balance = 0;
                        break;
                    } else {
                        $latest_total_cost += $latest_quantity * $st_cost_record->unit_cost;
                        $stock_balance += (-1) * $latest_quantity;
                    }
                }
            }
            // get details for specific product from cost matrix - start

            $html .= '<tr>';
            $html .= '<td valign="top">'.$product_data->stock_code.'</td>';
            $html .= '<td valign="top">'.$product_data->billing_description.'</td>';
            $html .= '<td valign="top" style="text-align: center">'.$specific_product_stock_balance.'</td>';
            $html .= '<td valign="top" style="text-align: right">SGD '.number_format($latest_total_cost, 2).'</td>';
            $html .= '</tr>';

            $fifo_stock_grand_total += $latest_total_cost;
            ++$i;
        }

        $html .= '<tr><td colspan="4" height="30" style="border: none;"></td></tr>';

        if ($process != '') {
            $this->referesh_ye_closing_tbl($fifo_stock_grand_total);
        }

        if ($i > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; border-top: 2px dotted dimgray; border-bottom: 2px dotted dimgray; text-align: right; font-weight: bold; color: blue;">GRAND TOTAL</td>';
            $html .= '<td style="border: none; border-top: 2px dotted dimgray; border-bottom: 2px dotted dimgray; font-weight: bold; text-align: right">SGD '.number_format($fifo_stock_grand_total, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4" style="border: none; color: tomato; text-align: center;">';
            $html .= 'No stock available';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function get_fifo_supporting_schedule($cut_off_date, $process)
    {
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td align="center" style="border: none; color: blue"><h4>FIFO VALUATION SUPPORTING SCHEDULE AS AT <span style="color: tomato">'.date('d-m-Y', strtotime($cut_off_date)).'</span></h4></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td align="right" style="border: none;"><strong>Report Date : </strong><span style="font-weight: normal">'.date('d-m-Y').'</span> <hr /></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $fifo_stock_grand_total = 0;
        $i = 0;

        // $sql_product_list = 'SELECT st.product_id FROM stock as st, master_billing as bm WHERE st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'" GROUP BY st.product_id ORDER BY bm.stock_code ASC';
        $this->db->select('st.product_id');
        $this->db->from('stock as st, master_billing as bm');
        $this->db->where('st.product_id = bm.billing_id AND st.created_on <= "'.$cut_off_date.'"');
        $this->db->group_by('st.product_id');
        $this->db->order_by('bm.stock_code', 'ASC');
        $query = $this->db->get();
        $st_list = $query->result();
        foreach ($st_list as $st_record) {
            $product_id = $st_record->product_id;

            // get balance stock in hand for specific product_id for WAC Computation - start
            // $sql = 'SELECT sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity FROM stock WHERE created_on <= "'.$cut_off_date.'" AND product_id = "'.$product_id.'" ';
            $this->db->select('sum(CASE WHEN sign = "+" THEN quantity WHEN sign = "-" THEN -quantity END) AS balance_quantity');
            $this->db->from('stock');
            $this->db->where('created_on <= "'.$cut_off_date.'" AND product_id = "'.$product_id.'"');
            $query = $this->db->get();
            $st_balance_list = $query->result();
            foreach ($st_balance_list as $st_balance_record) {
                $specific_product_stock_balance = $st_balance_record->balance_quantity;
            }
            $stock_in_balance = $specific_product_stock_balance;
            // get balance stock in hand for specific product_id for WAC Computation - end

            // get details for specific product from cost matrix - start
            if ($stock_in_balance > 0) {
                $html .= '<table>';

                // Get product details from billing master
                $product_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $product_id]);

                $html .= '<tr>';
                $html .= '<td colspan="5"><strong>Stock:</strong> '.$product_data->billing_description.' ('.$product_data->stock_code.')</td>';
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<th style="width: 80px">Date</th>';
                $html .= '<th>Reference</th>';
                $html .= '<th style="text-align: right">Quantity</th>';
                $html .= '<th style="text-align: right">Unit Cost</th>';
                $html .= '<th style="text-align: right">Amount</th>';
                $html .= '</tr>';

                $first_in_quantity = 0;
                $first_in_total_cost = 0;
                $i = 0;
                $stock_total_quantity = 0;
                $stock_total_amount = 0;

                $this->db->select('*');
                $this->db->from('stock_cost');
                $this->db->where('created_on <= "'.$cut_off_date.'" AND stock_code = "'.$product_data->stock_code.'"');
                $this->db->order_by('created_on', 'DESC');
                $query = $this->db->get();
                $st_cost_list = $query->result();
                foreach ($st_cost_list as $st_cost_record) {
                    $stock_date = date('d-m-Y', strtotime($st_cost_record->created_on));
                    $stock_reference = $this->custom->getSingleValue('stock', 'ref_no', ['stock_id' => $st_cost_record->stock_id]);
                    $first_in_quantity = $st_cost_record->quantity;
                    if ($stock_in_balance <= $first_in_quantity) {
                        $first_in_total_cost = $stock_in_balance * $st_cost_record->unit_cost;
                        $first_in_quantity = $stock_in_balance;
                        $stock_in_balance = 0;
                    } else {
                        $first_in_total_cost = $first_in_quantity * $st_cost_record->unit_cost;
                        $stock_in_balance += (-1) * $first_in_quantity;
                    }

                    $stock_total_quantity += $first_in_quantity;
                    $stock_total_amount += $first_in_total_cost;

                    $html .= '<tr>';
                    $html .= '<td valign="top">'.$stock_date.'</td>';
                    $html .= '<td valign="top">'.$stock_reference.'</td>';
                    $html .= '<td valign="top" style="text-align: right">'.$first_in_quantity.'</td>';
                    $html .= '<td valign="top" style="text-align: right">'.$st_cost_record->unit_cost.'</td>';
                    $html .= '<td valign="top" style="text-align: right">SGD '.number_format($first_in_total_cost, 2).'</td>';
                    $html .= '</tr>';

                    $fifo_stock_grand_total += $first_in_total_cost;
                    ++$i;
                }

                $html .= '<tr>';
                $html .= '<td valign="top" colspan="2" align="right"><strong>Sub Total</strong></td>';
                $html .= '<td valign="top" style="text-align: right">'.$stock_total_quantity.'</td>';
                $html .= '<td></td>';
                $html .= '<td valign="top" style="text-align: right">SGD '.number_format($stock_total_amount, 2).'</td>';
                $html .= '</tr>';

                $html .= '</table><br /><br />';
            }

            // get details for specific product from cost matrix - start
        }

        if ($process != '') {
            $this->referesh_ye_closing_tbl($fifo_stock_grand_total);
        }

        if ($i > 0) {
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td align="right" style="border: none; border-top: 2px dotted dimgray; border-bottom: 2px dotted dimgray; font-weight: bold;"><span style="color: blue">GRAND TOTAL:</span>      SGD '.number_format($fifo_stock_grand_total, 2).'</td>';
            $html .= '</tr>';
            $html .= '</table>';
        } else {
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<td style="border: none; color: tomato; text-align: center;">';
            $html .= 'No Stock Available';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
        }

        return $html;
    }

    public function print_fifo_support()
    {
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['fifo_cutoff'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['fifo_cutoff']));
            $process = $_GET['process'];

            $html .= $this->get_fifo_supporting_schedule($cut_off_date, $process);

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'st_fifo_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('stock/reports');
        }
    }

    public function print_fifo()
    {
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['fifo_cutoff'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['fifo_cutoff']));
            $process = $_GET['process'];

            $html = $this->get_fifo_valuation($cut_off_date, $process);

            $html .= '<div style="page-break-before: always;">';
            $html .= $this->get_fifo_supporting_schedule($cut_off_date, $process);
            $html .= '</div>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'st_fifo_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('stock/reports');
        }
    }

    public function print_ob()
    {
        if (isset($_GET['ob_type'])) {
            $ob_type = $_GET['ob_type'];
        } else {
            $ob_type = 'C';
        }

        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>Stock Opening Balance</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'stock_open';
        $columns = 'document_date, document_reference';
        $group_by = 'document_date, document_reference';
        $order_by = null;
        $order_by_many = 'document_date ASC, document_reference ASC';
        $where = ['status' => $ob_type];
        if ($_GET['rowID'] !== null) {
            $ob_id = $_GET['rowID'];
            $where = ['stock_ob_id' => $ob_id, 'status' => $ob_type];
        }
        $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6">';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->document_date)).'<br />';
            $html .= '<strong>Reference : </strong>'.$row->document_reference.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'stock_open';
            $columns = null;
            $group_by = null;
            $order_by = 'document_date';
            $where = ['document_date' => $row->document_date, 'document_reference' => $row->document_reference];
            $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $record) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Product</th>';
                    $html .= '<th style="text-align: center">UOM</th>';
                    $html .= '<th style="text-align: center">Quantity</th>';
                    $html .= '<th style="text-align: right">Unit Cost</th>';
                    $html .= '<th style="text-align: center">Sign</th>';
                    $html .= '<th>Remarks</th>';
                    $html .= '</tr>';
                }

                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $record->product_id]);

                $html .= '<tr>';
                $html .= '<td>('.$product_data->stock_code.') '.$product_data->billing_description.'</td>';
                $html .= '<td style="text-align: center">'.$product_data->billing_uom.'</td>';
                $html .= '<td style="text-align: center">'.$record->quantity.'</td>';
                $html .= '<td style="text-align: right">'.number_format($record->unit_cost, 2).'</td>';
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '<td>'.$record->remarks.'</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="6" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'st_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    // data patch print from stock.tbl for opening balance
    public function print_stock_ob()
    {
        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>Stock Opening Balance</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'stock';
        $columns = 'created_on, ref_no';
        $group_by = 'created_on, ref_no';
        $order_by = null;
        $order_by_many = 'created_on ASC, ref_no ASC';
        $where = ['stock_type' => 'OPBAL'];
        if ($_GET['rowID'] !== null) {
            $where = ['stock_id' => $_GET['rowID'], 'stock_type' => 'OPBAL'];
        }
        $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6">';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->created_on)).'<br />';
            $html .= '<strong>Reference : </strong>'.$row->ref_no.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'stock';
            $columns = null;
            $group_by = null;
            $order_by = 'created_on';
            $where = ['created_on' => $row->created_on, 'ref_no' => $row->ref_no];
            $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $record) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Product</th>';
                    $html .= '<th>UOM</th>';
                    $html .= '<th>Quantity</th>';
                    $html .= '<th style="text-align: right">Unit Cost</th>';
                    $html .= '<th style="text-align: center">Sign</th>';
                    $html .= '<th>Remarks</th>';
                    $html .= '</tr>';
                }

                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $record->product_id]);

                $html .= '<tr>';
                $html .= '<td>('.$product_data->stock_code.') '.$product_data->billing_description.'</td>';
                $html .= '<td>'.$product_data->billing_uom.'</td>';
                $html .= '<td>'.$record->quantity.'</td>';
                $html .= '<td style="text-align: right">'.number_format($record->unit_cost, 2).'</td>';
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '<td>'.$record->remark.'</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="6" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'st_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_purchase()
    {
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
        } else {
            $status = 'C';
        }

        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>Stock Purchase</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'stock_purchase';
        $columns = 'document_date, document_reference, supplier_id';
        $group_by = 'document_date, document_reference, supplier_id';
        $order_by = null;
        $order_by_many = 'document_date ASC, document_reference ASC';
        $where = ['status' => $status];
        if ($_GET['rowID'] !== null) {
            $purchase_id = $_GET['rowID'];
            $where = ['purchase_id' => $purchase_id, 'status' => $status];
        }
        $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $supplier_data = $this->custom->getMultiValues('master_supplier', 'name, code', ['supplier_id' => $row->supplier_id]);
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6">';
            $html .= '<strong>Supplier : </strong>('.$supplier_data->code.') '.$supplier_data->name.'<br />';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->document_date)).'<br />';
            $html .= '<strong>Reference : </strong>'.$row->document_reference.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'stock_purchase';
            $columns = null;
            $group_by = null;
            $order_by = 'document_date';
            $where = ['document_date' => $row->document_date, 'document_reference' => $row->document_reference, 'supplier_id' => $row->supplier_id, 'status' => $status];
            $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $record) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Product</th>';
                    $html .= '<th>UOM</th>';
                    $html .= '<th>Quantity</th>';
                    $html .= '<th style="text-align: right">Unit Cost</th>';
                    $html .= '<th style="text-align: center">Sign</th>';
                    $html .= '<th>Remarks</th>';
                    $html .= '</tr>';
                }

                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $record->product_id]);

                $html .= '<tr>';
                $html .= '<td>('.$product_data->stock_code.') '.$product_data->billing_description.'</td>';
                $html .= '<td style="text-align: center">'.$product_data->billing_uom.'</td>';
                $html .= '<td style="text-align: center">'.$record->quantity.'</td>';
                $html .= '<td style="text-align: right">'.number_format($record->unit_cost, 2).'</td>';
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '<td>'.$record->remarks.'</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="6" style="color: red; text-align: center">No Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'st_pr_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_adjustment()
    {
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
        } else {
            $status = 'C';
        }

        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>Stock Adjustment</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'stock_adjustment';
        $columns = 'document_date, document_reference';
        $group_by = 'document_date, document_reference';
        $order_by = null;
        $order_by_many = 'document_date ASC, document_reference ASC';
        $where = ['status' => $status];
        if ($_GET['rowID'] !== null) {
            $id = $_GET['rowID'];
            $where = ['adj_id' => $id, 'status' => $status];
        }
        $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6">';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->document_date)).'<br />';
            $html .= '<strong>Reference : </strong>'.$row->document_reference.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'stock_adjustment';
            $columns = null;
            $group_by = null;
            $order_by = 'document_date';
            $where = ['document_date' => $row->document_date, 'document_reference' => $row->document_reference];
            $query = $this->stock_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $record) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Product</th>';
                    $html .= '<th>UOM</th>';
                    $html .= '<th>Quantity</th>';
                    $html .= '<th style="text-align: center">Sign</th>';
                    $html .= '<th>Remarks</th>';
                    $html .= '</tr>';
                }

                $product_data = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $record->product_id]);

                $html .= '<tr>';
                $html .= '<td>('.$product_data->stock_code.') '.$product_data->billing_description.'</td>';
                $html .= '<td style="text-align: center">'.$product_data->billing_uom.'</td>';
                $html .= '<td style="text-align: center">'.$record->quantity.'</td>';
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '<td>'.$record->remarks.'</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="6" style="color: red; text-align: center">No adjustment transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'st_adj_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'stock_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['stock_open', 'stock_adjustment', 'stock_purchase', 'stock', 'stock_cost'],
            'format' => 'sql',           // sql, txt
            'filename' => $file_name,      // File name
            'add_drop' => true,            // Whether to add DROP TABLE statements to backup file
            'add_insert' => true,            // Whether to add INSERT data to backup file
            'newline' => "\n",             // Newline character used in backup file
        ];

        $backup = $CI->dbutil->backup($prefs);
        // Backup your entire database and assign it to a variable

        // Load the file helper and write the file to your server
        $CI->load->helper('file');
        write_file(FCPATH.'/assets/database_backups/'.$file_name, $backup);

        // Load the download helper and send the file to your desktop
        $CI->load->helper('download');
        force_download($file_name, $backup);
    }

    public function df_restore($action = 'form')
    {
        is_logged_in('admin');        
        $data = file_upload(date('YmdHis'), 'db_file', 'database_restore_files');
        $this->load->helper('file');
        if ($data['status']) {
            $sql_file = $data['upload_data']['full_path'];

            $search_str = [' ; ', 'com;', 'sg;'];
            $replace_str = [' : ', 'com:', 'sg:'];

            $query_list = explode(';', str_replace($search_str, $replace_str, read_file($sql_file)));

            // This foreign key check was disabled for 1 table referred by 2 tables
            // Cannot delete or update a parent row: a foreign key constraint fails # # TABLE STRUCTURE FOR: groups # DROP TABLE IF EXISTS `groups`
            $this->db->query('SET foreign_key_checks = 0');

            foreach ($query_list as $query) {
                $query = trim($query);
                if ($query != '') {
                    $this->db->query($query);
                }
            }
            $this->db->query('SET foreign_key_checks = 1');
            set_flash_message('message', 'success', 'STOCK RESTORED');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('stock/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'stock/blank.php';
        zapStock();
        redirect('stock/', 'refresh');
    }
}
