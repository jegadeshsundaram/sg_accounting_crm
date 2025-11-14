<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public $table;
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'bank_recon';
        $this->logged_id = $this->session->user_id;
        $this->load->model('bank_reconciliation/bank_reconciliation_model', 'bank');
    }

    public function populate_data_by_reference()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            if ($post['recon'] == 'OtherBank') {
                $ref_data = $this->custom->getSingleRow('gl', ['gl_id' => $post['entry_id']]);
                $data['ref_no'] = $ref_data->ref_no;
                $data['amount'] = $ref_data->total_amount;
            } elseif ($post['recon'] == 'ForeignBank') {
                $ref_data = $this->custom->getSingleRow('foreign_bank', ['fbl_id' => $post['entry_id']]);
                $data['ref_no'] = $ref_data->doc_ref_no;
                $data['amount'] = $ref_data->fa_amt;
            }
            $data['doc_date'] = date('d-m-Y', strtotime($ref_data->doc_date));
            $data['remarks'] = $ref_data->remarks;
            if ($ref_data->sign == '+') {
                $data['sign'] = '-';
            } elseif ($ref_data->sign == '-') {
                $data['sign'] = '+';
            }
            echo json_encode($data);
        } else {
            echo 'No Data Found';
        }
    }

    // Usually, other adjustment input will be used on the special scenario where user input
    // their last month unresolved items but those will be stored into recon_1 and Flag as "A"
    public function get_adj_details()
    {
        is_ajax();
        $this->body_file = 'bank_reconciliation/blank.php';
        $this->header_file = 'bank_reconciliation/blank.php';
        $this->footer_file = 'bank_reconciliation/blank.php';

        $post = $this->input->post();
        if ($post) {
            $bank_accn = $post['bank_accn'];
            $fb_id = $post['fb_id'];

            if ($fb_id == '') {
                $recon_data = $this->custom->getSingleRow('bank_recon_info', ['bank_accn' => $bank_accn]);
            } else {
                $recon_data = $this->custom->getSingleRow('bank_recon_info', ['bank_accn' => $bank_accn, 'fb_id' => $fb_id]);
            }

            $month = date('m', strtotime($recon_data->start_date));
            $year = date('Y', strtotime($recon_data->start_date));

            $last_month = $month - 1;
            if ($month == '01') {
                $last_month = '12';
                --$year;
            }

            $last_month_start_date = date('01-'.$last_month.'-'.$year);
            $last_month_end_date = date('t-'.$last_month.'-'.$year);

            if ($recon_data->recon_id != null) {
                $data['start_date'] = $last_month_start_date;
                $data['end_date'] = $last_month_end_date;
            } else {
                $data['start_date'] = '';
                $data['end_date'] = '';
            }

            echo json_encode($data);
        } else {
            redirect('/bank_reconciliation/cashbook?error=post');
        }
    }

    public function get_current_details()
    {
        is_ajax();

        $post = $this->input->post();
        if ($post) {
            $bank = $post['bank'];
            $fbank = $post['fbank'];

            if ($fbank == '') {
                $recon_data = $this->custom->getSingleRow('bank_recon_info', ['bank_accn' => $bank]);
            } else {
                $recon_data = $this->custom->getSingleRow('bank_recon_info', ['bank_accn' => $bank, 'fb_id' => $fbank]);
            }

            $default_start_date = $recon_data->start_date;
            $default_end_date = $recon_data->end_date;

            $default_month = date('m', strtotime($default_start_date));
            $default_year = date('Y', strtotime($default_start_date));

            $default_month_name = date('F', strtotime($default_start_date));

            if ($recon_data->recon_id != null) {
                $data['start_date'] = date('d-m-Y', strtotime($default_start_date));
                $data['end_date'] = date('d-m-Y', strtotime($default_end_date));

                $data['default_month'] = $default_month;
                $data['default_year'] = $default_year;

                $data['status'] = $recon_data->status;
                $data['default_month_name'] = $default_month_name;
            } else {
                $data['start_date'] = '';
                $data['end_date'] = '';
            }

            echo json_encode($data);
        } else {
            redirect('/bank_reconciliation?error=post');
        }
    }

    public function double_reference()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('bank_recon_last', ['doc_ref' => $post['ref_id']]);
        echo $ref;
    }

    public function delete_recon()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $recon_id = $post['recon_id'];
            $deleted = $this->custom->deleteRow($post['tbl'], ['br_id' => $recon_id]);
            $data['deleted'] = $deleted;
            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function get_previous_month_items()
    {
        is_ajax();
        $post = $this->input->post();

        if ($post) {
            $recon_id = $post['recon_id'];
            $latest_recon_data = $this->custom->getSingleRow('bank_recon_info', ['recon_id' => $recon_id]);

            $bank_accn = $latest_recon_data->bank_accn;
            $fb_id = $latest_recon_data->fb_id;

            $start_date = $latest_recon_data->start_date;
            $month = date('m', strtotime($start_date));
            $last_month = $month - 1;

            $html = '';
            $i = 0;

            // Last Month Recon Items
            $this->db->select('*');
            $this->db->from('bank_recon_last');
            if ($fb_id !== null && $fb_id !== '') {
                $this->db->where('bank_accn = "'.$bank_accn.'" AND fb_id = "'.$fb_id.'" AND accounted = "n"');
            } else {
                $this->db->where('bank_accn = "'.$bank_accn.'" AND accounted = "n"');
            }
            $this->db->order_by('doc_date, doc_ref', 'ASC, ASC');
            $query = $this->db->get();
            $list = $query->result();
            foreach ($list as $key => $value) {
                $document_date = implode('/', array_reverse(explode('-', $value->doc_date)));
                $html .= '<tr>';
                if ($value->sign == '+') {
                    $html .= '<td style="width: 60px"><span style="background: sandybrown; padding: 3px 5px; color: white;">DR</span></td>';
                } elseif ($value->sign == '-') {
                    $html .= '<td style="width: 60px"><span style="background: darkseagreen; padding: 3px 5px; color: white;">CR</span></td>';
                }
                $html .= '<td style="width: 130px"><span class="br_id" style="display: none;">'.$value->br_id.'</span>'.$document_date.'</td>';
                $html .= '<td style="width: 130px">'.$value->doc_ref.'</td>';
                $html .= '<td>'.$value->remarks.'</td>';
                $html .= '<td >'.number_format($value->amount, 2).'</td>';

                $html .= '<td style="width: 120px; text-align: center;"><label class="check-container">
									<input class="accounted_check" type="checkbox" name="recon_item_'.$key.'" id="recon_item_'.$key.'" />
									<span class="checkmark"></span>
								</label>
								</td>';

                $html .= '</tr>';
                ++$i;
            }

            if ($i == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="text-align: left; color: red;">No items found</td>';
                $html .= '</tr>';
            }

            // Bank & Foreign Bank details
            $bank_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $bank_accn]);
            $bank_html = '<strong>Bank : </strong>'.$bank_desc.' ('.$bank_accn.')';
            if ($fb_id !== null && $fb_id !== '') {
                $fb_data = $this->custom->getSingleRow('master_foreign_bank', ['fb_code' => $fb_id]);
                $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $fb_data->currency_id]);
                $bank_html .= '<br /><strong>Foreign Bank : </strong>'.$fb_data->fb_name.' ('.$fb_id.')';
                $bank_html .= '<br /><strong>Currency : </strong>'.$currency;
            }
            $bank_html .= '<br /><br />';
            $data['bank_details'] = $bank_html;

            $data['entries'] = $i;
            $data['items'] = $html;

            echo json_encode($data);
        } else {
            set_flash_message('message', 'danger', 'Post Error');
            redirect('bank_reconciliation');
        }
    }
}
