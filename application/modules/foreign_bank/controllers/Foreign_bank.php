<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Foreign_bank extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('foreign_bank/Foreign_bank_model', 'fbank');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $this->body_vars['bank_options'] = $this->custom->createDropdownSelect('master_foreign_bank', ['fb_code', 'fb_name', 'fb_code', 'currency_id'], '', ['( ', ') ', ' ']);

        $table = 'foreign_bank';
        $columns = 'currency';
        $where = ['offset' => 'n'];
        $group_by = 'currency';
        $order_by = null;
        $order_by_many = null;
        $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
        $list = $query->result();
        $options = '<option value="">Select</option>';
        foreach ($list as $record) {
            $currency_description = $this->custom->getSingleValue('ct_currency', 'description', ['code' => $record->currency]);
            $options .= '<option value="'.$record->currency.'">'.$record->currency.' : '.$currency_description.'</option>';
        }

        $options .= '<option value="all">All Currencies</option>';
        $this->body_vars['currency_options'] = $options;

        $this->body_file = 'foreign_bank/options.php';
    }

    public function opening_balance()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['banks'] = $this->custom->createDropdownSelect('master_foreign_bank', ['fb_code', 'fb_name', 'fb_code', 'currency_id'], '', ['( ', ') ', ' ']);
    }

    public function print_ob()
    {
        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>FB OPENING BALANCE</h3></div>';

        $html .= '<table>';
        $html .= '<tr>
                <th>Date</th>
                <th>Reference</th>
                <th style="width: 200px">Bank</th>
                <th style="text-align: center">Currency</th>
                <th style="text-align: right">FAmt $</th>
                <th style="text-align: right">SGD $</th>
                <th style="width: 200px">Remarks</th>
                <th>Sign</th>
            </tr>';

        $i = 0;
        $table = 'fb_open';
        $columns = '*';
        $group_by = null;
        $order_by = 'document_date';
        $where = ['status' => 'C'];
        if ($_GET['rowID'] !== null) {
            $ob_id = $_GET['rowID'];
            $where = ['status' => 'C', 'fb_ob_id' => $ob_id];
        }
        $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        $list = $query->result();
        foreach ($list as $record) {
            $html .= '<tr>';
            $html .= '<td>'.date('d-m-Y', strtotime($record->document_date)).'</td>';
            $html .= '<td>'.$record->document_reference.'</td>';

            $fb_data = $this->custom->getMultiValues('master_foreign_bank', 'fb_name, currency_id', ['fb_code' => $record->fb_code]);
            $html .= '<td>('.$record->fb_code.') '.$fb_data->fb_name.'</td>';

            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);
            $html .= '<td style="text-align: center">'.$currency.'</td>';

            $html .= '<td style="text-align: right">'.number_format($record->foreign_amount, 2).'</td>';
            $html .= '<td style="text-align: right">'.number_format($record->local_amount, 2).'</td>';

            if ($record->remarks == '' || $record->remarks == null) {
                $html .= '<td style="width: 150px">Balance B/F</td>';
            } else {
                $html .= '<td style="width: 150px">'.$record->remarks.'</td>';
            }
            $html .= '<td style="text-align: center">'.$record->sign.'</td>';
            $html .= '</tr>';

            ++$i;
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="8" style="color: red; text-align: center">No Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'fb_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);

    }

    public function print_bank_statement()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $bank = $post['bank'];
            $start_date = date('Y-m-d', strtotime($post['from']));
            $end_date = date('Y-m-d', strtotime($post['to']));

            $company_where = ['code' => 'CP'];
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
            $html .= '<table style="width: 100%;"><tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr></table>';

            $html .= '<br /><table style="border: none; width: 100%;">';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="text-align: center; border: none;"><h4>BANK STATEMENT AS AT '.date('d-m-Y', strtotime($end_date)).'</h4></td>';
            $html .= '</tr>';
            $html .= '</table><br />';
            $html .= '<table style="border: none; width: 100%; border-bottom: 2px solid brown"><tr>';

            $fb_data = $this->custom->getMultiValues('master_foreign_bank', 'fb_name, currency_id', ['fb_code' => $bank]);
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);
            $html .= '<td style="text-align: left; border: none;"><strong>Bank Account:</strong> '.$bank.' - '.$fb_data->fb_name.' ('.$currency.')</td>';

            $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.strtoupper(date('M j, Y', strtotime(date('d-m-Y')))).'</td>';
            $html .= '</tr>';
            $html .= '</table><br />';

            $html .= '<br /><table>';
            $html .= '<tr>
				<th style="width: 80px">DATE</th>
				<th style="width: 120px">RFERENCE</th>
				<th style="width: 250px">REMARKS</th>
				<th style="width: 120px; text-align: right">DEBIT</th>
				<th style="width: 120px; text-align: right">CREDIT</th>
				<th style="width: 120px; text-align: right">BALANCE</th>
			</tr>';

            // Balance block forward - Start
            // $sql_opening_balance = 'SELECT * FROM foreign_bank WHERE fb_code = "'.$fb_code.'" AND doc_date < "'.$start_date.'" ORDER BY doc_date ASC';
            $bf_amount = 0;

            $this->db->select('sign, fa_amt');
            $this->db->from('foreign_bank');
            $this->db->where('fb_code = "'.$bank.'" AND doc_date < "'.$start_date.'"');
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            $bf_list = $query->result();
            foreach ($bf_list as $record) {
                if ($record->sign == '+') {
                    $bf_amount += $record->fa_amt;
                } elseif ($value->sign == '-') {
                    $bf_amount -= $record->fa_amt;
                }
            }

            $running_balance = 0;
            if ($bf_amount != 0) {
                $html .= '<tr>';
                $html .= '<td>'.date('d-m-Y', strtotime($start_date)).'</td>';
                $html .= '<td>BALANCE B/F</td>';
                $html .= '<td><i>Transactions before Financial Year</i></td>';
                if ($bf_amount >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($bf_amount, 2).'</td>';
                    $html .= '<td style="text-align: right"></td>';
                    $html .= '<td style="text-align: right">'.number_format($bf_amount, 2).'</td>';
                } else {
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format((-1) * $bf_amount, 2).'</td>';
                    $html .= '<td style="text-align: right">('.number_format((-1) * $bf_amount, 2).')</td>';
                }
                $html .= '</tr>';
                $running_balance += $bf_amount;
            }
            // Balance block forward - End

            $sql = 'SELECT * FROM foreign_bank WHERE fb_code = "'.$fb_code.'" AND doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"ORDER BY doc_date ASC';
            $this->db->select('*');
            $this->db->from('foreign_bank');
            $this->db->where('fb_code = "'.$bank.'" AND doc_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"');
            $this->db->order_by('doc_date', 'ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $value) {
                $document_date = date('d-m-Y', strtotime($value->doc_date));
                $document_reference = strtoupper($value->doc_ref_no);

                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$document_reference.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                if ($value->sign == '+') {
                    $html .= '<td style="text-align: right">'.number_format($value->fa_amt, 2).'</td>';
                    $html .= '<td></td>';
                    $running_balance += $value->fa_amt;
                } elseif ($value->sign == '-') {
                    $html .= '<td></td>';
                    $html .= '<td style="text-align: right">'.number_format($value->fa_amt, 2).'</td>';
                    $running_balance -= $value->fa_amt;
                }

                if ($running_balance >= 0) {
                    $html .= '<td style="text-align: right">'.number_format($running_balance, 2).'</td>';
                } else {
                    $html .= '<td style="text-align: right">('.number_format((-1) * $running_balance, 2).')</td>';
                }

                $html .= '</tr>';
            }

            // Closing Balance - START
            $html .= '<tr>';
            $html .= '<td colspan="5" style="text-align:right; color: red">SUB TOTAL</td>';
            if ($running_balance >= 0) {
                $html .= '<td style="text-align: right">'.number_format($running_balance, 2).'</td>';
            } else {
                $html .= '<td style="text-align: right">('.number_format((-1) * $running_balance, 2).')</td>';
            }
            $html .= '</tr>';
            // Closing Balance - END

            $html .= '</thead>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'fb_stmt_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/foreign_bank?error=post');
        }
    }

    public function print_bank_listing()
    {
        $post = $this->input->post();
        if ($post) {
            $currency = $post['currency'];
            $entries = 0;
            $html = '';

            $sgd_grand_total = 0;

            // Selected currency or all currency
            $this->db->select('currency');
            $this->db->from('foreign_bank');
            if ($currency != '' && $currency != 'all') {
                $this->db->where(['currency' => $currency]);
            }
            $this->db->group_by('currency');
            $this->db->order_by('currency', 'ASC');
            $query = $this->db->get();
            $currency_list = $query->result();
            foreach ($currency_list as $value) {
                $currency = $value->currency;

                $debit_total_by_currency = 0;
                $credit_total_by_currency = 0;
                $famt_total_by_currency = 0;
                $lamt_total_by_currency = 0;

                $html .= "<table style='min-width: 1110px; width: 100%; margin-bottom: 20px;'>";
                $html .= '<tr>';
                $html .= "<td style='padding: 7px; border: none;'><strong>Currency: </strong><span style='color: red'>".$currency.'</td>';
                $html .= '</tr>';
                $html .= '</table>';

                $html .= "<table class='table-custom' style='min-width: 1110px; width: 100%;'>";
                // List Transactions Under Each Bank

                // Bank List under each Currency
                $this->db->select('fb_code');
                $this->db->from('foreign_bank');
                $this->db->where(['offset' => 'n', 'currency' => $currency]);
                $this->db->group_by('fb_code');
                $this->db->order_by('fb_code, doc_date', 'ASC, ASC');
                $fb_list_query = $this->db->get();
                $fb_list = $fb_list_query->result();
                foreach ($fb_list as $record) {
                    $fb_data = $this->custom->getSingleRow('master_foreign_bank', ['fb_code' => $record->fb_code]);

                    $currency_data = $this->custom->getSingleRow('ct_currency', ['currency_id' => $fb_data->currency_id]);
                    $currency = $currency_data->code;
                    $currency_rate = $currency_data->rate;

                    $html .= '<tr>';
                    $html .= "<td colspan='7' style='padding: 10px;'>";
                    $html .= '<strong>Bank : </strong>';
                    $html .= $fb_data->fb_name.' ('.$fb_data->fb_code.')';
                    $html .= '</td>';
                    $html .= '</tr>';

                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th style="width: 80px;">Date</th>';
                    $html .= '<th style="width: 100px;">Reference</th>';
                    $html .= '<th style="width: 200px;">Remarks</th>';
                    $html .= '<th style="width: 130px; text-align: right">Debit</th>';
                    $html .= '<th style="width: 130px; text-align: right">Credit</th>';
                    $html .= '<th style="width: 160px; text-align: right">Balance</th>';
                    $html .= '<th style="width: 160px; text-align: right">Balance <span style="font-weight: bold">(SGD)</span></th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';

                    $famt_running_balance = 0;
                    $lamt_running_balance = 0;
                    $famt_debit = 0;
                    $famt_credit = 0;

                    // $fb_entry_sql = 'SELECT * FROM foreign_bank WHERE offset = "n" AND fb_code = "'.$value1->fb_code.'" ORDER BY doc_date ASC, doc_ref_no ASC';
                    $this->db->select('*');
                    $this->db->from('foreign_bank');
                    $this->db->where(['offset' => 'n', 'fb_code' => $record->fb_code]);
                    $this->db->order_by('doc_date, doc_ref_no', 'ASC, ASC');
                    $fb_entry_query = $this->db->get();
                    $fb_entry = $fb_entry_query->result();
                    foreach ($fb_entry as $entry) {
                        $document_date = implode('/', array_reverse(explode('-', $entry->doc_date)));
                        $html .= '<tr>';
                        $html .= '<td>'.$document_date.'</td>';
                        $html .= '<td>'.$entry->doc_ref_no.'</td>';
                        $html .= '<td>'.$entry->remarks.'</td>';
                        if ($entry->sign == '+') {
                            $famt_debit += $entry->fa_amt;
                            $famt_running_balance += $entry->fa_amt;
                            $lamt_running_balance += $entry->local_amt;

                            $html .= '<td style="text-align: right">'.number_format($entry->fa_amt, 2).'</td>';
                            $html .= '<td></td>';
                        } elseif ($entry->sign == '-') {
                            $famt_credit += $entry->fa_amt;
                            $famt_running_balance -= $entry->fa_amt;
                            $lamt_running_balance -= $entry->local_amt;
                            $html .= '<td></td>';
                            $html .= '<td style="text-align: right">'.number_format($entry->fa_amt, 2).'</td>';
                        }

                        if ($famt_running_balance >= 0) {
                            $html .= '<td style="text-align: right">'.number_format($famt_running_balance, 2).' (DR)</td>';
                        } else {
                            $html .= '<td style="text-align: right">'.number_format(abs($famt_running_balance), 2).' (CR)</td>';
                        }

                        if ($lamt_running_balance >= 0) {
                            $html .= '<td style="text-align: right">'.number_format($lamt_running_balance, 2).' (DR)</td>';
                        } else {
                            $html .= '<td style="text-align: right">'.number_format(abs($lamt_running_balance), 2).' (CR)</td>';
                        }

                        $html .= '</tr>';
                    } // bank entry ends

                    // sub total section
                    $html .= '<tr>';
                    $html .= '<td colspan="3" style="color: red; text-align: right; letter-spacing: 1px;">Sub Total</td>';
                    $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$currency.'</span> '.number_format($famt_debit, 2).'</td>';
                    $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$currency.'</span> '.number_format($famt_credit, 2).'</td>';
                    if ($famt_running_balance >= 0) {
                        $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$currency.'</span> '.number_format($famt_running_balance, 2).' (DR)</td>';
                    } else {
                        $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$currency.'</span> '.number_format(abs($famt_running_balance), 2).' (CR)</td>';
                    }
                    if ($lamt_running_balance >= 0) {
                        $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$default_currency.'</span> '.number_format($lamt_running_balance, 2).' (DR)</td>';
                    } else {
                        $html .= '<td style="text-align: right"><span style="color: chocolate;">'.$default_currency.'</span> '.number_format(abs($lamt_running_balance), 2).' (CR)</td>';
                    }
                    $html .= '</tr>';

                    $html .= '<tr><td colspan="7" height="20" style="border: none"></td></tr>';

                    $html .= '</tbody>';

                    $debit_total_by_currency += $famt_debit;
                    $credit_total_by_currency += $famt_credit;
                    $famt_total_by_currency += $famt_running_balance;
                    $lamt_total_by_currency += $lamt_running_balance;
                } // bank list ends

                $html .= '<tr>';
                $html .= '<td colspan="3" style="color: blue; text-align: right; padding-right: 5px; border: none">TOTAL</td>';
                $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$value->currency.'</span> '.number_format($debit_total_by_currency, 2).'</td>';
                $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$value->currency.'</span> '.number_format($credit_total_by_currency, 2).'</td>';

                if ($famt_total_by_currency >= 0) {
                    $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$value->currency.'</span> '.number_format($famt_total_by_currency, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$value->currency.'</span> '.number_format(abs($famt_total_by_currency), 2).' (CR)</td>';
                }

                if ($lamt_total_by_currency >= 0) {
                    $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$default_currency.'</span> '.number_format($lamt_total_by_currency, 2).' (DR)</td>';
                } else {
                    $html .= '<td style="text-align: right; border: none"><span style="color: chocolate;">'.$default_currency.'</span> '.number_format(abs($lamt_total_by_currency), 2).' (CR)</td>';
                }

                $html .= '</tr>';
                $html .= '</table> <br /><br />';

                $off_setted = $this->custom->getCount('foreign_bank', ['offset' => 'n', 'currency' => $currency]);
                $entries += $off_setted;

                $sgd_grand_total += $lamt_total_by_currency;
            } // currency loop ends

            // before year end revision data
            $before_ye_revision_data = $this->custom->getSingleRow('ye_values_before_revision', ['sub_ledger' => 'foreign_bank']);
            $html .= '<table style="margin-bottom: 20px;" align="right">';
            $html .= '<tr>';
            $html .= '<td style="font-weight: bold; color: dimgray; text-align: right; letter-spacing: 1px; border: none;">';
            if ($before_ye_revision_data->revision_done != null && $before_ye_revision_data->revision_done == 0) {
                $html .= "GRAND TOTAL <span style='color: blue'>(REVISED)</span>";
            } else {
                $html .= 'GRAND TOTAL';
            }
            $html .= '</td>';
            $html .= '<td style="width: 200px; color: brown; letter-spacing: 1px; border: none; border-top: 3px dotted gainsboro; text-align: right">';
            if ($sgd_grand_total >= 0) {
                $html .= '<strong>SGD</strong> '.number_format($sgd_grand_total, 2).' (DR)';
            } else {
                $html .= '<strong>SGD</strong> '.number_format(abs($sgd_grand_total), 2).' (CR)';
            }
            $html .= '</td>';
            $html .= '</tr>';

            // before year end revision data
            if ($before_ye_revision_data->revision_done != null && $before_ye_revision_data->revision_done == 0) {
                $grand_total_before_revision = $before_ye_revision_data->sgd_grand_total_before_revision;
                $grand_total_after_revision = $sgd_grand_total;

                $html .= '<tr>';
                $html .= '<td style="font-weight: bold; color: dimgray; text-align: right; letter-spacing: 1px; border: none;">';
                $html .= 'GRAND TOTAL <span style="color: blue">(BEFORE REVISION)</span>';
                $html .= '</td>';
                $html .= '<td style="color: brown; letter-spacing: 1px; border: none; text-align: right">';
                if ($grand_total_before_revision >= 0) {
                    $html .= '<strong>SGD</strong> '.number_format($grand_total_before_revision, 2).' (DR)';
                } else {
                    $html .= '<strong>SGD</strong> '.number_format(abs($grand_total_before_revision), 2).' (CR)';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $exchange_difference_amount = $this->get_exchange_difference_amount($grand_total_before_revision, $grand_total_after_revision);

                $html .= '<tr>';
                $html .= '<td style="font-weight: bold; color: dimgray; text-align: right; letter-spacing: 1px; border: none;">';
                if ($grand_total_after_revision > $grand_total_before_revision) {
                    $html .= 'Exchange GAIN';
                } else {
                    $html .= 'Exchange LOSS';
                }

                $html .= '</td>';
                $html .= '<td style="color: brown; letter-spacing: 1px; border: none; border-top: 3px dotted gainsboro; text-align: right">';
                if ($exchange_difference_amount >= 0) {
                    $html .= '<strong>SGD</strong> '.number_format($exchange_difference_amount, 2).' (DR)';
                } else {
                    $html .= '<strong>SGD</strong> '.number_format(abs($exchange_difference_amount), 2).' (CR)';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $update_fb_revision_status = $this->custom->updateRow('ye_values_before_revision', ['revision_done' => 1], ['b_id' => 3]);
            } else {
                // this will display the total amount of CA110 (Foreign Bank Account) from GL Table
                $sql_ca110_gl = "SELECT *, sum(CASE WHEN sign = '+' THEN total_amount WHEN sign = '-' THEN -total_amount END) AS total_sgd_amount FROM gl WHERE accn = 'CA110'";
                $query_ca110_gl = $this->db->query($sql_ca110_gl);
                $ca110_gl = $query_ca110_gl->result();

                $foreign_amount = 0;
                $local_amount = 0;
                foreach ($ca110_gl as $key => $value) {
                    $total_per_ca110_in_gl += $value->total_sgd_amount;
                }
                $html .= '<tr>';
                $html .= '<td style="font-weight: bold; color: dimgray; text-align: right; letter-spacing: 1px; border: none;">';
                $html .= 'TOTAL per CA110';
                $html .= '</td>';
                $html .= '<td style="color: brown; letter-spacing: 1px; border: none; text-align: right">';
                if ($total_per_ca110_in_gl >= 0) {
                    $html .= '<strong>SGD</strong> '.number_format($total_per_ca110_in_gl, 2).' (DR)';
                } else {
                    $html .= '<strong>SGD</strong> '.number_format(abs($total_per_ca110_in_gl), 2).' (CR)';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $gl_difference_amount = $this->get_exchange_difference_amount($sgd_grand_total, $total_per_ca110_in_gl);

                $html .= '<tr>';
                $html .= '<td style="font-weight: bold; color: dimgray; text-align: right; letter-spacing: 1px; border: none;">';
                $html .= 'DIFFERENCE';
                $html .= '</td>';
                $html .= '<td style="color: brown; letter-spacing: 1px; border: none; border-top: 3px dotted gainsboro; text-align: right">';
                if ($gl_difference_amount == 0) {
                    $html .= '<strong>SGD</strong> '.number_format($gl_difference_amount, 2);
                } elseif ($gl_difference_amount > 0) {
                    $html .= '<strong>SGD</strong> '.number_format($gl_difference_amount, 2).' (DR)';
                } else {
                    $html .= '<strong>SGD</strong> '.number_format(abs($gl_difference_amount), 2).' (CR)';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $company_where = ['code' => 'CP'];
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
            $html_header = '<table style="width: 100%;"><tr><td style="border: none; text-align: center;"><h3>'.$company_profile->company_name.'</h3></td></tr></table>';

            $html_header .= '<br /><table style="border: none; width: 100%; border-bottom: 2px solid brown">';
            $html_header .= '<tr>';
            $html_header .= '<td style="text-align: left; border: none;"><h4>FOREIGN BANK</h4></td>';
            $html_header .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d/m/Y').'</td>';
            $html_header .= '</tr>';
            $html_header .= '</table><br />';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html_header.$html;

            $file = 'fb_lstng_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('foreign_bank/reports');
        }
    }

    public function get_exchange_difference_amount($amount_before_revision, $amount_after_revision)
    {
        if ($amount_after_revision > 0 && $amount_before_revision > 0) {
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
        } elseif ($amount_after_revision == 0 && $amount_before_revision >= 0) {
            $exchange_diff_amt = $amount_before_revision;
        } else {
            $exchange_diff_amt = $amount_after_revision - $amount_before_revision;
        }

        return $exchange_diff_amt;
    }    

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        $file_name = 'fb_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['fb_open', 'foreign_bank'],
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
            set_flash_message('message', 'success', 'Foreign Bank restored successfully');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('foreign_bank/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'foreign_bank/blank.php';
        zapFB();
        redirect('foreign_bank/', 'refresh');
    }
}
