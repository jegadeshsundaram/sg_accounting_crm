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
        $this->table = 'receipt_master';
        $this->logged_id = $this->session->user_id;
        $this->load->model('receipt/receipt_model', 'rec_model');
    }

    public function double_receipt()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $receipts = $this->custom->getCount('receipt_master', ['receipt_ref_no' => $ref_no]);
        echo $receipts;
    }

    public function double_receipt_inc()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            // checks posted reference is already used by another resource
            $ref = $this->custom->getCount('receipt_master', ['receipt_ref_no' => $post['ref_no']]);
            if ($ref > 0) { // reference is just before used by some other resource
                $setting = $this->custom->getLastInsertedRow('receipt_setting', 'updated_on');
                $number_suffix = $setting->number_suffix + 1;
                $ref = $setting->text_prefix.'.'.$receipt_total;
            } else { // receipt reference is not used so using the same receipt reference
                $ref = $post['ref_no'];
            }
        }

        echo $ref;
    }

    function get_settings() {
        is_ajax();
        // get last inserted row
        $settings = $this->custom->getLastInsertedRow('receipt_setting', 'updated_on');
        $data['settings'] = $settings;
        echo json_encode($data);
    }

    function save_settings() {

        is_ajax();
        $post = $this->input->post();
        $post['user_id'] = $this->session->user_id;

        // checks current text prefix from settings page is already exists or not
        $prefix = $this->custom->getCount('receipt_setting', ['text_prefix' => $post['text_prefix']]);
        if ($prefix > 0) { // Exists, Update Entry
            
            $this->custom->updateRow('receipt_setting', $post, ['text_prefix' => $post['text_prefix']]);
            echo 'Settings saved';

        } else { // Not Exists, Insert Entry
            
            $this->custom->insertRow('receipt_setting', $post);
            echo 'Settings Updated';

        }
    }

    public function get_receipt_details() // get_customer_details()
    {
        is_ajax();
        $post = $this->input->post();

        $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $post['customer_id']]);
        $data['customer_name'] = $customer->name;
        $data['customer_code'] = $customer->code;
        $data['customer_address'] = $this->custom->populateCustomerAddress($customer);

        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $customer->currency_id]);
        $data['customer_currency'] = $currency_data->code;
        $data['currency_amount'] = $currency_data->rate;

        // Credit Entries
        $credits = $this->rec_model->get_customer_credits($customer->code);
        $credits_html = '';
        $credits_count = 0;
        foreach ($credits as $key => $value) {
            // check credit reference is partially used in receipt which is CONFIRMED but not POSTED YET
            $partial_credit_amount = 0;
            $i = 0;
            $partial_used_data = $this->rec_model->rec_inv_amount($value->ar_id);
            foreach ($partial_used_data as $key => $value) {
                ++$i;
                $partial_credit_amount += $value->rec_inv_amount;
            }

            if ($i > 0) {
                $entry_amount = $partial_credit_amount;
            } else {
                $entry_amount = $value->total_foreign_amount;
            }

            $document_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $credits_html .= '<tr class="entry_'.$value->ar_id.'">';
            $credits_html .= '<td>'.$document_date.'</td>';
            $credits_html .= '<td class="entry_reference">'.$value->original_doc_ref.'</td>';
            $credits_html .= '<td class="entry_amount">'.number_format((float) $entry_amount, 2, '.', '').'</td>';

            $credits_html .= '<td style="text-align: center;">';
            $credits_html .= '<div style="position: relative">
                                <span class="entry_id" style="display: none">'.$value->ar_id.'</span>
                                <span style="display: none;" class="entry_type">CR</span>
                                <label class="check-container">
                                    <input class="entry_check" type="checkbox" name="contra_'.$value->ar_id.'" id="contra_'.$value->ar_id.'" />
                                    <span class="checkmark"></span>
                                </label>
                            </div>';
            $credits_html .= '</td>';

            $credits_html .= '</tr>';
            ++$i;
            ++$credits_count;
        }

        if ($credits_count == 0) {
            $credits_html .= '<tr>';
            $credits_html .= '<td colspan="4">';
            $credits_html .= '<span>No References found</span>';
            $credits_html .= '</td>';
            $credits_html .= '<tr>';
        }

        $data['credit_entries'] = $credits_html;

        // extract only DEBIT REFERENCES
        $debits = $this->rec_model->get_customer_debits($customer->code);
        $debits_html = '';
        $debits_count = 0;
        foreach ($debits as $key => $value) {
            // check credit reference is partially used in receipt which is CONFIRMED but not POSTED YET
            $partial_debit_amount = 0;
            $i = 0;
            $partial_used_data = $this->rec_model->rec_inv_amount($value->ar_id);
            foreach ($partial_used_data as $key => $value) {
                ++$i;
                $partial_debit_amount += $value->rec_inv_amount;
            }

            if ($i > 0) {
                $entry_amount = $partial_debit_amount;
            } else {
                $entry_amount = $value->total_foreign_amount;
            }

            $document_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $debits_html .= '<tr class="entry_'.$value->ar_id.'">';
            $debits_html .= '<td>'.$document_date.'</td>';
            $debits_html .= '<td class="entry_reference">'.$value->original_doc_ref.'</td>';
            $debits_html .= '<td class="entry_amount">'.number_format((float) $entry_amount, 2, '.', '').'</td>';

            $debits_html .= '<td style="text-align: center;">';
            $debits_html .= '<div style="position: relative">
                                <span class="entry_id" style="display: none">'.$value->ar_id.'</span>
                                <span class="entry_type" style="display: none">DR</span>
							    <label class="check-container">
								    <input class="entry_check" type="checkbox" name="contra_'.$value->ar_id.'" id="contra_'.$value->ar_id.'" />
								    <span class="checkmark"></span>
							    </label>
                            </div>';
            $debits_html .= '</td>';

            $debits_html .= '</tr>';
            ++$i;
            ++$debits_count;
        }

        if ($debits_count == 0) {
            $debits_html .= '<tr>';
            $debits_html .= '<td colspan="4">';
            $debits_html .= '<span>No References found</span>';
            $debits_html .= '</td>';
            $debits_html .= '<tr>';
        }

        $data['debit_entries'] = $debits_html;

        echo json_encode($data);
        exit;
    }

    public function double_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $post['text_prefix'].'.'.$post['number_suffix'];
        $ref = $this->custom->getCount('receipt_master', ['receipt_ref_no' => $ref]);
        echo $ref;
    }

    // post receipts to accounts
    public function post()
    {
        is_ajax();
        $receipt_id = $this->input->post('rowID');

        // Updating receipt status and get receipt details for the receipt selected
        $where = ['receipt_id' => $receipt_id];
        $result = $this->custom->updateRow($this->table, ['receipt_status' => 'POSTED'], $where);

        // get receipt details
        $receipt_data = $this->custom->getSingleRow($this->table, $where);
        $receipt_amount = $receipt_data->amount;
        $receipt_reference = $receipt_data->receipt_ref_no;
        $receipt_date = $receipt_data->modified_on;
        $receipt_currency = $receipt_data->currency;
        $receipt_remarks = $receipt_data->other_reference;
        $customer_id = $receipt_data->customer_id;

        // get customer details
        $customer = $this->custom->getSingleRow('master_customer', ['customer_id' => $customer_id]);
        $customer_code = $customer->code;

        // get currency details
        $receipt_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $customer->currency_id]);

        $sql = 'SELECT r_i_id, invoice_id, rec_inv_amount, full_amount FROM receipt_invoice_master WHERE receipt_id = "'.$receipt_id.'" ORDER BY r_i_id ASC';
        $query = $this->db->query($sql);
        $debit_entries_total_amount = 0;
        $debit_entries_count = 0;
        $debit_credit_entries_count = 0;
        $debit_entry_id_array = [];
        foreach ($query->result_array() as $key => $value) {
            $entry_type = $this->custom->getSingleValue('accounts_receivable', 'sign', ['ar_id' => $value['invoice_id']]);

            // receipt is credit entry
            // so summing all the debit entries amount to check whether the RECEIPT is completely SETTLED or NOT
            if ($entry_type == '+') {
                $debit_entries_total_amount += $value['full_amount'];
                ++$debit_entries_count;
                $debit_entry_id_array[] = $value['invoice_id'];
            }

            ++$debit_credit_entries_count;
        }

        // the above logic : If receipt Amout is Larger than sum of debit entries then there should be no credit same_entries
        // because in receipt creation, Credit entries can be selected only if receipt amount is completed settled by Debit same_entries
        // if receipt amount is not settled means the debit entries are not qnough to settle receipt amount so the balance in the receipt amount
        // since the the receipt is not settled fully, no credit entries will display to select

        // Contra Process will have RECEIPT and ATLEAST ONE DEBIT ENTRY
        if ($debit_entries_count == 0) { // NO CONTRA
            // Just Insert Receipt Entry into AR.TBL
            $this->insert_transaction_entry($receipt_reference, $customer_code, $receipt_date, $receipt_currency, $receipt_amount, $receipt_currency_rate, $receipt_remarks, 'U', '-'); // Un-Settled

            // if receipt amount is larger than sum of the all the debit entries selected, then SPLIT receipt
        } elseif ($debit_entries_count > 0 && $receipt_amount > $debit_entries_total_amount) {
            $settled_receipt_amount = $debit_entries_total_amount;
            $unsettled_receipt_amount = $receipt_amount - $debit_entries_total_amount;
            $this->insert_transaction_entry($receipt_reference.'_sp_1', $customer_code, $receipt_date, $receipt_currency, $settled_receipt_amount, $receipt_currency_rate, $receipt_remarks, 'S', '-'); // Settled
            $this->insert_transaction_entry($receipt_reference, $customer_code, $receipt_date, $receipt_currency, $unsettled_receipt_amount, $receipt_currency_rate, $receipt_remarks, 'U', '-'); // Unsettled

            // update all the debit entries as settled
            foreach ($debit_entry_id_array as $key => $value) {
                $ar_id = $value;
                $this->update_settlement_flag($ar_id); // Settled ALL Debit Records
            }
        } else {
            // there is contra process happened and credit entries are settled the receipt amount
            $this->insert_transaction_entry($receipt_reference, $customer_code, $receipt_date, $receipt_currency, $receipt_amount, $receipt_currency_rate, $receipt_remarks, 'S', '-'); // Settled

            // No Balance in receipt
            if ($debit_credit_entries_count > 0) {
                // receipt entry loop (receipt_purchase_master)
                foreach ($query->result_array() as $key => $value) {
                    $receipt_entry_full_amount = $value['full_amount']; // full amount
                    $receipt_entry_balance_amount = $value['rec_inv_amount']; // balance amount
                    $ar_id = $value['invoice_id'];

                    if ($receipt_entry_balance_amount == $receipt_entry_full_amount) {
                        $this->update_settlement_flag($ar_id); // All Settled Records
                    } elseif ($receipt_entry_balance_amount < $receipt_entry_full_amount) {
                        $ar_entry_data = $this->custom->getSingleRow('accounts_receivable', ['ar_id' => $ar_id]);
                        $ar_original_amount = $ar_entry_data->f_amt;

                        // settled amount
                        $receipt_entry_settled_amount = $receipt_entry_full_amount - $receipt_entry_balance_amount;
                        // un_settled_amount
                        $receipt_entry_UN_settled_amount = $ar_original_amount - $receipt_entry_settled_amount;

                        // Update - unsettled entry with only balance amount (same reference)
                        $receipt_entry_local_amount = round($receipt_entry_UN_settled_amount / $receipt_currency_rate, 2);
                        $receipt_foreign_amount = $receipt_entry_UN_settled_amount;
                        $updated = $this->custom->updateRow('accounts_receivable', ['total_amt' => $receipt_entry_local_amount, 'f_amt' => $receipt_foreign_amount], ['ar_id' => $ar_id]);

                        // insert - entry with settled amount (same reference with _sp1)
                        $receipt_entry_reference = $ar_entry_data->doc_ref_no.'_sp_1';
                        $receipt_entry_date = $ar_entry_data->doc_date;
                        $receipt_entry_remarks = $ar_entry_data->remarks;
                        $sign = $ar_entry_data->sign;

                        $this->insert_transaction_entry($receipt_entry_reference, $customer_code, $receipt_entry_date, $receipt_currency, $receipt_entry_settled_amount, $receipt_currency_rate, $receipt_entry_remarks, 'S', $sign); // Settled
                    }
                } // loop end
            } // $debit_credit_entries_count end
        } // receipt to AP - spliting process - end

        // Receipt POST - Posting to other tables (gl, gst)

        /* gl tbl values starts */
        $gl_data['doc_date'] = $receipt_date;
        $gl_data['ref_no'] = $receipt_reference;
        $gl_data['remarks'] = 'Receipt. Reference: '.$receipt_reference;
        $gl_data['accn'] = 'CA001';
        $gl_data['sign'] = '-';
        $gl_data['gstcat'] = 'SR';
        $gl_data['tran_type'] = 'RECEIPT';
        $gl_data['total_amount'] = round($receipt_amount / $receipt_currency_rate, 2);
        $gl_data['sman'] = '';
        $gl_data['iden'] = $customer_code;

        $this->custom->insertRow('gl', $gl_data);

        // Multiple bank account feature enabled - Now receipt will debit with the bank account selected by the user during receipt creation
        $gl_data['doc_date'] = $receipt_date;
        $gl_data['ref_no'] = $receipt_reference;
        $gl_data['remarks'] = 'Receipt. Reference: '.$receipt_reference;
        $gl_data['accn'] = $receipt_data->bank_accn;
        $gl_data['sign'] = '+';
        $gl_data['gstcat'] = 'SR';
        $gl_data['tran_type'] = 'RECEIPT';
        $gl_data['total_amount'] = round($receipt_amount / $receipt_currency_rate, 2);
        $gl_data['sman'] = '';
        $gl_data['iden'] = $customer_code;

        $this->custom->insertRow('gl', $gl_data);

        // insert into foreign bank ledger if foreign bank (CA110) selected
        if ($receipt_data->bank_accn == 'CA110' && $receipt_data->fb_accn != '') {
            $fb_ledger_data['doc_ref_no'] = $receipt_reference;

            $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $receipt_data->fb_accn]);
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $fb_currency_id]);
            $fb_currency = $currency_data->code;
            $fb_currency_rate = $currency_data->rate;

            $fb_ledger_data['fb_code '] = $receipt_data->fb_accn;
            $fb_ledger_data['doc_date'] = $receipt_date;
            $fb_ledger_data['currency'] = $fb_currency;

            if ($fb_currency == 'SGD') {
                $fb_foreign_amount = $receipt_amount;
                $fb_local_amount = round($receipt_amount / $receipt_currency_rate, 2);
            } else {
                $fb_foreign_amount = $receipt_amount;
                $fb_local_amount = round($receipt_amount / $fb_currency_rate, 2);
            }

            $fb_ledger_data['local_amt'] = $fb_local_amount;
            $fb_ledger_data['fa_amt'] = $fb_foreign_amount;
            $fb_ledger_data['sign'] = '+';
            $fb_ledger_data['remarks'] = 'Receipt. Reference: '.$receipt_reference;
            $fb_ledger_data['tran_type'] = 'RECEIPT';

            $fb_insert_true = $this->custom->insertRow('foreign_bank', $fb_ledger_data);
        }
    }

    public function update_settlement_flag($id)
    {
        $updated = $this->custom->updateRow('accounts_receivable', ['settled' => 'y'], ['ar_id' => $id]);

        return $updated;
    }

    public function insert_transaction_entry($receipt_reference, $customer_code, $receipt_date, $currency, $receipt_amount, $currency_rate, $remarks, $settled, $sign)
    {
        $transaction_data['doc_ref_no'] = $receipt_reference;
        $transaction_data['customer_code '] = $customer_code;
        $transaction_data['doc_date'] = $receipt_date;
        $transaction_data['currency'] = $currency;

        $receipt_local_amount = round($receipt_amount / $currency_rate, 2);
        $receipt_foreign_amount = $receipt_amount;

        $transaction_data['total_amt'] = $receipt_local_amount;
        $transaction_data['f_amt'] = $receipt_foreign_amount;
        $transaction_data['sign'] = $sign;
        $transaction_data['tran_type'] = 'RECEIPT';
        $transaction_data['remarks'] = $remarks;

        if ($settled == 'S') {
            $transaction_data['settled'] = 'y';
        }

        $inserted = $this->custom->insertRow('accounts_receivable', $transaction_data);
        unset($transaction_data);

        return $inserted;
    }

    // delete receipts in listing page
    public function delete()
    {
        is_ajax();
        $receipt_id = $this->input->post('rowID');

        $where = ['receipt_id' => $receipt_id];
        $status = $this->custom->updateRow($this->table, ['receipt_status' => 'DELETED'], $where);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo 'errors';
        } else {
            $this->db->trans_commit();
            echo 'success';
        }
    }
}
