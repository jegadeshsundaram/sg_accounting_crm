<?php

defined('BASEPATH') or exit('No direct script access allowed');

class General_ledger extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('general_ledger/general_ledger_model', 'general_ledger');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('ref_no, tran_type');
        $this->db->from('gl');
        $this->db->group_by('ref_no');
        $this->db->order_by('ref_no', 'ASC');
        $query = $this->db->get();
        $gl_data = $query->result();
        $ref = "<option value=''>-- Select --</option>";
        foreach ($gl_data as $value) {
            $ref .= "<option class='".$value->tran_type."' value='".$value->ref_no."'>";
            $ref .= $value->ref_no;
            $ref .= '</option>';
        }
        $this->body_vars['ref_list'] = $ref;

        // Supplier List from Batch.TBL
        $this->db->select('tran_type, doc_ref_no, supplier_code');
        $this->db->from('accounts_payable');
        $this->db->where("tran_type = 'BTHPURC' || tran_type = 'BTHSET'");
        $this->db->order_by('doc_ref_no', 'DESC');
        $query = $this->db->get();
        // print_r($this->db->last_query());
        $ap_data = $query->result();
        $sup = "<option value=''>-- Select --</option>";
        foreach ($ap_data as $value) {
            $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $value->supplier_code]);

            $sup .= "<option class='".$value->doc_ref_no."' value='".$value->supplier_code."'>";
            $sup .= $name.' ('.$value->supplier_code.')';
            $sup .= '</option>';
        }

        $this->body_vars['supplier_list'] = $sup;

        $this->body_file = 'general_ledger/options.php';
    }

    public function audit_listing()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('gl_id, ref_no, tran_type');
        $this->db->from('gl');
        $this->db->group_by('ref_no');
        $this->db->order_by('ref_no', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        $options = "<option value=''>Select</option>";
        foreach ($list as $value) {
            $options .= "<option class='".$value->tran_type."' value='".$value->gl_id."'>";
            $options .= $value->ref_no;
            $options .= '</option>';
        }
        $this->body_vars['dl_ref_list'] = $options;

        $this->db->select('gl_id, ref_no, tran_type');
        $this->db->from('gl_single_entry');
        $this->db->group_by('ref_no');
        $this->db->order_by('ref_no', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        $options = "<option value=''>Select</option>";
        foreach ($list as $value) {
            $options .= "<option class='".$value->tran_type."' value='".$value->gl_id."'>";
            $options .= $value->ref_no;
            $options .= '</option>';
        }
        $this->body_vars['se_ref_list'] = $options;

        $this->body_file = 'general_ledger/audit_listing.php';
    }

    public function opening_balance()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_ob($row_id = '') {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $this->body_vars['page'] = 'edit';

            $ob_data = $this->custom->getMultiValues('gl_open', 'doc_date, ref_no, remarks', ['ob_id' => $row_id]);
            $this->body_vars['doc_date'] = $ob_data->doc_date;
            $this->body_vars['ref_no'] = $ob_data->ref_no;
            $this->body_vars['remarks'] = $ob_data->remarks;

        } else {
            $this->body_vars['page'] = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['remarks'] = '';
        }

        $this->body_vars['coa_list'] = $this->custom->populateCOAByCode();

        $this->body_vars['save_url'] = '/general_ledger/save_ob';
    }

    public function ob_create()
    {
        is_logged_in('admin');
        has_permission();

        $this->body_vars['coa_list'] = $this->custom->populateCOAByCode();

        $this->body_vars['save_url'] = '/general_ledger/save_ob';
    }    

    public function ob_edit($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $ob_data = $this->custom->getMultiValues('gl_open', 'doc_date, ref_no, remarks', ['ob_id' => $row_id]);

            $this->body_vars['doc_date'] = $ob_data->doc_date;
            $this->body_vars['ref_no'] = $ob_data->ref_no;
            $this->body_vars['remarks'] = $ob_data->remarks;

            $this->body_vars['save_url'] = '/general_ledger/save_ob';
        }
    }

    public function save_ob()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $total_items = count($post['ob_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['ref_no'] = $post['ref_no'];
                $batch_data['remarks'] = $post['remarks'];
                $batch_data['sign'] = $post['sign'][$i];
                $batch_data['accn'] = $post['accn'][$i];

                if ($post['sign'][$i] == '+') {
                    $batch_data['total_amount'] = $post['debit_amount'][$i];
                } elseif ($post['sign'][$i] == '-') {
                    $batch_data['total_amount'] = $post['credit_amount'][$i];
                }

                $ob_id = $post['ob_id'][$i];
                $updated[] = $this->custom->updateRow('gl_open', $batch_data, ['ob_id' => $ob_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Batch Saved');
            }
            
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('general_ledger/opening_balance');
    }

    public function print_ob()
    {
        if (isset($_GET['ob_type'])) {
            $ob_type = $_GET['ob_type'];
        } else {
            $ob_type = 'C';
        }

        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>GL Opening Balance</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'gl_open';
        $columns = 'doc_date, ref_no, remarks';
        $group_by = 'doc_date, ref_no';
        $order_by = null;
        $order_by_many = 'doc_date ASC, ref_no ASC';
        $where = ['status' => $ob_type];
        if ($_GET['rowID'] !== null) {
            $ob_id = $_GET['rowID'];
            $where = ['ob_id' => $ob_id, 'status' => $ob_type];
        }
        $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $html .= '<tr>';
            $html .= '<td colspan="2" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td style="border-right: none">';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->doc_date));
            $html .= '</td>';
            $html .= '<td style="width: 200px; text-align: right; border-left: none">';
            $html .= '<strong>Reference : </strong>'.$row->ref_no.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="2">';
            $html .= '<strong>Remarks : </strong>'.$row->remarks.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'gl_open';
            $columns = null;
            $group_by = null;
            $order_by = 'doc_date';
            $where = ['doc_date' => $row->doc_date, 'ref_no' => $row->ref_no, 'status' => $ob_type];
            $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $record) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Account</th>';
                    $html .= '<th style="text-align: right">Amount</th>';
                    $html .= '</tr>';
                }

                $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $record->accn]);

                $html .= '<tr>';
                $html .= '<td>'.$record->accn.' : '.$accn_desc.'</td>';
                $html .= '<td style="text-align: right"><span style="color: red">';
                if ($record->sign == '+') {
                    $html .= 'DR ';
                } else {
                    $html .= 'CR ';
                }
                $html .= '</span>';
                $html .= number_format($record->total_amount, 2);
                $html .= '</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="2" style="color: red; text-align: center">No Opening Balance B/F Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'gl_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function reports()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['coa_list'] = $this->custom->populateCOAByCode();

        $pl_data = $this->custom->getSingleRow('pl_data', ['pl_type' => 'PL']);

        $this->body_vars['start_date'] = date('d-m-Y', strtotime($pl_data->start_date));
        $this->body_vars['end_date'] = date('d-m-Y', strtotime($pl_data->end_date));
        $this->body_vars['closing_stock'] = $pl_data->closing_stock;
    }

    public function tips_predefined_accounts()
    {
        is_logged_in('admin');
        has_permission();

        $document = '<div style="width: 100%; text-align: center;"><h2>PREDEFINED ACCOUNTS</h2></div>
            <h3>About Chart Of Account</h3>
            <p>ACCN comprises a PREFIX of 2 characters plus a SUFFIX of 3 digits.</p>

            <h3>Search for Account</h3>
            <p>Key in the prefix and all the accounts in that category will display.</p>
            Example:
            <ul>
            <li>S0 for Sales Accounts</li>
            <li>C0 for Cost Of Sales</li>
            <li>I0 for Other Income</li>
            <li>E0 for Expenses</li>
            <li>X0 for Extraordinary Items</li>
            <li>T0 for Tax Items</li>
            </ul>

            <h3>Assets & Liabilities</h3>
            <p>FA is for Fixed Asset, which must be matched by PD Accounts with similar suffixes.</p>
            <p>CA is for Current Asset, CL is for Current Liabilities.</p>
            <p>MT denotes Mid Term Liabilities while LT is used for Long Term Liabilities.</p>

            <h3>Equity Accounts</h3>
            <p>RP001 is for Retained Profit, CR denotes Capital Reserves, D0 represents DECLARED DIVIDENDS.</p>

            <h3>Predefined Accounts</h3>
            <p>Some examples include: CA001 which denotes DEBTORS CONTROL (and cannot used for other purposes)</p>
            <p>CL002 is Provision for Bad Debts</p>
            <p>CL001 is restricted to CREDITORS CONTROL</p>
            <p>PETTY CASH FLOAT is CA100</p>

            <h3>Foreign Bank Accounts</h3>
            <p>CA110 is FOREIGN BANK CONTROL ACCOUNT and this is linked to a Sub-Ledger.</p>

            <h3>Local Bank Accounts</h3>
            <p>CA101 to CA109 are designated for Local Bank Accounts. </p>
            <p>CA101 is the  Default Bank Account unless otherwise specified.</p>';

        $file = 'gl_tips_'.date('YmdHis').'.pdf';

        $this->custom->printMPDF($file, $document);
    }

    public function ye_closing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function print_audit_double()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $transaction = $post['transaction'];
            $transaction_text = $post['transaction_desc'];
            $gl_id = $post['ref_no'];
            $order_by = $post['order'];
            $cut_off_date = date('d-m-Y');

            $html = '';
            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="border: none">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td>';
            $html .= '<td align="right" style="border: none"><h3>GL AUDIT LISTING</h3><span style="color: red">(Double Entry Transactions)</span></td>';
            $html .= '</tr>';

            $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $html .= '<td style="border: none">';
            $html .= '<strong>Currency:</strong> <span style="color: blue">SGD</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.$cut_off_date.'</i></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $ref = '';
            if ($gl_id !== '') {
                $ref = $this->custom->getSingleValue('gl', 'ref_no', ['gl_id' => $gl_id]);
            }

            $i = 0;
            $debit_grand_total = 0;
            $credit_grand_total = 0;

            $this->db->select('*');
            $this->db->from('gl');
            $this->db->where('tran_type = "'.$transaction.'"');
            if ($ref !== '') {
                $this->db->where('ref_no = "'.$ref.'" AND tran_type = "'.$transaction.'"');
            }
            $this->db->order_by('doc_date '.$order_by.', ref_no ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                if ($i == 0) {
                    $html .= '<br /><br /><strong>Transaction:</strong> <span style="color: blue; padding-bottom: 10px;">'.$transaction_text.'</span><br /><br />
                        <table style="width: 100%;">
							<thead>
								<tr>
									<th>DATE</th>
									<th>REFERENCE</th>
                                    <th>REMARKS</th>
									<th>ACCOUNT</th>
									<th style="text-align: right">DEBIT</th>
									<th style="text-align: right">CREDIT</th>
								</tr>
							</thead>
							<tbody>';
                }

                $account_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $doc_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$doc_date.'</td>';
                $html .= '<td style="width: 110px;">'.$value->ref_no.'</td>';
                $html .= '<td style="width: 180px;">'.$value->remarks.'</td>';
                $html .= '<td style="width: 200px;">'.$value->accn.' : '.$account_description.'</td>';
                if ($value->sign == '+') {
                    $html .= '<td style="width: 150px; text-align: right">'.number_format($value->total_amount, 2).'</td>';
                    $html .= '<td style="width: 150px;"></td>';
                    $debit_grand_total += $value->total_amount;
                } elseif ($value->sign == '-') {
                    $html .= '<td style="width: 150px;"></td>';
                    $html .= '<td style="width: 150px; text-align: right">'.number_format($value->total_amount, 2).'</td>';
                    $credit_grand_total += $value->total_amount;
                }
                $html .= '</tr>';

                ++$i;
            }

            if ($i == 0) {
                $html .= '<table style="width: 100%;"><tbody><tr>';
                $html .= '<td colspan="8" align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            }

            if ($i > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="4" style="font-weight: bold">*** Total (SGD) ***</td>';
                $html .= '<td style="text-align: right; font-weight: bold;">'.number_format($debit_grand_total, 2).'</td>';
                $html .= '<td style="text-align: right; font-weight: bold">'.number_format($credit_grand_total, 2).'</td>';
                $html .= '</tr>';
            } else {
                $html .= '<table style="width: 100%;"><tbody><tr>';
                $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gl_audit_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/general_ledger/audit_listing');
        }
    }

    public function print_audit_single()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $transaction = $post['transaction'];
            $transaction_text = $post['transaction_desc'];
            $gl_id = $post['ref_no'];
            $order_by = $post['order'];
            $cut_off_date = date('d-m-Y');

            $html = '';

            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="border: none">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td>';
            $html .= '<td align="right" style="border: none"><h3>GL AUDIT LISTING</h3><span style="color: red">(Single Entry Transactions)</span></td>';
            $html .= '</tr>';

            $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $html .= '<td style="border: none">';
            $html .= '<strong>Currency:</strong> <span style="color: blue">SGD</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.$cut_off_date.'</i></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<br /><br />';
            $html .= '<table style="width: 100%;">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Date</th>';
            $html .= '<th>Reference</th>';
            $html .= '<th>Remarks</th>';
            $html .= '<th>IDEN</th>';
            $html .= '<th>Amount</th>';
            $html .= '<th><span style="color: red">(SGD)</span><br />Amount</th>';
            if (($transaction == 'BTHSALE' || $transaction == 'BTHPURC') && $this->ion_auth->isGSTMerchant()) {
                $html .= '<th>GST Cate</th>';
                $html .= '<th>GST Rate</th>';
                $html .= '<th>GST Amt</th>';
                $html .= '<th><span style="color: red">(SGD)</span><br />GST Amt</th>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $ref = '';
            if ($gl_id !== '') {
                $ref = $this->custom->getSingleValue('gl_single_entry', 'ref_no', ['gl_id' => $gl_id]);
            }

            $i = 0;
            $this->db->select('*');
            $this->db->from('gl_single_entry');
            $this->db->where('tran_type = "'.$transaction.'"');
            if ($ref !== '') {
                $this->db->where('ref_no = "'.$ref.'" AND tran_type = "'.$transaction.'"');
            }
            $this->db->order_by('doc_date '.$order_by.', ref_no ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                
                if ($transaction == 'BTHREC') {
                    $iden_data = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['code' => $value->iden]);
                } elseif($transaction == 'BTHSET') {
                    $iden_data = $this->custom->getMultiValues('master_supplier', 'name, code, currency_id', ['code' => $value->iden]);
                }

                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden_data->currency_id]);
                $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$document_date.'</td>';
                $html .= '<td style="width: 110px;">'.$value->ref_no.'</td>';
                $html .= '<td style="width: 120px;">'.$value->remarks.'</td>';
                $html .= '<td style="width: 150px;">'.$iden_data->name.' ('.$iden_data->code.') ('.$currency.')</td>';
                $html .= '<td style="width: 100px;">'.number_format($value->foreign_amount, 2).'</td>';
                $html .= '<td style="width: 100px;">'.number_format($value->local_amount, 2).'</td>';
                if (($transaction == 'BTHSALE' || $transaction == 'BTHPURC') && $this->ion_auth->isGSTMerchant()) {
                    $html .= '<td style="width: 100px;">'.$value->gst_category.'</td>';
                    $html .= '<td style="width: 100px;">'.$value->gst_rate.'</td>';
                    $html .= '<td style="width: 100px;">'.number_format($value->foreign_gst_amount, 2).'</td>';
                    $html .= '<td style="width: 100px;">'.number_format($value->local_gst_amount, 2).'</td>';
                }
                $html .= '</tr>';
                ++$i;
            }

            if ($i == 0) {
                $html .= '<table style="width: 100%;"><tbody><tr>';
                $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gl_audit_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/general_ledger/audit_listing_single_entry');
        }
    }

    public function data_patch()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $ref_no = $post['ref'];
            $supplier = $post['supplier'];
            $tran_type = $post['tran_type'];

            $where = ['ref_no' => $ref_no, 'tran_type' => $tran_type];
            if($supplier !== "") {
                $where = ['ref_no' => $ref_no, 'iden' => $supplier, 'tran_type' => $tran_type];
            }
            $gl_data = $this->custom->getMultiValues('gl', 'doc_date, iden, remarks', $where);

            $this->body_vars['doc_date'] = $gl_data->doc_date;
            $this->body_vars['ref_no'] = $ref_no;
            $this->body_vars['remarks'] = $gl_data->remarks;
            $this->body_vars['tran_type'] = $tran_type;
            $this->body_vars['iden'] = $gl_data->iden;
            $this->body_vars['supplier'] = $supplier;
            $this->body_vars['iden_exist'] = false;
            
            if($gl_data->iden !== '') { // customer / foreign bank

                $cust_entry = $this->custom->getCount('master_customer', ['code' => $gl_data->iden]);
                if($cust_entry > 0) { // control account = customer
                    $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1], ['code' => $gl_data->iden]);
                }

                $supp_entry = $this->custom->getCount('master_supplier', ['code' => $gl_data->iden]);
                if($supp_entry > 0) { // control account = customer
                    $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['code' => $gl_data->iden]);
                }

                $fb_entry = $this->custom->getCount('master_foreign_bank', ['code' => $gl_data->iden]);
                if($fb_entry > 0) { // control account = customer
                    $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_foreign_bank', ['fb_code', 'fb_name', 'fb_code', 'currency_id'], 'Foreign Bank', ['( ', ') ', ''], [], ['fb_code' => $gl_data->iden]);
                }

                $this->body_vars['iden_exist'] = true;
            }
            
            $this->body_vars['tran_type'] = $tran_type;
            $this->body_vars['coa_options'] = $this->custom->populateCOAByCode();

            $this->body_file = 'general_ledger/data_patch.php';
        }
    }
    
    public function deleteEntriesFrom_GL_AR_AP_FB($ref_no = '', $iden = '', $tran_type = '')
    {
        $gl_deleted = $this->custom->deleteRow('gl', ['ref_no' => $ref_no, 'iden' => $iden, 'tran_type' => $tran_type]);
        $ap_deleted = $this->custom->deleteRow('accounts_payable', ['doc_ref_no' => $ref_no, 'supplier_code' => $iden, 'tran_type' => $tran_type]);
        $ar_deleted = $this->custom->deleteRow('accounts_receivable', ['doc_ref_no' => $ref_no, 'customer_code' => $iden, 'tran_type' => $tran_type]);
        $fb_deleted = $this->custom->deleteRow('foreign_bank', ['doc_ref_no' => $ref_no, 'fb_code' => $iden, 'tran_type' => $tran_type]);

        return $gl_deleted;
    }

    public function save_patched_data()
    {
        $post = $this->input->post();
        $len = sizeof($post);
        if ($post) {

            $doc_date = date('Y-m-d', strtotime($post['doc_date']));
            $ref_no = $post['ref_no'];
            $remarks = $post['remarks'];
            $tran_type = $post['tran_type'];
            $iden = $post['iden'];
            
            $delete = 0;
            $len = count($post['gl_id']);
            for ($i = 0; $i <= $len - 1; ++$i) {
                $accn = $post['coa'][$i];
                $sign = $post['sign'][$i];

                if ($delete == 0) {
                    // Delete entries from AR, AP, GL and FB before RESUBMISSION
                    $delete_status = $this->deleteEntriesFrom_GL_AR_AP_FB($ref_no, $iden, $tran_type);
                    ++$delete;
                }
                
                if ($iden != '' && $accn == 'CA001') { // Accounts Receivable Sub Ledger
                    $ar_data['doc_ref_no'] = $ref_no;
                    $ar_data['doc_date'] = $doc_date;

                    $ar_data['customer_code'] = $iden;

                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $iden]);
                    $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
                    $ar_data['currency'] = $currency->code;
                    $currency_rate = $currency->rate;

                    if ($sign == '+') {
                        $local_amount = $post['dr_amount'][$i];
                        $foreign_amount = round($post['dr_amount'][$i] * $currency_rate, 2);
                    } elseif ($sign == '-') {
                        $local_amount = $post['cr_amount'][$i];
                        $foreign_amount = round($post['cr_amount'][$i] * $currency_rate, 2);
                    }

                    $ar_data['total_amt'] = $local_amount;
                    $ar_data['f_amt'] = $foreign_amount;

                    $ar_data['fa_amt'] = 0.00;

                    $ar_data['sign'] = $sign;
                    $ar_data['tran_type'] = $tran_type;
                    $ar_data['remarks'] = $remarks;

                    $ar_data['invoice_id'] = 0;

                    $ar_insert = $this->db->insert('accounts_receivable', $ar_data);

                    // Foreign Bank Sub Ledger - Insert
                } elseif ($iden != '' && $accn == 'CA110') {
                    $fb_data['doc_ref_no'] = $ref_no;
                    $fb_data['doc_date'] = $doc_date;

                    $fb_data['fb_code'] = $iden;
                    
                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $iden]);
                    $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
                    $fb_data['currency'] = $currency->code;
                    $currency_rate = $currency->rate;                    

                    if ($sign == '+') {
                        $local_amount = $post['dr_amount'][$i];
                        $foreign_amount = round($post['dr_amount'][$i] * $currency_rate, 2);
                    } elseif ($sign == '-') {
                        $local_amount = $post['cr_amount'][$i];
                        $foreign_amount = round($post['cr_amount'][$i] * $currency_rate, 2);
                    }

                    $fb_data['local_amt'] = $local_amount;
                    $fb_data['fa_amt'] = $foreign_amount;

                    $fb_data['sign'] = $sign;
                    $fb_data['tran_type'] = $tran_type;
                    $fb_data['remarks'] = $remarks;

                    $fb_insert = $this->db->insert('foreign_bank', $fb_data);

                    
                } elseif ($iden != '' && $accn == 'CL001') { // Accounts Payable Sub Ledger

                    $ap_data['doc_ref_no'] = $ref_no;
                    $ap_data['doc_date'] = $doc_date;

                    $ap_data['supplier_code'] = $iden;

                    $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['code' => $iden]);
                    $currency = $this->custom->getMultiValues('ct_currency', 'code, rate', ['currency_id' => $currency_id]);
                    $ap_data['currency'] = $currency->code;
                    $currency_rate = $currency->rate;

                    if ($sign == '+') {
                        $local_amount = $post['dr_amount'][$i];
                        $foreign_amount = round($post['dr_amount'][$i] * $currency_rate, 2);
                    } elseif ($sign == '-') {
                        $local_amount = $post['cr_amount'][$i];
                        $foreign_amount = round($post['cr_amount'][$i] * $currency_rate, 2);
                    }
                    
                    $ap_data['total_amt'] = $local_amount;
                    $ap_data['fa_amt'] = $foreign_amount;

                    $ap_data['sign'] = $sign;
                    $ap_data['tran_type'] = $tran_type;
                    $ap_data['remarks'] = $remarks;

                    $ap_data['purchase_id'] = 0;

                    $ap_insert = $this->db->insert('accounts_payable', $ap_data);
                }

                // insert all the entries into *** gl *** table
                $gl_data['doc_date'] = $doc_date;
                $gl_data['ref_no'] = $ref_no;
                $gl_data['remarks'] = $remarks;
                $gl_data['accn'] = $accn;
                $gl_data['sign'] = $sign;
                $gl_data['gstcat'] = '';
                $gl_data['tran_type'] = $tran_type;

                if ($sign == '+') {
                    $gl_data['total_amount'] = $post['dr_amount'][$i];
                } elseif ($sign == '-') {
                    $gl_data['total_amount'] = $post['cr_amount'][$i];
                }

                $gl_data['sman'] = '';
                $gl_data['iden'] = $iden;

                $gl_insert = $this->db->insert('gl', $gl_data);
                
            } // loop ends

            if ($gl_insert) {
                set_flash_message('message', 'success', 'GL Patched!');
            } else {
                set_flash_message('message', 'danger', 'Save Error');
            }
            
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('/general_ledger');
    }

    // page : reports
    public function export_stmt_in_excel()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post['from'] != '' && $post['to'] != '') {
            $from_date = date('Y-m-d', strtotime($post['from']));
            $to_date = date('Y-m-d', strtotime($post['to']));
        } else {
            redirect('/general_ledger/reports');
        }

        $accn = $post['accn'];

        $cond = '';
        if ($accn !== '') {
            $cond = 'accn = "'.$accn.'"';
        } else {
            $cond = '';
        }

        // create file name
        $fileName = 'gl_stmt_'.time().'.xlsx';
        // load excel library
        $this->load->library('excel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $where = 'doc_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
        if ($cond !== '') {
            $where = $where.'AND '.$cond;
        }
        $this->db->select('accn');
        $this->db->from('gl');
        $this->db->where($where);
        $this->db->group_by('accn');
        $this->db->order_by('accn ASC');
        $query = $this->db->get();
        $gl_data = $query->result();

        $len = sizeof($gl_data);
        $record = 0;
        $rowCount = 1;

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, ' ');
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ' ');
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ' ');
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, ' ');
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, ' ');
        ++$rowCount;

        $period = $post['from'].' to '.$post['to'];
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'Period: '.$period);
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);

        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, 'Report Date: '.$post['report_date']);
        $objPHPExcel->getActiveSheet()->mergeCells('E'.$rowCount.':F'.$rowCount);

        $objPHPExcel->getActiveSheet()
                    ->getStyle('E'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        ++$rowCount;

        for ($i = 0; $i < $len; ++$i) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, ' ');
            ++$rowCount;

            $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $gl_data[$i]->accn]);

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $gl_data[$i]->accn.' ('.$accn_desc.')');

            $objPHPExcel->getActiveSheet()
            ->getStyle('A'.$rowCount)
            ->getFont()
            ->getColor()
            ->setRGB('0000FF');

            $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);

            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, ' ');
            ++$rowCount;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'Reference');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'Remarks');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, 'Debit $');
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, 'Credit $');
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, 'Balance $');

            $objPHPExcel->getActiveSheet()
            ->getStyle('A'.$rowCount.':F'.$rowCount)
            ->getFont()
            ->getColor()
            ->setRGB('454545');

            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':F'.$rowCount)->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()
                    ->getStyle('D'.$rowCount.':F'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            ++$rowCount;

            // Balance block forward - Start
            $balance_bf_lum_sum = 0;
            $this->db->select('sign, total_amount');
            $this->db->from('gl');
            $this->db->where('accn = "'.$gl_data[$i]->accn.'" AND doc_date < "'.$from_date.'"');
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            // print_r($this->db->last_query());
            $bf_data = $query->result();
            foreach ($bf_data as $value) {
                if ($value->sign == '+') {
                    $balance_bf_lum_sum += $value->total_amount;
                } elseif ($value->sign == '-') {
                    $balance_bf_lum_sum -= $value->total_amount;
                }
            }

            $running_balance = 0;
            if ($balance_bf_lum_sum != 0) {
                $document_date = date('d-m-Y', strtotime($from_date));
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $document_date);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'Balance B/F');
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'Balance B/F');
                if ($balance_bf_lum_sum >= 0) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, number_format($balance_bf_lum_sum, 2));
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('D'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, ' ');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, number_format($balance_bf_lum_sum, 2));
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('F'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($balance_bf_lum_sum, 2));
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('E'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, number_format($balance_bf_lum_sum, 2));
                    $objPHPExcel->getActiveSheet()
                    ->getStyle('F'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }
                $running_balance += $balance_bf_lum_sum;
                ++$rowCount;
            }
            // Balance block forward - End

            // $sql_other_entries = 'SELECT * FROM gl WHERE accn = "'.$gl_data[$i]->accn.'" and doc_date BETWEEN "'.$from_date.'" AND "'.$to_date.'" ORDER BY doc_date ASC';
            $this->db->select('*');
            $this->db->from('gl');
            $this->db->where('accn = "'.$gl_data[$i]->accn.'" AND doc_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            $gl_stmts = $query->result();
            foreach ($gl_stmts as $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $document_date);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $value->ref_no);
                $objPHPExcel->getActiveSheet()
                    ->getStyle('B'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $value->remarks);

                if ($value->sign == '+') {
                    $html .= '<td>'.number_format($value->total_amount, 2).'</td>';
                    $html .= '<td></td>';
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, number_format($value->total_amount, 2));
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, '');

                    $objPHPExcel->getActiveSheet()
                    ->getStyle('D'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    $running_balance += $value->total_amount;
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, '');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($value->total_amount, 2));

                    $objPHPExcel->getActiveSheet()
                    ->getStyle('E'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    $running_balance -= $value->total_amount;
                }

                if ($running_balance >= 0) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, number_format($running_balance, 2));
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, number_format($running_balance, 2));
                }

                $objPHPExcel->getActiveSheet()
                    ->getStyle('F'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                ++$rowCount;
            }

            ++$record;
        }

        if ($record == 0) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'No Records Found');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', ' ');
        }

        foreach (range('A', 'F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $file_path = realpath(APPPATH.'../assets/uploads/');
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path.'/'.$fileName);
        header('Content-Type: application/vnd.ms-excel');
        $download_path = 'assets/uploads/'.$fileName;
        redirect($download_path);
    }

    // page : reports
    public function export_tb_in_excel()
    {
        is_logged_in('admin');
        has_permission();

        if (isset($_GET['cut_off_date'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['cut_off_date']));
            $sort_by = $_GET['sort_by'];

            // create file name
            $fileName = 'gl_tb_'.time().'.xlsx';
            // load excel library
            $this->load->library('excel');

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            if ($sort_by == 'code') {
                $sort_field = 'gl.accn';
            } elseif ($sort_by == 'desc') {
                $sort_field = 'chart_of_account.description';
            } else {
                $sort_field = 'gl.accn';
            }
            // $sql = 'SELECT *, sum(CASE WHEN gl.sign = "+" THEN gl.total_amount WHEN gl.sign = "-" THEN -gl.total_amount END) AS total_balance_amount FROM gl, chart_of_account WHERE gl.doc_date <= "'.$cut_off_date.'" AND gl.accn = chart_of_account.accn GROUP BY gl.accn ORDER BY '.$sort_field.' ASC';

            $this->db->select('*, sum(CASE WHEN gl.sign = "+" THEN gl.total_amount WHEN gl.sign = "-" THEN -gl.total_amount END) AS total_balance_amount');
            $this->db->from('gl, chart_of_account');
            $this->db->where('gl.doc_date <= "'.$cut_off_date.'" AND gl.accn = chart_of_account.accn');
            $this->db->group_by('gl.accn');
            $this->db->order_by($sort_field, 'ASC');
            $query = $this->db->get();
            $gl_data = $query->result();

            $len = sizeof($gl_data);
            $record = 0;
            $rowCount = 1;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
            ++$rowCount;

            // report title
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'TRIAL BALANCE AS AT '.date('d-m-Y', strtotime($_GET['cut_off_date'])));
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':D'.$rowCount);
            $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->getFont()->setBold(true);
            ++$rowCount;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'Report Date : '.date('d-m-Y'));
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':D'.$rowCount);
            $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            ++$rowCount;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ' ');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, ' ');
            ++$rowCount;

            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'ACCOUNT');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'DESCRIPTION');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'DEBIT');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, 'CREDIT');
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':D'.$rowCount)->getFont()->setBold(true);
            ++$rowCount;

            $debit_final_total = 0;
            $credit_final_total = 0;
            for ($i = 0; $i < $len; ++$i) {
                if ($gl_data[$i]->total_balance_amount !== 0) {
                    $account_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $gl_data[$i]->accn]);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $gl_data[$i]->accn);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $account_description);
                    if ($gl_data[$i]->total_balance_amount >= 0) {
                        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, number_format($gl_data[$i]->total_balance_amount, 2));
                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, '');
                        $debit_final_total += $gl_data[$i]->total_balance_amount;
                    } else {
                        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, number_format((-1) * $gl_data[$i]->total_balance_amount, 2));
                        $credit_final_total += (-1) * $gl_data[$i]->total_balance_amount;
                    }
                    ++$rowCount;
                    ++$record;
                }
            }

            if ($record == 0) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'No Records Found');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', ' ');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', ' ');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', ' ');
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'TOTAL');
                $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':B'.$rowCount);
                $objPHPExcel->getActiveSheet()
                    ->getStyle('A'.$rowCount)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, number_format($debit_final_total, 2));
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, number_format($credit_final_total, 2));
                $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':D'.$rowCount)->getFont()->setBold(true);
                ++$rowCount;
            }

            foreach (range('A', 'D') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }

            $file_path = realpath(APPPATH.'../assets/uploads/');
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save($file_path.'/'.$fileName);
            header('Content-Type: application/vnd.ms-excel');
            $download_path = 'assets/uploads/'.$fileName;
            redirect($download_path);
        } else {
            redirect('general_ledger/reports');
        }
    }

    // page : reports
    public function print_statement()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post['from'] != '' && $post['to'] != '') {
            $from_date = date('Y-m-d', strtotime($post['from']));
            $to_date = date('Y-m-d', strtotime($post['to']));

            $accn = $post['accn'];

            $date_display = '<strong>Period</strong> : '.$post['from'].' <strong>to</strong> '.$post['to'];
            $report_date = '<strong>Report Date</strong> : '.$post['report_date'];

            $html = '';

            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; text-align: center;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="text-align: center; border: none;"><h3>GL Report</h3></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="3" style="text-align: left; border: none; border-bottom: 2px solid brown">'.$date_display.'</td>';
            $html .= '<td colspan="3" style="text-align: right; border: none; border-bottom: 2px solid brown">'.$report_date.'</td>';
            $html .= '</tr>';

            $cond = '';
            if ($accn !== '') {
                $cond = 'accn = "'.$accn.'"';
            } else {
                $cond = '';
            }

            $where = 'doc_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
            if ($cond !== '') {
                $where = $where.'AND '.$cond;
            }
            $entry = 0;

            $this->db->select('accn');
            $this->db->from('gl');
            $this->db->where($where);
            $this->db->group_by('accn');
            $this->db->order_by('accn ASC');
            $query = $this->db->get();
            $accn_list = $query->result();
            foreach ($accn_list as $record) {
                $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $record->accn]);

                $html .= '<tr><td colspan="6" height="10" style="border: none;"></td></tr>
							<tr>
								<td colspan="6" style="border: none;">
									<strong>'.$record->accn.' ('.$accn_desc.')</strong><br />
								</td>
							</tr>
							<tr>
								<th style="padding-left: 10px; width: 90px; font-weight: bold">Date</th>
								<th style="padding-left: 10px; width: 120px; font-weight: bold">Reference</th>
								<th style="padding-left: 10px; width: 200px; font-weight: bold">Remarks</th>
								<th style="padding-left: 10px; width: 130px; font-weight: bold; text-align: right">Debit $</th>
								<th style="padding-left: 10px; width: 130px; font-weight: bold; text-align: right">Credit $</th>
								<th style="padding-left: 10px; width: 130px; font-weight: bold; text-align: right">Balance $</th>
							</tr>';

                // Balance block forward - Start
                $balance_bf_lum_sum = 0;
                $this->db->select('sign, total_amount');
                $this->db->from('gl');
                $this->db->where('accn = "'.$record->accn.'" AND doc_date < "'.$from_date.'"');
                $this->db->order_by('doc_date', 'ASC');
                $query = $this->db->get();
                // print_r($this->db->last_query());
                $bf_data = $query->result();
                foreach ($bf_data as $value) {
                    if ($value->sign == '+') {
                        $balance_bf_lum_sum += $value->total_amount;
                    } elseif ($value->sign == '-') {
                        $balance_bf_lum_sum -= $value->total_amount;
                    }
                }

                $running_balance = 0;
                if ($balance_bf_lum_sum != 0) {
                    $doc_date = date('d-m-Y', strtotime($from_date));
                    $html .= '<tr>';
                    $html .= '<td>'.$doc_date.'</td>';
                    $html .= '<td>BALANCE B/F</td>';
                    $html .= '<td><i>BALANCE B/F</i></td>';
                    if ($balance_bf_lum_sum >= 0) {
                        $html .= '<td style="text-align: right">'.number_format($balance_bf_lum_sum, 2).'</td>';
                        $html .= '<td></td>';
                        $html .= '<td style="text-align: right">'.number_format($balance_bf_lum_sum, 2).'</td>';
                    } else {
                        $html .= '<td></td>';
                        $html .= '<td style="text-align: right">'.number_format((-1) * $balance_bf_lum_sum, 2).'</td>';
                        $html .= '<td style="text-align: right">('.number_format((-1) * $balance_bf_lum_sum, 2).')</td>';
                    }
                    $html .= '</tr>';
                    $running_balance += $balance_bf_lum_sum;
                }
                // Balance block forward - End

                // Other Entries
                $this->db->select('*');
                $this->db->from('gl');
                $this->db->where('accn = "'.$record->accn.'" AND doc_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
                $this->db->order_by('doc_date', 'ASC');
                $query = $this->db->get();
                $gl_data = $query->result();
                foreach ($gl_data as $value) {
                    $document_date = date('d-m-Y', strtotime($value->doc_date));
                    $html .= '<tr>';
                    $html .= '<td>'.$document_date.'</td>';
                    $html .= '<td>'.$value->ref_no.'</td>';
                    $html .= '<td>'.$value->remarks.'</td>';
                    if ($value->sign == '+') {
                        $html .= '<td style="text-align: right">'.number_format($value->total_amount, 2).'</td>';
                        $html .= '<td></td>';
                        $running_balance += $value->total_amount;
                    } else {
                        $html .= '<td></td>';
                        $html .= '<td style="text-align: right">'.number_format($value->total_amount, 2).'</td>';
                        $running_balance -= $value->total_amount;
                    }

                    if ($running_balance >= 0) {
                        $html .= '<td style="text-align: right">'.number_format($running_balance, 2).'</td>';
                    } else {
                        $html .= '<td style="text-align: right">('.number_format((-1) * $running_balance, 2).')</td>';
                    }

                    $html .= '</tr>';
                }
                ++$entry;
            }

            if ($entry == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; color: red; text-align: center">No Transactions</td>';
                $html .= '</tr>';
            }
        } else {
            redirect('/general_ledger/reports');
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'gl_stmt_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_trail_balance()
    {
        is_logged_in('admin');
        has_permission();
        if (isset($_GET['cut_off_date'])) {
            $cut_off_date = date('Y-m-d', strtotime($_GET['cut_off_date']));
            $sort_by = $_GET['sort_by'];

            $html = '<table>';

            $html .= '<tr><td colspan="4" style="border: none; text-align: center;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td></tr>';

            $html .= '<tr><td colspan="4" style="border: none; text-align: center;"><h4>TRIAL BALANCE AS AT <span style="color: tomato">'.date('d-m-Y', strtotime($_GET['cut_off_date'])).'</span></h4></td></tr>';

            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="border: none;"><strong>Report Date : </strong><span style="font-weight: normal">'.date('d-m-Y').'</span> <hr /></td>';
            $html .= '</tr>';

            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th style="width: 70px;">ACCOUNT</th>';
            $html .= '<th style="width: 135px;">DISCRIPTION</th>';
            $html .= '<th style="width: 100px; text-align: right">DEBIT</th>';
            $html .= '<th style="width: 100px; text-align: right">CREDIT</th>';
            $html .= '</tr>';
            $html .= '</thead>';

            $debit_final_total = 0;
            $credit_final_total = 0;

            if ($sort_by == 'code') {
                $sort_field = 'gl.accn';
            } elseif ($sort_by == 'desc') {
                $sort_field = 'chart_of_account.description';
            } else {
                $sort_field = 'gl.accn';
            }

            // $sql = 'SELECT *, sum(CASE WHEN gl.sign = "+" THEN gl.total_amount WHEN gl.sign = "-" THEN -gl.total_amount END) AS total_balance_amount FROM gl, chart_of_account WHERE gl.doc_date <= "'.$cut_off_date.'" AND gl.accn = chart_of_account.accn GROUP BY gl.accn ORDER BY '.$sort_field.' ASC';

            $this->db->select('*, sum(CASE WHEN gl.sign = "+" THEN gl.total_amount WHEN gl.sign = "-" THEN -gl.total_amount END) AS total_balance_amount');
            $this->db->from('gl, chart_of_account');
            $this->db->where('gl.doc_date <= "'.$cut_off_date.'" AND gl.accn = chart_of_account.accn');
            $this->db->group_by('gl.accn');
            $this->db->order_by($sort_field, 'ASC');
            $query = $this->db->get();
            $gl_data = $query->result();
            foreach ($gl_data as $key => $value) {
                if ($value->total_balance_amount !== 0) {
                    $account_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                    $html .= '<tr>';
                    $html .= '<td>'.$value->accn.'</td>';
                    $html .= '<td>'.$account_description.'</td>';
                    if ($value->total_balance_amount >= 0) {
                        $html .= '<td style="text-align: right">'.number_format($value->total_balance_amount, 2).'</td>';
                        $html .= '<td></td>';
                        $debit_final_total += $value->total_balance_amount;
                    } else {
                        $html .= '<td></td>';
                        $html .= '<td style="text-align: right">'.number_format((-1) * $value->total_balance_amount, 2).'</td>';
                        $credit_final_total += (-1) * $value->total_balance_amount;
                    }

                    $html .= '</tr>';
                }
            }

            $html .= '<tr class="special">';
            $html .= '<td colspan="2" align="right" style="color: blue;">TOTAL</td>';
            $html .= '<td style="text-align: right"><strong>$'.number_format($debit_final_total, 2).'</strong></td>';
            $html .= '<td style="text-align: right"><strong>$'.number_format($credit_final_total, 2).'</strong></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gl_tb_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('general_ledger/reports');
        }
    }

    public function print_coa()
    {
        is_logged_in('admin');
        has_permission();

        if ($_GET['sort_by'] !== '') {
            $sort_by = $_GET['sort_by'];
        } else {
            $sort_by = 'accn';
        }

        $html = '';
        $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>CHART OF ACCOUNT</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br /><table>';

        $html .= '<tr>';
        if ($sort_by == 'accn') {
            $html .= '<th style="width: 100px">Code</td>';
            $html .= '<th>Description</td>';
        } else {
            $html .= '<th>Description</td>';
            $html .= '<th style="width: 100px">Code</td>';
        }

        $html .= '</tr>';

        $sql = 'SELECT description, accn FROM chart_of_account ORDER BY '.$sort_by.' ASC';
        $query = $this->db->query($sql);
        $coa_data = $query->result();
        foreach ($coa_data as $key => $value) {
            $html .= '<tr>';
            if ($sort_by == 'accn') {
                $html .= '<td>'.$value->accn.'</td>';
                $html .= '<td>'.$value->description.'</td>';
            } else {
                $html .= '<td>'.$value->description.'</td>';
                $html .= '<td>'.$value->accn.'</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'coa_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'gl_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['gl', 'gl_open', 'gl_single_entry'],
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
            set_flash_message('message', 'success', 'GENERAL LEDGER RESTORED');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('general_ledger/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'general_ledger/blank.php';
        zapGL();
        redirect('general_ledger/', 'refresh');
    }
}
