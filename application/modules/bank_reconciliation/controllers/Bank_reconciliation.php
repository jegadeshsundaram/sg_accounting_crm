<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Bank_reconciliation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->logged_id = $this->session->user_id;
        $this->load->model('bank_reconciliation/bank_reconciliation_model', 'bnk_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        // cash book - starts
        $this->body_vars['cb_bank_options'] = $this->custom->populateCOABankListWithFB();
        // $sql = 'SELECT fbl_id, foreign_bank.fb_code, fb_name, currency FROM foreign_bank, master_foreign_bank WHERE foreign_bank.fb_code = master_foreign_bank.fb_code GROUP BY foreign_bank.fb_code ORDER BY foreign_bank.fb_code ASC';
        $options = "<option value=''>-- Select --</option>";
        $this->db->select('fbl_id, fb_code, currency');
        $this->db->from('foreign_bank');
        $this->db->group_by('fb_code');
        $this->db->order_by('fb_code', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            $fb_name = $this->custom->getSingleValue('master_foreign_bank', 'fb_name', ['fb_code' => $value->fb_code]);
            $options .= "<option value='".$value->fb_code."'>";
            $options .= $value->fb_code.' : '.$fb_name.' | '.$value->currency;
            $options .= '</option>';
        }
        $this->body_vars['cb_fbank_options'] = $options;
        // cashbook - ends

        // statement - starts
        // $sql_recon = 'SELECT * FROM bank_recon_info WHERE user_id = "'.$this->logged_id.'" AND current_recon = 1';
        $i = 0;
        $this->db->select('*');
        $this->db->from('bank_recon_info');
        $this->db->where('user_id = "'.$this->logged_id.'" AND current_recon = 1');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            $bank = $value->bank_accn;
            $fbank = $value->fb_id;

            $this->body_vars['recon_id'] = $value->recon_id;
            $this->body_vars['fbank'] = $fb_id;
            $this->body_vars['start_date'] = date('d-m-Y', strtotime($value->start_date));
            $this->body_vars['end_date'] = date('d-m-Y', strtotime($value->end_date));

            $this->body_vars['month'] = date('F', strtotime($value->start_date));
            $this->body_vars['year'] = date('Y', strtotime($value->start_date));

            ++$i;
        }

        $this->body_vars['st_bank_options'] = $this->custom->populateCOABankListWithFB($bank);

        // $sql_fb_list = 'SELECT fbl_id, foreign_bank.fb_code, fb_name, currency_type FROM foreign_bank, foreign_bank_master WHERE foreign_bank.fb_code = foreign_bank_master.fb_code GROUP BY foreign_bank.fb_code ORDER BY foreign_bank.fb_code ASC';
        $options = "<option value=''>-- Select Foreign Bank --</option>";
        $this->db->select('fbl_id, fb_code, currency');
        $this->db->from('foreign_bank');
        $this->db->group_by('fb_code');
        $this->db->order_by('fb_code', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            $fb_name = $this->custom->getSingleValue('master_foreign_bank', 'fb_name', ['fb_code' => $value->fb_code]);
            if ($fbank == $value->fb_code) {
                $options .= "<option value='".$value->fb_code."' selected='selected'>";
            } else {
                $options .= "<option value='".$value->fb_code."'>";
            }
            $options .= $value->fb_code.' : '.$fb_name.' | '.$value->currency;
            $options .= '</option>';
        }

        $this->body_vars['st_fbank_options'] = $options;
        // statement - ends

        $this->body_file = 'bank_reconciliation/options.php';
    }

    public function print_cashbook()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $bank = $post['cb_bank'];
            $fbank = $post['cb_fbank'];
            $recon_status = $post['recon_status'];
            $default_period = $post['default_period'];

            $foreign_bank_reconciliation = false;
            if ($fbank !== '') {
                $foreign_bank_reconciliation = true;
            }

            $current_month_start_date = date('Y-m-d', strtotime($post['cb_start_date']));
            $current_month_end_date = date('Y-m-d', strtotime($post['cb_end_date']));

            if ($recon_status == 'RECON_COMPLETED' && $default_period == 'yes') {
                $default_month = $post['default_month'];
                $default_year = $post['default_year'];

                $default_start_date = date('01'.$default_month.'-'.$default_year);
                $default_end_date = date('t-'.$default_month.'-'.$default_year);

                $current_month_start_date = date('Y-m-d', strtotime($default_start_date));
                $current_month_end_date = date('Y-m-d', strtotime($default_end_date));
            }

            // User not selected following month for bank reconciliation and User selected different month
            // this will come under special scenario when recon_0 items will not be usefull because recon_0 will have only unresolved items of last bank recon completed month
            // Ex: If user doing bank recon for FEB, 2020 and once it is completed, all the unresolved items of FEB, 2020, will be stored in JAN 2020
            // and Next time if user do Bank Recon for Mar 2020 then RECON_0 items will be populated for user to Review and Delete
            // In Any Scenario, If user do Bank Recon for APR or After that (Not Mar 2020) then recon_0 TBL will not be Usefull Because recon_0 tbl will have FEB Unresolved Items
            if ($recon_status == 'RECON_COMPLETED' && $default_period == 'no') {
                $recon_deleted = $this->custom->deleteRow('bank_recon_last', ['bank_accn' => $bank]);
            }

            // Insert Bank and Period details to Bank_recon_info TBL
            if ($foreign_bank_reconciliation) {
                $bank_info_count = $this->custom->getCount('bank_recon_info', ['bank_accn' => $bank, 'fb_id' => $fbank]);
            } else {
                $bank_info_count = $this->custom->getCount('bank_recon_info', ['bank_accn' => $bank]);
            }

            // Update current recon to 0 for all the items before set current recon to this bank
            $recon_0_data['current_recon'] = 0;
            $recon_update_0 = $this->custom->updateRowWithoutWhere('bank_recon_info', $recon_0_data);

            $bank_recon_data['user_id'] = $this->logged_id;
            $bank_recon_data['start_date'] = $current_month_start_date;
            $bank_recon_data['end_date'] = $current_month_end_date;
            $bank_recon_data['status'] = 'PRINT_CASHBOOK';
            $bank_recon_data['current_recon'] = 1;

            if ($bank_info_count == 0) {
                $bank_recon_data['bank_accn'] = $bank;
                $bank_recon_data['fb_id'] = $fbank;

                $recon_updated = $this->custom->insertRow('bank_recon_info', $bank_recon_data);
            } else {
                $recon_inserted = $this->custom->updateRow('bank_recon_info', $bank_recon_data, ['bank_accn' => $bank]);
            }

            // Get Bank Code and Description
            $bank_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $bank]);

            $company_where = ['code' => 'CP'];
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
            $html = '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="text-align: center; border: none;"><h4>CASHBOOK AS AT '.date('d-m-Y', strtotime($current_month_end_date)).'</h4></td>';
            $html .= '</tr>';
            $html .= '</table><br />';

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            if ($foreign_bank_reconciliation) {
                $fb_data = $this->custom->getSingleRow('master_foreign_bank', ['fb_code' => $fbank]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);
                $html .= '<td style="text-align: left; border: none;"><strong>Bank Account:</strong> '.$fbank.' - '.$fb_data->fb_name.' ('.$currency.')</td>';
            } else {
                $html .= '<td style="text-align: left; border: none;"><strong>Bank Account:</strong> '.$bank.' - '.$bank_desc.'</td>';
            }
            $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.strtoupper(date('M j, Y', strtotime(date('d-m-Y')))).'</td>';
            $html .= '</tr>';
            $html .= '</table><br />';

            $html .= '<br /><table>';
            $html .= '<tr>
				<th style="width: 80px">DATE</th>
				<th style="width: 120px">RFERENCE</th>
				<th style="width: 250px">REMARKS</th>
				<th style="width: 120px">DEBIT</th>
				<th style="width: 120px">CREDIT</th>
				<th style="width: 120px">BALANCE</th>
			</tr>';

            // Balance block forward - Start
            $opening_balance = 0;
            $this->db->select('*');
            if ($foreign_bank_reconciliation) {
                $this->db->from('foreign_bank');
                $this->db->where('fb_code = "'.$fbank.'" AND doc_date < "'.$current_month_start_date.'"');
            } else {
                $this->db->from('gl');
                $this->db->where('accn = "'.$bank.'" AND doc_date < "'.$current_month_start_date.'"');
            }
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            $ob_list = $query->result();
            foreach ($ob_list as $value) {
                if ($foreign_bank_reconciliation) {
                    $amount = $value->fa_amt;
                } else {
                    $amount = $value->total_amount;
                }

                if ($value->sign == '+') {
                    $opening_balance += $amount;
                } elseif ($value->sign == '-') {
                    $opening_balance -= $amount;
                }
            }

            $running_balance = 0;

            if ($opening_balance != 0) {
                $html .= '<tr>';
                $html .= '<td>'.date('d-m-Y', strtotime($current_month_start_date)).'</td>';
                $html .= '<td>BALANCE B/F</td>';
                $html .= '<td><i>Transactions before Financial Year</i></td>';
                if ($opening_balance >= 0) {
                    $html .= '<td>'.number_format($opening_balance, 2).'</td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.number_format($opening_balance, 2).'</td>';
                } else {
                    $html .= '<td></td>';
                    $html .= '<td>'.number_format((-1) * $opening_balance, 2).'</td>';
                    $html .= '<td>('.number_format((-1) * $opening_balance, 2).')</td>';
                }
                $html .= '</tr>';
                $running_balance += $opening_balance;
            }
            // Balance block forward - End

            $this->db->select('*');
            if ($foreign_bank_reconciliation) {
                $this->db->from('foreign_bank');
                $this->db->where('fb_code = "'.$fbank.'" AND doc_date BETWEEN "'.$current_month_start_date.'" AND "'.$current_month_end_date.'"');
            } else {
                $this->db->from('gl');
                $this->db->where('accn = "'.$bank.'" AND doc_date BETWEEN "'.$current_month_start_date.'" AND "'.$current_month_end_date.'"');
            }
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                if ($foreign_bank_reconciliation) {
                    $document_reference = strtoupper($value->doc_ref_no);
                    $amount = $value->fa_amt;
                } else {
                    $document_reference = strtoupper($value->ref_no);
                    $amount = $value->total_amount;
                }

                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$document_reference.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $html .= '<td>'.number_format($amount, 2).'</td>';
                    $html .= '<td></td>';
                    $running_balance += $amount;
                } elseif ($value->sign == '-') {
                    $html .= '<td></td>';
                    $html .= '<td>'.number_format($amount, 2).'</td>';
                    $running_balance -= $amount;
                }

                if ($running_balance >= 0) {
                    $html .= '<td>'.number_format($running_balance, 2).'</td>';
                } else {
                    $html .= '<td>('.number_format((-1) * $running_balance, 2).')</td>';
                }

                $html .= '</tr>';
            }

            // Closing Balance - START
            $html .= '<tr>';
            $html .= '<td colspan="5" style="text-align:right; color: red">CLOSING BALANCE</td>';
            if ($running_balance >= 0) {
                $html .= '<td>'.number_format($running_balance, 2).'</td>';
            } else {
                $html .= '<td>('.number_format((-1) * $running_balance, 2).')</td>';
            }
            $html .= '</tr>';
            // Closing Balance - END

            $html .= '</thead>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'cashbook_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/bank_reconciliation?error=post');
        }
    }

    // review last few months item's and do recon
    public function review()
    {
        is_logged_in('admin');
        has_permission();

        // $sql_recon = 'SELECT * FROM bank_recon_info WHERE user_id = "'.$this->logged_id.'" AND current_recon = 1';

        $i = 0;
        $this->db->select('recon_id');
        $this->db->from('bank_recon_info');
        $this->db->where('user_id = "'.$this->logged_id.'" AND current_recon = 1');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            $this->body_vars['recon_id'] = $value->recon_id;
            ++$i;
        }

        if ($i == 0) {
            set_flash_message('message', 'danger', 'Bank Recon details not found. Please print cashbook to proceed.');
            redirect('bank_reconciliation');
        } else {
            $this->body_file = 'bank_reconciliation/review.php';
        }
    }

    public function account_items()
    {
        $post = $this->input->post();
        if ($post) {
            $br_ids = $post['br_ids'];

            $splitted_br_ids = explode(',', $br_ids);
            foreach ($splitted_br_ids as $key => $value) {
                $br_id = $value;

                $br[] = $this->custom->updateRow('bank_recon_last', ['accounted' => 'y'], ['br_id' => $br_id]);

                if ($this->db->trans_status() === false || in_array('error', $br)) {
                    set_flash_message('message', 'danger', 'DELETE ERROR');
                    $this->db->trans_rollback();
                } else {
                    set_flash_message('message', 'success', 'RECON ITEMS DELETED');
                    $this->db->trans_commit();
                }
            }

            redirect('bank_reconciliation');
        } else {
            set_flash_message('message', 'danger', 'POST ERROR');
            redirect('bank_reconciliation/review');
        }
    }

    // input current month items
    public function input_options()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function input()
    {
        is_logged_in('admin');
        has_permission();

        // $sql_recon = 'SELECT * FROM bank_recon_info WHERE user_id = "'.$this->logged_id.'" AND current_recon = 1';

        $i = 0;
        $this->db->select('*');
        $this->db->from('bank_recon_info');
        $this->db->where('user_id = "'.$this->logged_id.'" AND current_recon = 1');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            ++$i;
            $bank = $value->bank_accn;
            $fbank = $value->fb_id;

            $start_date = $value->start_date;
            $end_date = $value->end_date;
        }

        $this->body_vars['transaction_type'] = $this->uri->segment(3);
        $this->body_vars['bank'] = $bank;

        $this->body_vars['bank_list'] = $this->custom->populateCOABankListWithFB($bank);

        // $sql_fb_list = 'SELECT fbl_id, foreign_bank.fb_code, fb_name, currency FROM foreign_bank, master_foreign_bank WHERE foreign_bank.fb_code = master_foreign_bank.fb_code GROUP BY foreign_bank.fb_code ORDER BY foreign_bank.fb_code ASC';
        $options = "<option value=''>-- Select Foreign Bank --</option>";
        $this->db->select('fbl_id, foreign_bank.fb_code, fb_name, currency');
        $this->db->from('foreign_bank, master_foreign_bank');
        $this->db->where('foreign_bank.fb_code = master_foreign_bank.fb_code');
        $this->db->group_by('foreign_bank.fb_code');
        $this->db->order_by('foreign_bank.fb_code', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            if ($fb_id == $value->fb_code) {
                $options .= "<option value='".$value->fb_code."' selected='selected'>";
            } else {
                $options .= "<option value='".$value->fb_code."'>";
            }
            $options .= $value->fb_code.' : '.$value->fb_name.' | '.$value->currency;
            $options .= '</option>';
        }
        $this->body_vars['fb_list'] = $options;

        // Gl Reference List, So that User can select Reference and auto populate all the details instead on manual inputting
        if ($bank == 'CA110') {
            $this->body_vars['recon_bank'] = 'ForeignBank';

            $options = "<option value=''>Select Transaction</option>";

            // $sql = 'SELECT fbl_id, doc_date, doc_ref_no, fa_amt, remarks FROM foreign_bank WHERE doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY doc_date ASC, doc_ref_no ASC';
            $this->db->select('fbl_id, doc_date, doc_ref_no, fa_amt, remarks');
            $this->db->from('foreign_bank');
            $this->db->where('doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
            $this->db->order_by('doc_date, doc_ref_no', 'ASC, ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                $options .= "<option value='".$value->fbl_id."'>";
                $options .= date('d-m-Y', strtotime($value->doc_date)).' | '.$value->doc_ref_no.' | '.$value->remarks.' ($'.$value->fa_amt.')';
                $options .= '</option>';
            }

            $this->body_vars['reference_list'] = $options;
        } else {
            $this->body_vars['recon_bank'] = 'OtherBank';

            $gl_reference_options = "<option value=''>Select</option>";

            // $sql_gl_reference_list = 'SELECT gl_id, doc_date, ref_no, remarks, total_amount, sign FROM gl WHERE accn = "'.$bank.'" AND doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY doc_date ASC, ref_no ASC';
            $this->db->select('gl_id, doc_date, ref_no, remarks, total_amount, sign');
            $this->db->from('gl');
            $this->db->where('accn = "'.$bank.'" AND doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
            $this->db->order_by('doc_date, ref_no', 'ASC, ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                if ($value->sign == '+') { // Debit Transaction should be displayed as Credit and Viceversa
                    $sign_text = ' (CR)';
                } else {
                    $sign_text = ' (DR)';
                }
                $gl_reference_options .= "<option value='".$value->gl_id."'>";
                $gl_reference_options .= date('d-m-Y', strtotime($value->doc_date)).' | '.$value->ref_no.' | '.$value->remarks.' | $'.$value->total_amount.$sign_text;
                $gl_reference_options .= '</option>';
            }

            $this->body_vars['reference_list'] = $gl_reference_options;
        }

        if ($i == 0) {
            set_flash_message('message', 'danger', 'Bank Recon details not found. Please print cashbook to proceed.');
            redirect('bank_reconciliation');
        }

        $this->body_file = 'bank_reconciliation/input.php';
    }

    // SAVE to Bank_Recon_Current.TBL
    public function save_current()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $bank = $post['bank'];
            $fbank = $post['fbank'];

            if ($fbank !== null && $fbank !== '') {
                $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $fbank]);
                $exchange_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $fb_currency_id]);
            } else {
                $exchange_rate = '1.00000';
            }

            $total_items = count($post['doc_date']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                if ($post['doc_date'][$i] == '' || $post['doc_ref'][$i] == '') {
                    continue;
                }
                $batch_data['doc_date'] = date('Y-m-d', strtotime($post['doc_date'][$i]));
                $batch_data['doc_ref'] = $post['doc_ref'][$i];
                $batch_data['remarks'] = $post['remarks'][$i];

                $batch_data['amount'] = $post['amount'][$i];
                $local_amount = $post['amount'][$i] / $exchange_rate;
                $batch_data['exchange_rate'] = $exchange_rate;
                $batch_data['local_amount'] = number_format($local_amount, 2, '.', '');

                $batch_data['sign'] = $post['sign'][$i];
                $batch_data['bank_accn'] = $bank;
                $batch_data['fb_id'] = $fbank;
                $batch_data['tran_type'] = $post['tran_type'];

                $recon_item_id = $post['recon_item_id'][$i];
                if ($recon_item_id != '') {
                    $recon_status[] = $this->custom->updateRow('bank_recon_current', $batch_data, ['br_id' => $recon_item_id]);
                } else {
                    $recon_status[] = $this->custom->insertRow('bank_recon_current', $batch_data);
                }
            }

            if (in_array('error', $recon_status)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Recon Saved');
            }

            redirect('bank_reconciliation');
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
            redirect('bank_reconciliation');
        }
    }

    public function other_adjustment()
    {
        is_logged_in('admin');
        has_permission();

        // $sql_recon = 'SELECT * FROM bank_recon_info WHERE user_id = "'.$this->logged_id.'" AND current_recon = 1';
        $i = 0;
        $this->db->select('*');
        $this->db->from('bank_recon_info');
        $this->db->where('user_id = "'.$this->logged_id.'" AND current_recon = 1');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            ++$i;
            $bank = $value->bank_accn;
            $fbank = $value->fb_id;
        }

        $this->body_vars['bank_list'] = $this->custom->populateCOABankListWithFB($bank);

        // $sql_fb_list = 'SELECT fbl_id, foreign_bank.fb_code, fb_name, currency FROM foreign_bank, master_foreign_bank WHERE foreign_bank.fb_code = master_foreign_bank.fb_code GROUP BY foreign_bank.fb_code ORDER BY foreign_bank.fb_code ASC';
        $options = "<option value=''>-- Select Foreign Bank --</option>";
        $this->db->select('fbl_id, foreign_bank.fb_code, fb_name, currency');
        $this->db->from('foreign_bank, master_foreign_bank');
        $this->db->where('foreign_bank.fb_code = master_foreign_bank.fb_code');
        $this->db->group_by('foreign_bank.fb_code');
        $this->db->order_by('foreign_bank.fb_code', 'ASC');
        $query = $this->db->get();
        $list = $query->result();
        foreach ($list as $value) {
            if ($fbank == $value->fb_code) {
                $options .= "<option value='".$value->fb_code."' selected='selected'>";
            } else {
                $options .= "<option value='".$value->fb_code."'>";
            }
            $options .= $value->fb_code.' : '.$value->fb_name.' | '.$value->currency;
            $options .= '</option>';
        }

        $this->body_vars['fb_list'] = $options;

        if ($i == 0) {
            set_flash_message('message', 'danger', 'Bank Recon details not found. Please print cashbook to proceed.');
            redirect('bank_reconciliation');
        }

        $this->body_file = 'bank_reconciliation/other_adjustment.php';
    }

    public function complete_recon()
    {
        $post = $this->input->post();

        if ($post) {
            $bank = $post['st_bank'];
            $fbank = $post['st_fbank'];

            $foreign_bank_reconciliation = false;
            if ($fbank !== '') {
                $foreign_bank_reconciliation = true;
            }

            // On completing Bank Recon for Current Month,
            // updating default (Following) Start and End Date in Bank_Recon_Info.TBL
            $month = date('m', strtotime($post['st_start_date']));
            $year = date('Y', strtotime($post['st_start_date']));

            $default_month = $month + 1;
            $default_year = $year;
            if ($month == 12) {
                $default_month = 1;
                $default_year = $year + 1;
            }

            $default_start_date = date($default_year.'-'.$default_month.'-01');
            $default_end_date = date('Y-m-t', strtotime($default_start_date));

            $this->db->select('*');
            $this->db->from('bank_recon_current');
            if ($foreign_bank_reconciliation) {
                $this->db->where(['bank_accn' => $bank, 'fb_id' => $fbank]);
            } else {
                $this->db->where(['bank_accn' => $bank]);
            }
            $query = $this->db->get();
            $current_month_recon_data = $query->result();

            $recon_1_count = 0;
            foreach ($current_month_recon_data as $key => $value) {
                ++$recon_1_count;
                $last_month_recon_data['doc_date'] = $value->doc_date;
                $last_month_recon_data['doc_ref'] = $value->doc_ref;
                $last_month_recon_data['remarks'] = $value->remarks;
                $last_month_recon_data['amount'] = $value->amount;
                $last_month_recon_data['exchange_rate'] = $value->exchange_rate;
                $last_month_recon_data['local_amount'] = $value->local_amount;
                $last_month_recon_data['sign'] = $value->sign;
                $last_month_recon_data['month'] = $month;
                $last_month_recon_data['bank_accn'] = $value->bank_accn;
                $last_month_recon_data['fb_id'] = $value->fb_id;
                $last_month_recon_data['tran_type'] = $value->tran_type;

                $recon_inserted = $this->custom->insertRow('bank_recon_last', $last_month_recon_data);

                if ($recon_inserted) {
                    $recon_deleted = $this->custom->deleteRow('bank_recon_current', ['br_id' => $value->br_id]);
                }
            }

            if ($recon_inserted || $recon_1_count == 0) {
                $bank_recon_data['status'] = 'RECON_COMPLETED';
                $bank_recon_data['start_date'] = $default_start_date;
                $bank_recon_data['end_date'] = $default_end_date;

                if ($foreign_bank_reconciliation) {
                    $recon_updated = $this->custom->updateRow('bank_recon_info', $bank_recon_data, ['bank_accn' => $bank, 'fb_id' => $fbank]);
                } else {
                    $recon_updated = $this->custom->updateRow('bank_recon_info', $bank_recon_data, ['bank_accn' => $bank]);
                }

                set_flash_message('message', 'success', 'BANK RECONCILIATION COMPLETED');
            } else {
                set_flash_message('message', 'danger', 'BANK RECONCILIATION ERROR');
            }

            redirect('bank_reconciliation');
        }
    }

    public function statement()
    {
        is_logged_in('admin');
        has_permission();

        $sql_recon = 'SELECT * FROM bank_recon_info WHERE user_id = "'.$this->logged_id.'" AND current_recon = 1';
        $query_recon = $this->db->query($sql_recon);
        $recon_data = $query_recon->result();
        $i = 0;
        foreach ($recon_data as $key => $value) {
            $bank_accn = $value->bank_accn;
            $fb_id = $value->fb_id;

            $this->body_vars['recon_id'] = $recon_id = $value->recon_id;
            $this->body_vars['fb_id'] = $fb_id = $fb_id;
            $this->body_vars['start_date'] = $start_date = date('d-m-Y', strtotime($value->start_date));
            $this->body_vars['end_date'] = $end_date = date('d-m-Y', strtotime($value->end_date));

            $this->body_vars['month'] = $month = date('F', strtotime($value->start_date));
            $this->body_vars['year'] = $year = date('Y', strtotime($value->start_date));

            ++$i;
        }

        $this->body_vars['bank_list'] = $this->custom->populateCOABankListWithFB($bank_accn);

        $sql_fb_list = 'SELECT fbl_id, foreign_bank.fb_code, fb_name, currency FROM foreign_bank, master_foreign_bank WHERE foreign_bank.fb_code = master_foreign_bank.fb_code GROUP BY foreign_bank.fb_code ORDER BY foreign_bank.fb_code ASC';
        $query_fb_list = $this->db->query($sql_fb_list);
        $foreign_bank_list = $query_fb_list->result();
        $fb_options .= "<option value=''>-- Select Foreign Bank --</option>";
        foreach ($foreign_bank_list as $key => $value) {
            if ($fb_id == $value->fb_code) {
                $fb_options .= "<option value='".$value->fb_code."' selected='selected'>";
            } else {
                $fb_options .= "<option value='".$value->fb_code."'>";
            }
            $fb_options .= $value->fb_code.' : '.$value->fb_name.' | '.$value->currency;
            $fb_options .= '</option>';
        }

        $this->body_vars['fb_list'] = $fb_options;

        if ($i == 0) {
            set_flash_message('message', 'danger', 'Bank Recon details not found. Please print cashbook to proceed.');
            redirect('bank_reconciliation');
        }

        $this->body_file = 'bank_reconciliation/statement.php';
    }

    public function get_closing_balance_per_cashbook($bank_accn, $fb_id, $current_month_start_date, $current_month_end_date)
    {
        if ($fb_id !== '') {
            $sql_opening_balance = 'SELECT * FROM foreign_bank WHERE fb_code = "'.$fb_id.'" and doc_date < "'.$current_month_start_date.'" ORDER BY doc_date ASC';
        } else {
            $sql_opening_balance = 'SELECT * FROM gl WHERE accn = "'.$bank_accn.'" and doc_date < "'.$current_month_start_date.'" ORDER BY doc_date ASC';
        }

        $query_opening_balance = $this->db->query($sql_opening_balance);
        $gl_opening_balance = $query_opening_balance->result();
        $opening_balance = 0;
        $running_balance = 0;
        foreach ($gl_opening_balance as $key => $value) {
            if ($fb_id !== '') {
                $amount = $value->fa_amt;
            } else {
                $amount = $value->total_amount;
            }

            if ($value->sign == '+') {
                $opening_balance += $amount;
            } elseif ($value->sign == '-') {
                $opening_balance -= $amount;
            }
        }

        $running_balance += $opening_balance;

        if ($fb_id !== '') {
            $sql = 'SELECT * FROM foreign_bank WHERE fb_code = "'.$fb_id.'" AND doc_date BETWEEN "'.$current_month_start_date.'" AND "'.$current_month_end_date.'"ORDER BY doc_date ASC';
        } else {
            $sql = 'SELECT * FROM gl WHERE accn = "'.$bank_accn.'" AND doc_date BETWEEN "'.$current_month_start_date.'" AND "'.$current_month_end_date.'"ORDER BY doc_date ASC';
        }
        $query = $this->db->query($sql);
        $gl_data = $query->result();
        foreach ($gl_data as $key => $value) {
            if ($fb_id !== '') {
                $amount = $value->fa_amt;
            } else {
                $amount = $value->total_amount;
            }

            if ($value->sign == '+') {
                $running_balance += $amount;
            } elseif ($value->sign == '-') {
                $running_balance -= $amount;
            }
        }

        return $running_balance;
    }

    public function print_statement()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $bank = $post['st_bank'];
            $fbank = $post['st_fbank'];

            $foreign_bank_reconciliation = false;
            if ($fbank !== '') {
                $foreign_bank_reconciliation = true;
            }

            $current_month_start_date = date('Y-m-d', strtotime($post['st_start_date']));
            $current_month_end_date = date('Y-m-d', strtotime($post['st_end_date']));

            $month = date('m', strtotime($current_month_start_date));
            $year = date('Y', strtotime($current_month_start_date));

            $last_month = $month - 1;
            if ($month == '01') {
                $last_month = '12';
                --$year;
            }

            $last_month_start_date = date($year.'-'.$last_month.'-01');
            $last_month_end_date = date($year.'-'.$last_month.'-t');

            // Get Bank Code and Description
            $bank_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $bank]);

            $html = '';

            $company_where = ['code' => 'CP'];
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr></table>';

            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="text-align: center; border: none;"><h4>BANK RECONCILIATION STATEMENT AS AT '.$post['st_end_date'].'</h4></td>';
            $html .= '</tr>';
            $html .= '<tr><td colspan="2" height="5" style="border: none"></td></tr>';
            $html .= '<tr>';

            if ($foreign_bank_reconciliation) {
                $fb_data = $this->custom->getSingleRow('master_foreign_bank', ['fb_code' => $fbank]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);
                $html .= '<td style="text-align: left; border: none;"><strong>Bank Account:</strong> '.$fbank.' - '.$fb_data->fb_name.' ('.$currency.')</td>';
            } else {
                $html .= '<td style="text-align: left; border: none;"><strong>Bank Account:</strong> '.$bank.' - '.$bank_desc.'</td>';
            }

            $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.strtoupper(date('M j, Y', strtotime(date('d-m-Y')))).'</td>';
            $html .= '</tr>';
            $html .= '</table><br />';

            $html .= '<br /><table>';
            $html .= '<tr>
				<th style="padding-left: 10px;">Date</th>
				<th style="padding-left: 10px;">Reference</th>
				<th style="padding-left: 10px;">Remarks</th>
				<th style="padding-left: 10px; text-align: right">Amount</th>
			</tr>';

            // get closing balance per cashbook
            $closingbalance_per_cashbook = $this->get_closing_balance_per_cashbook($bank, $fbank, $current_month_start_date, $current_month_end_date);

            $running_balance = 0;

            $html .= '<tr>';
            $html .= '<td>'.$post['st_start_date'].'</td>';
            $html .= '<td>Closing Balance</td>';
            $html .= '<td>Closing Balance per Cash Book</td>';
            if ($closingbalance_per_cashbook >= 0) {
                $html .= '<td style="text-align: right;">'.number_format($closingbalance_per_cashbook, 2).'</td>';
            } else {
                $html .= '<td style="text-align: right;">('.number_format((-1) * $closingbalance_per_cashbook, 2).')</td>';
            }
            $html .= '</tr>';

            $running_balance += $closingbalance_per_cashbook;

            // 1. ALL THE PREVIOUS MONTH UNRESOLVED ITEMS IN RECON_0.TBL WILL BE DISPLAYED IF ANY
            // 2. ALL THE ITEMS FROM RECON_1.TBL EXCEPT "A" ITEMS (OTHER ADJUSTMENT) WILL BE DISPLAYED.
            // 3. SPECIAL SCENARIO :: ALL THE OTHER ADJUSTMENT ITEMS (FLAG - "A") FROM RECON_1.TBL WILL BE DISPLAYED.

            // Normal Scenario :: 1 :: RECON_0 (Pervious Month Items)
            $sql_recon_last .= 'SELECT * FROM bank_recon_last WHERE bank_accn = "'.$bank.'" ';
            if ($foreign_bank_reconciliation) {
                $sql_recon_last .= 'AND fb_id = "'.$fbank.'" ';
            }
            $sql_recon_last .= 'AND accounted = "n" ORDER BY doc_date ASC';

            $query = $this->db->query($sql_recon_last);
            $last_month_recon_data = $query->result();
            $last_month_recon_total = 0;
            $recon_0_entry = 0;
            foreach ($last_month_recon_data as $key => $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$value->doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $last_month_recon_total += $value->amount;
                    $html .= '<td style="text-align: right;">'.number_format($value->amount, 2).'</td>';
                } elseif ($value->sign == '-') {
                    $last_month_recon_total -= $value->amount;
                    $html .= '<td style="text-align: right;">('.number_format($value->amount, 2).')</td>';
                }
                $html .= '</tr>';
                ++$recon_0_entry;
            }

            // Recon Items Sub Total
            $running_balance += $last_month_recon_total;

            // Normal Scenario :: 2 :: RECON_1
            // $sql = 'SELECT * FROM bank_recon_current WHERE bank_accn = "'.$bank.'" AND doc_date BETWEEN "'.$current_month_start_date. '" AND "'.$current_month_end_date.'" AND accounted = "n" AND tran_type in ("EO", "TD") ORDER BY doc_date ASC';

            $sql_recon_current_C .= 'SELECT * FROM bank_recon_current WHERE bank_accn = "'.$bank.'" ';
            if ($foreign_bank_reconciliation) {
                $sql_recon_current_C .= 'AND fb_id = "'.$fbank.'" ';
            }
            $sql_recon_current_C .= 'AND doc_date BETWEEN "'.$current_month_start_date.'" AND "'.$current_month_end_date.'" AND accounted = "n" AND tran_type in ("EO", "TD") ORDER BY doc_date ASC';

            $query = $this->db->query($sql_recon_current_C);
            $current_month_recon_data = $query->result();
            $current_month_recon_total = 0;
            $recon_1_entry = 0;
            foreach ($current_month_recon_data as $key => $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$value->doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $current_month_recon_total += $value->amount;
                    $html .= '<td style="text-align: right;">'.number_format($value->amount, 2).'</td>';
                } elseif ($value->sign == '-') {
                    $current_month_recon_total -= $value->amount;
                    $html .= '<td style="text-align: right;">('.number_format($value->amount, 2).')</td>';
                }
                $html .= '</tr>';
                ++$recon_1_entry;
            }

            // Running_balance = Closing Balance Per Cashbook + Recon_1 Items Sub Total
            $running_balance += $current_month_recon_total;

            // Special Scenario :: 3 :: OTHER ADJUSTMENT
            // $sql = 'SELECT * FROM bank_recon_current WHERE bank_accn = "'.$bank_accn.'" AND accounted = "n" AND tran_type = "A" ORDER BY doc_date ASC';

            $sql_recon_current_A .= 'SELECT * FROM bank_recon_current WHERE bank_accn = "'.$bank.'" ';
            if ($foreign_bank_reconciliation) {
                $sql_recon_current_A .= 'AND fb_id = "'.$fbank.'" ';
            }
            $sql_recon_current_A .= 'AND accounted = "n" AND tran_type = "A" ORDER BY doc_date ASC';

            $query = $this->db->query($sql_recon_current_A);
            $other_adjustment_recon_data = $query->result();
            $other_adjustment_recon_total = 0;
            $other_adjustment_entry = 0;
            foreach ($other_adjustment_recon_data as $key => $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$value->doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $other_adjustment_recon_total += $value->amount;
                    $html .= '<td style="text-align: right;">'.number_format($value->amount, 2).'</td>';
                } elseif ($value->sign == '-') {
                    $other_adjustment_recon_total -= $value->amount;
                    $html .= '<td style="text-align: right;">('.number_format($value->amount, 2).')</td>';
                }
                $html .= '</tr>';
                ++$other_adjustment_entry;
            }

            // Recon_1 Other Adjustments Items Sub Total
            $running_balance += $other_adjustment_recon_total;

            // Balance per Bank Statement = Closing Balance per Cashbook + Recon Last Month Total + Recon Current Month Total + Other Adjustment Total
            // Balance per Bank Statement = Running Balance

            $html .= '<tr>';
            $html .= '<td colspan="3" style="text-align: right; font-weight: bold; font-style: italic">Balance Per Bank Statement</td>';
            $html .= '<td style="text-align: right; font-weight: bold">';
            if ($running_balance >= 0) {
                $html .= number_format($running_balance, 2);
            } else {
                $html .= '('.number_format((-1) * $running_balance, 2).')';
            }
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '</thead>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'recon_st_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/bank_reconciliation');
        }
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        $file_name = 'bank_recon_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['bank_recon_info', 'bank_recon_last', 'bank_recon_current'],
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
            set_flash_message('message', 'success', 'Bank Reconciliation Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('bank_reconciliation/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'bank_reconciliation/blank.php';
        zapBankRecon();
        redirect('bank_reconciliation/', 'refresh');
    }
}
