<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Data_migration extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('data_migration/data_migration_model', 'data_migration');
    }

    public function options()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function import()
    {
        is_logged_in('admin');
        has_permission();

        $process = $this->uri->segment(3);
        $title = 'Data Migration';

        if ($process == 'customer') {
            $title = 'Customer & Supplier';
        } elseif ($process == 'billing') {
            $title = 'Billing';
        } elseif ($process == 'employee') {
            $title = 'Employee';
        } elseif ($process == 'purchase_gst') {
            $title = 'Purchase GST';
        } elseif ($process == 'supply_gst') {
            $title = 'Supply GST';
        } elseif ($process == 'forex') {
            $title = 'Forex';
        } elseif ($process == 'fb_master') {
            $title = 'Foreign Bank Master';
        } elseif ($process == 'fb_ledger') {
            $title = 'Foreign Bank Subledger';
        } elseif ($process == 'ar') {
            $title = 'Accounts Receivable';
        } elseif ($process == 'ap') {
            $title = 'Accounts Payable';
        } elseif ($process == 'coa') {
            $title = 'Chart Of Account';
        } elseif ($process == 'gl') {
            $title = 'General Ledger';
        } elseif ($process == 'gst') {
            $title = 'GST';
        }

        $this->body_vars['title'] = $title;
        $this->body_vars['save_url'] = '/data_migration/'.$process;
    }

    public function customer($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $master_dbf = new DBFhandler($dbf_file, 'CUSTOMER.FPT');

            zapCustomer();
            zapSupplier();
            while (($dbf_record = $master_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $master_data['code'] = $dbf_record['IDEN'];
                $master_data['name'] = $dbf_record['NAME'];
                $master_data['contact_person'] = $dbf_record['CONT'];

                $address1 = $dbf_record['ADD1'];
                $address2 = $dbf_record['ADD2'];
                $address3 = $dbf_record['ADD3'];
                $address4 = $dbf_record['ADD4'];
                $address5 = $dbf_record['ADD5'];

                $postal_code = '';
                $street_name = '';
                $bldg_number = '';
                if ($address5 != '') {
                    $postal_code = $address5;
                    $street_name = $address2.', '.$address3.', '.$address4;
                    $bldg_number = $address1;
                } elseif ($address4 != '') {
                    $postal_code = $address4;
                    $street_name = $address2.', '.$address3;
                    $bldg_number = $address1;
                } elseif ($address3 != '') {
                    $postal_code = $address3;
                    $street_name = $address2;
                    $bldg_number = $address1;
                } elseif ($address2 != '') {
                    $street_name = $address2;
                    $bldg_number = $address1;
                } elseif ($address1 != '') {
                    $bldg_number = $address1;
                }
                $master_data['bldg_number'] = str_replace(';', ',', $bldg_number);
                $master_data['street_name'] = str_replace(';', ',', $street_name);
                $master_data['postal_code'] = str_replace(';', ',', $postal_code);

                $master_data['phone'] = $dbf_record['TEL1'];
                $master_data['fax'] = $dbf_record['FAXI'];
                $master_data['email'] = $dbf_record['TELX'];

                $master_data['uen_no'] = $dbf_record['UEN'];
                $master_data['gst_number'] = $dbf_record['GSTNO'];

                $master_data['currency_id'] = $this->custom->getSingleValue('ct_currency', 'currency_id', ['code' => $dbf_record['CURR']]);
                if ($dbf_record['CTY'] == null) {
                    $master_data['country_id'] = 7; // Singapore
                } else {
                    $master_data['country_id'] = $this->custom->getSingleValue('ct_country', 'country_id', ['country_code' => $dbf_record['CTY']]);
                }

                if ($dbf_record['IDEN'] != '' && $dbf_record['NAME'] != '') {
                    if ($dbf_record['FLAG'] == 'N') { // supplier
                        $result = $this->custom->insertRow('master_supplier', $master_data);
                    } else { // Flag == 'Y' and Flag == '' - Customer
                        $master_data['credit_limit'] = $dbf_record['LIMI'];
                        $master_data['credit_term_days'] = $dbf_record['TERM'];
                        $result = $this->custom->insertRow('master_customer', $master_data);
                    }
                }
                unset($master_data);
            }

            set_flash_message('message', 'success', 'Imported Customer & Supplier Master');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function employee($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $employee_dbf = new DBFhandler($dbf_file, 'EMPLOYEE.FPT');

            zapEmployee();
            while (($dbf_record = $employee_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $employee_data['code'] = $dbf_record['SMAN'];
                $employee_data['name'] = $dbf_record['NAME'];

                if ($dbf_record['SMAN'] != '') {
                    $this->custom->insertRow('master_employee', $employee_data);
                }
            }
            set_flash_message('message', 'success', 'Imported Employee Master');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function billing($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $billing_dbf = new DBFhandler($dbf_file, 'FOREX.FPT');

            zapBilling();
            while (($dbf_record = $billing_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $billing_data['stock_code'] = $dbf_record['CODE'];
                $desc = $dbf_record['DESC'];

                for ($i = 1; $i <= 9; ++$i) {
                    if ($dbf_record['DESC'.$i] != '') {
                        $desc .= '<br />'.$dbf_record['DESC'.$i];
                    }
                }

                // trim to remove left and right white space, preg to reduce more than one space to one inside the string
                $desc = trim(preg_replace('/\s+/', ' ', $desc));

                // $desc = str_replace("(","[",$desc);
                $desc = str_replace(';', ',', $desc);

                $billing_data['billing_description'] = $desc;
                $billing_data['billing_uom'] = $dbf_record['UOM'];
                if ($dbf_record['UOM'] == '') {
                    $billing_data['billing_type'] = 'Service';
                    $billing_data['billing_update_stock'] = 'NO';
                } else {
                    $billing_data['billing_type'] = 'Product';
                    $billing_data['billing_update_stock'] = 'YES';
                }

                $billing_data['billing_price_per_uom'] = $dbf_record['PRIC'];

                // gst id is required in order to display them in datatable as it is foreign key so keeping it 1
                $billing_data['gst_id'] = 1;

                if ($dbf_record['CODE'] != '') {
                    $this->custom->insertRow('master_billing', $billing_data);
                }
            }

            set_flash_message('message', 'success', 'Imported Billing Master');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function purchase_gst()
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $gst_dbf = new DBFhandler($dbf_file, 'GST_P.FPT');

            zapGSTMasterPurchase();
            while (($dbf_record = $gst_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $gst_data['gst_code'] = $dbf_record['CODE'];

                $gst_rate = 0;
                if ($dbf_record['PERC'] != '') {
                    $gst_rate = $dbf_record['PERC'];
                }
                $gst_data['gst_rate'] = $gst_rate;

                $gst_desc = '';
                if ($dbf_record['DESC'] != '') {
                    $gst_desc .= $dbf_record['DESC'];
                }

                if ($dbf_record['DESC'] != '' && $dbf_record['DESC2'] != '') {
                    $gst_desc .= '<br />';
                }

                if ($dbf_record['DESC2'] != '') {
                    $gst_desc .= $dbf_record['DESC2'];
                }

                $gst_data['gst_description'] = $gst_desc;

                $gst_data['gst_type'] = 'purchase';

                if ($dbf_record['CODE'] != null && $dbf_record['CODE'] != '') {
                    $result = $this->custom->insertRow('ct_gst', $gst_data);
                }
            }

            set_flash_message('message', 'success', 'Imported GST Purchase');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function supply_gst($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $gst_dbf = new DBFhandler($dbf_file, 'GST_S.FPT');

            zapGSTMasterSupply();
            while (($dbf_record = $gst_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $gst_data['gst_code'] = $dbf_record['CODE'];

                $gst_rate = 0;
                if ($dbf_record['PERC'] != '') {
                    $gst_rate = $dbf_record['PERC'];
                }
                $gst_data['gst_rate'] = $gst_rate;

                $gst_desc = '';
                if ($dbf_record['DESC'] != '') {
                    $gst_desc .= $dbf_record['DESC'];
                }

                if ($dbf_record['DESC'] != '' && $dbf_record['DESC2'] != '') {
                    $gst_desc .= '<br />';
                }

                if ($dbf_record['DESC2'] != '') {
                    $gst_desc .= $dbf_record['DESC2'];
                }

                $gst_data['gst_description'] = $gst_desc;

                $gst_data['gst_type'] = 'supply';

                if ($dbf_record['CODE'] != null && $dbf_record['CODE'] != '') {
                    $result = $this->custom->insertRow('ct_gst', $gst_data);
                }
            }

            set_flash_message('message', 'success', 'Imported GST Supply');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function forex($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $forex_dbf = new DBFhandler($dbf_file, 'FOREX.FPT');

            zapCurrency();
            while (($dbf_record = $forex_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $forex_data['code'] = $dbf_record['CURR'];

                $currency_rate = 1 / $dbf_record['RATE'];
                $forex_data['rate'] = $currency_rate;

                $forex_data['description'] = $dbf_record['DESC'];

                // Created on filed is not added in ct_currency.tbl, so commenting this code
                /*$year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $forex_data['created_on'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));*/

                if ($dbf_record['CURR'] != '' && $dbf_record['RATE'] != '') {
                    $this->custom->insertRow('ct_currency', $forex_data);
                }
            }

            set_flash_message('message', 'success', 'Imported Forex');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function coa($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $coa_dbf = new DBFhandler($dbf_file, 'COA.FPT');

            zapCOA();
            while (($dbf_record = $coa_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $coa_data['accn'] = $dbf_record['ACCN'];
                $coa_data['description'] = str_replace("'", '', $dbf_record['DESC']);

                if ($dbf_record['ACCN'] != null && $dbf_record['ACCN'] != '') {
                    $this->custom->insertRow('chart_of_account', $coa_data);
                    ++$entry;
                }
            }

            set_flash_message('message', 'success', 'Imported Chart of Accounts');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function gl($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $gl_dbf = new DBFhandler($dbf_file, 'GL.FPT');

            zapGL();
            while (($dbf_record = $gl_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $gl_data['doc_date'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));

                $gl_data['ref_no'] = $dbf_record['DREF'];
                $gl_data['remarks'] = $dbf_record['REMA'];
                $gl_data['accn'] = $dbf_record['ACCN'];

                $amount = $dbf_record['AMOU'];
                if ($amount < 0) {
                    $gl_data['sign'] = '-';
                    $gl_data['total_amount'] = (-1) * $amount;
                } else {
                    $gl_data['sign'] = '+';
                    $gl_data['total_amount'] = $amount;
                }

                $gl_data['gstcat'] = $dbf_record['XREF'];

                $bf_flag = $dbf_record['BF_FLAG'];
                $tran_type = $this->get_tran_type($bf_flag);
                $gl_data['tran_type'] = $tran_type;

                $gl_data['sman'] = $dbf_record['CNTR'];
                $gl_data['iden'] = $dbf_record['IDEN'];

                if ($dbf_record['ACCN'] != '' && $dbf_record['DREF'] != '') {
                    $this->custom->insertRow('gl', $gl_data);
                }
            }

            set_flash_message('message', 'success', 'Imported GL');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function gst($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $gst_dbf = new DBFhandler($dbf_file, 'GST.FPT');

            zapGST();
            while (($dbf_record = $gst_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $gst_data['gstcate'] = $dbf_record['GSTCATE'];
                $gst_data['gsttype'] = $dbf_record['GSTTYPE'];
                $gst_data['dref'] = $dbf_record['DREF'];

                $year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $gst_data['date'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));

                $gst_data['rema'] = $dbf_record['REMA'];
                $gst_data['iden'] = $dbf_record['IDEN'];

                $amount = $dbf_record['AMOU'];
                if ($amount < 0) {
                    $gst_data['amou'] = (-1) * $amount;
                } else {
                    $gst_data['amou'] = $amount;
                }

                $gst_data['gstperc'] = $dbf_record['GSTPERC'];

                $gst_amount = $dbf_record['GSTAMOU'];
                if ($gst_amount < 0) {
                    $gst_data['gstamou'] = (-1) * $gst_amount;
                } else {
                    $gst_data['gstamou'] = $gst_amount;
                }

                $bf_flag = $dbf_record['BF_FLAG'];
                $tran_type = $this->get_tran_type($bf_flag);
                $gst_data['tran_type'] = $tran_type;

                if ($dbf_record['GSTCATE'] != '' && $dbf_record['GSTTYPE'] != '') {
                    $this->custom->insertRow('gst', $gst_data);
                }
            }

            set_flash_message('message', 'success', 'Imported GST');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function ar($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $ar_dbf = new DBFhandler($dbf_file, 'AR.FPT');

            zapAR();
            while (($dbf_record = $ar_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $ar_data['doc_ref_no'] = $dbf_record['DREF'];
                $ar_data['customer_code'] = $dbf_record['IDEN'];

                $year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $ar_data['doc_date'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));

                $ar_data['currency'] = $dbf_record['CURR'];

                $local_amount = $dbf_record['LAMT'];
                if ($local_amount < 0) {
                    $ar_data['total_amt'] = (-1) * $local_amount;
                    $ar_data['sign'] = '-';
                } else {
                    $ar_data['total_amt'] = $local_amount;
                    $ar_data['sign'] = '+';
                }

                $foreign_amount_debit = $dbf_record['DAMO'];
                $foreign_amount_credit = $dbf_record['CAMO'];

                if ($foreign_amount_debit != '' && $foreign_amount_debit > 0) {
                    $ar_data['f_amt'] = $foreign_amount_debit;
                } elseif ($foreign_amount_credit != '' && $foreign_amount_credit > 0) {
                    $ar_data['f_amt'] = $foreign_amount_credit;
                }

                $ar_data['remarks'] = $dbf_record['REMA'];

                $bf_flag = $dbf_record['BF_FLAG'];
                $tran_type = $this->get_tran_type($bf_flag);
                $ar_data['tran_type'] = $tran_type;

                if ($dbf_record['DREF'] != '' && $dbf_record['IDEN'] != '') {
                    $this->custom->insertRow('accounts_receivable', $ar_data);
                }
            }

            set_flash_message('message', 'success', 'Imported Accounts Receivable');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function ap($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $ap_dbf = new DBFhandler($dbf_file, 'AP.FPT');

            zapAP();
            while (($dbf_record = $ap_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $ap_data['doc_ref_no'] = $dbf_record['DREF'];
                $ap_data['supplier_code'] = $dbf_record['IDEN'];

                $year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $ap_data['doc_date'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));

                $ap_data['currency'] = $dbf_record['CURR'];

                $local_amount = $dbf_record['LAMT'];
                if ($local_amount < 0) {
                    $ap_data['total_amt'] = (-1) * $local_amount;
                    $ap_data['sign'] = '-';
                } else {
                    $ap_data['total_amt'] = $local_amount;
                    $ap_data['sign'] = '+';
                }

                $foreign_amount_debit = $dbf_record['DAMO'];
                $foreign_amount_credit = $dbf_record['CAMO'];

                if ($foreign_amount_debit != '' && $foreign_amount_debit > 0) {
                    $ap_data['fa_amt'] = $foreign_amount_debit;
                } elseif ($foreign_amount_credit != '' && $foreign_amount_credit > 0) {
                    $ap_data['fa_amt'] = $foreign_amount_credit;
                }

                $ap_data['remarks'] = $dbf_record['REMA'];

                $bf_flag = $dbf_record['BF_FLAG'];
                $tran_type = $this->get_tran_type($bf_flag);
                $ap_data['tran_type'] = $tran_type;

                if ($dbf_record['DREF'] != '' && $dbf_record['IDEN'] != '') {
                    $this->custom->insertRow('accounts_payable', $ap_data);
                }
            }

            set_flash_message('message', 'success', 'Imported Accounts Payable');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function fb_master($action = 'form')
    {
        is_logged_in('admin');
        has_permission();

        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $fb_dbf = new DBFhandler($dbf_file, 'FB.FPT');

            zapFBMaster();
            while (($dbf_record = $fb_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $fb_data['fb_code'] = $dbf_record['BCODE'];
                $fb_data['fb_name'] = $dbf_record['DESC'];

                $currency_id = $this->custom->getSingleValue('ct_currency', 'currency_id', ['code' => $dbf_record['CURR']]);
                $fb_data['currency_id'] = $currency_id;

                if ($dbf_record['CURR'] != '') {
                    $this->custom->insertRow('master_foreign_bank', $fb_data);
                }
            }

            set_flash_message('message', 'success', 'Imported FB Master');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function fb_ledger($action = 'form')
    {
        $data = file_upload(date('YmdHis'), 'db_file', 'database_import_files');
        $this->load->helper('file');

        if ($data['status']) {
            $dbf_file = $data['upload_data']['full_path'];
            $fb_dbf = new DBFhandler($dbf_file, 'FB.FPT');

            zapFB();
            while (($dbf_record = $fb_dbf->GetNextRecord(true)) and !empty($dbf_record)) {
                $fb_data['doc_ref_no'] = $dbf_record['DREF'];

                $year = substr($dbf_record['DATE'], 0, 4);
                $month = substr($dbf_record['DATE'], 4, 2);
                $day = substr($dbf_record['DATE'], 6, 2);
                $fb_data['doc_date'] = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));

                $fb_data['fb_code'] = $dbf_record['BCODE'];
                $fb_data['currency'] = $dbf_record['CURR'];

                $local_amount = $dbf_record['LAMT'];
                if ($local_amount < 0) {
                    $fb_data['local_amt'] = (-1) * $local_amount;
                    $fb_data['sign'] = '-';
                } else {
                    $fb_data['local_amt'] = $local_amount;
                    $fb_data['sign'] = '+';
                }

                $foreign_amount_debit = $dbf_record['DAMO'];
                $foreign_amount_credit = $dbf_record['CAMO'];

                if ($foreign_amount_debit != '' && $foreign_amount_debit > 0) {
                    $fb_data['fa_amt'] = $foreign_amount_debit;
                } elseif ($foreign_amount_credit != '' && $foreign_amount_credit > 0) {
                    $fb_data['fa_amt'] = $foreign_amount_credit;
                }

                $fb_data['remarks'] = $dbf_record['REMA'];

                $bf_flag = $dbf_record['BF_FLAG'];
                $tran_type = $this->get_tran_type($bf_flag);
                $fb_data['tran_type'] = $tran_type;

                if ($dbf_record['DREF'] != '' && $dbf_record['DATE'] != '') {
                    $this->custom->insertRow('foreign_bank', $fb_data);
                }
            }

            set_flash_message('message', 'success', 'Imported Foreign Bank Subledger');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }

        redirect('data_migration/options', 'refresh');
    }

    public function get_tran_type($bf_flag)
    {
        $tran_type = '';
        if ($bf_flag == 'T') {
            $tran_type = 'OPBAL';
        } elseif ($bf_flag == 'M') {
            $tran_type = 'OPBAL';
        } elseif ($bf_flag == 'A') {
            $tran_type = 'INVOICE';
        } elseif ($bf_flag == 'B') {
            $tran_type = 'INVOICE';
        } elseif ($bf_flag == 'C') {
            $tran_type = 'BTHSALE';
        } elseif ($bf_flag == 'D') {
            $tran_type = 'BTHPURC';
        } elseif ($bf_flag == 'E') {
            $tran_type = 'BTHPURC';
        } elseif ($bf_flag == 'F') {
            $tran_type = 'EZPAY';
        } elseif ($bf_flag == 'G') {
            $tran_type = 'PTCASH';
        } elseif ($bf_flag == 'H') {
            $tran_type = 'OPBAL';
        } elseif ($bf_flag == 'I') {
            $tran_type = 'RECEIPT';
        } elseif ($bf_flag == 'J') {
            $tran_type = 'JRENTR';
        } elseif ($bf_flag == 'K') {
            $tran_type = 'K';
        }

        return $tran_type;
    }
}
