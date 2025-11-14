<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_payable extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_payable/accounts_payable_model', 'ap_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('supplier_code');
        $this->db->from('accounts_payable');
        $this->db->where(['tran_type' => 'OPBAL']);
        $this->db->group_by('supplier_code');
        $this->db->order_by('supplier_code', 'ASC');
        $query = $this->db->get();
        $suppliers = $query->result();
        $options = "<option value=''>-- Select --</option>";
        foreach ($suppliers as $value) {
            $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $value->supplier_code]);
            $options .= "<option value='".$value->supplier_code."'>";
            $options .= '('.$value->supplier_code.') '.$name;
            $options .= '</option>';
        }
        $this->body_vars['suppliers'] = $options;
        $this->body_file = 'accounts_payable/options.php';
    }

    public function reports()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ' '], ['active' => 1]);

        $table = 'accounts_payable';
        $columns = 'currency';
        $where = ['offset' => 'n'];
        $group_by = 'currency';
        $order_by = null;
        $order_by_many = null;
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $list = $query->result();
        $options = '<option value="">Select</option>';
        foreach ($list as $record) {
            $currency_description = $this->custom->getSingleValue('ct_currency', 'description', ['code' => $record->currency]);
            $options .= '<option value="'.$record->currency.'">'.$record->currency.' : '.$currency_description.'</option>';
        }

        $options .= '<option value="all">All Currencies</option>';
        $this->body_vars['currencies'] = $options;
    }

    public function ap_ob_listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function batch_ob_listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function batch_ob_create()
    {
        is_logged_in('admin');
        has_permission();

        $this->body_vars['supplier_options'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ' '], ['active' => 1]);
        $this->body_vars['save_url'] = 'accounts_payable/save_batch_ob';
    }

    public function batch_ob_edit($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['supplier_id'] = $row_id;
            $this->body_vars['supplier_options'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['supplier_id' => $row_id]);

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $row_id]);
            $currency_data = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);

            $this->body_vars['supplier_currency'] = $currency_data->code;
            $this->body_vars['currency_rate'] = $currency_data->rate;

            $this->body_vars['save_url'] = '/accounts_payable/save_batch_ob';
        }
    }

    public function save_batch_ob()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $supplier_id = $post['supplier_id'];
            $total_items = count($post['entry_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['supplier_id'] = $supplier_id;
                $batch_data['document_date'] = date('Y-m-d', strtotime($post['doc_date'][$i]));
                $batch_data['document_reference'] = $post['ref_no'][$i];
                $batch_data['foreign_amount'] = $post['foreign_amount'][$i];
                $batch_data['local_amount'] = $post['local_amount'][$i];
                $batch_data['sign'] = $post['entry'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];

                $ap_ob_id = $post['entry_id'][$i];
                $updated[] = $this->custom->updateRow('ap_open', $batch_data, ['ap_ob_id' => $ap_ob_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Batch Saved');
            }
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('accounts_payable/batch_ob_listing');
    }

    public function manage_batch_ob($row_id = '') {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['page'] = 'edit';

            $this->body_vars['supplier_id'] = $row_id;
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['supplier_id' => $row_id]);

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $row_id]);
            $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
            $this->body_vars['supplier_currency'] = $currency->code;
            $this->body_vars['currency_rate'] = $currency->rate;

        } else {
            $this->body_vars['page'] = 'new';

            $this->body_vars['supplier_id'] = '';
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ' '], ['active' => 1]);

            $this->body_vars['supplier_currency'] = '';
            $this->body_vars['currency_rate'] = '';
        }

        $this->body_vars['save_url'] = '/accounts_payable/save_batch_ob';
    }

    // data patch opening balance transactions in accounts_payable.TBL
    public function data_patch()
    {
        is_logged_in('admin');
        has_permission();

        $post = $this->input->post();
        if ($post) {    
            
            $this->body_vars['supplier_code'] = $post['supplier'];
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['code' => $post['supplier']]);

            $this->body_vars['ref_no'] = '';
            if($post['ref_no'] != '') {
                $this->body_vars['ref_no'] = $post['ref_no'];
            }

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['code' => $post['supplier']]);
            $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
            $this->body_vars['supplier_currency'] = $currency->code;
            $this->body_vars['currency_rate'] = $currency->rate;

            $default_currency = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);
            $this->body_vars['default_currency'] = $default_currency;

            $this->body_vars['save_url'] = '/accounts_payable/save_patched_data';
        }
    }

    // datapatch Opening Blanace Tranasctions in AP.TBL
    public function save_patched_data()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $supplier_code = $post['supplier'];
            $total_items = count($post['entry_id']);

            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $ob_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date'][$i]));
                $ob_data['doc_ref_no'] = $post['ref_no'][$i];
                $ob_data['fa_amt'] = $post['foreign_amount'][$i];
                $ob_data['total_amt'] = $post['local_amount'][$i];
                $ob_data['sign'] = $post['sign'][$i];
                $ob_data['remarks'] = $post['remarks'][$i];

                $ap_id = $post['entry_id'][$i];
                $updated[] = $this->custom->updateRow('accounts_payable', $ob_data, ['supplier_code' => $supplier_code, 'ap_id' => $ap_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Transactions Saved');
            }
            
        } else {
            set_flash_message('message', 'danger', 'Post Error');
        }
        redirect('accounts_payable/');
    }

    public function ob_datapatch()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/ob_datapatch_list.php';
    }

    public function opening_balance_options()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/opening_balance_options.php';
    }

    public function current_statement()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['supplier_options'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ' '], ['active' => 1]);
        $this->body_file = 'accounts_payable/creditor_current_statement.php';
    }

    public function print_statement()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $supplier_id = $post['supplier'];

            $html = '<table style="width: 100%">';
            $html .= '<tr>
                            <td style="border: none; padding-left: 0">
                                '.$this->custom->populateCompanyHeader().'
                            </td>
                        
                            <td style="border: none; text-align: right; vertical-align: bottom">
                                <br /><h4>CREDITOR STATEMENT</h4> <br /><br /><br />
                                <strong>Date :</strong> '.date('d/m/Y').'</td>
                        </tr>';
            $html .= '</table> <hr /> <br />';

            // supplier details
            $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $supplier_id]);

            $html .= '<table class="table" width="100%">
                        <tr>
                            <td style="text-align: left; border: none; padding-left: 0">
                                '.$supplier->name.' ('.$supplier->code.') <br />
                                '.$this->custom->populateSupplierAddress($supplier).'
                            </td>
                        </tr>
                    </table>';

            // table of statements
            $html .= '<table class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 85px;">Date</th>
                        <th style="width: 110px;">Reference</th>
                        <th style="width: 300px;">Remarks</th>
                        <th style="width: 120px; text-align: right">Debit</th>
                        <th style="width: 120px; text-align: right">Credit</th>
                        <th style="width: 145px; text-align: right">Balance</th>
                    </tr>
                </thead>
                <tbody>';

            $current_amount = 0;
            $balance_amount = 0;

            $entries = 0;
            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier->code, 'offset' => 'n'];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = null;
            $order_by_many = 'doc_date ASC, doc_ref_no ASC';
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();
            foreach ($list as $record) {
                $current_amount = $record->total_foreign_amount;
                $document_date = date('d-m-Y', strtotime($record->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$record->original_doc_ref.'</td>';
                $html .= '<td>'.$record->remarks.'</td>';
                if ($record->sign == '+') {
                    $balance_amount += $current_amount;
                    $html .= '<td style="text-align: right">'.number_format($current_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($record->sign == '-') {
                    $balance_amount -= $current_amount;
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($current_amount, 2).'</td>';
                }

                if ($balance_amount >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($balance_amount, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format(abs($balance_amount), 2).' (CR)</td>';
                }

                $html .= '</tr>';
                ++$entries;
            }

            if($entries == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="text-align: center; color: red">No Transactions</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $file = 'creditor_'.date('YmdHis').'.pdf';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $this->custom->printMPDF($file, $document);
            
        } else {
            redirect('/accounts_payable/reports');
        }
    }

    public function print_historical_statement()
    {
        is_logged_in('admin');
        has_permission();

        $post = $this->input->post();
        if ($post) {
            $from_date = date('Y-m-d', strtotime($post['from_date']));
            $to_date = date('Y-m-d', strtotime($post['to_date']));
            $supplier_id = $post['supplier_id'];

            $html = $this->custom->populateMPDFStyle();

            $html .= '<table style="width: 100%">';
            $html .= '<tr>
                            <td style="border: none;">
                                '.$this->custom->populateCompanyHeader().'
                            </td>
                        
                            <td style="border: none; text-align: right; vertical-align: bottom">
                                <br /><h4>HISTORICAL CREDITOR STATEMENT</h4> <br /><br /><br />
                                <strong>Period: </strong> '.$post['from_date'].' <i>to</i> '.$post['to_date'].'</td>
                        </tr>';
            $html .= '</table> <hr /> <br />';

            $html .= '<table width="100%"><tr><td style="text-align: left; border: none">';

            // supplier Details
            $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $supplier_id]);
            $html .= '<strong>To,</strong>';
            $html .= '<br />'.$supplier->name.' ('.$supplier->code.')<br />';
            $html .= $this->custom->populateSupplierAddress($supplier);

            // report date
            $html .= '</td><td valign="bottom" width="160" style="text-align: left; border: none">';
            $html .= '<br /><strong>Date: </strong>'.date('d-m-Y');
            $html .= '</td></tr></table>';

            // Balance block forward - starts
            $balance_bf_lum_sum = 0;
            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier->code, 'doc_date <' => $from_date];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = 'doc_date';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();
            foreach ($list as $record) {
                if ($record->sign == '+') {
                    $balance_bf_lum_sum += $record->total_foreign_amount;
                } elseif ($record->sign == '-') {
                    $balance_bf_lum_sum -= $record->total_foreign_amount;
                }
            }

            $html .= '<br /><table style="width: 100%;">
				<thead>
					<tr>
						<th style="width: 85px;">Date</th>
						<th style="width: 110px;">Reference</th>
						<th style="width: 120px;">Transaction</th>
						<th style="width: 150px; text-align: right">Debit</th>
						<th style="width: 150px; text-align: right">Credit</th>
						<th style="width: 180px; text-align: right">Balance</th>
					</tr>
				</thead>
				<tbody>';

            $current_amount = 0;
            $balance_amount = 0;

            $html .= '<tr>';
            $html .= '<td>'.date('d-m-Y', strtotime($from_date)).'</td>';
            $html .= '<td>Balance B/F</td>';
            $html .= '<td>OPBAL B/F</td>';
            if ($balance_bf_lum_sum > 0) {
                $html .= '<td style="text-align: right">'.number_format($balance_bf_lum_sum, 2).'</td>';
                $html .= '<td></td>';
            } elseif ($balance_bf_lum_sum < 0) {
                $html .= '<td></td>';
                $html .= '<td style="text-align: right">'.number_format((-1) * $balance_bf_lum_sum, 2).'</td>';
            } else {
                $html .= '<td style="text-align: right">0.00</td>';
                $html .= '<td></td>';
            }

            if ($balance_bf_lum_sum >= 0) {
                $html .= '<td style="text-align: right">'.number_format($balance_bf_lum_sum, 2).' (DR)</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format(abs($balance_bf_lum_sum), 2).' (CR)</td>';
            }
            $html .= '</tr>';
            // Balance block forward - ends

            // other transactions - starts
            $i = 0;
            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier->code, 'doc_date >= ' => $from_date, 'doc_date <= ' => $to_date];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = 'doc_date';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();            
            foreach ($list as $record) {
                $current_amount = $record->total_foreign_amount;
                $document_date = date('d-m-Y', strtotime($record->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$record->original_doc_ref.'</td>';
                $html .= '<td>'.$record->tran_type.'</td>';
                if ($record->sign == '+') {
                    $balance_amount += $current_amount;
                    $html .= '<td style="text-align: right">'.number_format($current_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($record->sign == '-') {
                    $balance_amount -= $current_amount;
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($current_amount, 2).'</td>';
                }

                if ($i == 0) {
                    if ($balance_bf_lum_sum >= 0) {
                        $balance_amount += $balance_bf_lum_sum;
                    } else {
                        $balance_amount -= (-1) * $balance_bf_lum_sum;
                    }
                }

                if ($balance_amount >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($balance_amount, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format(abs($balance_amount), 2).' (CR)</td>';
                }

                ++$i;

                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            
            $file = 'hist_creditor_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $html);

        } else {
            redirect('/accounts_payable/reports');
        }
    }

    public function current_listing()
    {
        is_logged_in('admin');
        has_permission();

        $table = 'accounts_payable';
        $columns = 'currency';
        $where = ['offset' => 'n'];
        $group_by = 'currency';
        $order_by = null;
        $order_by_many = null;
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $list = $query->result();
        $options = '<option value="">Select</option>';
        foreach ($list as $record) {
            $currency_description = $this->custom->getSingleValue('ct_currency', 'description', ['code' => $record->currency]);
            $options .= '<option value="'.$record->currency.'">'.$record->currency.' : '.$currency_description.'</option>';
        }

        $options .= '<option value="all">All Currencies</option>';

        $this->body_vars['currency_options'] = $options;
        $this->body_file = 'accounts_payable/creditor_current_listing.php';
    }

    public function get_current_listing($currency)
    {
        $default_currency = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

        $amount_debit_grandtotal = 0;
        $amount_credit_grandtotal = 0;
        $amount_foreign_grandtotal = 0;
        $amount_local_grandtotal = 0;

        $grand_total = 0;

        // currency and report date
        $html .= '<table style="width: 100%; border: none;">';
        $html .= '<tr>';
        $html .= '<td style="padding: 7px; border: none"><strong>Currency: <strong><span style="color: red">'.$currency.'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table class="table" style="width: 100%;">';

        $i = 0;

        // supplier code list by currency selected
        $table = 'accounts_payable';
        $columns = 'supplier_code';
        $where = ['currency' => $currency, 'offset' => 'n'];
        $group_by = 'supplier_code';
        $order_by = 'supplier_code';
        $order_by_many = null;
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $list = $query->result();
        foreach ($list as $supplier_record) {
            // supplier details
            $supplier_data = $this->custom->getSingleRow('master_supplier', ['code' => $supplier_record->supplier_code]);

            // currency details
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $supplier_data->currency_id]);
            $supplier_currency_code = $currency_data->code;
            $supplier_currency_rate = $currency_data->rate;

            $html .= '<tr><td colspan="6" height="20" style="border: none"></td></tr>';

            // display supplier details - d-none row added to fix the font issues
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none">';
            $html .= '<strong>Supplier Name & Code : </strong>';
            $html .= $supplier_data->name.' ('.$supplier_data->code.')';
            if ($supplier_data->contact_person !== '') {
                $html .= '<br /><strong>Contact Person : </strong>';
                $html .= $supplier_data->contact_person;
                $html .= '<br />';
            }
            if ($supplier_data->phone !== '') {
                $html .= '<strong>Phone : </strong>';
                $html .= $supplier_data->phone;
                $html .= '<br />';
            }
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>
                            <th style="width: 100px">Date</th>
                            <th style="width: 120px">Reference</th>
                            <th style="width: 170px; text-align: right">Debit</th>
                            <th style="width: 170px; text-align: right">Credit</th>
                            <th style="width: 170px; text-align: right"><span style="color: dimgray">'.$currency.'</span> Balance</th>
                            <th style="width: 170px; text-align: right"><span style="color: dimgray">'.$default_currency.'</span> Balance</th>
                        </tr>';

            $foreign_amount_subtotal = 0;
            $local_amount_subtotal = 0;
            $foreign_amount_debit = 0;
            $foreign_amount_credit = 0;

            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier_record->supplier_code, 'offset' => 'n'];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = null;
            $order_by_many = 'doc_date ASC, doc_ref_no ASC';
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $stmt_list = $query->result();
            foreach ($stmt_list as $stmt_record) {
                $document_date = implode('/', array_reverse(explode('-', $stmt_record->doc_date)));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$stmt_record->original_doc_ref.'</td>';
                if ($stmt_record->sign == '+') {
                    $foreign_amount_debit += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $foreign_amount_subtotal += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $local_amount_subtotal += number_format($stmt_record->total_local_amount, 2, '.', '');

                    $html .= '<td style="text-align: right">'.number_format($stmt_record->total_foreign_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($stmt_record->sign == '-') {
                    $foreign_amount_credit += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $foreign_amount_subtotal -= number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $local_amount_subtotal -= number_format($stmt_record->total_local_amount, 2, '.', '');
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($stmt_record->total_foreign_amount, 2).'</td>';
                }

                $html .= '<td style="text-align: right">';
                if ($foreign_amount_subtotal >= 0) {
                    $html .= number_format($foreign_amount_subtotal, 2).' (DR)';
                } else {
                    $html .= number_format(abs($foreign_amount_subtotal), 2).' (CR)';
                }
                $html .= '</td>';

                $html .= '<td style="text-align: right">';
                if ($local_amount_subtotal >= 0) {
                    $html .= number_format($local_amount_subtotal, 2).' (DR)';
                } else {
                    $html .= number_format(abs($local_amount_subtotal), 2).' (CR)';
                }
                $html .= '</td>';

                $html .= '</tr>';
                ++$i;
            } // statement per supplier loops ends

            // sub total section
            $html .= '<tr class="sub-total">';
            $html .= '<td colspan="2" style="border: none !important; color: red; text-align: right; letter-spacing: 1px;">Sub Total</td>';

            // Sub Total - Column : Debit
            $html .= '<td style="text-align: right;"><span style="color: brown">'.$currency.'</span> '.number_format($foreign_amount_debit, 2).'</td>';

            // Sub Total - Column : Credit
            $html .= '<td style="text-align: right"><span style="color: brown">'.$currency.'</span> '.number_format($foreign_amount_credit, 2).'</td>';

            // Sub Total - Column : Balance
            $html .= '<td style="text-align: right"><span style="color: brown">'.$currency.'</span>  ';
            if ($foreign_amount_subtotal >= 0) {
                $html .= number_format($foreign_amount_subtotal, 2).' (DR)';
            } else {
                $html .= number_format(abs($foreign_amount_subtotal), 2).' (CR)';
            }
            $html .= '</td>';

            // Sub Total - Column : Balance (SGD)
            $html .= '<td style="text-align: right"><span style="color: brown">'.$default_currency.'</span> ';
            if ($local_amount_subtotal >= 0) {
                $html .= number_format($local_amount_subtotal, 2).' (DR)';
            } else {
                $html .= number_format(abs($local_amount_subtotal), 2).' (CR)';
            }
            $html .= '</td>';
            $html .= '</tr>'; // row ends

            $amount_debit_grandtotal += $foreign_amount_debit;
            $amount_credit_grandtotal += $foreign_amount_credit;
            $amount_foreign_grandtotal += $foreign_amount_subtotal;
            $amount_local_grandtotal += $local_amount_subtotal;

        } // supplier loop ends

        // total currency creditors
        $html .= '<tr><td colspan="6" height="20" style="border: none"></td></tr>';

        $html .= '<tr>';
        $html .= '<td colspan="2" style="width: 220px; color: blue; text-align: right; padding-right: 5px; border: none">TOTAL '.$currency.' CREDITORS</td>';

        // Grand Total - Column : Debit
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> '.number_format($amount_debit_grandtotal, 2).'</td>';

        // Grand Total - Column : Credit
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> '.number_format($amount_credit_grandtotal, 2).'</td>';

        // Grand Total - Column : Balance
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> ';
        if ($amount_foreign_grandtotal >= 0) {
            $html .= number_format($amount_foreign_grandtotal, 2).' (DR)';
        } else {
            $html .= number_format(abs($amount_foreign_grandtotal), 2).' (CR)';
        }
        $html .= '</td>';

        // Grand Total - Column : Balance (SGD)
        $html .= '<td style="text-align: right; border: none"><strong>'.$default_currency.'</strong> ';
        if ($amount_local_grandtotal >= 0) {
            $html .= number_format($amount_local_grandtotal, 2).' (DR)';
        } else {
            $html .= number_format(abs($amount_local_grandtotal), 2).' (CR)';
        }
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr><td colspan="6" height="20" style="border: none"><hr /></td></tr>';
        
        $html .= '</table>';

        if ($amount_local_grandtotal > 0) {
            $grand_total += $amount_local_grandtotal;
        } else {
            $grand_total -= $amount_local_grandtotal;
        }

        $data['grand_total'] = $grand_total;
        $data['html_document'] = $html;

        return $data;
    }

    // current statement by currency
    public function print_listing()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $currency = $post['currency'];

            $html = '<table style="width: 100%">';
            $html .= '<tr>
                                        <td style="border: none;">
                                            '.$this->custom->populateCompanyHeader().'
                                        </td>
                                    
                                        <td style="border: none; text-align: right; vertical-align: bottom">
                                            <br /><h4>CREDITORS LISTING</h4> <br /><br /><br />
                                        <strong>Date :</strong> '.date('d/m/Y').'</td>
                                    </tr>';
            $html .= '</table> <hr />';

            $entry = 0;
            $grand_total = 0;
            $table = 'accounts_payable';
            $columns = 'currency';
            $where = ['offset' => 'n'];
            if ($currency !== '' && $currency !== 'all') { // print / display by currency
                $where = ['offset' => 'n', 'currency' => $currency];
            }
            $group_by = 'currency';
            $order_by = 'currency';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $currencies = $query->result();
            foreach ($currencies as $record) {
                $data = $this->get_current_listing($record->currency);
                $html .= $data['html_document'];
                $grand_total += $data['grand_total'];

                // atleat one transaction should be not offsetted for this debtor, only then debtor details and entries will be displayed otherwise it no use to display the details
                $entry += $this->custom->getCount('accounts_payable', ['offset' => 'n', 'currency' => $record->currency]);
            }

            if ($currency == 'all') {
                $default_currency = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);
                $html .= '<table class="table" style="width: 100%; font-weight: bold">';                
                $html .= '<tr>';
                $html .= '<td style="color: blue; text-align: right; border: none">GRAND TOTAL</td>';
                $html .= '<td style="text-align: right; width: 200px; border: none">'.$default_currency.' '.number_format($grand_total, 2).'</td>';
                $html .= '</tr>';
                $html .= '</table>';
            }

            $file = 'creditor_listing_'.date('YmdHis').'.pdf';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $this->custom->printMPDF($file, $document);
            
        } else {
            redirect('accounts_payable/reports');
        }
    }

    public function get_historical_listing($currency, $from_date, $to_date)
    {
        $default_currency = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

        $amount_debit_grandtotal = 0;
        $amount_credit_grandtotal = 0;
        $amount_foreign_grandtotal = 0;
        $amount_local_grandtotal = 0;

        $grand_total = 0;

        // currency and report date
        $html = '<table style="width: 100%; border: none;">';
        $html .= '<tr>';
        $html .= '<td style="padding: 7px; border: none;"><strong>Currency: <strong><span style="color: red">'.$currency.'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table class="table" style="width: 100%;">';       

        // supplier code list by currency selected
        $i = 0;
        $table = 'accounts_payable';
        $columns = 'supplier_code';
        $where = ['currency' => $currency, 'doc_date >= ' => $from_date, 'doc_date <= ' => $to_date];
        $group_by = 'supplier_code';
        $order_by = 'supplier_code';
        $order_by_many = null;
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $supplier_list = $query->result();
        foreach ($supplier_list as $supplier_record) {
            // supplier details
            $supplier_data = $this->custom->getSingleRow('master_supplier', ['code' => $supplier_record->supplier_code]);

            // currency details
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $supplier_data->currency_id]);
            $supplier_currency_code = $currency_data->code;
            $supplier_currency_rate = $currency_data->rate;

            $html .= '<tr><td colspan="6" height="20" style="border: none"></td></tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none">';
            $html .= '<strong>Supplier Name & Code : </strong>';
            $html .= $supplier_data->name.' ('.$supplier_record->supplier_code.')';
            if ($supplier_data->contact_person !== '') {
                $html .= '<br /><strong>Contact Person : </strong>';
                $html .= $supplier_data->contact_person;
                $html .= '<br />';
            }
            if ($supplier_data->phone !== '') {
                $html .= '<strong>Phone : </strong>';
                $html .= $supplier_data->phone;
                $html .= '<br />';
            }
            $html .= '</td>';
            $html .= '</tr>';

            // Balance block forward - starts
            $foreign_balance_bf_lum_sum = 0;
            $local_balance_bf_lum_sum = 0;

            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier_data->code, 'doc_date <' => $from_date];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = 'doc_date';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();
            foreach ($list as $record) {
                if ($record->sign == '+') {
                    $foreign_balance_bf_lum_sum += $record->total_foreign_amount;
                    $local_balance_bf_lum_sum += $record->total_local_amount;
                } elseif ($record->sign == '-') {
                    $foreign_balance_bf_lum_sum -= $record->total_foreign_amount;
                    $local_balance_bf_lum_sum -= $record->total_local_amount;
                }
            }

            $html .= '<tr>
                        <th style="width: 100px">Date</th>
                        <th style="width: 120px">Reference</th>
                        <th style="width: 170px; text-align: right">Debit</th>
                        <th style="width: 170px; text-align: right">Credit</th>
                        <th style="width: 170px; text-align: right"><span style="color: dimgray">'.$currency.'</span> Balance</th>
                        <th style="width: 170px; text-align: right"><span style="color: dimgray">'.$default_currency.'</span> Balance</th>
                    </tr>';
            $current_amount = 0;
            $balance_amount = 0;

            $foreign_amount_subtotal = 0;
            $local_amount_subtotal = 0;
            $foreign_amount_debit = 0;
            $foreign_amount_credit = 0;

            $html .= '<tr>';
            $html .= '<td>'.date('d/m/Y', strtotime($from_date)).'</td>';
            $html .= '<td>Balance B/F</td>';
            if ($foreign_balance_bf_lum_sum > 0) {
                $html .= '<td style="text-align: right">'.number_format($foreign_balance_bf_lum_sum, 2).'</td>';
                $html .= '<td></td>';
            } elseif ($foreign_balance_bf_lum_sum < 0) {
                $html .= '<td></td>';
                $html .= '<td style="text-align: right">'.number_format((-1) * $foreign_balance_bf_lum_sum, 2).'</td>';
            } else {
                $html .= '<td style="text-align: right">0.00</td>';
                $html .= '<td></td>';
            }

            if ($foreign_balance_bf_lum_sum >= 0) {
                $html .= '<td style="text-align: right">'.number_format($foreign_balance_bf_lum_sum, 2).' (DR)</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format(abs($foreign_balance_bf_lum_sum), 2).' (CR)</td>';
            }

            if ($local_balance_bf_lum_sum >= 0) {
                $html .= '<td style="text-align: right">'.number_format($local_balance_bf_lum_sum, 2).' (DR)</td>';
            } else {
                $html .= '<td style="text-align: right">'.number_format(abs($local_balance_bf_lum_sum), 2).' (CR)</td>';
            }

            $html .= '</tr>';
            // Balance block forward - ends

            $i = 0;
            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['supplier_code' => $supplier_record->supplier_code, 'doc_date >= ' => $from_date, 'doc_date <= ' => $to_date];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = null;
            $order_by_many = 'doc_date ASC, doc_ref_no ASC';
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $stmt_list = $query->result();            
            foreach ($stmt_list as $stmt_record) {
                $document_date = implode('/', array_reverse(explode('-', $stmt_record->doc_date)));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$stmt_record->original_doc_ref.'</td>';
                if ($stmt_record->sign == '+') {
                    $foreign_amount_debit += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $foreign_amount_subtotal += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $local_amount_subtotal += number_format($stmt_record->total_local_amount, 2, '.', '');

                    $html .= '<td style="text-align: right">'.number_format($stmt_record->total_foreign_amount, 2).'</td>';
                    $html .= '<td></td>';
                } elseif ($stmt_record->sign == '-') {
                    $foreign_amount_credit += number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $foreign_amount_subtotal -= number_format($stmt_record->total_foreign_amount, 2, '.', '');
                    $local_amount_subtotal -= number_format($stmt_record->total_local_amount, 2, '.', '');
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($stmt_record->total_foreign_amount, 2).'</td>';
                }

                // adding Opening Balance B/F Amount
                if ($i == 0) {
                    if ($foreign_balance_bf_lum_sum >= 0) {
                        $foreign_amount_subtotal += $foreign_balance_bf_lum_sum;
                    } else {
                        $foreign_amount_subtotal -= (-1) * $foreign_balance_bf_lum_sum;
                    }

                    if ($local_balance_bf_lum_sum >= 0) {
                        $local_amount_subtotal += $local_balance_bf_lum_sum;
                    } else {
                        $local_amount_subtotal -= (-1) * $local_balance_bf_lum_sum;
                    }
                }

                $html .= '<td style="text-align: right">';
                if ($foreign_amount_subtotal >= 0) {
                    $html .= number_format($foreign_amount_subtotal, 2).' (DR)';
                } else {
                    $html .= number_format(abs($foreign_amount_subtotal), 2).' (CR)';
                }
                $html .= '</td>';

                $html .= '<td style="text-align: right">';
                if ($local_amount_subtotal >= 0) {
                    $html .= number_format($local_amount_subtotal, 2).' (DR)';
                } else {
                    $html .= number_format(abs($local_amount_subtotal), 2).' (CR)';
                }
                $html .= '</td>';

                $html .= '</tr>';

                ++$i;
            } // statement per supplier loops ends

            if ($foreign_balance_bf_lum_sum >= 0) {
                $foreign_amount_debit += $foreign_balance_bf_lum_sum;
            } else {
                $foreign_amount_credit += (-1) * $foreign_balance_bf_lum_sum;
            }

            // adding Opening Balance B/F Amount (if No statements)
            if ($i == 0) {
                if ($foreign_balance_bf_lum_sum >= 0) {
                    $foreign_amount_subtotal += $foreign_balance_bf_lum_sum;
                } else {
                    $foreign_amount_subtotal -= (-1) * $foreign_balance_bf_lum_sum;
                }

                if ($local_balance_bf_lum_sum >= 0) {
                    $local_amount_subtotal += $local_balance_bf_lum_sum;
                } else {
                    $local_amount_subtotal -= (-1) * $local_balance_bf_lum_sum;
                }
            }

            // sub total section
            $html .= '<tr class="sub-total">';
            $html .= '<td colspan="2" style="border: none !important; color: red; text-align: right; letter-spacing: 1px;">Sub Total</td>';

            // Sub Total - Column : Debit
            $html .= '<td style="text-align: right;"><span style="color: brown">'.$currency.'</span> '.number_format($foreign_amount_debit, 2).'</td>';

            // Sub Total - Column : Credit
            $html .= '<td style="text-align: right"><span style="color: brown">'.$currency.'</span> '.number_format($foreign_amount_credit, 2).'</td>';

            // Sub Total - Column : Balance
            $html .= '<td style="text-align: right"><span style="color: brown">'.$currency.'</span>  ';
            if ($foreign_amount_subtotal >= 0) {
                $html .= number_format($foreign_amount_subtotal, 2).' (DR)';
            } else {
                $html .= number_format(abs($foreign_amount_subtotal), 2).' (CR)';
            }
            $html .= '</td>';

            // Sub Total - Column : Balance (SGD)
            $html .= '<td style="text-align: right"><span style="color: brown">'.$default_currency.'</span> ';
            if ($local_amount_subtotal >= 0) {
                $html .= number_format($local_amount_subtotal, 2).' (DR)';
            } else {
                $html .= number_format(abs($local_amount_subtotal), 2).' (CR)';
            }
            $html .= '</td>';

            $html .= '</tr>'; // row ends

            $amount_debit_grandtotal += $foreign_amount_debit;
            $amount_credit_grandtotal += $foreign_amount_credit;
            $amount_foreign_grandtotal += $foreign_amount_subtotal;
            $amount_local_grandtotal += $local_amount_subtotal;

        } // supplier loop ends

        $html .= '<tr><td colspan="6" height="20" style="border: none"></td></tr>';

        // total currency debtors
        $html .= '<tr>';
        $html .= '<td colspan="2" style="width: 220px; color: blue; text-align: right; padding-right: 5px; border: none">TOTAL '.$currency.' DEBTORS</td>';

        // Grand Total - Column : Debit
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> '.number_format($amount_debit_grandtotal, 2).'</td>';

        // Grand Total - Column : Credit
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> '.number_format($amount_credit_grandtotal, 2).'</td>';

        // Grand Total - Column : Balance
        $html .= '<td style="text-align: right; border: none"><strong>'.$currency.'</strong> ';
        if ($amount_foreign_grandtotal >= 0) {
            $html .= number_format($amount_foreign_grandtotal, 2).' (DR)';
        } else {
            $html .= number_format(abs($amount_foreign_grandtotal), 2).' (CR)';
        }
        $html .= '</td>';

        // Grand Total - Column : Balance (SGD)
        $html .= '<td style="text-align: right; border: none"><strong>'.$default_currency.'</strong> ';
        if ($amount_local_grandtotal >= 0) {
            $html .= number_format($amount_local_grandtotal, 2).' (DR)';
        } else {
            $html .= number_format(abs($amount_local_grandtotal), 2).' (CR)';
        }
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '<tr><td colspan="6" height="20" style="border: none"><hr /></td></tr>';
        
        $html .= '</table>';

        if ($amount_local_grandtotal > 0) {
            $grand_total += $amount_local_grandtotal;
        } else {
            $grand_total -= $amount_local_grandtotal;
        }

        $data['grand_total'] = $grand_total;
        $data['html_document'] = $html;

        return $data;
    }

    public function print_historical_listing()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $from_date = date('Y-m-d', strtotime($post['hl_from_date']));
            $to_date = date('Y-m-d', strtotime($post['hl_to_date']));
            $currency = $post['hl_currency'];

            $html = '<table style="width: 100%">';
            $html .= '<tr>
                                        <td style="border: none;">
                                            '.$this->custom->populateCompanyHeader().'
                                        </td>
                                    
                                        <td style="border: none; text-align: right; vertical-align: bottom">
                                            <br /><h4>HISTORICAL CREDITOR LISTING</h4> <br /><br /><br />
                                        <strong>Date :</strong> '.date('d/m/Y').'</td>
                                    </tr>';
            $html .= '</table> <hr />';


            $entry = 0;
            $grand_total = 0;
            $table = 'accounts_payable';
            $columns = 'currency';
            $where = ['offset' => 'n'];
            if ($currency !== '' && $currency !== 'all') { // print / display by currency
                $where = ['currency' => $currency];
            }
            $group_by = 'currency';
            $order_by = 'currency';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $currencies = $query->result();
            foreach ($currencies as $record) {
                $data = $this->get_historical_listing($record->currency, $from_date, $to_date);
                $html .= $data['html_document'];
                $grand_total += $data['grand_total'];

                // atleat one transaction should be not offsetted for this creditor, only then creditor details and entries will be displayed otherwise it no use to display the details
                $entry += $this->custom->getCount('accounts_payable', ['currency' => $record->currency, 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);
            }

            if ($currency == 'all') {
                $default_currency = $this->custom->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);
                $html .= '<table class="table" style="width: 100%; font-weight: bold">';                
                $html .= '<tr>';
                $html .= '<td style="color: blue; text-align: right; border: none">GRAND TOTAL</td>';
                $html .= '<td style="text-align: right; width: 200px; border: none">'.$default_currency.' '.number_format($grand_total, 2).'</td>';
                $html .= '</tr>';
                $html .= '</table>';
            }

            $file = 'crdtr_hist_'.date('YmdHis').'.pdf';
            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $this->custom->printMPDF($file, $document);
        } else {
            redirect('accounts_payable/reports');
        }
    }

    public function offset()
    {
        is_logged_in('admin');
        has_permission();

        $post = $this->input->post();
        $date_to_offset = date('Y-m-d', strtotime($post['cutoff_date']));

        $status = $this->custom->updateRow('accounts_payable', ['offset' => 'o'], ['doc_date <=' => $date_to_offset, 'settled' => 'y']);

        if ($status == 'updated') {
            set_flash_message('message', 'success', 'Settled Transactions Offsetted');
        } else {
            set_flash_message('message', 'danger', 'Offset Error');
        }

        redirect('/accounts_payable');
    }

    public function creditor_listing()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/creditor_listing.php';
    }

    public function historical_creditor_listing()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/historical_creditor_listing.php';
    }

    public function creditor_by_specific_currency()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->createDropdownSelect2('accounts_payable', ['ap_id', 'currency'], 'Currency');
        $this->body_file = 'accounts_payable/creditor_by_specific_currency.php';
    }

    public function creditor_by_all_currency()
    {
        is_logged_in('admin');
        has_permission();

        $ye_revision_data = $this->custom->getSingleRow('ye_values_before_revision', ['b_id' => 2]);
        if ($ye_revision_data->revision_done != null && $ye_revision_data->revision_done == 0) {
            set_flash_message('message', 'success', 'YEAR END EXCHANGE RATE UPDATED. EXCHANGE DIFFERENCE POSTED TO GL.');
        }

        $this->body_file = 'accounts_payable/creditor_by_all_currency.php';
    }

    public function historical_creditor_by_specific_currency()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->createDropdownSelect2('accounts_payable', ['ap_id', 'currency'], 'Currency');
        $this->body_file = 'accounts_payable/historical_creditor_by_specific_currency.php';
    }

    public function historical_creditor_by_all_currency()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/historical_creditor_by_all_currency.php';
    }

    public function creditor_ageing()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->createDropdownSelect2('accounts_payable', ['currency', 'currency'], 'Currency');
        $this->body_file = 'accounts_payable/creditor_ageing.php';
    }

    public function get_ageing($currency, $cutoff_date)
    {
        // currency and report date
        $html = '<table style="width: 100%; border: none;">';
        $html .= '<tr>';
        $html .= '<td style="padding: 7px;"><strong>Currency: <strong><span style="color: red">'.$currency.'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table class="table" style="width: 100%">';
        $html .= '<tr>
                    <th style="width: 250px">CREDITOR</th>
                    <th style="width: 140px; text-align: right">1 - 30 days</th>
                    <th style="width: 140px; text-align: right">31 - 60 days</th>
                    <th style="width: 140px; text-align: right">61 - 90 days</th>
                    <th style="width: 140px; text-align: right">91 - 120 days</th>
                    <th style="width: 140px; text-align: right">> 120 days</th>
                    <th style="width: 190px; text-align: right">TOTAL</th>
                </tr>';

        $days_1_to_30_grand_total = 0;
        $days_31_to_60_grand_total = 0;
        $days_61_to_90_grand_total = 0;
        $days_91_to_120_grand_total = 0;
        $days_120_plus_grand_total = 0;
        $grand_creditor_total = 0;

        // supplier code list by currency selected
        $table = 'accounts_payable';
        $columns = 'supplier_code';
        $where = ['offset' => 'n', 'currency' => $currency, 'doc_date <= ' => $cutoff_date];
        $group_by = 'supplier_code';
        $order_by = 'supplier_code';
        $order_by_many = null;
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $creditor_list = $query->result();
        foreach ($creditor_list as $creditor_record) {
            // Creditor details
            $creditor_data = $this->custom->getSingleRow('master_supplier', ['code' => $creditor_record->supplier_code]);

            $days = 0;
            $days_1_to_30_total = 0;
            $days_31_to_60_total = 0;
            $days_61_to_90_total = 0;
            $days_91_to_120_total = 0;
            $days_120_plus_total = 0;

            $table = 'accounts_payable';
            $columns = 'doc_date, sign, fa_amt';
            $where = ['offset' => 'n', 'supplier_code' => $creditor_record->supplier_code, 'doc_date <= ' => $cutoff_date];
            $group_by = null;
            $order_by = null;
            $order_by_many = 'doc_date ASC, doc_ref_no ASC';
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $stmt_list = $query->result();
            foreach ($stmt_list as $stmt_record) {
                $days = $this->getDaysBetweenDates($cutoff_date, $stmt_record->doc_date);

                if ($days >= 1 && $days <= 30) {
                    if ($stmt_record->sign == '+') {
                        $days_1_to_30_total += $stmt_record->fa_amt;
                    } elseif ($stmt_record->sign == '-') {
                        $days_1_to_30_total -= $stmt_record->fa_amt;
                    }
                } elseif ($days >= 31 && $days <= 60) {
                    if ($stmt_record->sign == '+') {
                        $days_31_to_60_total += $stmt_record->fa_amt;
                    } elseif ($stmt_record->sign == '-') {
                        $days_31_to_60_total -= $stmt_record->fa_amt;
                    }
                } elseif ($days >= 61 && $days <= 90) {
                    if ($stmt_record->sign == '+') {
                        $days_61_to_90_total += $stmt_record->fa_amt;
                    } elseif ($stmt_record->sign == '-') {
                        $days_61_to_90_total -= $stmt_record->fa_amt;
                    }
                } elseif ($days >= 91 && $days <= 120) {
                    if ($stmt_record->sign == '+') {
                        $days_91_to_120_total += $stmt_record->fa_amt;
                    } elseif ($stmt_record->sign == '-') {
                        $days_91_to_120_total -= $stmt_record->fa_amt;
                    }
                } elseif ($days > 120) {
                    if ($stmt_record->sign == '+') {
                        $days_120_plus_total += $stmt_record->fa_amt;
                    } elseif ($stmt_record->sign == '-') {
                        $days_120_plus_total -= $stmt_record->fa_amt;
                    }
                }
            } // transaction loops ends

            $html .= '<tr>';
            $html .= '<td>'.$creditor_data->name.' ('.$creditor_data->code.')</td>';

            $html .= '<td style="text-align: right">';
            if ($days_1_to_30_total >= 0) {
                $html .= number_format($days_1_to_30_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($days_1_to_30_total), 2).' (CR)';
            }
            $html .= '</td>';

            $html .= '<td style="text-align: right">';
            if ($days_31_to_60_total >= 0) {
                $html .= number_format($days_31_to_60_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($days_31_to_60_total), 2).' (CR)';
            }
            $html .= '</td>';

            $html .= '<td style="text-align: right">';
            if ($days_61_to_90_total >= 0) {
                $html .= number_format($days_61_to_90_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($days_61_to_90_total), 2).' (CR)';
            }
            $html .= '</td>';

            $html .= '<td style="text-align: right">';
            if ($days_91_to_120_total >= 0) {
                $html .= number_format($days_91_to_120_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($days_91_to_120_total), 2).' (CR)';
            }
            $html .= '</td>';

            $html .= '<td style="text-align: right">';
            if ($days_120_plus_total >= 0) {
                $html .= number_format($days_120_plus_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($days_120_plus_total), 2).' (CR)';
            }
            $html .= '</td>';

            $creditor_total = $days_1_to_30_total + $days_31_to_60_total + $days_61_to_90_total + $days_91_to_120_total + $days_120_plus_total;
            $html .= '<td style="text-align: right; font-weight: bold;">';
            if ($creditor_total >= 0) {
                $html .= number_format($creditor_total, 2).' (DR)';
            } else {
                $html .= number_format(abs($creditor_total), 2).' (CR)';
            }
            $html .= '</td>';
            $html .= '</tr>';

            $days_1_to_30_grand_total += $days_1_to_30_total;
            $days_31_to_60_grand_total += $days_31_to_60_total;
            $days_61_to_90_grand_total += $days_61_to_90_total;
            $days_91_to_120_grand_total += $days_91_to_120_total;
            $days_120_plus_grand_total += $days_120_plus_total;
            $grand_creditor_total += $creditor_total;
        } // creditor loops ends

        $html .= '<tr>';
        $html .= '<td style="color: blue; font-weight: bold;">TOTAL '.$currency.'</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($days_1_to_30_grand_total >= 0) {
            $html .= number_format($days_1_to_30_grand_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($days_1_to_30_grand_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($days_31_to_60_grand_total >= 0) {
            $html .= number_format($days_31_to_60_grand_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($days_31_to_60_grand_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($days_61_to_90_grand_total >= 0) {
            $html .= number_format($days_61_to_90_grand_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($days_61_to_90_grand_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($days_91_to_120_grand_total >= 0) {
            $html .= number_format($days_91_to_120_grand_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($days_91_to_120_grand_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($days_120_plus_grand_total >= 0) {
            $html .= number_format($days_120_plus_grand_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($days_120_plus_grand_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '<td style="text-align: right; font-weight: bold;">';
        if ($grand_creditor_total >= 0) {
            $html .= number_format($grand_creditor_total, 2).' (DR)';
        } else {
            $html .= number_format(abs($grand_creditor_total), 2).' (CR)';
        }
        $html .= '</td>';

        $html .= '</tr>';

        $html .= '</table>';
        return $html;
    }

    public function print_ageing()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $currency = $post['currency_code'];
            $cutoff_date = date('Y-m-d', strtotime($post['cutoff_date']));

            $html = '<table style="width: 100%">';
            $html .= '<tr>
                            <td style="border: none;">
                                '.$this->custom->populateCompanyHeader().'
                            </td>
                        
                            <td style="border: none; text-align: right; vertical-align: bottom;">
                                <br /><h4>CREDITORS AGEING</h4> <br /><br /><br />
                            <strong>Date :</strong> '.date('d/m/Y').'</td>
                        </tr>';
            $html .= '</table> <hr /> <br /><br />';
            
            $table = 'accounts_payable';
            $columns = 'currency';
            $where = null;
            if ($currency !== '' && $currency !== 'all') {
                $where = ['currency' => $currency];
            }
            $group_by = 'currency';
            $order_by = 'currency';
            $order_by_many = null;
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $currencies = $query->result();
            foreach ($currencies as $record) {
                $html .= $this->get_ageing($record->currency, $cutoff_date);
            }            

            $file = 'crdtr_ageing_'.date('YmdHis').'.pdf';
            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $this->custom->printMPDF($file, $document);
        }
    }

    public function getDaysBetweenDates($cutoff_date, $document_date)
    {
        $datediff = strtotime($cutoff_date) - strtotime($document_date);

        return round($datediff / (60 * 60 * 24));
    }        

    public function print_ap_ob_list()
    {
        $user_id = $this->session->user_id;

        if ($_GET['row_id'] !== '') {
            $supplier_code = $this->custom->getSingleValue('accounts_payable', 'supplier_code', ['ap_id' => $_GET['row_id']]);
            $sql_print_list = 'SELECT doc_date, doc_ref_no, master_supplier.code, name, fa_amt, total_amt, sign, remarks FROM accounts_payable, master_supplier WHERE accounts_payable.supplier_code = master_supplier.code AND accounts_payable.supplier_code = "'.$supplier_code.'" AND tran_type = "OPBAL" ORDER BY sign DESC, doc_date ASC, doc_ref_no ASC';
        } else {
            $sql_print_list = 'SELECT doc_date, doc_ref_no, master_supplier.code, name, fa_amt, total_amt, sign, remarks FROM accounts_payable, master_supplier WHERE accounts_payable.supplier_code = master_supplier.code AND tran_type = "OPBAL" ORDER BY sign DESC, doc_date ASC, doc_ref_no ASC';
        }

        $query_print_list = $this->db->query($sql_print_list);
        $print_list = $query_print_list->result();

        $html = '';

        $html .= '<style type="text/css">
		table { width: 100%; }
		table { border-collapse: collapse; }
		table th {background: gainsboro; }
		table th, table td {
		border: 1px solid gainsboro;
		padding: 15px; text-align: left;
		}
		</style>';

        $html .= '<div style="width: 100%; margin: auto;text-align: center;"><h3>AP OPENING BALANCE LIST</h3></div>';

        $html .= '<table>
			<tr>
				<th>Date</th>
				<th>Reference</th>
				<th>Supplier</th>
				<th>Amount</th>
				<th>Amount (SGD)</th>
				<th>Sign</th>
				<th>Remarks</th>
			</tr>
		';

        $i = 0;
        foreach ($print_list as $key => $value) {
            $html .= '<tr>
					<td>'.date('d-m-Y', strtotime($value->doc_date)).'</td>
					<td>'.$value->doc_ref_no.'</td>
					<td style="width: 200px">'.$value->name.' ('.$value->code.')</td>
					<td>'.number_format($value->fa_amt, 2).'</td>
					<td>'.number_format($value->total_amt, 2).'</td>
					<td>'.$value->sign.'</td>
					<td>'.$value->remarks.'</td>
				</tr>';
            ++$i;
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="7" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $file = 'ap_ob_'.date('YmdHis').'.pdf';
        $document = $html;
        $this->custom->printMPDF($file, $document);
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
            $default_currency = $this->custom->getDefaultCurrency();

            $html = '<table style="width: 100%; border: none">';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding-left: 0;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td>';
            $html .= '<td style="border: none; text-align: right; padding-right: 0;"><h3>AP AUDIT LISTING</h3></td>';
            $html .= '</tr>';
            $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $html .= '<td style="border: none; color: blue; padding-left: 0;"><h4>'.$transaction_text.'</h4></td>';
            $html .= '<td style="border: none; text-align: right; padding-right: 0;"><strong>Date:</strong> <i>'.date('d-m-Y').'</i></td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table style="width: 100%;">';
            $html .= '<thead>
							<tr>
								<th>Date</th>
								<th>Reference</th>
								<th style="width: 260px">Supplier</th>
                                <th>Currency</th>
                                <th>Sign</th>
								<th style="width: 140px; text-align: right;">Foreign Amount</th>
								<th style="width: 140px; text-align: right;">'.$default_currency.' Amount</th>
							</tr>
						</thead>';
            $html .= '<tbody>';

            $i = 0;
            $bf_total_per_subledger = 0;

            $table = 'accounts_payable';
            $columns = '*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount';
            $where = ['tran_type' => $transaction_type];
            $group_by = 'REPLACE(doc_ref_no, "_sp_1", "")';
            $order_by = null;
            $order_by_many = 'doc_date '.$transaction_order.', doc_ref_no ASC';
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();
            foreach ($list as $record) {
                $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $record->supplier_code]);
                $foreign_amount = $record->total_foreign_amount;
                $local_amount = $record->total_local_amount;
                $document_date = implode('/', array_reverse(explode('-', $record->doc_date)));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$record->original_doc_ref.'</td>';
                $html .= '<td>('.$record->supplier_code.') '.$name.'</td>';
                $html .= '<td style="text-align: center">'.$record->currency.'</td>';                
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '<td style="text-align: right">'.number_format($foreign_amount, 2).'</td>';
                $html .= '<td style="text-align: right">'.number_format($local_amount, 2).'</td>';

                $html .= '</tr>';

                if ($record->sign == '+') {
                    $bf_total_per_subledger += $local_amount;
                } else {
                    $bf_total_per_subledger -= $local_amount;
                }
                ++$i;
            }

            if ($i == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="7" align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            } else {
                
                $html .= '<tr>';
                $html .= '<td colspan="7" style="border: none;" height="20"></td>';
                $html .= '</tr>';

                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">GRAND TOTAL <strong>('.$default_currency.')</strong></td>';
                $html .= '<td style="border: none; text-align: right">';
                if ($bf_total_per_subledger < 0) {
                    $html .= number_format((-1) * $bf_total_per_subledger, 2).' CR';
                } else {
                    $html .= number_format($bf_total_per_subledger, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $bf_total_per_gl = 0;

                $table = 'gl';
                $columns = 'sign, total_amount';
                $where = ['accn' => 'CL001', 'tran_type' => $transaction_type];
                $group_by = null;
                $order_by = null;
                $order_by_many = null;
                $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
                $list = $query->result();
                foreach ($list as $record) {
                    if ($record->sign == '+') {
                        $bf_total_per_gl += $record->total_amount;
                    } else {
                        $bf_total_per_gl -= $record->total_amount;
                    }
                }

                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">';
                if ($transaction_type == 'OPBAL') {
                    $html .= 'BALANCE B/F PER GL';
                } else {
                    $html .= 'TOTAL per CL001';
                }
                $html .= ' <strong>('.$default_currency.')</strong></td>';

                $html .= '<td style="border: none; text-align: right">';
                if ($bf_total_per_gl < 0) {
                    $html .= number_format((-1) * $bf_total_per_gl, 2).' CR';
                } else {
                    $html .= number_format($bf_total_per_gl, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $diff_amount = $this->abs_diff($bf_total_per_subledger < 0 ? $bf_total_per_subledger : (-1) * $bf_total_per_subledger, $bf_total_per_gl < 0 ? $bf_total_per_gl : (-1) * $bf_total_per_gl);
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">DIFFERENCE <strong>('.$default_currency.')</strong></td>';
                $html .= '<td style="border: none; text-align: right">';
                if ($diff_amount < 0) {
                    $html .= number_format((-1) * $diff_amount, 2).' CR';
                } else {
                    $html .= number_format($diff_amount, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
            $html .= '<tbody>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'ap_audit_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/accounts_payable/');
        }
    }

    public function abs_diff($v1, $v2)
    {
        $diff = $v1 - $v2;

        return $diff < 0 ? (-1) * $diff : $diff;
    }

    public function print_batch_ob()
    {
        if (isset($_GET['ob_type'])) {
            $ob_type = $_GET['ob_type'];
        } else {
            $ob_type = 'C';
        }

        if ($ob_type == 'posted') {
            $ob_bf_type = 'P';
        } elseif ($ob_type == 'deleted') {
            $ob_bf_type = 'D';
        } else {
            $ob_bf_type = 'C';
        }

        $html = '';

        $html .= $this->custom->populateMPDFStyle();

        $html .= '<div style="width: 100%; margin: auto; text-align: center;"><h3>OPENING BALANCE</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'ap_open';
        $columns = 'supplier_id';
        $group_by = 'supplier_id';
        $order_by = 'document_date';
        $where = ['status' => $ob_bf_type];
        if ($_GET['rowID'] !== null) {
            $supplier_id = $_GET['rowID'];
            $where = ['supplier_id' => $supplier_id, 'status' => $ob_bf_type];
        }
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        $list = $query->result();
        foreach ($list as $row) {
            $supplier_data = $this->custom->getMultiValues('master_supplier', 'name, code', ['supplier_id' => $row->supplier_id]);            

            $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $row->supplier_id]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
            $cp = '6';
            if($currency == "SGD") {
                $cp = '5';
            }

            $html .= '<tr>';
            $html .= '<td colspan="'.$cp.'" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="'.$cp.'" style="border: none; font-weight: bold">'.$supplier_data->name.' ('.$supplier_data->code.')</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<th>Date</th>';
            $html .= '<th>Reference</th>';
            $html .= '<th style="width: 300px">Remarks</th>';
            if($currency !== 'SGD') {
                $html .= '<th style="width: 150px; text-align: right">'.$currency.' Amount $</th>';
                $html .= '<th style="width: 150px; text-align: right">SGD Amount $</th>';
            } else {
                $html .= '<th style="width: 150px; text-align: right">Amount $</th>';
            }
            $html .= '<th style="width: 50px; text-align: center">Sign</th>';
            $html .= '</tr>';

            $table = 'ap_open';
            $columns = null;
            $group_by = null;
            $order_by = 'document_date';
            $where = ['supplier_id' => $row->supplier_id];
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            $record_list = $query->result();
            foreach ($record_list as $record) {
                $html .= '<tr>';
                $html .= '<td>'.date('d-m-Y', strtotime($record->document_date)).'</td>';
                $html .= '<td>'.$record->document_reference.'</td>';
                if ($record->remarks == '' || $record->remarks == null) {
                    $html .= '<td>Balance B/F</td>';
                } else {
                    $html .= '<td>'.$record->remarks.'</td>';
                }
                if($currency !== 'SGD') {
                    $html .= '<td style="text-align: right">'.number_format($record->foreign_amount, 2).'</td>';
                    $html .= '<td style="text-align: right">'.number_format($record->local_amount, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right">'.number_format($record->local_amount, 2).'</td>';
                }
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';
                $html .= '</tr>';
            }

            ++$i;
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="'.$cp.'" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $file = 'ap_ob_list_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_ap_ob()
    {
        $html = '';
        $html .= $this->custom->populateMPDFStyle();
        $html .= '<div style="width: 100%; margin: auto; text-align: center;"><h3>OPENING BALANCE</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'accounts_payable';
        $columns = 'supplier_code, currency';
        $group_by = 'supplier_code';
        $order_by = null;
        $order_by_many = 'supplier_code ASC, doc_date ASC, doc_ref_no ASC';
        $where = ['tran_type' => 'OPBAL'];
        if ($_GET['rowID'] !== null) {
            $supplier_id = $_GET['rowID'];
            $supplier_code = $this->custom->getSingleValue('master_supplier', 'code', ['supplier_id' => $supplier_id]);
            $where = ['supplier_code' => $supplier_code, 'tran_type' => 'OPBAL'];
        }
        $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $supplier_data = $this->custom->getMultiValues('master_supplier', 'name, code', ['code' => $row->supplier_code]);

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; height: 40px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6">'.$supplier_data->name.' ('.$supplier_data->code.') | Currency : '.$row->currency.'</td>';
            $html .= '</tr>';

            $html .= '<tr>
                <th style="width: 90px">Date</th>
                <th style="width: 120px">Reference</th>
                <th style="width: 150px">'.$row->currency.'  Amount</th>
                <th style="width: 150px">'.$this->custom->getDefaultCurrency().' Amount</th>
                <th style="width: 40px">Sign</th>
                <th style="width: 300px">Remarks</th>
            </tr>';

            $table = 'accounts_payable';
            $columns = null;
            $group_by = null;
            $order_by = null;
            $order_by_many = 'doc_date ASC, doc_ref_no ASC';
            $where = ['supplier_code' => $row->supplier_code, 'tran_type' => 'OPBAL'];
            $query = $this->ap_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            $record_list = $query->result();
            foreach ($record_list as $record) {
                $html .= '<tr>';
                $html .= '<td>'.date('d-m-Y', strtotime($record->doc_date)).'</td>';
                $html .= '<td>'.$record->doc_ref_no.'</td>';
                $html .= '<td>'.number_format($record->fa_amt, 2).'</td>';
                $html .= '<td>'.number_format($record->total_amt, 2).'</td>';
                $html .= '<td style="text-align: center">'.$record->sign.'</td>';

                if ($record->remarks == '' || $record->remarks == null) {
                    $html .= '<td>Balance B/F</td>';
                } else {
                    $html .= '<td>'.$record->remarks.'</td>';
                }
                $html .= '</tr>';
            }

            ++$i;
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="6" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $file = 'ap_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'ap_'.date('j-F-Y_H-i-s').'.sql';

        $CI->load->dbutil();
        $prefs = [
            'tables' => ['ap_open', 'accounts_payable'],
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
            set_flash_message('message', 'success', 'Accounts Payable datafile restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('accounts_payable/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'accounts_payable/blank.php';
        zapAR();
        redirect('accounts_payable/', 'refresh');
    }
}
