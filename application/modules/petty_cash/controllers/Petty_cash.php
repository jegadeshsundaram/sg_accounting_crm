<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Petty_cash extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('petty_cash/petty_cash_model', 'pc_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $this->body_file = 'petty_cash/options.php';
    }    

    public function generate_ref_no($setting) {

        $number_suffix = $setting->number_suffix;
        $ref_no = $setting->text_prefix.'.'.$number_suffix;

        $first_digit = substr($number_suffix, 0, 1);
        if($first_digit == 0) {
            $number_suffix = substr_replace($number_suffix, "1", 0, 1);
        }

        $number_suffix = intval($number_suffix) + 1;

        if($first_digit == 0) {
            $number_suffix = substr_replace($number_suffix,"0", 0, 1);
        }

        $update = $this->custom->updateRow('petty_cash_setting', ['number_suffix' => $number_suffix], ['text_prefix' => $setting->text_prefix]);        

        return $ref_no;
    }

    public function manage($row_id = '') 
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') { // edit page

            $this->body_vars['page'] = 'edit';

            $pc_data = $this->custom->getMultiValues('petty_cash_batch', 'doc_date, ref_no, pay_to, received_by, approved_by', ['pcb_id' => $row_id]);

            $this->body_vars['pc_voucher_number'] = $pc_data->ref_no;
            $this->body_vars['doc_date'] = $pc_data->doc_date;
            $this->body_vars['pay_to'] = $pc_data->pay_to;
            $this->body_vars['received_by'] = $pc_data->received_by;
            $this->body_vars['approved_by'] = $pc_data->approved_by;

            $query = $this->db->query("SELECT count(*) as ca FROM `petty_cash_batch` WHERE ref_no = '".$pc_data->ref_no."' AND (accn = 'CA001' OR accn = 'CL001')");
            $tbls = $query->result();
            $ca_len = $tbls[0]->ca;
            $this->body_vars['ca_len'] = $ca_len;

        } else { // create page

            $this->body_vars['page'] = 'new';

            // pett cash settings - reference
            $setting = $this->custom->getLastInsertedRow('petty_cash_setting', 'updated_on');
            if (is_null($setting)) {
                set_flash_message('message', 'warning', 'Define a Petty Cash Settings first !');
                redirect('petty_cash/');
            }
            
            $this->body_vars['pc_voucher_number'] = $this->generate_ref_no($setting);

            $this->body_vars['doc_date'] = '';
            $this->body_vars['pay_to'] = '';
            $this->body_vars['received_by'] = '';
            $this->body_vars['approved_by'] = '';

            $this->body_vars['ca_len'] = 0;

        }        

        $this->body_vars['coa_list'] = $this->custom->populateCOAByCode();

        $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'TX']);
        $this->body_vars['std_gst_rate'] = $std_gst_rate;

        $this->body_vars['gst_input_categories'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);

        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
        $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
    }

    // save petty cash
    public function save()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $doc_date = date('Y-m-d', strtotime($post['doc_date']));
            $ref_no = $post['ref_no']; 
            
            $total_items = count($post['entry_id']);

            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['pay_to'] = $post['pay_to'];
                $batch_data['doc_date'] = $doc_date;
                $batch_data['ref_no'] = $ref_no;
                $batch_data['accn'] = $post['coa'][$i];
                
                $batch_data['iden'] = $post['iden'][$i];
                $batch_data['gst_type'] = $post['gst_type'][$i];
                $batch_data['gst_category'] = $post['gst_category'][$i];
                $batch_data['net_amount'] = $post['net_amount'][$i];
                $batch_data['gst_amount'] = $post['gst_amount'][$i];

                $batch_data['amount'] = $post['amount'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];
                $batch_data['received_by'] = $post['received_by'];
                $batch_data['approved_by'] = $post['approved_by'];                

                $updated[] = $this->custom->updateRow('petty_cash_batch', $batch_data, ['pcb_id' => $post['entry_id'][$i]]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Batch Saved');
            }
            redirect('petty_cash/listing');
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
            redirect('petty_cash/manage?error=post');
        }
    }

    public function print_stage_1()
    {
        $data = $this->input->post();
        $this->data['pay_to'] = $data['pay_to'];
        $this->data['document_reference'] = $data['document_reference'];
        $this->data['document_date'] = $data['document_date'];
        $this->data['received_by'] = $data['received_by'];
        $this->data['approved_by'] = $data['approved_by'];

        $document = $this->load->view('petty_cash/print_stage_1.php', $this->data, true);

        $file = 'pettycash_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_stage_2()
    {
        if (isset($_GET['rowID'])) {
            $id = $_GET['rowID'];
            $action = 'print';
        } else {
            $id = $this->input->post('rowID');
            $action = 'email';
        }

        if ($id != '') {
            $data = $this->custom->getSingleRow('petty_cash_batch', ['pcb_id' => $id]);
            $doc_date = implode('/', array_reverse(explode('-', $data->doc_date)));
            $this->data['pay_to'] = $data->pay_to;
            $this->data['document_reference'] = $data->ref_no;
            $this->data['document_date'] = $doc_date;
            $this->data['received_by'] = $data->received_by;
            $this->data['approved_by'] = $data->approved_by;

            $document = $this->load->view('petty_cash/print_stage_1.php', $this->data, true);

            $file = 'pettycash_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        }
    }

    

    public function listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function edit($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $batch_data = $this->custom->getSingleRow('petty_cash_batch', ['pcb_id' => $row_id]);

            $this->body_vars['pay_to'] = $batch_data->pay_to;
            $this->body_vars['document_date'] = date('d-m-Y', strtotime($batch_data->doc_date));
            $this->body_vars['document_reference'] = $batch_data->ref_no;
            $this->body_vars['received_by'] = $batch_data->received_by;
            $this->body_vars['approved_by'] = $batch_data->approved_by;

            $this->body_vars['coa_list'] = $this->custom->populateCOAList();

            $std_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => 'TX']);
            $this->body_vars['std_gst_rate'] = $std_gst_rate;

            $this->body_vars['gst_input_category'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);

            $this->body_vars['customer_list'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
            $this->body_vars['supplier_list'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);

            $this->body_file = 'petty_cash/edit.php';
        }
    }

    public function delete_petty_cash_by_reference($batch_entry_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($batch_entry_id != '') {
            $batch_data = $this->custom->getSingleRow('petty_cash_batch', ['pcb_id' => $batch_entry_id]);
            if ($batch_data) {
                $delete_status = $this->custom->deleteRow('petty_cash_batch', ['ref_no' => $batch_data->ref_no, 'doc_date' => $batch_data->doc_date]);
            }

            if ($this->db->trans_status() === false) {
                set_flash_message('message', 'danger', 'BATCH DELETE ERROR');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'BATCH DELETED');
                $this->db->trans_commit();
            }

            redirect('petty_cash/list/delete');
        }
    }

    public function delete_all_petty_cash()
    {
        $this->db->select('pcb_id');
        $this->db->from('petty_cash_batch');
        $query = $this->db->get();
        $batch_data = $query->result();

        foreach ($batch_data as $key => $value) {
            $deleted[] = $this->custom->deleteRow('petty_cash_batch', ['pcb_id' => $value->pcb_id]);
        }

        if ($this->db->trans_status() === false) {
            set_flash_message('message', 'danger', 'BATCH DELETE ERROR');
            $this->db->trans_rollback();
        } else {
            set_flash_message('message', 'success', 'BATCH DELETED');
            $this->db->trans_commit();
        }

        redirect('petty_cash/list/delete');
    }

    public function move_petty_cash_to_GL()
    {
        $this->body_file = 'petty_cash/blank.php';
        $this->header_file = 'petty_cash/blank.php';
        $this->footer_file = 'petty_cash/blank.php';

        $this->db->select('*');
        $this->db->from('petty_cash_batch');
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_reference = $query->result();
        foreach ($batch_reference as $key => $value) {
            $this->db->select('*');
            $this->db->from('petty_cash_batch');
            $this->db->where('doc_date = "'.$value->doc_date.'" AND ref_no = "'.$value->ref_no.'"');
            $query = $this->db->get();
            $batch_endtry_data_by_reference = $query->result();

            $document_date = $value->doc_date;
            $document_reference = $value->ref_no;
            $credit_total_amount = 0;
            foreach ($batch_endtry_data_by_reference as $key => $value) {
                $credit_total_amount += $value->amount;

                // insert all the entries into *** GL.TBL ***
                $gl_data['doc_date'] = $document_date;
                $gl_data['ref_no'] = $document_reference;
                $gl_data['remarks'] = $value->remarks;
                $gl_data['accn'] = $value->accn;
                $gl_data['sign'] = $value->sign;
                $gl_data['tran_type'] = 'PTCASH';
                $gl_data['total_amount'] = $value->amount;
                $gl_inserted = $this->db->insert('gl', $gl_data);

                // insert CA001 entries into *** accounts_receivable *** table
                if ($value->accn == 'CA001') {
                    $ar_data['doc_date'] = $document_date;
                    $ar_data['doc_ref_no'] = $document_reference;
                    $ar_data['remarks'] = $value->remarks;
                    $ar_data['customer_code'] = $value->iden;

                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $value->iden]);
                    $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
                    $rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);
                    $ar_data['currency'] = $currency;

                    $local_amount = $value->amount / $rate;
                    $ar_data['total_amt'] = round($local_amount, 2);
                    $ar_data['f_amt'] = $value->amount;

                    $ar_data['fa_amt'] = 0.00;

                    $ar_data['sign'] = $value->sign;
                    $ar_data['tran_type'] = 'PTCASH';

                    $ar_data['invoice_id'] = 0;
                    $ar_posted = $this->db->insert('accounts_receivable', $ar_data);
                } elseif ($value->accn == 'CL001') {
                    // insert CL001 entries into *** accounts_payable *** table
                    $ap_data['doc_date'] = $document_date;
                    $ap_data['doc_ref_no'] = $document_reference;

                    $ap_data['remarks'] = $value->remarks;
                    $ap_data['supplier_code'] = $value->iden;

                    $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['code' => $value->iden]);
                    $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $currency_id]);
                    $rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $currency_id]);
                    $ap_data['currency'] = $currency;

                    $local_amount = $value->amount / $rate;
                    $ap_data['total_amt'] = round($local_amount, 2);
                    $ap_data['fa_amt'] = $value->amount;

                    $ap_data['sign'] = $value->sign;
                    $ap_data['tran_type'] = 'PTCASH';

                    $ap_data['purchase_id'] = 0;

                    $ap_posted = $this->db->insert('accounts_payable', $ap_data);
                } elseif ($value->accn == 'CL300') { // Insert "CL300" entries into *** GST.TBL ***
                    $gst_data['date'] = $document_date;
                    $gst_data['dref'] = $document_reference;
                    $gst_data['rema'] = $value->remarks;

                    if ($value->gst_type == 'I') {
                        $gst_data['iden'] = 'Input Tax';
                        $gst_data['gsttype'] = 'I';
                        $gst_data['gstcate'] = $value->gst_category;
                        $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                        $gst_data['gstperc'] = $gst_rate;

                        $gst_data['amou'] = $value->net_amount;
                        $gst_data['gstamou'] = $value->gst_amount;
                    }
                    $gst_data['tran_type'] = 'PTCASH';

                    $gst_inserted = $this->db->insert('gst', $gst_data);
                }
            }

            // Credit Default Bank - Inserting One Lum Sum Entry to GL.TBL
            $gl_data['doc_date'] = $document_date;
            $gl_data['ref_no'] = $document_reference;
            $gl_data['remarks'] = 'Credit to Petty Cash Account';
            $gl_data['accn'] = 'CA100';
            $gl_data['sign'] = '-';
            $gl_data['tran_type'] = 'PTCASH';
            $gl_data['total_amount'] = $credit_total_amount;

            $gl_inserted = $this->db->insert('gl', $gl_data);

            if ($gl_inserted) {
                $delete_status = $this->custom->deleteRow('petty_cash_batch', ['ref_no' => $document_reference]);
            }
        }

        unset($gl_data);

        if ($gl_inserted) {
            set_flash_message('message', 'success', 'BATCH POSTED');
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('/petty_cash/options');
    }

    public function print_audit()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h3>'.$company_profile->company_name.'</h3></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>AUDIT TRAIL</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        $i = 0;
        // Extract ALL the records from PETTY_CASH_BATCH TBL GROUP BY DATE, REFERENCE is SAME
        $this->db->select('doc_date, ref_no');
        $this->db->from('petty_cash_batch');
        $this->db->where(['status' => 'C']);
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        // $sql = $this->db->last_query();
        $batch_data = $query->result();
        foreach ($batch_data as $record) {
            // Document Information
            $doc_date = implode('/', array_reverse(explode('-', $record->doc_date)));
            $doc_ref = $record->ref_no;

            $html .= '<table style="width: 100%;">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<td colspan="4">';
            $html .= '<strong>Date : </strong>'.$doc_date.' | ';
            $html .= '<strong>Reference : </strong>'.$doc_ref;
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>
                        <th style="width: 300px;">Account</th>
                        <th style="width: 130px; text-align: right">Debit</th>
                        <th style="width: 130px; text-align: right">Credit</th>
                        <th style="width: 200px;">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>';

            $grand_total_by_reference = 0;
            $batch_entry = $this->custom->getRows('petty_cash_batch', ['ref_no' => $doc_ref]);
            foreach ($batch_entry as $key => $value) {
                $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                // Debit Entry - Start
                $html .= '<tr>';
                $html .= '<td>'.$value->accn.' : '.$accn_description.'</td>';
                $html .= '<td style="text-align: right">'.number_format($value->amount, 2).'</td>';
                $html .= '<td></td>';
                $html .= '<td>'.$value->remarks.'</td>';
                $html .= '</tr>';

                $grand_total_by_reference += $value->amount;

                // Debit Entry - End
            }

            // Credit Entry - Start {Credit Always PETTY CASH ACCOUNT (CA100)}
            $html .= '<tr>';
            $html .= '<td>CA100 : PETTY CASH</td>';
            $html .= '<td></td>';
            $html .= '<td style="text-align: right">'.number_format($grand_total_by_reference, 2).'</td>';
            $html .= '<td>Credit to Petty Cash Account</td>';
            $html .= '</tr>';
            // Credit Entry - End

            $html .= '<tr><td colspan="4" style="border: none; height: 30px"></td></tr>';

            $html .= '</tbody>';
            $html .= '</table>';

            ++$i;
        } // First Loop End

        if ($i == 0) {
            $html .= '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
            $html .= '</table>';
        }

        $css = $this->custom->populateMPDFStyle();
        $document = $css.$html;

        $file = 'pettycash_audt_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_list()
    {
        is_logged_in('admin');
        has_permission();

        $html .= '<table style="width: 100%; border: none">';
        $company_where = ['code' => 'CP'];
        $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
        $html .= '<tr><td style="border: none; text-align: center;"><h3>'.$company_profile->company_name.'</h3></td></tr>';

        $html .= '<tr>';
        $html .= '<td align="center" style="border: none"><h4>PETTY CASH</h4></td>';
        $html .= '</tr>';
        $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
        $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.date('d-m-Y').'</i></td>';
        $html .= '</tr>';
        $html .= '</table><br />';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        // Extract ALL the records from PETTY_CASH_BATCH TBL GROUP BY DATE, REFERENCE is SAME
        $this->db->select('doc_date, ref_no');
        $this->db->from('petty_cash_batch');
        $this->db->where(['status' => 'C']);
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        // $sql = $this->db->last_query();
        $batch_data = $query->result();
        foreach ($batch_data as $record) {
            // Document Information
            $doc_date = implode('/', array_reverse(explode('-', $record->doc_date)));
            $doc_ref = $record->ref_no;

            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<td colspan="3">';
            $html .= '<strong>Date : </strong>'.$doc_date.' | ';
            $html .= '<strong>Reference : </strong>'.$doc_ref;
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>
                        <th style="width: 300px;">Account</th>
                        <th style="width: 130px; text-align: right">Amount</th>
                        <th style="width: 200px;">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>';

            $grand_total_by_reference = 0;
            $batch_entry = $this->custom->getRows('petty_cash_batch', ['ref_no' => $doc_ref]);
            foreach ($batch_entry as $key => $value) {
                $accn_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $html .= '<tr>';
                $html .= '<td>'.$value->accn.' : '.$accn_description.'</td>';
                $html .= '<td style="text-align: right">'.number_format($value->amount, 2).'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                $html .= '</tr>';
            }

            $html .= '<tr><td colspan="3" style="border: none; height: 30px"></td></tr>';

            $html .= '</tbody>';

            ++$i;
        } // First Loop End

        $html .= '</table>';

        if ($i == 0) {
            $html = '<table style="width: 100%;"><tbody><tr>';
            $html .= '<td align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
            $html .= '</tr>';
        }

        $css = $this->custom->populateMPDFStyle();
        $document = $css.$html;

        $file = 'pettycash_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'petty_cash_'.date('dmYHis').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['petty_cash_setting', 'petty_cash_batch'],
            'format' => 'sql',
            'filename' => $file_name,
            'add_drop' => true,
            'add_insert' => true,
            'newline' => "\n",
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
            set_flash_message('message', 'success', 'PETTY CASH RESTORED');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('petty_cash/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'petty_cash/blank.php';
        zapPettyCash();
        redirect('petty_cash/', 'refresh');
    }
}
