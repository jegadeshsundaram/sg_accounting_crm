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
        $this->table = 'payment_master';
        $this->logged_id = $this->session->user_id;
        $this->load->model('payment/payment_model', 'pay_model');
    }

    public function double_payment()
    {
        is_ajax();
        $post = $this->input->post();
        $ref_no = $post['text_prefix'].'.'.$post['number_suffix'];
        $payments = $this->custom->getCount('payment_master', ['payment_ref_no' => $ref_no]);
        echo $payments;
    }

    public function double_payment_inc()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            // checks posted reference is already used by another resource
            $ref = $this->custom->getCount('payment_master', ['payment_ref_no' => $post['ref_no']]);
            if ($ref > 0) { // reference is just before used by some other resource
                $setting = $this->custom->getLastInsertedRow('payment_setting', 'updated_on');
                $number_suffix = $setting->number_suffix + 1;
                $ref = $setting->text_prefix.'.'.$number_suffix;
            } else { // reference is not used so using the same reference
                $ref = $post['ref_no'];
            }
        }

        echo $ref;
    }

    public function double_reference()
    {
        is_ajax();

        $post = $this->input->post();
        $ref = $post['text_prefix'].'.'.$post['number_suffix'];
        $ref = $this->custom->getCount('payment_master', ['payment_ref_no' => $ref]);
        echo $ref;
    }

    function get_settings() {
        is_ajax();
        // get last inserted row
        $settings = $this->custom->getLastInsertedRow('payment_setting', 'updated_on');
        $data['settings'] = $settings;
        echo json_encode($data);
    }

    function save_settings() {

        is_ajax();
        $post = $this->input->post();
        $post['user_id'] = $this->session->user_id;

        // checks current text prefix from settings page is already exists or not
        $prefix = $this->custom->getCount('payment_setting', ['text_prefix' => $post['text_prefix']]);
        if ($prefix > 0) { // Exists, Update Entry
            
            $this->custom->updateRow('payment_setting', $post, ['text_prefix' => $post['text_prefix']]);
            echo 'Settings saved';

        } else { // Not Exists, Insert Entry
            
            $this->custom->insertRow('payment_setting', $post);
            echo 'Settings Updated';

        }
    }

    public function get_payment_voucher_number()
    {
        is_ajax();
        $post = $this->input->post();
        $number = $post['number'] + 1;
        $toCheck = $post['text'].'.'.$number;
        $nextPayment = $this->custom->getSingleRow('payment_master', ['payment_ref_no' => $toCheck]);
        if (count($nextPayment)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function get_payment_details() // get_supplier_details()
    {
        is_ajax();
        $post = $this->input->post();

        $supplier = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $post['supplier_id']]);
        $data['supplier_name'] = $supplier->name;
        $data['supplier_code'] = $supplier->code;
        $data['supplier_address'] = $this->custom->populateSupplierAddress($supplier);

        $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $supplier->currency_id]);
        $data['supplier_currency'] = $currency_data->code;
        $data['currency_amount'] = $currency_data->rate;

        // Credit Entries
        $credits_html = '';
        $credits_count = 0;
        // $credits = $this->pay_model->get_credits($supplier->code);
        // $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount FROM accounts_payable WHERE supplier_code = "'.$supplier.'" AND sign = "+" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $this->db->select('*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount');
        $this->db->from('accounts_payable');
        $this->db->where('supplier_code = "'.$supplier->code.'" AND sign = "-" AND settled = "n"');
        $this->db->group_by('REPLACE(doc_ref_no, "_sp_1", "")');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $credits = $query->result();
        foreach ($credits as $value) {
            // check credit reference is partially used in Payment which is CONFIRMED but not POSTED YET
            $this->db->select('pay_pur_amount');
            $this->db->from('payment_purchase_master, payment_master');
            $this->db->where('payment_purchase_master.payment_id = payment_master.payment_id AND payment_purchase_master.purchase_id = '.$value->ap_id.' AND payment_master.payment_status = "C"');
            $this->db->order_by('pay_pur_id DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            $pay_data = $query->row();
            $partial_credit_amount = $pay_data->pay_pur_amount;

            $display_cr = true;
            if ($partial_credit_amount > 0) {
                if ($value->total_foreign_amount == $partial_credit_amount) { // CR Amount FULLY Utilized
                    $display_cr = false;
                } else { // CR Amount PARTIALLY Utilized
                    $entry_amount = $partial_credit_amount;
                }
            } else {
                $entry_amount = $value->total_foreign_amount;
            }

            // 1. Use if the CREDIT Entry is YET to use for any Payment
            // 2. Use if the CREDIT Entry is PARTIALLY USED for any CONFIRMED PAYMENT (Un-Utilized Amount should be used)
            if ($display_cr) {
                $document_date = implode('-', array_reverse(explode('-', $value->doc_date)));
                $credits_html .= '<tr class="entry_'.$value->ap_id.'">';
                $credits_html .= '<td>'.$document_date.'</td>';
                $credits_html .= '<td class="entry_reference">'.$value->original_doc_ref.'</td>';
                $credits_html .= '<td class="entry_amount">'.number_format((float) $entry_amount, 2, '.', '').'</td>';

                $credits_html .= '<td style="text-align: center;">';
                $credits_html .= '<div style="position: relative">
                                        <span class="entry_id" style="display: none">'.$value->ap_id.'</span>
                                        <span style="display: none;" class="entry_type">CR</span>
                                        <label class="check-container">
                                            <input class="entry_check" type="checkbox" name="contra_'.$value->ap_id.'" id="contra_'.$value->ap_id.'" />
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>';
                $credits_html .= '</td>';

                $credits_html .= '</tr>';
            }

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
        $debits_html = '';
        $debits_count = 0;

        // $sql = 'SELECT *, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount FROM accounts_payable WHERE supplier_code = "'.$result->supplier_code.'" AND sign = "+" AND settled = "n" GROUP BY REPLACE(doc_ref_no, "_sp_1", "") ORDER BY doc_date ASC';
        $this->db->select('*, REPLACE(doc_ref_no, "_sp_1", "") AS original_doc_ref, sum(total_amt) AS total_local_amount, sum(fa_amt) AS total_foreign_amount');
        $this->db->from('accounts_payable');
        $this->db->where('supplier_code = "'.$supplier->code.'" AND sign = "+" AND settled = "n"');
        $this->db->group_by('REPLACE(doc_ref_no, "_sp_1", "")');
        $this->db->order_by('doc_date', 'ASC');
        $query = $this->db->get();
        $debits = $query->result();
        foreach ($debits as $value) {
            // check invoice reference is partially used in Payment which is CONFIRMED but not POSTED YET
            $this->db->select('pay_pur_amount');
            $this->db->from('payment_purchase_master, payment_master');
            $this->db->where('payment_purchase_master.payment_id = payment_master.payment_id AND payment_purchase_master.purchase_id = '.$value->ap_id.' AND payment_master.payment_status = "C"');
            $this->db->order_by('pay_pur_id DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            $pay_data = $query->row();
            $partial_debit_amount = $pay_data->pay_pur_amount;

            if ($partial_debit_amount > 0) {
                $entry_amount = $partial_debit_amount;
            } else {
                $entry_amount = $value->total_foreign_amount;
            }

            $document_date = implode('-', array_reverse(explode('-', $value->doc_date)));
            $debits_html .= '<tr class="entry_'.$value->ap_id.'">';
            $debits_html .= '<td>'.$document_date.'</td>';
            $debits_html .= '<td class="entry_reference">'.$value->original_doc_ref.'</td>';
            $debits_html .= '<td class="entry_amount">'.number_format((float) $entry_amount, 2, '.', '').'</td>';

            $debits_html .= '<td style="text-align: center;">';
            $debits_html .= '<div style="position: relative">
                                <span class="entry_id" style="display: none">'.$value->ap_id.'</span>
                                <span class="entry_type" style="display: none">DR</span>
							    <label class="check-container">
								    <input class="entry_check" type="checkbox" name="contra_'.$value->ap_id.'" id="contra_'.$value->ap_id.'" />
								    <span class="checkmark"></span>
							    </label>
                            </div>';
            $debits_html .= '</td>';
            $debits_html .= '</tr>';

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

    public function payment_new_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $number = $post['number'];
        $toCheck = $post['text'].'.'.$number;
        $nextPayment = $this->custom->getSingleRow('payment_master', ['payment_ref_no' => $toCheck]);
        if (count($nextPayment)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    // delete payment in listing page
    public function delete()
    {
        is_ajax();
        $id = $this->input->post('rowID');

        $where = ['payment_id' => $id];
        $status = $this->custom->updateRow($this->table, ['payment_status' => 'DELETED'], $where);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo 'errors';
        } else {
            $this->db->trans_commit();
            echo 'success';
        }
    }

    // post payments to accounts
    public function post()
    {
        is_ajax();
        $payment_id = $this->input->post('rowID');

        // Updating payment status and get payment details for the payment selected
        $where = ['payment_id' => $payment_id];
        $result = $this->custom->updateRow($this->table, ['payment_status' => 'P'], $where);

        // get payment details
        $payment_data = $this->custom->getSingleRow($this->table, $where);
        $payment_amount = $payment_data->amount;
        $payment_reference = $payment_data->payment_ref_no;
        $payment_date = $payment_data->created_on;
        $payment_currency = $payment_data->currency;
        $payment_remarks = $payment_data->other_reference;
        $supplier_id = $payment_data->supplier_id;

        // get supplier details
        $supplier_data = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $supplier_id]);
        $supplier_code = $supplier_data->code;

        // get currency details
        $payment_currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['currency_id' => $supplier_data->currency_id]);

        $credit_entries_total_amount = 0;
        $credit_entries_count = 0;
        $debit_credit_entries_count = 0;
        $credit_entry_id_array = [];

        $sql = 'SELECT pay_pur_id, purchase_id, pay_pur_amount, full_amount FROM payment_purchase_master WHERE payment_id = "'.$payment_id.'" ORDER BY pay_pur_id ASC';
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $key => $value) {
            $entry_type = $this->custom->getSingleValue('accounts_payable', 'sign', ['ap_id' => $value['purchase_id']]);

            if ($entry_type == '-') {
                $credit_entries_total_amount += $value['full_amount'];
                ++$credit_entries_count;
                $credit_entry_id_array[] = $value['purchase_id'];
            }

            ++$debit_credit_entries_count;
        }

        // the above logic : If Payment Amout is Larger than sum of credit entries then there should be no debit same_entries
        // because in payment creation, Debit entries can be selected only if payment amount is completely settled by credit same_entries
        // if payment amount is not settled means the credit entries are not enough to settle payment amount so the balance in the payment amount
        // since the the payment is not settled fully, do debit entries will display to select

        // Contra Process will have PAYMENT and ATLEAST ONE CREDIT ENTRY
        if ($credit_entries_count == 0) { // NO CONTRA
            // Just Insert Payment Entry into AP.TBL
            $this->insert_transaction_entry($payment_reference, $supplier_code, $payment_date, $payment_currency, $payment_amount, $payment_currency_rate, $payment_remarks, 'U', '+'); // Un-Settled
        } elseif ($credit_entries_count > 0 && $payment_amount > $credit_entries_total_amount) { // SPLIT PAYMENT
            // if payment amount is larger than sum of the all the credit entries selected
            // Split Payment and Insert into AP.TBL
            // And insert all Credit Entries into AP.TBL as SETTED
            $settled_payment_amount = $credit_entries_total_amount;
            $unsettled_payment_amount = $payment_amount - $credit_entries_total_amount;

            $this->insert_transaction_entry($payment_reference.'_sp_1', $supplier_code, $payment_date, $payment_currency, $settled_payment_amount, $payment_currency_rate, $payment_remarks, 'S', '+'); // Settled
            $this->insert_transaction_entry($payment_reference, $supplier_code, $payment_date, $payment_currency, $unsettled_payment_amount, $payment_currency_rate, $payment_remarks, 'U', '+'); // Unsettled

            // update all the credit entries as settled
            foreach ($credit_entry_id_array as $key => $value) {
                $ap_id = $value;
                $this->update_settlement_flag($ap_id); // Settled ALL Credit Records
            }
        } else {
            // there is contra process happened and credit entries are settled the payment amount
            $this->insert_transaction_entry($payment_reference, $supplier_code, $payment_date, $payment_currency, $payment_amount, $payment_currency_rate, $payment_remarks, 'S', '+'); // Settled

            // No Balance in Payment
            if ($debit_credit_entries_count > 0) {
                // payment entry loop (payment_purchase_master)
                foreach ($query->result_array() as $key => $value) {
                    $payment_entry_full_amount = $value['full_amount']; // full amount
                    $payment_entry_balance_amount = $value['pay_pur_amount']; // balance amount
                    $ap_id = $value['purchase_id'];

                    if ($payment_entry_balance_amount == $payment_entry_full_amount) {
                        $this->update_settlement_flag($ap_id); // SET THIS RECORD IN AP.TBL AS SETTLED
                    } elseif ($payment_entry_balance_amount < $payment_entry_full_amount) {
                        $ap_entry_data = $this->custom->getSingleRow('accounts_payable', ['ap_id' => $ap_id]);
                        $ap_original_amount = $ap_entry_data->fa_amt;

                        // settled amount
                        $payment_entry_settled_amount = $payment_entry_full_amount - $payment_entry_balance_amount;
                        // un_settled_amount
                        $payment_entry_UN_settled_amount = $ap_original_amount - $payment_entry_settled_amount;

                        // Update - unsettled entry with only balance amount (same reference)
                        $payment_entry_local_amount = round($payment_entry_UN_settled_amount / $payment_currency_rate, 2);
                        $payment_foreign_amount = $payment_entry_UN_settled_amount;
                        $updated = $this->custom->updateRow('accounts_payable', ['total_amt' => $payment_entry_local_amount, 'fa_amt' => $payment_foreign_amount], ['ap_id' => $ap_id]);

                        // insert - entry with settled amount (same reference with _sp1)
                        $payment_entry_reference = $ap_entry_data->doc_ref_no.'_sp_1';
                        $payment_entry_date = $ap_entry_data->doc_date;
                        $payment_entry_remarks = $ap_entry_data->remarks;
                        $sign = $ap_entry_data->sign;

                        $this->insert_transaction_entry($payment_entry_reference, $supplier_code, $payment_entry_date, $payment_currency, $payment_entry_settled_amount, $payment_currency_rate, $payment_entry_remarks, 'S', $sign); // Settled
                    }
                } // loop end
            } // $debit_credit_entries end
        } // payment to AP - spliting process - end

        /* gl Table values starts */
        $gl_data['doc_date'] = $payment_date;
        $gl_data['ref_no'] = $payment_reference;
        $gl_data['remarks'] = 'Payment. Reference: '.$payment_reference;
        $gl_data['accn'] = 'CL001';
        $gl_data['sign'] = '+';
        $gl_data['gstcat'] = 'SR';
        $gl_data['tran_type'] = 'PAYMENT';
        $gl_data['total_amount'] = round($payment_amount / $payment_currency_rate, 2);
        $gl_data['sman'] = '';
        $gl_data['iden'] = $supplier_code;

        $this->custom->insertRow('gl', $gl_data);

        // Multiple bank account feature enabled - Now Payment will debit with the bank account selected by the user during Payment creation
        $gl_data['doc_date'] = $payment_date;
        $gl_data['ref_no'] = $payment_reference;
        $gl_data['remarks'] = 'Payment. Reference: '.$payment_reference;
        $gl_data['accn'] = $payment_data->bank_accn;
        $gl_data['sign'] = '-';
        $gl_data['gstcat'] = 'SR';
        $gl_data['tran_type'] = 'PAYMENT';
        $gl_data['total_amount'] = round($payment_amount / $payment_currency_rate, 2);
        $gl_data['sman'] = '';
        $gl_data['iden'] = $supplier_code;

        $this->custom->insertRow('gl', $gl_data);

        // insert into foreign bank ledger if foreign bank (CA110) selected
        if ($payment_data->bank_accn == 'CA110' && $payment_data->fb_accn != '0') {
            $fb_ledger_data['doc_ref_no'] = $payment_reference;

            $fb_currency_id = $this->custom->getSingleValue('master_foreign_bank', 'currency_id', ['fb_code' => $payment_data->fb_accn]);
            $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $fb_currency_id]);
            $fb_currency = $currency_data->code;
            $fb_currency_rate = $currency_data->rate;

            $fb_ledger_data['fb_code '] = $payment_data->fb_accn;
            $fb_ledger_data['doc_date'] = $payment_date;
            $fb_ledger_data['currency'] = $fb_currency;

            if ($fb_currency == 'SGD') {
                $fb_foreign_amount = $payment_amount;
                $fb_local_amount = round($payment_amount / $payment_currency_rate, 2);
            } else {
                $fb_foreign_amount = $payment_amount;
                $fb_local_amount = round($payment_amount / $fb_currency_rate, 2);
            }

            $fb_ledger_data['local_amt'] = $fb_local_amount;
            $fb_ledger_data['fa_amt'] = $fb_foreign_amount;
            $fb_ledger_data['sign'] = '-';
            $fb_ledger_data['remarks'] = 'Payment. Reference: '.$payment_reference;
            $fb_ledger_data['tran_type'] = 'PAYMENT';

            $fb_insert_true = $this->custom->insertRow('foreign_bank', $fb_ledger_data);
        }
    }

    public function update_settlement_flag($id)
    {
        $updated = $this->custom->updateRow('accounts_payable', ['settled' => 'y'], ['ap_id' => $id]);

        return $updated;
    }

    public function insert_transaction_entry($payment_reference, $supplier_code, $payment_date, $currency, $payment_amount, $currency_rate, $remarks, $settled, $sign)
    {
        $transaction_data['doc_ref_no'] = $payment_reference;
        $transaction_data['supplier_code '] = $supplier_code;
        $transaction_data['doc_date'] = $payment_date;
        $transaction_data['currency'] = $currency;

        $payment_local_amount = round($payment_amount / $currency_rate, 2);
        $payment_foreign_amount = $payment_amount;

        $transaction_data['total_amt'] = $payment_local_amount;
        $transaction_data['fa_amt'] = $payment_foreign_amount;
        $transaction_data['sign'] = $sign;
        $transaction_data['tran_type'] = 'PAYMENT';
        $transaction_data['remarks'] = $remarks;

        if ($settled == 'S') {
            $transaction_data['settled'] = 'y';
        }

        $inserted = $this->custom->insertRow('accounts_payable', $transaction_data);
        unset($transaction_data);

        return $inserted;
    }
}
