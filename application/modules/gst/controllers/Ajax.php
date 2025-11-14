<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->logged_id = $this->session->user_id;
    }

    // page : ob_listing
    public function populate_opening_balance()
    {
        $join_table = null;
        $join_condition = null;
        $where = null;

        $ob_status = 'C';
        $data = [];
        $no = $this->input->post('start');

        $table = 'gst_open';
        $columns = ['ob_id', 'date', 'dref', 'rema', 'gsttype'];
        $where = ['status' => $ob_status];
        $group_by = 'dref';
        $order_by = 'date';
        $order = 'DESC';
        $list = $this->dt_model->get_datatables($table, $columns, $join_table, $join_condition, $where, $group_by, $order_by, $order);
        foreach ($list as $record) {
            ++$no;
            $row = [];

            $row[] = $record->ob_id;

            $row[] = '<a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                    <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                    <a class="dt-btn dt_post"><i class="fa fa-check"></i><span>Post</span></a>';

            $row[] = date('d-m-Y', strtotime($record->date));
            $row[] = $record->dref;            
            if ($record->gsttype == 'I') {
                $row[] = 'INPUT';
            } elseif ($record->gsttype == 'O') {
                $row[] = 'OUTPUT';
            }
            $row[] = $record->rema;

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

    // page : ob_create and ob_edit
    public function double_ob()
    {
        is_ajax();
        $post = $this->input->post();
        // Step 1 :: checks same reference exists in GL_OPEN.TBL
        $ref = $this->custom->getCount('gst_open', ['dref' => $post['ref_no']]);
        if ($ref == 0) { // If NOT Exists
            // Step 2 :: checks same reference exists in GST.TBL (Already Posted)
            $ref = $this->custom->getCount('gst', ['dref' => $post['ref_no'], 'tran_type' => 'OPBAL']);
        }
        echo $ref;
    }

    // page : data patch
    public function double_dp_ob()
    {
        is_ajax();
        $post = $this->input->post();
        $ref = $this->custom->getCount('gst', ['dref' => $post['ref_no'], 'tran_type' => $post['tran_type']]);
        
        echo $ref;
    }

    // page : ob_create and ob_edit
    public function save_ob()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $ob_id = $post['ob_id'];
            $data['date'] = date('Y-m-d', strtotime($post['date']));
            $data['dref'] = $post['dref'];
            $data['rema'] = $post['rema'];
            $data['iden'] = $post['iden'];
            $data['gsttype'] = $post['gsttype'];

            $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gstcate']]);
            $data['gstcate'] = $post['gstcate'];
            $data['gstperc'] = $gst_rate;

            $data['amou'] = $post['amou'];

            $gst_amount = $post['amou'] * $gst_rate / 100;
            $data['gstamou'] = round($gst_amount, 2);

            if ($ob_id == '') {
                $ob_id = $this->custom->insertRow('gst_open', $data);
            } else {
                $updated = $this->custom->updateRow('gst_open', $data, ['ob_id' => $ob_id]);
            }

            echo $ob_id;
        } else {
            echo 'post error';
        }
    }

    // page : manage_ob
    public function delete_ob_entry()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $status = $this->custom->deleteRow('gst_open', ['ob_id' => $post['ob_id']]);
            echo $status;
        } else {
            echo 'post error';
        }
    }

    // page : opening_balance
    public function delete_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');
        $ob_data = $this->custom->getMultiValues('gst_open', 'date, dref', ['ob_id' => $ob_id]);

        $where = ['date' => $ob_data->date, 'dref' => $ob_data->dref];
        $result = $this->custom->updateRow('gst_open', ['status' => 'D'], $where);

        echo $result;
    }

    // page : opening_balance
    public function delete_gst()
    {
        is_ajax();
        $gst_id = $this->input->post('gst_id');

        $deleted = $this->custom->deleteRow('gst', ['gst_id' => $gst_id]);

        echo $deleted;
    }

    // page : opening_balance
    public function post_ob()
    {
        is_ajax();
        $ob_id = $this->input->post('rowID');

        $posted = 0;
        $this->db->select('dref, date');
        $this->db->from('gst_open');
        $this->db->where('status = "C"');
        if ($ob_id != 0) {
            $this->db->where(['ob_id' => $ob_id, 'status' => 'C']);
        }
        $this->db->group_by('dref');
        $query = $this->db->get();
        $refs = $query->result();
        foreach ($refs as $val) {

            $this->db->select('*');
            $this->db->from('gst_open');
            $this->db->where(['dref' => $val->dref, 'date' => $val->date, 'status' => 'C']);            
            $query = $this->db->get();
            $ob_entries = $query->result();
            foreach ($ob_entries as $value) {
                $data['date'] = $value->date;
                $data['dref'] = $value->dref;
                $data['iden'] = $value->iden;
                $data['rema'] = $value->rema;
                $data['gsttype'] = $value->gsttype;
                $data['gstcate'] = $value->gstcate;
                $data['gstperc'] = $value->gstperc;
                $data['amou'] = $value->amou;
                $data['gstamou'] = $value->gstamou;
                $data['tran_type'] = 'OPBAL';
                $insert = $this->custom->insertData('gst', $data);
            }

            if ($insert) {
                $ob_delete = $this->custom->updateRow('gst_open', ['status' => 'P'], ['dref' => $val->dref, 'date' => $val->date]);
                ++$posted;
            }
        }

        $status = '';
        $msg = '';
        if ($posted > 0) {
            $msg = 'POSTED TO ACCOUNTS';
            $status = 'success';
        } else {
            $msg = 'POST ERROR';
            $status = 'danger';
        }

        $st_data['status'] = $status;
        $st_data['msg'] = $msg;

        echo $posted;
    }

    // page : iras_api_fe_validation
    public function save_grp1_reasons()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $grp_id = $post['grp_id'];
            $gst_returns_data['grp1BadDebtRecoveryChk'] = $post['grp1BadDebtRecoveryChk'];
            $gst_returns_data['grp1PriorToRegChk'] = $post['grp1PriorToRegChk'];
            $gst_returns_data['grp1OtherReasonChk'] = $post['grp1OtherReasonChk'];
            $gst_returns_data['grp1OtherReasons'] = $post['grp1OtherReasons'];

            if ($grp_id == '') {
                $inserted_id = $this->custom->insertRow('gst_returns_grp_reasons', $gst_returns_data);
            } else {
                $inserted_id = 'Update';
                $updated = $this->custom->updateRow('gst_returns_grp_reasons', $gst_returns_data, ['grp_id' => $grp_id]);
            }

            $data['grp_id'] = $inserted_id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    // page : iras_api_fe_validation
    public function save_grp2_reasons()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $grp_id = $post['grp_id'];
            $gst_returns_data['grp2TouristRefundChk'] = $post['grp2TouristRefundChk'];
            $gst_returns_data['grp2AppvBadDebtReliefChk'] = $post['grp2AppvBadDebtReliefChk'];
            $gst_returns_data['grp2CreditNotesChk'] = $post['grp2CreditNotesChk'];
            $gst_returns_data['grp2OtherReasonsChk'] = $post['grp2OtherReasonsChk'];
            $gst_returns_data['grp2OtherReasons'] = $post['grp2OtherReasons'];

            if ($grp_id == '') {
                $inserted_id = $this->custom->insertRow('gst_returns_grp_reasons', $gst_returns_data);
            } else {
                $inserted_id = 'Update';
                $updated = $this->custom->updateRow('gst_returns_grp_reasons', $gst_returns_data, ['grp_id' => $grp_id]);
            }

            $data['grp_id'] = $inserted_id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    // page : iras_api_fe_validation
    public function save_grp3_reasons()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $grp_id = $post['grp_id'];
            $gst_returns_data['grp3CreditNotesChk'] = $post['grp3CreditNotesChk'];
            $gst_returns_data['grp3OtherReasonsChk'] = $post['grp3OtherReasonsChk'];
            $gst_returns_data['grp3OtherReasons'] = $post['grp3OtherReasons'];

            if ($grp_id == '') {
                $inserted_id = $this->custom->insertRow('gst_returns_grp_reasons', $gst_returns_data);
            } else {
                $inserted_id = 'Update';
                $updated = $this->custom->updateRow('gst_returns_grp_reasons', $gst_returns_data, ['grp_id' => $grp_id]);
            }

            $data['grp_id'] = $inserted_id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }   

    // page : datapatch
    public function get_gst_rate()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post['gst_code']) {
            $data['gst_percentage'] = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gst_code']]);
            echo json_encode($data);
        } else {
            echo '0';
        }
    }

    // page : options
    public function set_revenue()
    {
        is_ajax();
        $tbl = $this->input->post('tbl');

        $rev = $this->custom->getCount('gst_revenue_setting', ['process' => 'REVENUE']);

        if($rev == 0) {
            $status = $this->custom->insertRow('gst_revenue_setting', ['tbl' => $tbl]);
            
        } else {
            $status = $this->custom->updateRow('gst_revenue_setting', ['tbl' => $tbl], ['process' => 'REVENUE']);
        }        

        echo $status;
    }

}
