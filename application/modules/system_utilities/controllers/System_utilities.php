<?php

defined('BASEPATH') or exit('No direct script access allowed');

class System_utilities extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('system_utilities/system_utilities_model', 'su_model');
    }

    public function db_options()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function db_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
          'tables' => [
            'accounts_payable',
            'accounts_receivable',
            'ap_open',
            'ar_open',
            'bank',
            'bank_recon_current',
            'bank_recon_info',
            'bank_recon_last',
            'chart_of_account',
            'chart_of_account_prefix',
            // 'company_profile', Just added these 2 in the enhancements phase and commented for now
            // 'configuration_master',
            // 'ct_country', combo tables - not added
            // 'currency',
            // 'ct_currency', combo tables - not added
            // 'ct_gst', combo tables - not added
            'customer_price',
            // 'default_bank' not added
            'ez_sales',
            'ez_purchase',
            'ez_receipt',
            'ez_settlement',
            'ez_payment',
            'ez_adjustment',
            'ez_debtor',
            'ez_creditor',
            'fb_open',
            'foreign_bank',
            'gl',
            'gl_open',
            'gl_single_entry',
            // 'groups' not needed to backup
            'gst',
            'gst_open',
            'gst_returns_contact_info',
            'gst_returns_declaration',
            'gst_returns_filing_info',
            'gst_returns_form_5',
            'gst_returns_form_7',
            'gst_returns_grp_reasons',
            'gst_revenue_setting',
            'gst_std_rate',
            'invoice_master',
            'invoice_product_master',
            'invoice_setting',
            'master_accountant',
            'master_billing',
            'master_customer',
            'master_department',
            'master_employee',
            'master_foreign_bank',
            'master_supplier',
            'other_payment',
            'payment_master',
            'payment_purchase_master',
            'payment_setting',
            'petty_cash_batch',
            'petty_cash_setting',
            'pl_data',
            'quotation_master',
            'quotation_product_master',
            'quotation_setting',
            'receipt_invoice_master',
            'receipt_master',
            'receipt_setting',
            'sac_job',            
            'staff_activity',
            'stock',
            'stock_adjustment',
            'stock_cost',
            'stock_open',
            'stock_purchase',
            // 'users', - not needed
            // 'users_groups', - not needed
            'ye_closing',
            'ye_revision',
            'ye_values_before_revision',
        ],
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

    public function db_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'system_utilities/blank.php';

        zapCustomer();
        zapSupplier();
        zapBilling();
        zapEmployee();
        zapDepartment();
        zapForeignBank();
        zapAccountant();
        zapQuotation();
        zapInvoice();
        zapCustomer_price();
        zapReceipt();
        zapPayment();
        zapStock();
        zapAR();
        zapAP();
        zapFB();
        zapPettyCash();
        zapBankRecon();
        zapEzentry();
        zapGST();
        zapGL();
        zapStaffActivity();
        zapSACJob();
        set_flash_message('message', 'success', 'Database Zapped');
        redirect('/system_utilities/db_options', 'refresh');
    }

    public function db_restore($action = 'form')
    {
        is_logged_in('admin');
        if ($action == 'form') {
            $this->body_vars['save_url'] = '/system_utilities/db_restore/save';
        }
        if ($action == 'save') {
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
                set_flash_message('message', 'success', 'Database Restored');
            } else {
                set_flash_message('message', 'warning', $data['error']);
            }
            redirect('system_utilities/db_options', 'refresh');
        }
    }

    public function DownloadFile($file)
    { // $file = include path
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
    }

    public function getInsertReceiptDbf($start_date = '', $end_date = '')
    {
        $table = 'receipt_master';
        $join_table = ['master_customer',
                        'ct_currency', ];

        // $where = ['user_id' => $this->session->user_id/* , 'export_status' => '0' */];
        $where = ['receipt_status' => 'C'];

        $join_condition = [
                        'receipt_master.customer_id = master_customer.customer_id',
                        'master_customer.currency_id = ct_currency.currency_id',
                    ];

        $c_start_date = date('Y-m-d', strtotime($start_date));
        if ($start_date != '' && $c_start_date != '1970-01-01') {
            $where['receipt_master.doc_date >= '] = $c_start_date;
        }

        $c_end_date = date('Y-m-d', strtotime($end_date));
        if ($end_date != '' && $c_end_date != '1970-01-01') {
            $where['receipt_master.doc_date <= '] = $c_end_date;
        }

        $columns = [
                    'receipt_master.receipt_ref_no',
                    'receipt_master.doc_date',
                    'master_customer.code',
                    'receipt_master.currency',
                    'ct_currency.rate',
                    'receipt_master.amount AS foreign_amount',
                    'receipt_master.amount/ct_currency.rate AS local_amount',
                    'other_reference', ];

        $table_id = 'receipt_id';
        $list = $this->su_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $table_id);

        return $list;
    }

    public function export_receipts($start_date = '', $end_date = '')
    {
        echo "<script>console.log( 'export_receipts from Receipt List :: ".$start_date.$end_date."' );</script>";

        require_once 'util_github/Column.class.php';
        require_once 'util_github/Record.class.php';
        require_once 'util_github/Table.class.php';
        require_once 'util_github/WritableTable.class.php';

        /* sample data */
        $fields = [
        ['ENTR', DBFFIELD_TYPE_CHAR, 5, 0],
        ['ACCN', DBFFIELD_TYPE_CHAR, 5, 0],
        ['DREF', DBFFIELD_TYPE_CHAR, 12, 0],
        ['DATE', DBFFIELD_TYPE_DATE, 10, 0],
        ['IDEN', DBFFIELD_TYPE_CHAR, 10, 0],
        ['CURR', DBFFIELD_TYPE_CHAR, 3, 0],
        ['RATE', DBFFIELD_TYPE_FLOATING, 9, 4],
        ['FAMT', DBFFIELD_TYPE_FLOATING, 13, 2],
        ['AMOU', DBFFIELD_TYPE_FLOATING, 13, 2],
        ['REMA', DBFFIELD_TYPE_CHAR, 80, 0],
      ];

        /* create a new table */
        $receipt_dbf = 'crm_receipt_'.date('YmdHis').'.dbf';
        $tableNew = XBaseWritableTable::create($receipt_dbf, $fields);

        $start_date = ltrim($start_date, 'start-');
        $end_date = ltrim($end_date, 'end-');

        $insert_dbf = $this->getInsertReceiptDbf($start_date, $end_date);
        // var_dump($insert_dbf);exit;

        $cnt = count($insert_dbf);

        for ($i = 0; $i < $cnt; ++$i) {
            $r = $tableNew->appendRecord();

            if ($insert_dbf[$i]->bank == '') {
                $r->setObjectByName('ENTR', 'CA101');
            } else {
                $r->setObjectByName('ENTR', 'CA101');
            }
            $r->setObjectByName('ACCN', 'CA001');
            $r->setObjectByName('DREF', $insert_dbf[$i]->receipt_ref_no);
            $r->setObjectByName('DATE', $insert_dbf[$i]->doc_date);
            $r->setObjectByName('IDEN', $insert_dbf[$i]->code);
            $r->setObjectByName('CURR', $insert_dbf[$i]->currency);
            $r->setObjectByName('RATE', $insert_dbf[$i]->currency_rate);
            $r->setObjectByName('FAMT', $insert_dbf[$i]->amount);
            $r->setObjectByName('AMOU', round($insert_dbf[$i]->amount / $insert_dbf[$i]->currency_rate, 2));
            $r->setObjectByName('REMA', $insert_dbf[$i]->other_reference);

            $tableNew->writeRecord();
        }

        $this->db->set('export_status', '1');
        $c_start_date = date('Y-m-d', strtotime($start_date));
        if ($start_date != '' && $c_start_date != '1970-01-01') {
            $this->db->where('receipt_master.doc_date >= ', $c_start_date);
        }

        $c_end_date = date('Y-m-d', strtotime($end_date));
        if ($end_date != '' && $c_end_date != '1970-01-01') {
            $this->db->where('receipt_master.doc_date <= ', $c_end_date);
        }
        $this->db->update('receipt_master');

        $this->DownloadFile($receipt_dbf);
    }

    public function getCompanyGstReg()
    {
        return $this->su_model->get_company_profile();
    }

    public function getInsertInvoiceDbf($start_date = '', $end_date = '')
    {
        $table = 'invoice_product_master';
        $join_table = ['ct_gst',
                        'invoice_master',
                        'master_employee',
                        'master_customer',
                        'ct_currency', ];
        $where = [/* 'user_id' => $this->session->user_id, */ 'status !=' => 'D'/* , 'export_status' => '0' */];

        $c_start_date = date('Y-m-d', strtotime($start_date));
        if ($start_date != '' && $c_start_date != '1970-01-01') {
            $where['invoice_master.created_on >= '] = $c_start_date;
        }

        $c_end_date = date('Y-m-d', strtotime($end_date));
        if ($end_date != '' && $c_end_date != '1970-01-01') {
            $where['invoice_master.created_on <= '] = $c_end_date;
        }

        $join_condition = [
                        'invoice_product_master.gst_category = ct_gst.gst_code',
                        'invoice_product_master.invoice_id = invoice_master.invoice_id',
                        'invoice_master.employee_id = master_employee.e_id',
                        'invoice_master.customer_id = master_customer.customer_id',
                        'ct_currency.currency_id = master_customer.currency_id'];

        $columns = [
                    'invoice_master.invoice_ref_no',
                    'invoice_master.created_on',
                    'master_customer.code',
                    'ct_currency.code as currency',
                    'ct_currency.rate',
                    'invoice_master.net_total',
                    'invoice_master.net_total/ct_currency.rate',
                    'invoice_master.lsd_value',
                    'ct_gst.gst_code',
                    'ct_gst.gst_rate',
                    'invoice_master.net_total/ct_currency.rate/ct_gst.gst_rate',
                    'master_employee.code'];
        $table_id = 'i_p_id';

        $list = $this->su_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $table_id);

        return $list;
    }

    public function export_invoices($start_date, $end_date)
    {
        require_once 'util_github/Column.class.php';
        require_once 'util_github/Record.class.php';
        require_once 'util_github/Table.class.php';
        require_once 'util_github/WritableTable.class.php';

        /* sample data */
        $fields = [
        ['ENTR', DBFFIELD_TYPE_CHAR, 5, 0],
        ['ACCN', DBFFIELD_TYPE_CHAR, 5, 0],
        ['DREF', DBFFIELD_TYPE_CHAR, 12, 0],
        ['DATE', DBFFIELD_TYPE_DATE, 10, 0],
        ['IDEN', DBFFIELD_TYPE_CHAR, 10, 0],
        ['CURR', DBFFIELD_TYPE_CHAR, 3, 0],
        ['RATE', DBFFIELD_TYPE_NUMERIC, 9, 5],
        ['FAMT', DBFFIELD_TYPE_FLOATING, 13, 2],
        ['AMOU', DBFFIELD_TYPE_FLOATING, 13, 2],
        ['DOCUAMOU', DBFFIELD_TYPE_FLOATING, 13, 2],
        ['SMAN', DBFFIELD_TYPE_CHAR, 5, 0],
        ['DONE', DBFFIELD_TYPE_LOGICAL, 1, 0],
        ['GSTCATE', DBFFIELD_TYPE_CHAR, 8, 0],
        ['GSTPERC', DBFFIELD_TYPE_FLOATING, 7, 2],
        ['GSTAMOU', DBFFIELD_TYPE_FLOATING, 13, 2],
      ];

        $sales_dbf = 'crm_sales_'.date('YmdHis').'.dbf';

        /* create a new table */
        $tableNew = XBaseWritableTable::create($sales_dbf, $fields);

        $start_date = ltrim($start_date, 'start-');
        $end_date = ltrim($end_date, 'end-');

        $insert_dbf = $this->getInsertInvoiceDbf($start_date, $end_date);

        $companygstreg = $this->getCompanyGstReg()[0]->gst_reg_no;

        $cnt = count($insert_dbf);

        for ($i = 0; $i < $cnt; ++$i) {
            $cur_rate = number_format($insert_dbf[$i]->currency_rate, 5, '.', '');

            $lum_dic = $insert_dbf[$i]->amount * $insert_dbf[$i]->lsd_percentage / 100;
            $after_dic_value = $insert_dbf[$i]->amount - $lum_dic;
            $gst_value = $after_dic_value * $insert_dbf[$i]->gst_rate / 100;

            $famt = $after_dic_value + $gst_value;
            $famt = number_format($famt, 2, '.', '');

            $amou = $famt / $cur_rate;
            $amou = number_format($amou, 2, '.', '');

            $r = $tableNew->appendRecord();

            $r->setObjectByName('ENTR', 'S0001');
            $r->setObjectByName('ACCN', 'CA001');
            $r->setObjectByName('DREF', $insert_dbf[$i]->invoice_ref_no);
            $r->setObjectByName('DATE', $insert_dbf[$i]->created_on);
            $r->setObjectByName('IDEN', $insert_dbf[$i]->code);
            $r->setObjectByName('CURR', $insert_dbf[$i]->currency);
            $r->setObjectByName('RATE', $cur_rate);
            $r->setObjectByName('FAMT', $famt);
            $r->setObjectByName('AMOU', $amou);
            $r->setObjectByName('DOCUAMOU', $amou);
            $r->setObjectByName('SMAN', $insert_dbf[$i]->code);

            if (isset($companygstreg)) {
                $gst_value = number_format($gst_value, 2, '.', '');
                $gstamou = number_format($gst_value / $cur_rate, 2, '.', '');
                $r->setObjectByName('DOCUAMOU', $amou - $gstamou);
                $r->setObjectByName('DONE', 'T');
                $r->setObjectByName('GSTCATE', $insert_dbf[$i]->gst_code);
                $r->setObjectByName('GSTPERC', $insert_dbf[$i]->gst_rate);
                $r->setObjectByName('GSTAMOU', $gstamou);
            } else {
                $r->setObjectByName('DONE', 'F');
            }
            $tableNew->writeRecord();
        }

        $this->db->set('export_status', '1');
        $c_start_date = date('Y-m-d', strtotime($start_date));
        if ($start_date != '' && $c_start_date != '1970-01-01') {
            $this->db->where('invoice_master.created_on >= ', $c_start_date);
        }

        $c_end_date = date('Y-m-d', strtotime($end_date));
        if ($end_date != '' && $c_end_date != '1970-01-01') {
            $this->db->where('invoice_master.created_on <= ', $c_end_date);
        }
        $this->db->update('invoice_master');

        $this->DownloadFile($sales_dbf);
    }
}
