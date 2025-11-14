<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Payment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Payment/payment_model', 'pay_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'payment/options.php';
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

        $update = $this->custom->updateRow('payment_setting', ['number_suffix' => $number_suffix], ['text_prefix' => $setting->text_prefix]);        

        return $ref_no;
    }

    public function create()
    {
        is_logged_in('admin');
        has_permission();

        $setting = $this->custom->getLastInsertedRow('payment_setting', 'updated_on');
        if (is_null($setting)) {
            set_flash_message('message', 'warning', 'Define a Payment Settings first !');
            redirect('payment/');
        }

        $this->body_vars['ref_no'] = $this->generate_ref_no($setting);

        $this->body_vars['supplier_options'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ' '], ['active' => 1]);

        $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
        $this->body_vars['bank_accn'] = $bank_data->accn;
        $this->body_vars['fb_accn'] = $bank_data->fb_accn;
        $this->body_vars['bank_accn_list'] = $this->custom->populateCOABankListWithFB($bank_data->accn);
        $this->body_vars['fb_accn_list'] = $this->custom->populateFBAccounts($bank_data->fb_accn);

        $this->body_file = 'payment/create.php';
    }

    public function save()
    {
        is_logged_in('admin');
        has_permission();
        $data = $this->input->post();

        if ($data) {

            // Bank - can be upated only in Transaction Input (NOT in EDIT)
            $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
            if ($bank_data->accn != $data['bank_accn'] || $bank_data->fb_accn != $data['fb_accn']) {
                $bank_insert_data['accn'] = $data['bank_accn'];
                $bank_insert_data['fb_accn'] = '';
                if ($data['bank_accn'] == 'CA110') {
                    $bank_insert_data['fb_accn'] = $data['fb_accn'];
                }
                $bank_update = $this->custom->updateRow('bank', $bank_insert_data, ['accn_type' => 'CA']);
            }
   
            // inserting into payment_master tbl
            $document_date = date('Y-m-d', strtotime($data['created_on']));
            $mt_data['payment_ref_no'] = $data['ref_no'];
            $mt_data['supplier_id'] = $data['supplier_id'];
            $mt_data['user_id'] = $this->session->user_id;
            $mt_data['purchase_reference_id'] = '';
            $mt_data['bank'] = $data['bank'];
            $mt_data['cheque'] = $data['cheque'];
            $mt_data['other_reference'] = $data['other_reference'];
            $mt_data['currency'] = $data['currency'];
            $mt_data['amount'] = $data['amount'];
            $mt_data['bank_accn'] = $data['bank_accn'];
            $mt_data['fb_accn'] = $data['fb_accn'];
            $mt_data['created_on'] = $document_date;
            $mt_data['modified_on'] = date('Y-m-d');            
            $payment_id = $this->custom->insertRow('payment_master', $mt_data);

            // contra : YES  *** inserting debit and credit entries into payment_purchase_master tbl
            if ($data['ap_ids'] != '') {
                $splitted_ap_ids = explode(',', $data['ap_ids']);
                $final_balance_entry_id = $data['final_balance_entry_id'];
                $final_balance_entry_reference = $data['final_balance_entry_reference'];
                $final_balance_total = $data['final_balance_total'];

                foreach ($splitted_ap_ids as $value) {
                    $ap_id = $value;

                    $partial_used_amount = 0;
                    $i = 0;

                    // check debit or credit reference is partially used in payment which is CONFIRMED but not POSTED YET
                    // $sql_partial_used_data = 'SELECT full_amount, pay_pur_amount FROM payment_purchase_master, payment_master WHERE payment_purchase_master.payment_id = payment_master.payment_id AND payment_purchase_master.purchase_id = "'.$ap_id.'" AND payment_master.payment_status = "C"';
                    $this->db->select('full_amount, pay_pur_amount');
                    $this->db->from('payment_purchase_master, payment_master');
                    $this->db->where('payment_purchase_master.payment_id = payment_master.payment_id AND payment_purchase_master.purchase_id = '.$ap_id.' AND payment_master.payment_status = "C"');
                    $query = $this->db->get();
                    $partial_data = $query->result();
                    foreach ($partial_data as $record) {
                        ++$i;
                        $partial_used_amount += $record->full_amount - $record->pay_pur_amount;
                    }

                    $ap_data = $this->custom->getSingleRow('accounts_payable', ['ap_id' => $ap_id]);
                    $full_amount = $ap_data->fa_amt;
                    $purchase_id = $ap_data->ap_id;
                    if ($i > 0) {
                        $full_amount = $ap_data->fa_amt - $partial_used_amount;
                    }
                    
                    $entry_data['purchase_id'] = $purchase_id;
                    $entry_data['pay_pur_amount'] = $full_amount;
                    if ($final_balance_entry_id !== '' && $ap_id == $final_balance_entry_id) {
                        $entry_data['pay_pur_amount'] = $final_balance_total;
                    }
                    $entry_data['payment_id'] = $payment_id;
                    $entry_data['full_amount'] = $full_amount;

                    $inserted[] = $this->custom->insertRow('payment_purchase_master', $payment_entry_data);
                }
            }

            if ($this->db->trans_status() === false || in_array('error', $inserted)) {
                set_flash_message('message', 'danger', 'PAYMENT ERROR');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'PAYMENT CREATED');
                $this->db->trans_commit();
            }

            redirect('payment/listing/');
        }
        exit;
    }

    public function listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage($mode, $row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        $documentToRow = '';
        if ($row_id != '') {
            // payment master details
            $this->body_vars['payment_data'] = $payment_data = $this->custom->getSingleRow('payment_master', ['payment_id' => $row_id]);

            if ($payment_data) {
                // company details
                $company_where = ['code' => 'CP'];
                $this->body_vars['company_details'] = $company_details = $this->custom->getSingleRow('company_profile', $company_where);

                if (isset($mode)) {
                    $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $payment_data->supplier_id]);
                    $this->body_vars['supplier_name_code'] = $supplier->name.' ('.$supplier->code.')';
                    $this->body_vars['supplier_address'] = $this->custom->populateSupplierAddress($supplier);

                    // currency details
                    $this->body_vars['currency_code'] = $payment_data->currency;

                    $this->body_vars['payment_id'] = $row_id;
                    $this->body_vars['supplier_id'] = $payment_data->supplier_id;
                    $this->body_vars['bank'] = $payment_data->bank;
                    $this->body_vars['cheque'] = $payment_data->cheque;
                    $this->body_vars['other_reference'] = $payment_data->other_reference;
                    $this->body_vars['amount'] = $payment_data->amount;

                    $this->body_vars['payment_ref_no'] = $payment_data->payment_ref_no;

                    // payment date
                    $this->body_vars['payment_date'] = date('d-m-Y', strtotime($payment_data->created_on));

                    if ($mode == 'view') {
                        $balance = 0;

                        // $sql = 'SELECT * FROM payment_purchase_master, accounts_payable WHERE payment_purchase_master.purchase_id = accounts_payable.ap_id AND payment_id = '.$row_id.' ORDER BY payment_purchase_master.pay_pur_id ASC';
                        $this->db->select('*');
                        $this->db->from('payment_purchase_master, accounts_payable');
                        $this->db->where('payment_purchase_master.purchase_id = accounts_payable.ap_id AND payment_id = '.$row_id);
                        $this->db->order_by('payment_purchase_master.pay_pur_id', 'ASC');
                        $query = $this->db->get();
                        $documents = $query->result();
                        foreach ($documents as $value) {
                            $documentToRow .= '<tr>';

                            $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px;">'.$value->doc_ref_no.'</td>';

                            if ($value->sign == '+') {
                                $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($value->full_amount, 2).' DR</td>';
                                $balance += $value->full_amount;
                            } else {
                                $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($value->full_amount, 2).' CR</td>';
                                $balance -= $value->full_amount;
                            }

                            $documentToRow .= '</tr>';
                        }

                        $documentToRow .= '<tr >';
                        $documentToRow .= '<td style="border: none"></td>';
                        if ($balance < 0) {
                            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format((-1) * $balance, 2).' CR</td>';
                        } else {
                            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format($balance, 2).' DR</td>';
                        }
                        $documentToRow .= '</tr>';

                        $this->body_vars['documentToRow'] = $documentToRow;
                        $this->body_vars['mode'] = 'view';

                        if ($payment_edit_data->payment_status == 'P') {
                            $this->body_vars['listing_type'] = 'posted';
                        } elseif ($payment_edit_data->payment_status == 'D') {
                            $this->body_vars['listing_type'] = 'deleted';
                        } else {
                            $this->body_vars['listing_type'] = 'confirmed';
                        }

                        $this->body_file = 'payment/view.php';
                    } elseif ($mode == 'edit') {
                        $this->body_vars['save_url'] = 'payment/update';
                        $this->body_file = 'payment/edit.php';
                    }
                }
            }
        }
    }

    public function print_stage_1()
    {
        // post data
        $data = $this->input->post();
        $this->data['data'] = $data;

        // Company Details
        $this->data['company_details'] = $this->custom->populateCompanyHeader();

        // Supplier
        $supplier_data = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $data['supplier_id']]);
        $this->data['supplier_address'] = $this->custom->populateSupplierAddress($supplier_data);
        $this->data['supplier_name'] = $supplier_data->name;
        $this->data['supplier_code'] = $supplier_data->code;
        $this->data['supplier_gst_number'] = $supplier_data->gst_number;

        // Currency
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $supplier_data->currency_id]);
        $this->data['supplier_currency'] = $currency_data->code;
        $this->data['currency_rate'] = $currency_data->rate;

        // reference
        $this->data['document_date'] = $data['created_on'];
        $this->data['document_reference'] = $data['ref_no'];

        // Page Inputs
        $this->data['bank'] = $data['bank'];
        $this->data['cheque'] = $data['cheque'];
        $this->data['amount'] = $data['amount'];
        $this->data['other_reference'] = $data['other_reference'];

        $ap_ids = $data['ap_ids'];
        if ($ap_ids != '') {
            $splitted_ap_ids = explode(',', $ap_ids);
        } else {
            $splitted_ap_ids = '';
        }

        $balance = 0;
        $documentToRow = '';
        $entry = 0;
        foreach ($splitted_ap_ids as $key => $value) {
            $ap_id = $value;
            $ap_data = $this->custom->getSingleRow('accounts_payable', ['ap_id' => $ap_id]);

            $documentToRow .= '<tr style="page-break-inside: avoid;">';
            $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px;">'.$ap_data->doc_ref_no.'</td>';

            if ($ap_data->sign == '+') {
                $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($ap_data->fa_amt, 2).' DR</td>';
                $balance += $ap_data->fa_amt;
            } else {
                $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($ap_data->fa_amt, 2).' CR</td>';
                $balance -= $ap_data->fa_amt;
            }
            $documentToRow .= '</tr>';

            ++$entry;
        }

        // balance row
        $documentToRow .= '<tr style="page-break-inside: avoid;">';
        $documentToRow .= '<td style="border: none"></td>';
        if ($balance < 0) {
            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; text-align: right; color: dimgray">$'.number_format((-1) * $balance, 2).' CR</td>';
        } else {
            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; text-align: right; color: dimgray">$'.number_format($balance, 2).' DR</td>';
        }
        $documentToRow .= '</tr>';

        $this->data['documentToRow'] = $documentToRow;
        $this->data['entry'] = $entry;

        $document = $this->load->view('payment/print_stage_1.php', $this->data, true);

        $file = 'payment_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_stage_2()
    {
        if (isset($_GET['rowID'])) {
            $row_id = $_GET['rowID'];
            $action = 'print';
        } else {
            $row_id = $this->input->post('rowID');
            $action = 'email';
        }

        $documentToRow = '';
        $this->data['mt_data'] = $mt_data = $this->custom->getSingleRow('payment_master', ['payment_id' => $row_id]);

        if ($mt_data) {
            
            if ($mt_data) {
                if (isset($action)) {

                    $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $mt_data->supplier_id]);
                    $this->data['supplier_name_code'] = $supplier->name.' ('.$supplier->code.')';
                    $this->data['supplier_address'] = $this->custom->populateSupplierAddress($supplier);

                    $balance = 0;
                    $records = 0;
                    $this->db->select('*');
                    $this->db->from('payment_purchase_master, accounts_payable');
                    $this->db->where('payment_purchase_master.purchase_id = accounts_payable.ap_id AND payment_id = '.$row_id);
                    $this->db->order_by('payment_purchase_master.pay_pur_id', 'ASC');
                    $query = $this->db->get();
                    $documents = $query->result();
                    foreach ($documents as $value) {
                        $documentToRow .= '<tr style="page-break-inside: avoid;">';
                        $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px;">'.$value->doc_ref_no.'</td>';

                        if ($value->sign == '+') {
                            $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($value->full_amount, 2).' DR</td>';
                            $balance += $value->full_amount;
                        } else {
                            $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($value->full_amount, 2).' CR</td>';
                            $balance -= $value->full_amount;
                        }

                        $documentToRow .= '</tr>';
                        ++$records;
                    }

                    $documentToRow .= '<tr >';
                    if ($records > 0) {
                        $documentToRow .= '<td style="border: none"></td>';
                        if ($balance < 0) {
                            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; text-align: right; font-weight: bold">$'.number_format((-1) * $balance, 2).' CR</td>';
                        } else {
                            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; text-align: right; font-weight: bold">$'.number_format($balance, 2).' DR</td>';
                        }
                    } else {
                        $documentToRow .= '<td colspan="2" style="border: 1px solid #ccc; padding: 10px 10px;">No details found</td>';
                    }
                    $documentToRow .= '</tr>';

                    $this->data['documentToRow'] = $documentToRow;

                    $file = 'payment_'.date('YmdHis').'.pdf';
                    $document = $this->load->view('payment/print_stage_2.php', $this->data, true);
                    include 'application/third_party/mpdf/vendor/autoload.php';
                    $mpdf = new \Mpdf\Mpdf([
                        'margin_left' => '10mm',
                        'margin_right' => '10mm',
                        'margin_top' => '10mm',
                        'margin_bottom' => '20mm',
                    ]);
                    $mpdf->showWatermarkText = true;
                    $mpdf->setFooter('Page {PAGENO} of {nb}');
                    $mpdf->SetHeader();
                    $mpdf->WriteHTML($document);

                    if ($action == 'print') {

                        $mpdf->Output($file, 'I');

                    } elseif ($action == 'email') {

                        $path = BASEPATH.'upload/payment/';

                        if (is_dir($path)) {
                            $mpdf->Output(realpath($path).'/'.$file, 'F');
                        } else {
                            echo 'error'.$path;
                        }

                        $pdfFilePath = realpath($path).'/'.$file;

                        // Email settings
                        $to_address = $supplier->email;

                        if ($to !== '') {
                            $mail = $this->custom->populateEmailHeaders();

                            // to address
                            $mail->addAddress($to);

                            // subject
                            $mail->Subject = 'Information About Payment';                        

                            // body
                            $mail->Body = $document;

                            // attachements
                            $mail->AddAttachment($pdfFilePath);

                            // Send
                            if (!$mail->send()) {
                                // echo 'Email could not be sent.';
                                // echo 'Mailer Error: '.$mail->ErrorInfo;
                                $message = 'error';
                            } else {
                                // echo 'Email has been sent';
                                $message = 'success';
                            }
                        } else {
                            $message = 'error';
                        }
                        echo $message;
                    }
                }
            } else {
                redirect('payment/listing/confirmed', 'refresh');
            }
        }
    }

    public function print_payment_list()
    {
        $type = strtoupper($this->uri->segment(3));

        $html = '';

        $html .= "<div style='width: 100%; margin: auto;text-align: center;'><h3>".strtoupper($type)." PAYMENT'S</h3></div>";

        $html .= '<table>
			<tr>
				<th>Date</th>
				<th>Reference</th>
				<th>Supplier</th>
				<th>Bank</th>
				<th>Cheque</th>
				<th>Amount</th>
				<th>Remarks</th>
			</tr>
		';

        $i = 0;

        $this->db->select('*');
        $this->db->from('payment_master');
        $this->db->where('payment_status = "'.$type.'"');
        $this->db->order_by('payment_ref_no', 'DESC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $record) {
            $document_date = strtoupper(date('M j, Y', strtotime($record->created_on)));
            $supplier = $this->custom->getMultiValues('master_supplier', 'name, code', ['supplier_id' => $record->supplier_id]);
            $html .= '<tr>
				<td style="width: 120px">'.$document_date.'</td>
				<td style="width: 110px">'.$record->payment_ref_no.'</td>
				<td style="width: 280px">'.strtoupper($supplier->name).'<br />'.strtoupper($supplier->code).' | <span style="color: brown">'.$record->currency.'</span></td>
				<td style="width: 100px">'.$record->bank.'</td>
				<td style="width: 110px">'.$record->cheque.'</td>
				<td style="width: 150px">'.number_format($record->amount, 2).'</td>
				<td style="width: 150px">'.$record->other_reference.'</td>
			</tr>';
            ++$i;
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="7" style="color: red; text-align: center">No Payments</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'payment_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'payment_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['accounts_payable', 'payment_setting', 'payment_master', 'payment_purchase_master'],
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
            set_flash_message('message', 'success', 'PAYMENT RESTORED');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('payment/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'payment/blank.php';
        zapPayment();
        redirect('payment/', 'refresh');
    }
}
