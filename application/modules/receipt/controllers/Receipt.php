<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Receipt extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('receipt/receipt_model', 'rec_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'receipt/options.php';
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

        $update = $this->custom->updateRow('receipt_setting', ['number_suffix' => $number_suffix], ['text_prefix' => $setting->text_prefix]);        

        return $ref_no;
    }

    public function create()
    {
        is_logged_in('admin');
        has_permission();

        $setting = $this->custom->getLastInsertedRow('receipt_setting', 'updated_on');
        if (is_null($setting)) {
            set_flash_message('message', 'warning', 'Define a Receipt Settings first !');
            redirect('receipt/');
        }
                
        $this->body_vars['receipt_ref_no'] = $this->generate_ref_no($setting);

        $this->body_vars['customer_options'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ' '], ['active' => 1]);

        $bank_data = $this->custom->getSingleRow('bank', ['accn_type' => 'CA']);
        $this->body_vars['bank_accn'] = $bank_data->accn;
        $this->body_vars['fb_accn'] = $bank_data->fb_accn;
        $this->body_vars['bank_accn_list'] = $this->custom->populateCOABankListWithFB($bank_data->accn);
        $this->body_vars['fb_accn_list'] = $this->custom->populateFBAccounts($bank_data->fb_accn);

        $this->body_file = 'receipt/create.php';
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

            // inserting into receipt_master tbl
            $document_date = date('Y-m-d', strtotime($data['created_on']));
            $mt_data['receipt_ref_no'] = $data['receipt_ref_no'];
            $mt_data['customer_id'] = $data['customer_id'];
            $mt_data['user_id'] = $this->session->user_id;
            $mt_data['invoice_reference_id'] = '';
            $mt_data['bank'] = $data['bank'];
            $mt_data['cheque'] = $data['cheque'];
            $mt_data['other_reference'] = $data['other_reference'];
            $mt_data['currency'] = $data['currency'];
            $mt_data['amount'] = $data['amount'];
            $mt_data['bank_accn'] = $data['bank_accn'];
            $mt_data['fb_accn'] = $data['fb_accn'];
            $mt_data['doc_date'] = $document_date;
            $mt_data['modified_on'] = date('Y-m-d');
            $receipt_id = $this->custom->insertRow('receipt_master', $mt_data);

            // contra : YES  *** inserting debit and credit entries into receipt_invoice_master tbl
            if ($data['ar_ids'] != '') {
                $splitted_ar_ids = explode(',', $data['ar_ids']);
                $final_balance_entry_id = $data['final_balance_entry_id'];
                $final_balance_entry_reference = $data['final_balance_entry_reference'];
                $final_balance_total = $data['final_balance_total'];

                foreach ($splitted_ar_ids as $value) {
                    $ar_id = $value;                    

                    $partial_used_amount = 0;
                    $i = 0;

                    // check invoice or credit reference is partially used in receipt which is CONFIRMED but not POSTED YET
                    $this->db->select('full_amount, rec_inv_amount');
                    $this->db->from('receipt_invoice_master, receipt_master');
                    $this->db->where('receipt_invoice_master.receipt_id = receipt_master.receipt_id AND receipt_invoice_master.invoice_id = '.$ar_id.' AND receipt_master.receipt_status = "C"');
                    $query = $this->db->get();
                    $partial_data = $query->result();
                    foreach ($partial_data as $record) {
                        ++$i;
                        $partial_used_amount += $record->full_amount - $record->rec_inv_amount;
                    }

                    $ar_data = $this->custom->getSingleRow('accounts_receivable', ['ar_id' => $ar_id]);
                    $full_amount = $ar_data->f_amt;
                    $invoice_id = $ar_data->ar_id;
                    if ($i > 0) {
                        $full_amount = $ar_data->f_amt - $partial_used_amount;
                    }

                    $entry_data['invoice_id'] = $invoice_id;
                    $entry_data['rec_inv_amount'] = $full_amount;
                    if ($final_balance_entry_id !== '' && $ar_id == $final_balance_entry_id) {
                        $entry_data['rec_inv_amount'] = $final_balance_total;
                    }
                    $entry_data['receipt_id'] = $receipt_id;
                    $entry_data['full_amount'] = $full_amount;

                    $inserted[] = $this->custom->insertRow('receipt_invoice_master', $entry_data);
                }
            }

            if ($this->db->trans_status() === false || in_array('error', $inserted)) {
                set_flash_message('message', 'danger', 'RECEIPT ERROR');
                $this->db->trans_rollback();
            } else {
                set_flash_message('message', 'success', 'RECEIPT CREATED');
                $this->db->trans_commit();
            }

            redirect('receipt/listing/');
        }
        exit;
    }

    public function listing()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage($mode, $receipt_id = '')
    {
        is_logged_in('admin');
        has_permission();

        $documentToRow = '';
        if ($receipt_id != '') {
            // receipt master details
            $this->body_vars['receipt_data'] = $receipt_data = $this->custom->getSingleRow('receipt_master', ['receipt_id' => $receipt_id]);

            if ($receipt_data) {
                // customer id
                $customer_id = $receipt_data->customer_id;

                // company details
                $company_where = ['code' => 'CP'];
                $this->body_vars['company_details'] = $company_details = $this->custom->getSingleRow('company_profile', $company_where);

                if (isset($mode)) {
                    $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $customer_id]);
                    $this->body_vars['customer_name_code'] = $customer->name.' ('.$customer->code.')';
                    $this->body_vars['customer_address'] = $this->custom->populateCustomerAddress($customer);

                    // currency details
                    $this->body_vars['currency_code'] = $receipt_data->currency;

                    $this->body_vars['receipt_id'] = $receipt_id;
                    $this->body_vars['customer_id'] = $customer_id;
                    $this->body_vars['bank'] = $receipt_data->bank;
                    $this->body_vars['cheque'] = $receipt_data->cheque;
                    $this->body_vars['other_reference'] = $receipt_data->other_reference;
                    $this->body_vars['amount'] = $receipt_data->amount;

                    $this->body_vars['receipt_ref_no'] = $receipt_data->receipt_ref_no;

                    // receipt date
                    $receipt_date = date('d-m-Y', strtotime($receipt_data->modified_on));
                    $this->body_vars['receipt_date'] = $receipt_date;

                    if ($mode == 'view') {
                        $balance = 0;
                        $records = 0;
                        $documents = $this->rec_model->get_rec_ar_entries($receipt_id);
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

                            ++$records;
                        }

                        $documentToRow .= '<tr >';
                        if ($records > 0) {
                            $documentToRow .= '<td style="border: none"></td>';
                            if ($balance < 0) {
                                $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format((-1) * $balance, 2).' CR</td>';
                            } else {
                                $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format($balance, 2).' DR</td>';
                            }
                        } else {
                            $documentToRow .= '<td colspan="2" style="border: 1px solid #ccc; padding: 10px 10px;">No details found</td>';
                        }
                        $documentToRow .= '</tr>';

                        $this->body_vars['documentToRow'] = $documentToRow;
                        $this->body_vars['mode'] = 'view';

                        $this->body_vars['listing_type'] = $receipt_data->receipt_status;

                        $this->body_file = 'receipt/view.php';
                    } elseif ($mode == 'edit') {
                        $this->body_vars['save_url'] = '/receipt/save/edit';
                        $this->body_file = 'receipt/edit.php';
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
        $this->data['receipt_ref_no'] = $data['receipt_ref_no'];

        // Customer & Currency Details
        $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $data['customer_id']]);
        $this->data['customer_address'] = $this->custom->populateCustomerAddress($customer);
        $this->data['customer_name'] = $customer->name;
        $this->data['customer_code'] = $customer->code;
        $this->data['customer_gst_number'] = $customer->gst_number;

        $currency = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer->currency_id]);
        $this->data['customer_currency'] = $currency->code;
        $this->data['currency_rate'] = $currency->rate;

        // Receipt Page Inputs
        $this->data['bank'] = $data['bank'];
        $this->data['cheque'] = $data['cheque'];
        $this->data['amount'] = $data['amount'];
        $this->data['other_reference'] = $data['other_reference'];

        $ar_ids = $data['ar_ids'];
        if ($ar_ids != '') {
            $splitted_ar_ids = explode(',', $ar_ids);
        } else {
            $splitted_ar_ids = '';
        }

        $balance = 0;
        $documentToRow = '';
        $entry = 0;
        if (is_array($splitted_ar_ids) || is_object($splitted_ar_ids)) {
            foreach ($splitted_ar_ids as $value) {
                $ar_id = $value;
                $ar_data = $this->custom->getSingleRow('accounts_receivable', ['ar_id' => $ar_id]);

                $documentToRow .= '<tr style="page-break-inside: avoid;">';
                $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px;">'.$ar_data->doc_ref_no.'</td>';

                if ($ar_data->sign == '+') {
                    $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($ar_data->f_amt, 2).' DR</td>';
                    $balance += $ar_data->f_amt;
                } else {
                    $documentToRow .= '<td style="border: 1px solid #ccc; padding: 10px 10px; text-align: right">$'.number_format($ar_data->f_amt, 2).' CR</td>';
                    $balance -= $ar_data->f_amt;
                }

                $documentToRow .= '</tr>';

                ++$entry;
            }
        }

        // balance row
        $documentToRow .= '<tr style="page-break-inside: avoid;">';
        $documentToRow .= '<td style="border: none"></td>';
        if ($balance < 0) {
            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format((-1) * $balance, 2).' CR</td>';
        } else {
            $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format($balance, 2).' DR</td>';
        }
        $documentToRow .= '</tr>';

        $this->data['documentToRow'] = $documentToRow;
        $this->data['entry'] = $entry;

        $file = 'receipt_'.date('YmdHis').'.pdf';
        $document = $this->load->view('receipt/print_stage_1.php', $this->data, true);
        $this->custom->printMPDF($file, $document);
    }

    public function print_stage_2()
    {
        if (isset($_GET['rowID'])) {
            $receipt_id = $_GET['rowID'];
            $action = 'print';
        } else {
            $receipt_id = $this->input->post('rowID');
            $action = 'email';
        }

        $documentToRow = '';

        $this->data['mt_data'] = $mt_data = $this->custom->getSingleRow('receipt_master', ['receipt_id' => $receipt_id]);

        if ($mt_data) {

            $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $mt_data->customer_id]);
            $this->data['customer_name_code'] = $customer->name.' ('.$customer->code.')';
            $this->data['customer_address'] = $this->custom->populateCustomerAddress($customer);

            $balance = 0;
            $records = 0;
            $documents = $this->rec_model->get_rec_ar_entries($receipt_id);
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
                    $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format((-1) * $balance, 2).' CR</td>';
                } else {
                    $documentToRow .= '<td style="padding: 5px 10px; border-left: none; border-right: none; border-top: 2px solid dimgray; border-bottom: 2px solid dimgray; font-weight: bold; text-align: right">$'.number_format($balance, 2).' DR</td>';
                }
            } else {
                $documentToRow .= '<td colspan="2" style="border: 1px solid #ccc; padding: 10px 10px;">No details found</td>';
            }
            $documentToRow .= '</tr>';

            $this->data['documentToRow'] = $documentToRow;

            $file = 'receipt_'.date('YmdHis').'.pdf';
            $document = $this->load->view('receipt/print_stage_2.php', $this->data, true);
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

                $path = BASEPATH.'upload/receipt/';

                if (is_dir($path)) {
                    $mpdf->Output(realpath($path).'/'.$file, 'F');
                } else {
                    echo 'error'.$path;
                }

                $pdfFilePath = realpath($path).'/'.$file;

                // Email settings
                $to_address = $customer->email;

                if ($to_address !== '') {
                    
                    $mail = $this->custom->populateEmailHeaders();

                    // to address
                    $mail->addAddress($to);

                    // subject
                    $mail->Subject = 'Information About Receipt';

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
            
        } else {
            redirect('receipt/listing/', 'refresh');
        }
        
    }

    public function print_receipts()
    {
        $type = strtoupper($this->uri->segment(3));

        $html = '';

        $html .= "<div style='width: 100%; margin: auto; text-align: center;'><h3>".strtoupper($type)." RECEIPT'S</h3></div>";

        $html .= '<table>
			<tr>
				<th>Date</th>
				<th>Reference</th>
				<th>Customer</th>
				<th>Bank</th>
				<th>Cheque</th>
				<th>Amount</th>
				<th>Remarks</th>
			</tr>
		';

        $i = 0;

        $this->db->select('*');
        $this->db->from('receipt_master');
        $this->db->where('receipt_status = "'.$type.'"');
        $this->db->order_by('receipt_ref_no', 'DESC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $record) {
            $document_date = strtoupper(date('M j, Y', strtotime($record->doc_date)));
            $customer = $this->custom->getMultiValues('master_customer', 'name, code', ['customer_id' => $record->customer_id]);
            $html .= '<tr>
				<td style="width: 120px">'.$document_date.'</td>
				<td style="width: 110px">'.$record->receipt_ref_no.'</td>
				<td style="width: 280px">'.strtoupper($customer->name).'<br />'.strtoupper($customer->code).' | <span style="color: brown">'.$record->currency.'</span></td>
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

        $file = 'receipt_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'receipt_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['receipt_setting', 'receipt_master', 'receipt_invoice_master'],
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
            set_flash_message('message', 'success', 'Receipt Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('receipt/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'receipt/blank.php';
        zapReceipt();
        redirect('receipt/', 'refresh');
    }
}
