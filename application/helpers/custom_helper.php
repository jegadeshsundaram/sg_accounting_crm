<?php

if (!function_exists('breacrumb')) {
    function breadcrumb($list = [])
    {
        $CI = &get_instance();
        $level = $CI->session->userdata('level');
        $html = '';

        $html .= '<ol class="breadcrumb pull-right">';
        $html .= "<li><a href='/dashboard'><i class='fa fa-dashboard'></i> Dashboard</a></li>";
        foreach ($list as $key => $value) {
            if ($key == 'active') {
                $html .= '<li class="active">'.$value.'</li>';
            } else {
                $html .= "<li><a href='".base_url($level.'/'.$key)."'>".$value.'</a></li>';
            }
        }
        $html .= '</ol>';

        return $html;
    }
}

if (!function_exists('buttonsPanel')) {
    function buttonsPanel($new = 1, $edit = 1, $view = 1, $delete = 1, $print_rec = 0, $print_all = 0, $refresh = 1)
    // set 1 to display button , set 0 to hide buttons .. ex: buttonsPanel(1,0,0,1,0,0,0)
    {
        $buttonsPanel = '';
        $btn_style = 'btn-sm';

        $buttonsPanel .= "<div class='row buttons-panel'><div class='col-lg-12'>";
        if ($new == 1) {
            $buttonsPanel .= "<button class='btn btn-info $btn_style' id='btn_new'>
                    <i class='fa fa-plus-circle' aria-hidden='true'></i> New
                </button>";
        }

        if ($edit == 1) {
            $buttonsPanel .= "<button class='btn bg-maroon $btn_style' id='btn_edit'>
                    <i class='fa-solid fa-user-pen' aria-hidden='true'></i> Edit
                </button>";
        }

        if ($view == 1) {
            $buttonsPanel .= "<button class='btn btn-warning $btn_style' id='btn_view'>
                    <i class='fa fa-eye' aria-hidden='true'></i> View
                </button>";
        }

        if ($delete == 1) {
            $buttonsPanel .= "<button class='btn btn-danger $btn_style' id='btn_delete'>
                    <i class='fa fa-trash' aria-hidden='true'></i> Delete
                </button>";
        }

        if ($print_rec == 1) {
            $buttonsPanel .= "<button class='btn bg-navy $btn_style' id='btn_print'>
                    <i class='fa fa-print' aria-hidden='true'></i> Print Record
                </button>";
        }

        if ($print_all == 1) {
            $buttonsPanel .= "<button class='btn bg-navy $btn_style' id='btn_print_all'>
                    <i class='fa fa-print' aria-hidden='true'></i> Print All
                </button>";
        }

        $buttonsPanel .= '</div></div>';

        return $buttonsPanel;
    }
}

function set_flash_message($message_name, $message_type = null, $message_content)
{
    // Get current CodeIgniter instance
    $CI = &get_instance();
    $div = '';
    $div .= "<div class='alert alert-".$message_type." fade in alert-dismissible show'>";
    $div .= $message_content;
    $div .= "<a href='#' class='close' data-dismiss='alert' aria-label='close' title='close' style='text-decoration: none'>X</a></div>";
    if ($message_type == null) {
        $message_type = 'info';
    }
    $CI->session->set_flashdata($message_name, $div);
}

function get_flash_message($message_name)
{
    // Get current CodeIgniter instance
    $CI = &get_instance();
    // We need to use $CI->session instead of $this->session
    echo $CI->session->flashdata($message_name);
}

function is_ajax()
{
    $CI = &get_instance();
    if (!$CI->input->is_ajax_request()) {
        show_404();
    }
}

/**
 * it will Encrypt String data and return encrypted string(this is a two way encription/decryption).
 */
function encryptIt($stringdata)
{
    if (is_string($stringdata)) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));

        return $stringdataEncoded;
    } else {
        exit('not a valid input.');
    }
}

/**
 * it will Decrypt String data and return Decrpted string.
 */
function decryptIt($stringdata)
{
    if (is_string($stringdata)) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");

        return $stringdataDecoded;
    } else {
        exit('not a valid input.');
    }
}

// this function will change data from arrray to json format
function array2json($arr, $pretty = false)
{
    if (!is_array($arr)) {
        $this->setError('Give value is not an array');
    }
    // use JSON_PRETTY_PRINT constant with json_encode function as 2nd param.
    if (function_exists('json_encode')) {
        if ($pretty) {
            return json_encode($arr, JSON_PRETTY_PRINT);
        } else {
            return json_encode($arr);
        }
    } // Lastest versions of PHP already has this functionality.

    $parts = [];
    $is_list = false;

    // Find out if the given array is a numerical array
    $keys = array_keys($arr);
    $max_length = count($arr) - 1;
    if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) { // See if the first key is 0 and last key is length - 1
        $is_list = true;
        for ($i = 0; $i < count($keys); ++$i) { // See if each key correspondes to its position
            if ($i != $keys[$i]) { // A key fails at position check.
                $is_list = false; // It is an associative array.
                break;
            }
        }
    }
    foreach ($arr as $key => $value) {
        if (is_array($value)) { // Custom handling for arrays
            if ($is_list) {
                $parts[] = array2json($value);
            }
            /* :RECURSION: */
            else {
                $parts[] = '"'.$key.'":'.array2json($value);
            }
            /* :RECURSION: */
        } else {
            $str = '';
            if (!$is_list) {
                $str = '"'.$key.'":';
            }
            // Custom handling for multiple data types
            if (is_numeric($value)) {
                $str .= $value;
            } // Numbers
            elseif ($value === false) {
                $str .= 'false';
            } // The booleans
            elseif ($value === true) {
                $str .= 'true';
            } else {
                $str .= '"'.addslashes($value).'"';
            } // All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
            $parts[] = $str;
        }
    }
    $json = implode(',', $parts);
    if ($is_list) {
        return '['.$json.']';
    } // Return numerical JSON

    return '{'.$json.'}'; // Return associative JSON
}

// this function will change data from arrray to json format
function json2array($json, $assoc = false, $depth = 512, $options = 0)
{
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $json = json_decode($json, $assoc, $depth, $options);
    } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
        $json = json_decode($json, $assoc, $depth);
    } else {
        $json = json_decode($json, $assoc);
    }
    if (json_last_error() == JSON_ERROR_NONE) {
        return $json;
    } else {
        echo json_last_error(); // 4 (JSON_ERROR_SYNTAX)
        echo json_last_error_msg(); // unexpected character
    }
}

function create_json_file($filename, $jsondata, $pretty = false)
{
    if (is_array($jsondata)) {
        $jsondata = $this->array2json($jsondata, $pretty);
    }
    $fp = fopen($filename, 'wb');
    if (!fwrite($fp, $jsondata)) {
        echo "Cannot write to file ($filename)";

        return false;
    } else {
        return true;
    }

    fclose($fp);
    // return $jsondata;
}

function read_json_file($fialname)
{
    if (!is_file($filename)) {
        $this->setError('Give file is not a json file.');
    } else {
        return $jsondata;
    }
}

if (!function_exists('createSimpleDropdown')) {
    function createSimpleDropdown($values = [], $caption = 'Value', $selected = null, $keyValueSame = 1)
    {
        // $keyValueSame=0 , When it set to 0 then in option value user $key as value otherwise option value and its display value both same  by default it is 1
        $options = '';
        $options .= "<option value=''>-- Select $caption --</option>";
        if (!empty($values)) {
            if ($selected != null) {
                foreach ($values as $key => $value) {
                    if ($keyValueSame == 1) {
                        $key = $value;
                    }
                    if ($key == $selected) {
                        $options .= "<option value='$key' selected>$value</option>";
                    } else {
                        $options .= "<option value='$key'>$value</option>";
                    }
                }
            } else {
                foreach ($values as $key => $value) {
                    if ($keyValueSame == 1) {
                        $key = $value;
                    }
                    $options .= "<option value='$key'>$value</option>";
                }
            }
        }

        return $options;
    }
}

/**
 * it will validate json string and return if string is valid josn format.
 */
function json_validate($string)
{
    // decode the JSON data
    $result = $this->json_clean_decode($jsonstring);
    // switch and check possible JSON errors
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $error = ''; // JSON is valid // No error has occurred
            break;
        case JSON_ERROR_DEPTH:
            $error = 'The maximum stack depth has been exceeded.';
            break;

        case JSON_ERROR_STATE_MISMATCH:
            $error = 'Invalid or malformed JSON.';
            break;

        case JSON_ERROR_CTRL_CHAR:
            $error = 'Control character error, possibly incorrectly encoded.';
            break;

        case JSON_ERROR_SYNTAX:
            $error = 'Syntax error, malformed JSON.';
            break;

            // PHP >= 5.3.3
        case JSON_ERROR_UTF8:
            $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
            break;

            // PHP >= 5.5.0
        case JSON_ERROR_RECURSION:
            $error = 'One or more recursive references in the value to be encoded.';
            break;

            // PHP >= 5.5.0
        case JSON_ERROR_INF_OR_NAN:
            $error = 'One or more NAN or INF values in the value to be encoded.';
            break;

        case JSON_ERROR_UNSUPPORTED_TYPE:
            $error = 'A value of a type that cannot be encoded was given.';
            break;

        default:
            $error = 'Unknown JSON error occured.';
            break;
    }
    if ($error !== '') {
        // throw the Exception or exit // or whatever :)
        exit($error);
    }

    // everything is OK
    return $result;
}

function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
{
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $json = json_decode($json, $assoc, $depth, $options);
    } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
        $json = json_decode($json, $assoc, $depth);
    } else {
        $json = json_decode($json, $assoc);
    }

    return $json;
}

function is_logged_in($role = 'admin')
{
    $CI = &get_instance();
    if (empty($CI->ion_auth->user()->result())) {
        redirect('login/signout');
    } else {
        if (!$CI->ion_auth->logged_in()) {
            redirect('');
        }
    }
}

function has_permission()
{
    $CI = &get_instance();
    $method = $CI->uri->segment(2);
    $permissions = (array) json_decode($CI->custom->getSingleValue('groups', 'permissions', ['id' => $CI->session->group_id]));
    /*  if (array_key_exists($method, $permissions)) {
        if ($permissions[$method] == 0) {
            set_flash_message("message", "danger", "You have no permission to access this");
            redirect('dashboard', 'refresh');
        }
    } else {
        if ((!$CI->ion_auth->is_admin() && $CI->session->group_id != 1) && $CI->session->level != "admin") {
            set_flash_message("message", "danger", "You have no permission to access this");
            redirect('dashboard', 'refresh');
        }
    }*/
}

function file_upload($file_name, $control_name, $upload_in)
{
    $CI = &get_instance();

    $config['upload_path'] = FCPATH.'assets/uploads/'.$upload_in.'/';
    if ($upload_in == 'database_restore_files') {
        $config['allowed_types'] = 'sql';
    } elseif ($upload_in == 'database_import_files') {
        $config['allowed_types'] = 'dbf';
        // $config['max_size']   = 4096000000;
    } else {
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG';
        $config['max_size'] = 2048;
        $config['max_width'] = 2000;
        $config['max_height'] = 2000;
    }
    // d($config['allowed_types']);
    $config['file_name'] = $file_name; // set the name here
    $config['overwrite'] = true;
    // d($config);
    $CI->load->library('Upload', $config);
    if (!$CI->upload->do_upload($control_name)) {
        $error = [
            'error' => $CI->upload->display_errors(),
            'status' => false,
        ];

        return $error;
    } else {
        $data = [
            'upload_data' => $CI->upload->data(),
            'status' => true,
        ];

        return $data;
    }
}

if (!function_exists('databaseBackup')) {
    function databaseBackup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'format' => 'sql',           // gzip, zip, txt
            'filename' => $file_name,      // File name - NEEDED ONLY WITH ZIP FILES
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
}

if (!function_exists('databaseInitialize')) {
    function databaseInitialize($tables = [])
    {
        $CI = &get_instance();
        if ($CI->db->table_exists('master_customer')) {
            $res[] = $CI->db->truncate('master_customer');
        }
        if ($CI->db->table_exists('master_billing')) {
            $res[] = $CI->db->truncate('master_billing');
        }
        if ($CI->db->table_exists('master_employee')) {
            $res[] = $CI->db->truncate('master_employee');
        }
        if ($CI->db->table_exists('master_department')) {
            $res[] = $CI->db->truncate('master_department');
        }
        if ($CI->db->table_exists('master_foreign_bank')) {
            $res[] = $CI->db->truncate('master_foreign_bank');
        }
        if ($CI->db->table_exists('master_accountant')) {
            $res[] = $CI->db->truncate('master_accountant');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Error in initialization');
        } else {
            set_flash_message('message', 'success', 'System Initialized Successfully');
        }
    }
}

if (!function_exists('zapCustomer_price')) {
    function zapCustomer_price($tables = [])
    {
        $CI = &get_instance();
        if ($CI->db->table_exists('customer_price')) {
            $res[] = $CI->db->truncate('customer_price');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Error in zap');
        } else {
            set_flash_message('message', 'success', 'Customer price datafiles zapped');
        }
    }
}

if (!function_exists('zapCurrency')) {
    function zapCurrency()
    {
        $CI = &get_instance();
        $CI->db->truncate('ct_currency');
    }
}

if (!function_exists('zapCustomer')) {
    function zapCustomer()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_customer');
    }
}

if (!function_exists('zapSupplier')) {
    function zapSupplier()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_supplier');
    }
}

if (!function_exists('zapBilling')) {
    function zapBilling()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_billing');
    }
}

if (!function_exists('zapEmployee')) {
    function zapEmployee()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_employee');
    }
}

if (!function_exists('zapDepartment')) {
    function zapDepartment()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_department');
    }
}

if (!function_exists('zapForeignBank')) {
    function zapForeignBank()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_foreign_bank');
    }
}

if (!function_exists('zapAccountant')) {
    function zapAccountant()
    {
        $CI = &get_instance();
        $CI->db->truncate('master_accountant');
    }
}

if (!function_exists('zapQuotation')) {
    function zapQuotation($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('quotation_setting')) {
            $res[] = $CI->db->truncate('quotation_setting');
        }
        if ($CI->db->table_exists('quotation_master')) {
            $res[] = $CI->db->truncate('quotation_master');
        }
        if ($CI->db->table_exists('quotation_product_master')) {
            $res[] = $CI->db->truncate('quotation_product_master');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Quotation datafiles zapped');
        }
    }
}

if (!function_exists('zapInvoice')) {
    function zapInvoice($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('invoice_setting')) {
            $res[] = $CI->db->truncate('invoice_setting');
        }
        if ($CI->db->table_exists('invoice_master')) {
            $res[] = $CI->db->truncate('invoice_master');
        }
        if ($CI->db->table_exists('invoice_product_master')) {
            $res[] = $CI->db->truncate('invoice_product_master');
        }
        /*if ($CI->db->table_exists('stock')) {
            $res[] = $CI->db->where('stock_type', 'Invoice');
            $CI->db->delete('stock');
        }*/
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Invoice datafiles zapped');
        }
    }
}

if (!function_exists('zapAR')) {
    function zapAR($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('ar_open')) {
            $res[] = $CI->db->truncate('ar_open');
        }
        if ($CI->db->table_exists('accounts_receivable')) {
            $res[] = $CI->db->truncate('accounts_receivable');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Accounts Receivable datafiles zapped');
        }
    }
}

if (!function_exists('zapGL')) {
    function zapGL($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('gl')) {
            $res[] = $CI->db->truncate('gl');
        }

        if ($CI->db->table_exists('gl_open')) {
            $res[] = $CI->db->truncate('gl_open');
        }

        if ($CI->db->table_exists('gl_single_entry')) {
            $res[] = $CI->db->truncate('gl_single_entry');
        }

        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'GL datafiles zapped');
        }
    }
}

if (!function_exists('zapReceipt')) {
    function zapReceipt($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('receipt_setting')) {
            $res[] = $CI->db->truncate('receipt_setting');
        }
        if ($CI->db->table_exists('receipt_master')) {
            $res[] = $CI->db->truncate('receipt_master');
        }
        if ($CI->db->table_exists('receipt_invoice_master')) {
            $res[] = $CI->db->truncate('receipt_invoice_master');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Receipt datafiles zapped');
        }
    }
}

if (!function_exists('zapPayment')) {
    function zapPayment($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('payment_setting')) {
            $res[] = $CI->db->truncate('payment_setting');
        }
        if ($CI->db->table_exists('payment_master')) {
            $res[] = $CI->db->truncate('payment_master');
        }
        if ($CI->db->table_exists('payment_purchase_master')) {
            $res[] = $CI->db->truncate('payment_purchase_master');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Payment datafiles zapped');
        }
    }
}

if (!function_exists('zapBankRecon')) {
    function zapBankRecon($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('bank_recon_info')) {
            $res[] = $CI->db->truncate('bank_recon_info');
        }
        if ($CI->db->table_exists('bank_recon_last')) {
            $res[] = $CI->db->truncate('bank_recon_last');
        }
        if ($CI->db->table_exists('bank_recon_current')) {
            $res[] = $CI->db->truncate('bank_recon_current');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Bank recon datafiles zapped');
        }
    }
}

if (!function_exists('zapPettyCash')) {
    function zapPettyCash($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('petty_cash_setting')) {
            $res[] = $CI->db->truncate('petty_cash_setting');
        }

        if ($CI->db->table_exists('petty_cash_batch')) {
            $res[] = $CI->db->truncate('petty_cash_batch');
        }

        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Petty Cash datafiles zapped');
        }
    }
}

if (!function_exists('zapEzentry')) {
    function zapEzentry($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('ez_sales')) {
            $res[] = $CI->db->truncate('ez_sales');
        }
        if ($CI->db->table_exists('ez_purchase')) {
            $res[] = $CI->db->truncate('ez_purchase');
        }
        if ($CI->db->table_exists('ez_receipt')) {
            $res[] = $CI->db->truncate('ez_receipt');
        }
        if ($CI->db->table_exists('ez_settlement')) {
            $res[] = $CI->db->truncate('ez_settlement');
        }
        if ($CI->db->table_exists('ez_payment')) {
            $res[] = $CI->db->truncate('ez_payment');
        }
        if ($CI->db->table_exists('ez_adjustment')) {
            $res[] = $CI->db->truncate('ez_adjustment');
        }
        if ($CI->db->table_exists('ez_creditor')) {
            $res[] = $CI->db->truncate('ez_creditor');
        }
        if ($CI->db->table_exists('ez_debtor')) {
            $res[] = $CI->db->truncate('ez_debtor');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'EZ Entry datafiles zapped');
        }
    }
}

if (!function_exists('zapGST')) {
    function zapGST($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('gst')) {
            $res[] = $CI->db->truncate('gst');
        }
        if ($CI->db->table_exists('gst_open')) {
            $res[] = $CI->db->truncate('gst_open');
        }
        if ($CI->db->table_exists('gst_returns_filing_info')) {
            $res[] = $CI->db->truncate('gst_returns_filing_info');
        }
        if ($CI->db->table_exists('gst_returns_contact_info')) {
            $res[] = $CI->db->truncate('gst_returns_contact_info');
        }
        if ($CI->db->table_exists('gst_returns_declaration')) {
            $res[] = $CI->db->truncate('gst_returns_declaration');
        }
        if ($CI->db->table_exists('gst_returns_form_5')) {
            $res[] = $CI->db->truncate('gst_returns_form_5');
        }
        if ($CI->db->table_exists('gst_returns_form_7')) {
            $res[] = $CI->db->truncate('gst_returns_form_7');
        }
        if ($CI->db->table_exists('gst_returns_grp_reasons')) {
            $res[] = $CI->db->truncate('gst_returns_grp_reasons');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'GST datafiles zapped');
        }
    }
}

if (!function_exists('zapGSTMasterPurchase')) {
    function zapGSTMasterPurchase($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('ct_gst')) {
            $res[] = $CI->db->where('gst_type', 'purchase');
            $CI->db->delete('ct_gst');
        }
    }
}

if (!function_exists('zapGSTMasterSupply')) {
    function zapGSTMasterSupply($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('ct_gst')) {
            $res[] = $CI->db->where('gst_type', 'supply');
            $CI->db->delete('ct_gst');
        }
    }
}

if (!function_exists('zapCOA')) {
    function zapCOA($tables = [])
    {
        $CI = &get_instance();

        /*if ($CI->db->table_exists('chart_of_account_prefix')) {
            $res[] = $CI->db->truncate('chart_of_account_prefix');
        }*/
        if ($CI->db->table_exists('chart_of_account')) {
            $res[] = $CI->db->truncate('chart_of_account');
        }
    }
}

if (!function_exists('zapStock')) {
    function zapStock($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('stock_open')) {
            $res[] = $CI->db->truncate('stock_open');
        }
        if ($CI->db->table_exists('stock_adjustment')) {
            $res[] = $CI->db->truncate('stock_adjustment');
        }
        if ($CI->db->table_exists('stock_purchase')) {
            $res[] = $CI->db->truncate('stock_purchase');
        }
        if ($CI->db->table_exists('stock_cost')) {
            $res[] = $CI->db->truncate('stock_cost');
        }
        if ($CI->db->table_exists('stock')) {
            // $res[] = $CI->db->where('stock_type!=', 'Invoice');
            // $CI->db->where('stock_type','Purchase');
            // $CI->db->where('stock_type','Adjustment');
            // $CI->db->delete('stock');
            $res[] = $CI->db->truncate('stock');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Stock datafiles zapped');
        }
    }
}

if (!function_exists('zapAP')) {
    function zapAP($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('ap_open')) {
            $res[] = $CI->db->truncate('ap_open');
        }
        if ($CI->db->table_exists('accounts_payable')) {
            $res[] = $CI->db->truncate('accounts_payable');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Accounts Payable datafiles zapped');
        }
    }
}

if (!function_exists('zapFBMaster')) {
    function zapFBMaster($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('master_foreign_bank')) {
            $res[] = $CI->db->truncate('master_foreign_bank');
        }
    }
}

if (!function_exists('zapFB')) {
    function zapFB($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('fb_open')) {
            $res[] = $CI->db->truncate('fb_open');
        }
        if ($CI->db->table_exists('foreign_bank')) {
            $res[] = $CI->db->truncate('foreign_bank');
        }
        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Foreign Bank datafiles zapped');
        }
    }
}

if (!function_exists('zapStaffActivity')) {
    function zapStaffActivity($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('staff_activity')) {
            $res[] = $CI->db->truncate('staff_activity');
        }

        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'Staff Activity datafiles zapped');
        }
    }
}

if (!function_exists('zapSACJob')) {
    function zapSACJob($tables = [])
    {
        $CI = &get_instance();

        if ($CI->db->table_exists('sac_job')) {
            $res[] = $CI->db->truncate('sac_job');
        }

        if (in_array(false, $res)) {
            set_flash_message('message', 'danger', 'Zap process is faied due to error');
        } else {
            set_flash_message('message', 'success', 'SAC JOB ZAPPED');
        }
    }
}

if (!function_exists('array_diff_assoc_recursive')) {
    function array_diff_assoc_recursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif ((!isset($array2[$key]) || $array2[$key] != $value) && !($array2[$key] === null && $value === null)) {
                $difference[$key] = $value;
            }
        }

        return !isset($difference) ? 0 : $difference;
    }
}
