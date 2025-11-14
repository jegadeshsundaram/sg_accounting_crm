<?php

defined('BASEPATH') or exit('No direct script access allowed');
class YE_revision extends CI_Controller
{
    public $view_path;
    public $data;
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'ye_revision';
        $this->logged_id = $this->session->user_id;
        $this->view_path = 'combo_tables/ye_revision';
    }

    // year end revision
    public function currency_exist_check()
    {
        is_ajax();
        $post = $this->input->post();

        $currency_list_array = $this->currency_list();

        $exist_currency_count = 0;
        foreach ($currency_list_array as $key => $value) {
            $individual_currency = $this->custom->getSingleRow('ye_revision', ['currency' => $value]);
            if (count($individual_currency)) {
                ++$exist_currency_count;
            }
        }

        if ($exist_currency_count == count($currency_list_array)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function backup_subledger_before_ye_revision($sl_name, $sl_tbl_name)
    {
        // Create New Table for AR Backup Before do Year End Revision
        $backup_table_name = $sl_name.'_before_ye_revision_'.date('dmY').'_'.time();
        $sql_backup_table = 'CREATE TABLE '.$backup_table_name.' LIKE '.$sl_tbl_name;
        $create_backup_tbl = $this->db->query($sql_backup_table);

        // Inserting all the transactions from AR TBl to AR BACKUP TBL
        $sql_clone_real_tbl = 'INSERT INTO '.$backup_table_name.' SELECT * FROM '.$sl_tbl_name;
        $clone_real_tbl = $this->db->query($sql_clone_real_tbl);

        return $clone_real_tbl;
    }

    public function get_grand_total_at_different_stages($subledger)
    {
        // save SGD Grand Total before do REVISION.

        if ($subledger == 'accounts_receivable') {
            $sql_grand_total = 'SELECT sign, total_amt FROM accounts_receivable WHERE offset = "n" ORDER BY ar_id ASC';
        } elseif ($subledger == 'accounts_payable') {
            $sql_grand_total = 'SELECT sign, total_amt FROM accounts_payable WHERE offset = "n" ORDER BY ap_id ASC';
        } elseif ($subledger == 'foreign_bank') {
            $sql_grand_total = 'SELECT sign, local_amt as total_amt FROM foreign_bank WHERE offset = "n" ORDER BY fbl_id ASC';
        }

        $query_grand_total = $this->db->query($sql_grand_total);
        $grand_total = $query_grand_total->result();

        $sgd_grand_total = 0;
        foreach ($grand_total as $key => $value) {
            if ($value->sign == '+') {
                $sgd_grand_total += $value->total_amt;
            } elseif ($value->sign == '-') {
                $sgd_grand_total -= $value->total_amt;
            }
        }

        return $sgd_grand_total;
    }

    public function apply_YE_revision_AR($ye_cutoff_date)
    {
        $backup_done = $this->backup_subledger_before_ye_revision('ar', 'accounts_receivable');

        if ($backup_done) {
            // save SGD Grand Total before do REVISION.
            $sgd_grand_total_before_ye_revision = $this->get_grand_total_at_different_stages('accounts_receivable');
            $update_grand_total = $this->custom->updateRow('ye_values_before_revision', ['sgd_grand_total_before_revision' => $sgd_grand_total_before_ye_revision, 'revision_done' => 0], ['b_id' => 1]);

            // get currency details from ye_revision TBL one by one
            $sql_ye_currency_list = 'SELECT * FROM ye_revision WHERE currency != "SGD" GROUP BY currency ORDER BY currency ASC';
            $query_ye_currency_list = $this->db->query($sql_ye_currency_list);
            $ye_currency_list = $query_ye_currency_list->result();

            // Loop each and every currency from YE_REVISION TBL
            $current_currency = '';
            $current_rate = '';
            foreach ($ye_currency_list as $key => $value) {
                $current_currency = $value->currency;
                $current_rate = $value->rate;
                $current_cutoff_date = $value->cutoff_date;

                // get all the UNSETTLED TRANSACTIONS FROM ACCOUNTS_RECEIVABLE.TBL
                $sql_unsettled_transactions = 'SELECT * FROM accounts_receivable WHERE doc_date <= "'.$current_cutoff_date.'" AND currency = "'.$current_currency.'" AND settled = "n" ORDER BY ar_id ASC';
                $query_unsettled_transactions = $this->db->query($sql_unsettled_transactions);
                $unsettled_transactions = $query_unsettled_transactions->result();

                $ar_id = 0;
                foreach ($unsettled_transactions as $key => $value) {
                    $ar_id = $value->ar_id;
                    $individual_foreign_amount = $value->f_amt;

                    $new_individual_local_amount = round($individual_foreign_amount / $current_rate, 2);

                    $sgd_update_by_ye_rate[] = $this->custom->updateRow('accounts_receivable', ['total_amt' => $new_individual_local_amount], ['ar_id' => $ar_id]);
                }
            }
        }

        return $sgd_update_by_ye_rate;
    }

    public function apply_YE_revision_AP($ye_cutoff_date)
    {
        $backup_done = $this->backup_subledger_before_ye_revision('ap', 'accounts_payable');

        if ($backup_done) {
            // save SGD Grand Total before do REVISION.
            $sgd_grand_total_before_ye_revision = $this->get_grand_total_at_different_stages('accounts_payable');
            $update_grand_total = $this->custom->updateRow('ye_values_before_revision', ['sgd_grand_total_before_revision' => $sgd_grand_total_before_ye_revision, 'revision_done' => 0], ['b_id' => 2]);

            // get currency details from ye_revision TBL one by one
            $sql_ye_currency_list = 'SELECT * FROM ye_revision WHERE currency != "SGD" GROUP BY currency ORDER BY currency ASC';
            $query_ye_currency_list = $this->db->query($sql_ye_currency_list);
            $ye_currency_list = $query_ye_currency_list->result();

            // Loop each and every currency from YE_REVISION TBL
            $current_currency = '';
            $current_rate = '';
            foreach ($ye_currency_list as $key => $value) {
                $current_currency = $value->currency;
                $current_rate = $value->rate;
                $current_cutoff_date = $value->cutoff_date;

                // get all the UNSETTLED TRANSACTIONS FROM ACCOUNTS_PAYABLE.TBL
                $sql_unsettled_transactions = 'SELECT * FROM accounts_payable WHERE doc_date <= "'.$current_cutoff_date.'" AND currency = "'.$current_currency.'" AND settled = "n" ORDER BY ap_id ASC';
                $query_unsettled_transactions = $this->db->query($sql_unsettled_transactions);
                $unsettled_transactions = $query_unsettled_transactions->result();

                $ap_id = 0;
                foreach ($unsettled_transactions as $key => $value) {
                    $ap_id = $value->ap_id;
                    $individual_foreign_amount = $value->fa_amt;

                    $new_individual_local_amount = round($individual_foreign_amount / $current_rate, 2);

                    $sgd_update_by_ye_rate[] = $this->custom->updateRow('accounts_payable', ['total_amt' => $new_individual_local_amount], ['ap_id' => $ap_id]);
                }
            }
        }

        return $sgd_update_by_ye_rate;
    }

    public function apply_YE_revision_FB($ye_cutoff_date)
    {
        $backup_done = $this->backup_subledger_before_ye_revision('fb', 'foreign_bank');

        if ($backup_done) {
            // save SGD Grand Total before do REVISION.
            $sgd_grand_total_before_ye_revision = $this->get_grand_total_at_different_stages('foreign_bank');
            $update_grand_total = $this->custom->updateRow('ye_values_before_revision', ['sgd_grand_total_before_revision' => $sgd_grand_total_before_ye_revision, 'revision_done' => 0], ['b_id' => 3]);

            // get currency details from ye_revision TBL one by one
            $sql_ye_currency_list = 'SELECT * FROM ye_revision WHERE currency != "SGD" GROUP BY currency ORDER BY currency ASC';
            $query_ye_currency_list = $this->db->query($sql_ye_currency_list);
            $ye_currency_list = $query_ye_currency_list->result();

            // Loop each and every currency from YE_REVISION TBL
            $current_currency = '';
            $current_rate = '';
            foreach ($ye_currency_list as $key => $value) {
                $current_currency = $value->currency;
                $current_rate = $value->rate;
                $current_cutoff_date = $value->cutoff_date;

                // get all the UNSETTLED TRANSACTIONS FROM FOREIGN_BANK.TBL
                $sql_unsettled_transactions = 'SELECT * FROM foreign_bank WHERE doc_date <= "'.$current_cutoff_date.'" AND currency = "'.$current_currency.'" AND settled = "n" ORDER BY fbl_id ASC';
                $query_unsettled_transactions = $this->db->query($sql_unsettled_transactions);
                $unsettled_transactions = $query_unsettled_transactions->result();

                $fbl_id = 0;
                foreach ($unsettled_transactions as $key => $value) {
                    $fbl_id = $value->fbl_id;
                    $individual_foreign_amount = $value->fa_amt;

                    $new_individual_local_amount = round($individual_foreign_amount / $current_rate, 2);

                    $sgd_update_by_ye_rate[] = $this->custom->updateRow('foreign_bank', ['local_amt' => $new_individual_local_amount], ['fbl_id' => $fbl_id]);
                }
            }
        }

        return $sgd_update_by_ye_rate;
    }

    public function update_ye_revision()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        // gets cutoff date from YE_REVISION.TBL
        $ye_cutoff_date = $this->custom->getSingleValue('ye_revision', 'cutoff_date', ['currency !=' => 'SGD']);

        // Checks ATLEASE ONE UNSETTLED RECORDS IN AR.TBL
        $ar_unsettled = $this->custom->getCount('accounts_receivable', ['currency !=' => 'SGD', 'doc_date <=' => $ye_cutoff_date, 'settled' => 'n']);
        if ($ar_unsettled > 0) {
            $ar_update = $this->apply_YE_revision_AR($ye_cutoff_date);

            $this->post_AR_exchange_difference_to_GL();
        }

        // Checks ATLEASE ONE UNSETTLED RECORDS IN AP.TBL
        $ap_unsettled = $this->custom->getCount('accounts_payable', ['currency !=' => 'SGD', 'doc_date <=' => $ye_cutoff_date, 'settled' => 'n']);
        if ($ap_unsettled > 0) {
            $ap_update = $this->apply_YE_revision_AP($ye_cutoff_date);

            $this->post_AP_exchange_difference_to_GL();
        }

        // Checks ATLEASE ONE UNSETTLED RECORDS IN FB.TBL
        $fb_unsettled = $this->custom->getCount('foreign_bank', ['currency !=' => 'SGD', 'doc_date <=' => $ye_cutoff_date, 'settled' => 'n']);
        if ($fb_unsettled > 0) {
            $fb_update = $this->apply_YE_revision_FB($ye_cutoff_date);

            $this->post_FB_exchange_difference_to_GL();
        }

        if ($this->db->trans_status() === false || in_array('error', $ar_update) || in_array('error', $ap_update) || in_array('error', $fb_update)) {
            set_flash_message('message', 'danger', 'YEAR END REVISION ERROR');
            $this->db->trans_rollback();
        } else {
            set_flash_message('message', 'success', 'YEAR END EXCHANGE RATE UPDATED. EXCHANGE DIFFERENCE POSTED TO GL.');
            $this->db->trans_commit();
        }

        redirect('/accounts_receivable/debtor_by_all_currency');
    }

    public function post_AR_exchange_difference_to_GL()
    {
        $grand_total_before_revision = $this->custom->getSingleValue('ye_values_before_revision', 'sgd_grand_total_before_revision', ['sub_ledger' => 'accounts_receivable']);
        $grand_total_after_revision = $this->get_grand_total_at_different_stages('accounts_receivable');

        $exchange_difference_amount = $this->get_exchange_difference_amount($grand_total_before_revision, $grand_total_after_revision);

        // POST TO EXCHANGE DIFFERENCE TO GL
        // common parameters
        $ar_gl_data['doc_date'] = date('Y-m-d');
        $ar_gl_data['ref_no'] = 'YE_REV';
        $ar_gl_data['remarks'] = 'Year End Exchange Rate Revision';
        $ar_gl_data['gstcat'] = '';
        $ar_gl_data['tran_type'] = 'YE_REV';
        $ar_gl_data['sman'] = '';

        if ($grand_total_after_revision > $grand_total_before_revision) {
            // debit
            $ar_gl_data['accn'] = 'CA001';
            $ar_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $ar_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ar_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ar_gl_data);

            // credit
            $ar_gl_data['accn'] = 'E0900';
            $ar_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $ar_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ar_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ar_gl_data);
        } else {
            // credit
            $ar_gl_data['accn'] = 'CA001';
            $ar_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $ar_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ar_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ar_gl_data);

            // debit
            $ar_gl_data['accn'] = 'E0900';
            $ar_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $ar_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ar_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ar_gl_data);
        }
    }

    public function post_AP_exchange_difference_to_GL()
    {
        $grand_total_before_revision = $this->custom->getSingleValue('ye_values_before_revision', 'sgd_grand_total_before_revision', ['sub_ledger' => 'accounts_payable']);
        $grand_total_after_revision = $this->get_grand_total_at_different_stages('accounts_payable');

        $exchange_difference_amount = $this->get_exchange_difference_amount($grand_total_before_revision, $grand_total_after_revision);

        // POST TO EXCHANGE DIFFERENCE TO GL
        // common parameters
        $ap_gl_data['doc_date'] = date('Y-m-d');
        $ap_gl_data['ref_no'] = 'YE_REV';
        $ap_gl_data['remarks'] = 'Year End Exchange Rate Revision';
        $ap_gl_data['gstcat'] = '';
        $ap_gl_data['tran_type'] = 'YE_REV';
        $ap_gl_data['sman'] = '';

        if ($grand_total_after_revision > $grand_total_before_revision) {
            // debit
            $ap_gl_data['accn'] = 'CL001';
            $ap_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $ap_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ap_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ap_gl_data);

            // credit
            $ap_gl_data['accn'] = 'E0900';
            $ap_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $ap_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ap_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ap_gl_data);
        } else {
            // credit
            $ap_gl_data['accn'] = 'CL001';
            $ap_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $ap_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ap_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ap_gl_data);

            // debit
            $ap_gl_data['accn'] = 'E0900';
            $ap_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $ap_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $ap_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $ap_gl_data);
        }
    }

    public function post_FB_exchange_difference_to_GL()
    {
        $grand_total_before_revision = $this->custom->getSingleValue('ye_values_before_revision', 'sgd_grand_total_before_revision', ['sub_ledger' => 'foreign_bank']);
        $grand_total_after_revision = $this->get_grand_total_at_different_stages('foreign_bank');

        $exchange_difference_amount = $this->get_exchange_difference_amount($grand_total_before_revision, $grand_total_after_revision);

        // POST TO EXCHANGE DIFFERENCE TO GL
        // common parameters
        $fb_gl_data['doc_date'] = date('Y-m-d');
        $fb_gl_data['ref_no'] = 'YE_REV';
        $fb_gl_data['remarks'] = 'Year End Exchange Rate Revision';
        $fb_gl_data['gstcat'] = '';
        $fb_gl_data['tran_type'] = 'YE_REV';
        $fb_gl_data['sman'] = '';

        if ($grand_total_after_revision > $grand_total_before_revision) {
            // debit
            $fb_gl_data['accn'] = 'CA110';
            $fb_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $fb_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $fb_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $fb_gl_data);

            // credit
            $fb_gl_data['accn'] = 'E0900';
            $fb_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $fb_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $fb_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $fb_gl_data);
        } else {
            // credit
            $fb_gl_data['accn'] = 'CA110';
            $fb_gl_data['sign'] = '-';
            if ($exchange_difference_amount < 0) {
                $fb_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $fb_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $fb_gl_data);

            // debit
            $fb_gl_data['accn'] = 'E0900';
            $fb_gl_data['sign'] = '+';
            if ($exchange_difference_amount < 0) {
                $fb_gl_data['total_amount'] = round(-1 * $exchange_difference_amount, 2);
            } else {
                $fb_gl_data['total_amount'] = round($exchange_difference_amount, 2);
            }
            $this->custom->insertRow('gl', $fb_gl_data);
        }
    }

    public function get_exchange_difference_amount($amount_before_revision, $amount_after_revision)
    {
        if ($amount_after_revision >= 0 && $amount_before_revision >= 0) {
            $exchange_diff_amt = $amount_after_revision - $amount_before_revision;
        } elseif ($amount_after_revision < 0 && $amount_before_revision < 0) {
            if ($amount_after_revision > $amount_before_revision) {
                $exchange_diff_amt = $amount_before_revision - $amount_after_revision;
            } else {
                $exchange_diff_amt = $amount_after_revision - $amount_before_revision;
            }
        } elseif ($amount_after_revision >= 0 && $amount_before_revision < 0) {
            $exchange_diff_amt = $amount_before_revision + $amount_after_revision;
        } elseif ($amount_after_revision < 0 && $amount_before_revision >= 0) {
            $exchange_diff_amt = $amount_after_revision + $amount_before_revision;
        } else {
            $exchange_diff_amt = $amount_after_revision - $amount_before_revision;
        }

        return $exchange_diff_amt;
    }

    public function currency_list()
    {
        // Bind - Currency Codes from AR
        $sql_currency_list_AR = 'SELECT currency FROM accounts_receivable group by currency';
        $query_currency_list_AR = $this->db->query($sql_currency_list_AR);
        $currency_list_AR = $query_currency_list_AR->result();
        $currency_codes_array = [];

        foreach ($currency_list_AR as $key => $value) {
            $currency_codes_array[] = $value->currency;
        }

        $sql_currency_list_AP = 'SELECT currency FROM accounts_payable group by currency';
        $query_currency_list_AP = $this->db->query($sql_currency_list_AP);
        $currency_list_AP = $query_currency_list_AP->result();

        foreach ($currency_list_AP as $key => $value) {
            $currency_codes_array[] = $value->currency;
        }

        $sql_currency_list_FB = 'SELECT currency FROM foreign_bank group by currency';
        $query_currency_list_FB = $this->db->query($sql_currency_list_FB);
        $currency_list_FB = $query_currency_list_FB->result();

        foreach ($currency_list_FB as $key => $value) {
            $currency_codes_array[] = $value->currency;
        }

        $unique_currency_codes = array_unique($currency_codes_array);
        sort($unique_currency_codes);

        return $unique_currency_codes;
    }

    public function save()
    {
        $post = $post = $this->input->post();
        if ($post) {
            $currency = $post['currency'];
            $cutoff_date = date('Y-m-d', strtotime($post['cutoff_date']));
            $post['cutoff_date'] = $cutoff_date;

            $same_currency_id = $this->custom->getSingleValue('ye_revision', 'r_id', ['currency' => $currency, 'cutoff_date' => $cutoff_date]);

            if (count($same_currency_id)) {
                $post['cutoff_date'] = $cutoff_date;
                $where = ['r_id' => $same_currency_id];
                $result = $this->custom->updateRow($this->table, $post, $where);
            } else {
                $description = $this->custom->getSingleValue('ct_currency', 'description', ['code' => $currency]);
                $post['description'] = $description;

                $result = $this->custom->insertRow($this->table, $post);
                if ($result != 'error') {
                    set_flash_message('message', 'success', 'EXCHANGE RATE SAVED');
                } else {
                    set_flash_message('message', 'danger', 'ERROR');
                }
            }

            redirect('/combo_tables/ye_revision');
        } else {
            show_404();
        }
    }

    public function update()
    {
        $post = $this->input->post();
        if ($post) {
            $id = $post['id'];
            unset($post['id']);

            $cutoff_date = date('Y-m-d', strtotime($post['cutoff_date']));
            $post['cutoff_date'] = $cutoff_date;

            $where = ['r_id' => $id];
            $result = $this->custom->updateRow($this->table, $post, $where);
            if ($result) {
                set_flash_message('message', 'success', 'CURRENCY DETAILS UPDATED');
            } else {
                set_flash_message('message', 'danger', 'UDPATE ERROR');
            }
            redirect('/combo_tables/ye_revision');
        } else {
            show_404();
        }
    }

    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');
        $where = ['r_id' => $id];
        $result = $this->custom->deleteRow($this->table, $where);
        echo $result;
        if ($result) {
            set_flash_message('message', 'success', 'RECORD DELETED');
        } else {
            set_flash_message('message', 'danger', 'RECORD DELETE ERROR');
        }
        redirect('/combo_tables/ye_revision');
    }
}
