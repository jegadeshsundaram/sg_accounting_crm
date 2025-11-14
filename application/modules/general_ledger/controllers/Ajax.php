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

        $this->logged_id = $this->session->user_id;
        $this->load->model('general_ledger/general_ledger_model', 'gl_model');
    }

    public function get_refs()
    {
        is_ajax();

        $post = $this->input->post();
        $table = 'gl';
        if($post['entry_type'] == "S") {
            $table = 'gl_single_entry';
        }

        $entries = 0;
        $opts = '<option value="">-- Select --</option>';

        $this->db->select('gl_id, ref_no, tran_type');
        $this->db->from($table);
        $this->db->where(['tran_type' => $post['transaction_type']]);
        $this->db->group_by('ref_no');
        $this->db->order_by('ref_no', 'ASC');
        $query = $this->db->get();
        $refs = $query->result();
        foreach ($refs as $value) {
            $opts .= '<option value='.$value->gl_id.'>'.$value->ref_no.'</option>';
            ++$entries;
        }

        $data['entries'] = $entries;
        $data['options'] = $opts;

        echo json_encode($data);
    }

    // page : data patch
    public function double_dp_ob()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('gl', ['ref_no' => $post['ref_no'], 'tran_type' => $post['tran_type']]);
        
        echo $ref;
    }

    // opening balance - starts
    public function double_ob()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['ref_no'];
        // Step 1 :: checks same reference exists in GL_OPEN.TBL
        $ref = $this->custom->getCount('gl_open', ['ref_no' => $ref_no]);
        if ($ref == 0) { // If NOT Exists
            // Step 2 :: checks same reference exists in GL.TBL (Already Posted)
            $ref = $this->custom->getCount('gl', ['ref_no' => $ref_no, 'tran_type' => 'OPBAL']);
        }
        echo $ref;
    }

    public function same_ref_transactions() {
        is_ajax();
        $post = $this->input->post();
        
        $entries = 0;
        $this->db->select('gl_id');
        $this->db->from('gl');
        $this->db->where(['ref_no' => $post['ref_no'], 'tran_type' => $post['tran_type']]);
        $this->db->group_by('iden');
        $query = $this->db->get();
        $refs = $query->result();
        foreach ($refs as $value) {
            ++$entries;
        }

        echo $entries;
    }

    public function save_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob_id = $post['ob_id'];
            $data['doc_date'] = date('Y-m-d', strtotime($post['doc_date']));
            $data['ref_no'] = $post['ref_no'];
            $data['remarks'] = $post['remarks'];           
            $data['accn'] = $post['accn'];
            $data['total_amount'] = $post['amount'];
            $data['sign'] = $post['sign'];

            if ($ob_id == '') {
                $ob_id = $this->custom->insertRow('gl_open', $data);
            } else {
                $updated = $this->custom->updateRow('gl_open', $data, ['ob_id' => $ob_id]);
            }

            echo $ob_id;
        } else {
            echo 'post error';
        }
    }

    public function delete_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('gl_open', ['ob_id' => $post['ob_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    public function delete_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');
        $ob_data = $this->custom->getMultiValues('gl_open', 'doc_date, ref_no', ['ob_id' => $ob_id]);
        
        $where = ['doc_date' => $ob_data->doc_date, 'ref_no' => $ob_data->ref_no];
        $result = $this->custom->updateRow('gl_open', ['status' => 'D'], $where);

        echo $result;
    }

    public function post_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {

            $unbalanced = 0;
            $posted = 0;

            $this->db->select('ref_no');
            $this->db->from('gl_open');        
            $this->db->where(['ob_id' => $post['rowID'], 'status' => 'C']);
            $this->db->group_by('ref_no');
            $query = $this->db->get();
            $ob_data = $query->result();
            foreach ($ob_data as $value) {
                // debit and credit todal - equals check
                $dr_cr_diff = 0;
                $sql = 'SELECT sum(CASE WHEN sign = "+" THEN total_amount WHEN sign = "-" THEN -total_amount END) AS sum_of_debit_and_credit FROM gl_open WHERE ref_no = "'.$value->ref_no.'" AND status = "C"';
                $query = $this->db->query($sql);
                $dr_cr_entries = $query->result();
                foreach ($dr_cr_entries as $record) {
                    $dr_cr_diff = $record->sum_of_debit_and_credit;
                }

                // System will POST Tranasctions whose debit total and credit total should be same for double entry
                if ($dr_cr_diff == 0.00) {
                    $this->db->select('*');
                    $this->db->from('gl_open');
                    $this->db->where(['ref_no' => $value->ref_no, 'status' => 'C']);
                    $this->db->order_by('doc_date', 'asc');
                    $this->db->order_by('accn', 'asc');
                    $gl_ob_entries = $this->db->get();
                    $gl_ob_data = $gl_ob_entries->result();
                    foreach ($gl_ob_data as $record) {
                        // post opening balance entries to GL
                        $gl_insert_data['doc_date'] = $record->doc_date;
                        $gl_insert_data['ref_no'] = $record->ref_no;
                        $gl_insert_data['remarks'] = $record->remarks;
                        $gl_insert_data['accn'] = $record->accn;
                        $gl_insert_data['sign'] = $record->sign;
                        $gl_insert_data['tran_type'] = 'OPBAL';
                        $gl_insert_data['total_amount'] = $record->total_amount;
                        
                        $gl_insert = $this->custom->insertData('gl', $gl_insert_data);
                    }

                    // delete the inserted opening balance entries by reference
                    if ($gl_insert) {
                        $ob_delete = $this->custom->updateRow('gl_open', ['status' => 'P'], ['ref_no' => $value->ref_no]);
                        ++$posted;
                    }
                } else { // sum of debit and credit = 0
                    ++$unbalanced;
                }
            }

            $data['posted'] = $posted;
            $data['unbalanced'] = $unbalanced;

            echo json_encode($data);
        } else {
            redirect("/general_ledger/opening_balance?error=post");
        }
    }

    // this will populate opening balance transactions from gl_open.TBL for do changes if any before post to gl.tbl
    public function populate_opening_balance()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $ob_status = 'C';
        $data = [];
        $no = $this->input->post('start');

        $table = 'gl_open';
        $columns = ['ob_id', 'doc_date', 'ref_no', 'remarks'];
        $where = ['status' => $ob_status];
        $group_by = 'ref_no';
        $order_by = 'doc_date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->ob_id;

            $row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                    <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                    <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';

            $row[] = date('d-m-Y', strtotime($record->doc_date));
            $row[] = $record->ref_no;
            $row[] = $record->remarks;

            $data[] = $row;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->dt_model->count_all($table),
            'recordsFiltered' => $this->dt_model->count_filtered($table, $columns, $join_table, $join_condition, $where),
            'data' => $data,
        ];

        echo json_encode($output);
    }

    public function populate_coa() {

        $join_table = null;
        $join_condition = null;
        $where = null;
        $order = 'ASC';
        $group_by = null;

        $table = 'chart_of_account';
        $columns = ['coa_id', 'accn', 'description'];

        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];
            $row[] = $record->coa_id;
            $row[] = $record->accn;
            $row[] = $record->description;
            $data[] = $row;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->dt_model->count_all($table),
            'recordsFiltered' => $this->dt_model->count_filtered($table, $columns, $join_table, $join_condition, $where),
            'data' => $data,
        ];

        echo json_encode($output);
    }

    public function get_supplier()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $post['ref'];

        $this->db->select('supplier_code');
        $this->db->from('accounts_payable');
        $this->db->where(['doc_ref_no' => $ref]);
        $this->db->group_by('supplier_code');
        $query = $this->db->get();
        $ref_list = $query->result();

        $same_ref = 0;
        foreach ($ref_list as $value) {
            ++$same_ref;
        }

        echo $same_ref;
    }

    public function delete_from_gl_datapatch()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $document_reference = $post['old_document_reference'];
            $iden = $post['old_iden'];

            if ($iden != '') {
                $gl_deleted = $this->custom->deleteRow('gl', ['ref_no' => $document_reference, 'iden' => $iden]);
                $ap_deleted = $this->custom->deleteRow('accounts_payable', ['doc_ref_no' => $document_reference, 'supplier_code' => $iden]);
            } else {
                $gl_deleted = $this->custom->deleteRow('gl', ['ref_no' => $document_reference]);
                $ap_deleted = $this->custom->deleteRow('accounts_payable', ['doc_ref_no' => $document_reference]);
            }

            $ar_deleted = $this->custom->deleteRow('accounts_receivable', ['doc_ref_no' => $document_reference]);
            $fb_deleted = $this->custom->deleteRow('foreign_bank', ['doc_ref_no' => $document_reference]);

            if ($gl_deleted == 'deleted') {
                $data['deleted'] = 1;
            } else {
                $data['deleted'] = 0;
            }
        } else {
            echo 'post error';
        }
    }

    public function auto_save_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $open_id = $post['ob_id'];
            $ob_data['doc_date'] = date('Y-m-d', strtotime($post['transaction_date']));
            $ob_data['ref_no'] = $post['reference'];
            $ob_data['remarks'] = $post['remarks'];
            $ob_data['accn'] = $post['coa_code'];
            $ob_data['sign'] = $post['sign'];
            $ob_data['total_amount'] = $post['amount'];
            if ($open_id == '') {
                $inserted_id = $this->custom->insertRow('gl_open', $ob_data);
            } else {
                $inserted_id = '';
                $updated = $this->custom->updateRow('gl_open', $ob_data, ['ob_id' => $open_id]);
            }

            $data['open_id'] = $inserted_id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function delete_auto_saved_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $open_id = $post['ob_id'];

            $deleted = $this->custom->deleteRow('gl_open', ['ob_id' => $open_id]);

            $data['deleted'] = $deleted;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function list_ob_entries()
    {
        is_ajax();
        $this->body_file = 'general_ledger/blank.php';
        $this->header_file = 'general_ledger/blank.php';
        $this->footer_file = 'general_ledger/blank.php';

        $post = $this->input->post();
        $process = $post['process'];

        $this->db->select('*');
        $this->db->from('gl_open');
        $this->db->where(['status' => 'C']);
        $this->db->group_by('ref_no, doc_date');
        $this->db->order_by('doc_date', 'asc');
        $query = $this->db->get();
        $batch_data = $query->result();

        $html = '';

        foreach ($batch_data as $key => $value) {
            $this->db->select('*');
            $this->db->from('gl_open');
            $this->db->where(['ref_no' => $value->ref_no, 'doc_date' => $value->doc_date, 'status' => 'C']);
            $this->db->order_by('doc_date', 'asc');
            $query = $this->db->get();
            $batch_entry = $query->result();

            $debit_total = 0;
            $credit_total = 0;
            foreach ($batch_entry as $key => $value) {
                if ($value->sign == '+') {
                    $debit_total += $value->total_amount;
                } elseif ($value->sign == '-') {
                    $credit_total += $value->total_amount;
                }
            }

            $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
            $html .= '<tr id="'.$value->ob_id.'">';
            $html .= '<td>'.$document_date.'</td>';
            $html .= '<td>'.$value->ref_no.'</td>';
            $html .= '<td>'.$debit_total.'</td>';
            $html .= '<td>'.$credit_total.'</td>';
            $html .= '<td>'.$value->remarks.'</td>';

            $html .= '</tr>';
            ++$i;
        }

        $data['table_html'] = $html;

        echo json_encode($data);
    }

    public function get_ye_values()
    {
        is_ajax();

        $ye_data = $this->custom->getSingleRow('ye_closing', ['process' => 'YE']);

        if ($ye_data->closing_year != '') {
            $data['new_fy_start_date'] = $ye_data->current_fy_start_date;
            $data['new_fy_end_date'] = $ye_data->current_fy_end_date;
            $data['tb_cut_off'] = $ye_data->new_fy_start_date;
            $data['ye_closing_status'] = $ye_data->ye_closing_status;

            $data['closing_stock'] = $ye_data->closing_stock;
        } else {
            $data['new_fy_start_date'] = '';
            $data['new_fy_end_date'] = '';
            $data['tb_cut_off'] = '';
            $data['ye_closing_status'] = '';

            $data['closing_stock'] = 0;
        }

        echo json_encode($data);
    }

    public function backup_gl()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            // Create New Table for GL Backup Before do Year End Closing
            $bkp_tbl = 'gl_copy_'.date('dmY').'_'.time();
            $sql = 'CREATE TABLE '.$bkp_tbl.' LIKE gl';
            $query = $this->db->query($sql);

            // Inserting all the transactions from GL TBl to GL BACKUP TBL
            $sql = 'INSERT INTO '.$bkp_tbl.' SELECT * FROM gl';
            $clone_real_tbl = $this->db->query($sql);

            $status = $this->save_ye_values($post['cut_off_date'], $bkp_tbl);

            echo $clone_real_tbl;
        } else {
            echo 'post error';
        }
    }

    public function save_ye_values($cut_off_date, $bkp_tbl)
    {
        $current_year = substr($cut_off_date, 6, 4);

        $ye_date = date('d-m-Y', strtotime($cut_off_date));
        $current_fy_start_date = date('Y-m-d', strtotime($ye_date.' -1 year'));
        $current_fy_end_date = date('Y-m-d', strtotime($ye_date));

        $ye_data['current_fy_start_date'] = $current_fy_start_date;
        $ye_data['current_fy_end_date'] = $current_fy_end_date;

        // Current FY Closing Date + One Day = New FY Start Date
        $ye_data['new_fy_start_date'] = date('Y-m-d', strtotime($current_fy_end_date.' +1 day'));
        $ye_data['new_fy_end_date'] = date('Y-m-d', strtotime($current_fy_end_date.' +1 year'));

        $ye_data['closing_stock'] = 0;
        $ye_data['closing_year'] = $current_year;
        $ye_data['gl_backup'] = $bkp_tbl;
        $ye_data['ye_closing_status'] = 'backup';
        $ye_data['process'] = 'YE';

        // Update Process = "" to all the rows in "YE_CLOSING.TBL"
        // And Insert/Update Process = "YE" to only the lastest closing year inputted
        // And do all the process using "Process = 'YE'"
        $updated = $this->custom->updateRow('ye_closing', ['process' => ''], ['process' => 'YE']);

        $closing_ye_data = $this->custom->getSingleRow('ye_closing', ['closing_year' => $current_year]);
        if ($closing_ye_data->closing_year !== null && $closing_ye_data->closing_year !== '') { // Record Exists - Update only closing stock
            $where = ['closing_year' => $current_year];
            $db_status = $this->custom->updateRow('ye_closing', $ye_data, $where);
        } else {
            $db_status = $this->custom->insertRow('ye_closing', $ye_data);
        }

        return $db_status;
    }

    public function get_stock_values()
    {
        is_ajax();

        $ye_data = $this->custom->getSingleRow('ye_closing', ['process' => 'YE']);
        $current_fy_end_date = $ye_data->current_fy_end_date;

        $i = 0;
        $sql = 'SELECT product_id FROM stock WHERE created_on <= "'.$current_fy_end_date.'"';
        $query = $this->db->query($sql);
        $stock_list = $query->result();
        foreach ($stock_list as $key => $value) {
            ++$i;
        }

        echo $i;
    }

    public function save_closing_stock()
    {
        is_ajax();
        $post = $this->input->post();

        $db_status = $this->custom->updateRow('ye_closing', ['closing_stock' => $post['closing_stock']], ['process' => 'YE']);

        echo '1';
    }

    public function process_year_end_closing()
    {
        is_ajax();

        $where = ['process' => 'YE'];
        $ye_data = $this->custom->getSingleRow('ye_closing', $where);
        $from_date = $ye_data->current_fy_start_date;
        $to_date = $ye_data->current_fy_end_date;
        $closing_stock = $ye_data->closing_stock;

        // STEP 1: SUM OF ALL P&L ACCOUNTS AND ADD THE NET AMOUNT TO RP001 (RETAINED PROFITS) ACCOUNT
        $current_profit = $this->step_1_sum_pl_accounts($from_date, $to_date, $closing_stock);

        // STEP 2: ADD NET AMOUNT (SUM OF PL Accounts) To RP001 (RETAINED PROFITS)
        $add_current_profit_to_rp001 = $this->step_2_add_current_profit_to_rp001($from_date, $to_date, $current_profit);

        // STEP 3: REMOVE ALL THE P&L TRANSACTIONS FROM GL TBL AND ONLY BALANCE SHEET TRANSACTIONS SHOULD BE REMAIN IN GL TBL
        $delete_pl_accounts = $this->step_3_remove_pl_accounts($from_date, $to_date);

        // STEP 4: BRING FORWARD CA002 (Opening Stock) TRANSACTION WITH CLOSING STOCK AMOUNT TO START DATE OF THE NEW FINANCIAL YEAR
        $bf_opening_stock = $this->step_4_bf_opening_stock($closing_stock);

        // STEP 5: EACH AND EVERY BALANCE SHEET ACCOUNT MUST BE COMPRESSED AND SUMMARIZED AS ONE RECORD AND IT WILL HAVE THE FOLLOWING VALUES
        $combine_bs_accounts = $this->step_5_combine_bs_accounts($from_date, $to_date);

        // STEP 6 (FINAL STEP): CHECK TOTAL OF DEBIT = TOTAL OF CREDIT
        $sum_of_debit_and_credit = $this->final_step_compare_debit_credit();

        $data['sum_of_debit_and_credit'] = $sum_of_debit_and_credit;

        echo json_encode($data);
    }

    public function restore_gl()
    {
        is_ajax();

        $where = ['process' => 'YE'];
        $backup_table_name = $this->custom->getSingleValue('ye_closing', 'gl_backup', $where);

        $this->db->empty_table('gl');

        // Inserting all the transactions from GL TBl to GL BACKUP TBL
        $sql_clone_real_tbl = 'INSERT INTO gl SELECT * FROM '.$backup_table_name;
        $clone_real_tbl = $this->db->query($sql_clone_real_tbl);

        $ye_status_data['ye_closing_status'] = 'restored';
        $updated = $this->custom->updateRow('ye_closing', $ye_status_data, $where);

        echo $clone_real_tbl;
    }

    public function final_step_compare_debit_credit()
    {
        $where = ['process' => 'YE'];
        $ye_data = $this->custom->getSingleRow('ye_closing', $where);

        $debit_total = 0;
        $sql_debit = "SELECT sum(total_amount) as debit_total FROM gl WHERE sign='+' AND doc_date BETWEEN '".$ye_data->new_fy_start_date."' and '".$ye_data->new_fy_end_date."'";
        $query_debit = $this->db->query($sql_debit);
        $gl_debit_data = $query_debit->result();
        foreach ($gl_debit_data as $key => $value) {
            $debit_total = $value->debit_total;
        }

        $credit_total = 0;
        $sql_credit = "SELECT sum(total_amount) as credit_total FROM gl WHERE sign='-' AND doc_date BETWEEN '".$ye_data->new_fy_start_date."' and '".$ye_data->new_fy_end_date."'";
        $query_credit = $this->db->query($sql_credit);
        $gl_credit_data = $query_credit->result();
        foreach ($gl_credit_data as $key => $value) {
            $credit_total = $value->credit_total;
        }

        // if total of debit and credit == 0 then ye closing process successfully completed
        // if total of debit and credit !== 0 then ye closing process is failed
        $sum_of_debit_and_credit = $debit_total - $credit_total;
        if ($sum_of_debit_and_credit == 0) {
            $ye_status_data['ye_closing_status'] = 'success';
            $ye_status_data['process'] = ''; // set process = '' once ye closing is success and ready for next years
        } else {
            $ye_status_data['ye_closing_status'] = 'failed';
        }
        $updated = $this->custom->updateRow('ye_closing', $ye_status_data, $where);

        return $sum_of_debit_and_credit;
    }

    public function step_5_combine_bs_accounts($from_date, $to_date)
    {
        $sql_accn = "SELECT accn FROM gl WHERE doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_accn = $this->db->query($sql_accn);
        $gl_accns = $query_accn->result();
        foreach ($gl_accns as $key => $value) {
            if ($value->accn == 'CA002') { // This is already bring forwward with closing stock amount to the next financial year
            } else {
                $sql_bf_gl = "SELECT accn, sign, total_amount FROM gl WHERE accn = '".$value->accn."' AND doc_date BETWEEN '".$from_date."' AND '".$to_date."'";
                $query_bf_gl = $this->db->query($sql_bf_gl);
                $data_gl = $query_bf_gl->result();
                $specific_gl_accn_total = 0;
                foreach ($data_gl as $key => $value) {
                    if ($value->sign == '+') {
                        $specific_gl_accn_total += $value->total_amount;
                    } else {
                        $specific_gl_accn_total -= $value->total_amount;
                    }
                }

                // After sum the transactions under each balance sheet account, delete all the transactions for the specific account
                $delete_gl_account = $this->custom->deleteRow('gl', ['accn' => $value->accn, 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

                // insert combined transactions as one lump sum record into GL TBL

                $ye_data = $this->custom->getSingleRow('ye_closing', ['process' => 'YE']);

                $gl_data['doc_date'] = $ye_data->new_fy_start_date;
                $gl_data['ref_no'] = 'FY'.$ye_data->closing_year;
                $gl_data['remarks'] = 'Opening Balance';
                $gl_data['accn'] = $value->accn;
                if ($specific_gl_accn_total < 0) {
                    $gl_data['sign'] = '-';
                    $gl_data['total_amount'] = (-1) * $specific_gl_accn_total;
                } else {
                    $gl_data['sign'] = '+';
                    $gl_data['total_amount'] = $specific_gl_accn_total;
                }

                $gl_data['gstcat'] = '';
                $gl_data['tran_type'] = 'YE';

                $gl_data['sman'] = '';
                $inserted = $this->custom->insertRow('gl', $gl_data);
            }
        }

        return $inserted;
    }

    public function step_4_bf_opening_stock($closing_stock)
    {
        $ye_data = $this->custom->getSingleRow('ye_closing', ['process' => 'YE']);

        $ca002_data['doc_date'] = $ye_data->new_fy_start_date;
        $ca002_data['ref_no'] = 'FY'.$ye_data->closing_year;
        $ca002_data['remarks'] = 'Opening Balance';
        $ca002_data['accn'] = 'CA002';
        $ca002_data['sign'] = '+';
        $ca002_data['gstcat'] = '';
        $ca002_data['tran_type'] = 'YE';
        $ca002_data['total_amount'] = $closing_stock;

        $ca002_data['sman'] = '';

        $inserted = $this->custom->insertRow('gl', $ca002_data);

        return $inserted;
    }

    public function step_3_remove_pl_accounts($from_date, $to_date)
    {
        $delete_s0 = $this->custom->deleteRow('gl', ['accn like' => 'S0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_ca002 = $this->custom->deleteRow('gl', ['accn' => 'CA002', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_c0 = $this->custom->deleteRow('gl', ['accn like' => 'C0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_i0 = $this->custom->deleteRow('gl', ['accn like' => 'I0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_e0 = $this->custom->deleteRow('gl', ['accn like' => 'E0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_t0 = $this->custom->deleteRow('gl', ['accn like' => 'T0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        $delete_x0 = $this->custom->deleteRow('gl', ['accn like' => 'X0%', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        if ($delete_s0 && $delete_ca002 && $delete_c0 && $delete_i0 && $delete_e0 && $delete_t0 && $delete_x0) {
            return true;
        } else {
            return false;
        }
    }

    public function step_2_add_current_profit_to_rp001($from_date, $to_date, $current_profit)
    {
        $sql_rp001 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn = 'RP001' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_rp001 = $this->db->query($sql_rp001);
        $rp001_data = $query_rp001->result();
        $rp001_current_year_total = 0;
        foreach ($rp001_data as $key => $value) {
            $rp001_current_year_total += $value->account_total_amount;
        }

        // remove current year rp001 transactions from GL
        $delete_rp001 = $this->custom->deleteRow('gl', ['accn' => 'RP001', 'doc_date >=' => $from_date, 'doc_date <=' => $to_date]);

        // add current profit to RP001 (Current Year) amount
        if ($rp001_current_year_total < 0) {
            $rp001_bf_amount = (-1) * $rp001_current_year_total + $current_profit;
        } else {
            $rp001_bf_amount = $rp001_current_year_total + $current_profit;
        }

        $ye_data = $this->custom->getSingleRow('ye_closing', ['process' => 'YE']);

        $gl_data['doc_date'] = $ye_data->new_fy_start_date;
        $gl_data['ref_no'] = 'FY'.$ye_data->closing_year;
        $gl_data['remarks'] = 'Opening Balance';
        $gl_data['accn'] = 'RP001';
        $gl_data['sign'] = '-';
        if ($rp001_bf_amount < 0) {
            $gl_data['total_amount'] = (-1) * $rp001_bf_amount;
        } else {
            $gl_data['total_amount'] = $rp001_bf_amount;
        }
        $gl_data['gstcat'] = '';
        $gl_data['tran_type'] = 'YE';
        $gl_data['sman'] = '';

        $inserted = $this->custom->insertRow('gl', $gl_data);

        return $inserted;
    }

    public function step_1_sum_pl_accounts($from_date, $to_date, $closing_stock_amount)
    {
        // Total Sales - S0 Series Items
        // SALES (S0) Logic
        // SALES ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE) (SALES REVERSAL)
        // SALES ACCOUNT ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT SALES OR DEBIT SALES
        // CREDIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
        // DEBIT SALES - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

        $sql_S0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'S0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_S0 = $this->db->query($sql_S0);
        $S0_data = $query_S0->result();
        $total_sales = 0;
        foreach ($S0_data as $key => $value) {
            $sales_item_amount = (-1) * $value->account_total_amount;
            $total_sales += $sales_item_amount;
        }

        // OPENING STOCK - CA002 - Control Account. It should not change and can not be used for any Other Purpose.
        $sql_OS = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn = 'CA002' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_OS = $this->db->query($sql_OS);
        $OS_data = $query_OS->result();

        $opening_stock = 0;
        foreach ($OS_data as $key => $value) {
            $opening_stock += $value->account_total_amount;
        }

        // COST C0 Items
        // COST (C0) Logic
        // COST ITEMS ARE ALWAYS DEBIT (POSITIVE) BUT SOMETIMES IT MAY BE CREDIT (NEGATIVE)
        // DEBIT COST ITEM - THE AMOUNT WILL BE DISPLAYED AS IT IS AS POSITIVE NUMBER
        // CREDIT COST ITEM - THE AMOUNT HAS TO BE MULTIPLIED BY -1 AND DISPLAY INSIDE BRACKETS

        $sql_C0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'C0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_C0 = $this->db->query($sql_C0);
        $C0_data = $query_C0->result();
        $C0_account_total = 0;
        foreach ($C0_data as $key => $value) {
            $C0_account_total += $value->account_total_amount;
        }
        $opening_stock_with_C0_total = $opening_stock + $C0_account_total;

        // COST OF SALES
        $cost_of_sales = $opening_stock_with_C0_total - $closing_stock_amount;

        // Gross Margin
        $gross_margin = $total_sales - $cost_of_sales;

        // Total Other Income
        // I0 ITEMS ARE ALWAYS CREDIT (NEGATIVE) BUT SOMETIMES IT MAY BE DEBIT (POSITIVE)
        // I0 ITEMS ALWAYS MULTIPLIED BY (-1) WHETHER IT IS CREDIT OR DEBIT AMOUNT
        // CREDIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS POSITIVE NUMBER
        // DEBIT I0 ITEMS - THE AMOUNT HAS TO BE MULTIPLIED BY -1 IN ORDER TO DISPLAY AS NEGATIVE NUMBER BUT IT SHOULD BE DISPLAYED INSIDE BRACKETS

        $sql_I0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'I0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_I0 = $this->db->query($sql_I0);
        $I0_data = $query_I0->result();
        $total_other_income = 0;
        foreach ($I0_data as $key => $value) {
            $i0_item_amount = (-1) * $value->account_total_amount;
            $total_other_income += $i0_item_amount;
        }

        // TOTAL INCOME
        $total_income = 0;
        if ($gross_margin < 0 && $total_other_income > 0) {
            $total_income = $gross_margin + $total_other_income;
        } elseif ($total_other_income < 0 && $gross_margin > 0) {
            $total_income = $total_other_income + $gross_margin;
        } else {
            $total_income = $gross_margin + $total_other_income;
        }

        // Total Expenses
        $sql_E0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'E0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_E0 = $this->db->query($sql_E0);
        $E0_data = $query_E0->result();
        $total_expenses = 0;
        foreach ($E0_data as $key => $value) {
            $total_expenses += $value->account_total_amount;
        }

        // OPERATIONAL NET PROFIT / (LOSS) BEFORE TAX = TOTAL INCOME - TOTAL EXPENSES
        $net_profit_before_tax = $total_income - $total_expenses;

        // Income Tax - T0 Series
        $sql_T0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'T0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_T0 = $this->db->query($sql_T0);
        $T0_data = $query_T0->result();
        $income_tax = 0;
        foreach ($T0_data as $key => $value) {
            $income_tax += $value->account_total_amount;
        }

        // NET PROFIT / (LOSS) AFTER TAX = NET PROFIT / (LOSS) BEFORE TAX - T0 Series Total Amount (Income Tax)
        $net_profit_after_tax = 0;
        $net_profit_after_tax = $net_profit_before_tax - $income_tax;

        // Extra Ordinary Items
        $sql_X0 = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS account_total_amount FROM gl WHERE accn LIKE 'X0%' and doc_date BETWEEN '".$from_date."' and '".$to_date."' GROUP BY accn";
        $query_X0 = $this->db->query($sql_X0);
        $X0_data = $query_X0->result();
        $ex_ordinary = 0;
        foreach ($X0_data as $key => $value) {
            $ex_ordinary += $value->account_total_amount;
        }

        // NET PROFIT / (LOSS) AFTER EXTRAORDINARY ITEMS
        $net_profit_after_Exo = $net_profit_after_tax - $ex_ordinary;

        return $net_profit_after_Exo;
    }

    public function double_document_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $document_reference = $post['document_reference'];
        $gl_reference = $this->custom->getSingleRow('gl', ['ref_no' => $document_reference]);
        if (count($gl_reference)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function get_COA_details()
    {
        is_ajax();
        $post = $this->input->post();
        $coa_data = $this->gl_model->get_COA_details($post);
        $data['coa_code'] = $coa_data->accn;
        $data['coa_description'] = $coa_data->description;
        echo json_encode($data);
    }

    public function get_acc_des()
    {
        is_ajax();
        $post = $this->input->post();
        $coa_data = $this->gl_model->get_acc_des($post);
        $data['coa_code'] = $coa_data->accn;
        $data['coa_description'] = $coa_data->description;
        echo json_encode($data);
    }

    public function get_customer_details()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_customer', 'currency_id', ['customer_id' => $post['customer_id']]);
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $currency_id]);

        $data['currency'] = $currency_data->code;
        $data['currency_rate'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function get_fbank_details()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_id' => $post['fb_id']]);
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $currency_id]);

        $data['currency'] = $currency_data->code;
        $data['currency_rate'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function get_supplier_details()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_id = $this->custom->getSingleValue('master_supplier', 'currency_id', ['supplier_id' => $post['supplier_id']]);
        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $currency_id]);

        $data['currency'] = $currency_data->code;
        $data['currency_rate'] = $currency_data->rate;

        echo json_encode($data);
    }

    public function opening_balance_print()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $cut_off_date = date('d-m-Y');

            if ($post['sort_by'] !== '') {
                $sort_by = $post['sort_by'];
            } else {
                $sort_by = 'accn_code';
            }

            $html = '';
            $html .= '<style type="text/css">
				table { border-collapse: collapse; }
				table th { background: gainsboro; }
				table th, table td {
					border: 1px solid gainsboro;
					padding: 15px 10px; text-align: left;
				}
				</style>';

            $html .= '<table style="width: 100%; border: none">';
            $html .= '<tr>';
            $html .= '<td colspan="2" align="center" style="border: none"><h3>GL OPENING BALANCE</h3></td>';
            $html .= '</tr>';
            $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $html .= '<td style="border: none">';
            $html .= '</td>';
            $html .= '<td style="border: none; text-align: right"><strong>Report Date:</strong> <i>'.$cut_off_date.'</i></td>';
            $html .= '</tr>';

            $html .= '</table> <br /><br />';

            if ($sort_by == 'accn_desc') {
                $sql = 'SELECT gl_open.* FROM gl_open, chart_of_account WHERE gl_open.accn = chart_of_account.accn AND gl_open.status = "C" ORDER BY chart_of_account.description ASC';
            } else {
                $sql = 'SELECT * from gl_open WHERE status = "C" ORDER BY accn ASC, ref_no ASC';
            }
            $query = $this->db->query($sql);
            $gl_data = $query->result();

            $i = 0;
            $total_debit = 0;
            $total_credit = 0;
            foreach ($gl_data as $key => $value) {
                if ($i == 0) {
                    $html .= '<table style="width: 100%;">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th>DATE</th>';
                    $html .= '<th>REFERENCE</th>';
                    if ($sort_by == 'accn_desc') {
                        $html .= '<th>ACCN DESC</th>';
                        $html .= '<th>ACCN CODE</th>';
                    } else {
                        $html .= '<th>ACCN CODE</th>';
                        $html .= '<th>ACCN DESC</th>';
                    }
                    $html .= '<th>DEBIT</th>';
                    $html .= '<th>CREDIT</th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    ++$i;
                }

                $account_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);

                $local_amount = $value->total_local_amount;
                $new_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                $html .= '<td style="width: 90px;">'.$new_date.'</td>';
                $html .= '<td style="width: 110px;">'.$value->ref_no.'</td>';

                if ($sort_by == 'accn_desc') {
                    $html .= '<td style="width: 200px;">'.$account_description.'</td>';
                    $html .= '<td style="width: 120px;">'.$value->accn.'</td>';
                } else {
                    $html .= '<td style="width: 120px;">'.$value->accn.'</td>';
                    $html .= '<td style="width: 200px;">'.$account_description.'</td>';
                }

                if ($value->sign == '+') {
                    $html .= '<td style="width: 100px;">'.number_format($value->total_amount, 2).'</td>';
                    $html .= '<td style="width: 100px;"></td>';
                    $total_debit += $value->total_amount;
                } elseif ($value->sign == '-') {
                    $html .= '<td style="width: 100px;"></td>';
                    $html .= '<td style="width: 100px;">'.number_format($value->total_amount, 2).'</td>';
                    $total_credit += $value->total_amount;
                }
                $html .= '</tr>';
            }

            if ($i == 0) {
                $html .= '<table style="width: 100%;"><tbody><tr>';
                $html .= '<td colspan="6" align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="4" align="right"><strong>TOTAL</strong></td>';
                $html .= '<td><strong>$'.number_format($total_debit, 2).'</strong></td>';
                $html .= '<td><strong>$'.number_format($total_credit, 2).'</strong></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            $document = $html_header.$html;

            $file = 'gl_ob_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/general_ledger/opening_balance_list/print');
        }
    }

    public function get_product_details($billing_id = null)
    {
        $product_details = $this->custom->getSingleRow('master_billing', ['billing_id' => $billing_id]);

        return $product_details;
    }

    public function check_coa_state()
    {
        is_ajax();
        $id = $this->input->post('coa_id');
        $where = ['coa_id' => $id];
        $result = $this->custom->getSingleValue('chart_of_account', 'coa_state', $where);
        echo $result;
    }

    public function double_doc_ref()
    {
        is_ajax();

        $post = $this->input->post();
        $doc_ref_data = $this->custom->getSingleRow('ap_open', ['document_reference' => $post['doc_ref_no'], 'status!=' => 'D']);
        if (empty($doc_ref_data)) {
            echo '0';
        } else {
            echo '1';
        }
        exit;
    }

    public function double_accn()
    {
        is_ajax();
        $post = $this->input->post();
        $accn = $post['accn'];
        $accn_prefix = strtoupper($post['accn_prefix']);

        if ($post['original_accn'] != null && $post['original_accn'] != '') {
            $original_accn = strtoupper($post['original_accn']);
        } else {
            $original_accn = '';
        }

        $accn_prefix_data = $this->custom->getSingleRow('chart_of_account_prefix', ['coa_pre_character' => $accn_prefix]);
        if (count($accn_prefix_data)) {
            $accn_data = $this->custom->getSingleRow('chart_of_account', ['accn' => $accn]);
            if (count($accn_data)) {
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '2';
        }
    }

    public function save_accn()
    {
        $post = $this->input->post();
        if ($post) {
            if ($post['coa_id'] != '') {
                $data['accn'] = $post['accn'];
                $data['description'] = $post['description'];
                $status = $this->custom->updateRow('chart_of_account', $data, ['coa_id' => $post['coa_id']]);
            } else {
                $status = $this->custom->insertRow('chart_of_account', $post);
            }

            if ($status == 'error') {
                echo '1';
            } else {
                echo '0';
            }
        } else {
            echo '1';
        }
    }

    public function delete_accn()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['coa_id' => $id];
        $result = $this->custom->deleteRow('chart_of_account', $where);
        echo $result;
    }
}
