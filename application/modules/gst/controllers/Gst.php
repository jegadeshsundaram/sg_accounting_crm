<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gst extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('gst/gst_model', 'gst_model');
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('gst_id, dref');
        $this->db->from('gst');
        $this->db->group_by('dref');
        $this->db->order_by('dref', 'ASC');
        $query = $this->db->get();
        $ref_data = $query->result();
        $ref = "<option value=''>-- Select --</option>";
        foreach ($ref_data as $value) {
            $ref .= "<option value='".$value->gst_id."'>";
            $ref .= $value->dref;
            $ref .= '</option>';
        }
        $this->body_vars['ref_list'] = $ref;

        $this->body_file = 'gst/options.php';
    }
    
    public function ob_create()
    {
        if ($_GET['type'] == 'input') {
            $this->body_vars['gst_list'] = $this->custom->createDropdownSelect('ct_gst', ['gst_id', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
            $this->body_vars['iden_list'] = $this->custom->createDropdownSelect('master_supplier', ['supplier_id', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
            $this->body_vars['type'] = 'I';
        } elseif ($_GET['type'] == 'output') {
            $this->body_vars['gst_list'] = $this->custom->createDropdownSelect('ct_gst', ['gst_id', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);
            $this->body_vars['iden_list'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
            $this->body_vars['type'] = 'O';
        }

        $this->body_vars['save_url'] = '/gst/save_ob';
    }

    public function opening_balance()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function manage_ob($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if(isset($_GET['type'])) {

            $this->body_vars['page'] = 'new';

            $this->body_vars['doc_date'] = '';
            $this->body_vars['ref_no'] = '';
            $this->body_vars['remarks'] = '';

            if ($_GET['type'] == 'input') {
                $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
                $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);

                $this->body_vars['gst_type'] = 'I';

            } elseif ($_GET['type'] == 'output') {
                $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);
                $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
                
                $this->body_vars['gst_type'] = 'O';
            }

        } elseif ($row_id != '') {
        
            $ob_data = $this->custom->getMultiValues('gst_open', 'date, dref, rema, gsttype', ['ob_id' => $row_id]);

            $this->body_vars['page'] = 'edit';

            $this->body_vars['doc_date'] = $ob_data->date;
            $this->body_vars['ref_no'] = $ob_data->dref;
            $this->body_vars['remarks'] = $ob_data->rema;
            $this->body_vars['gst_type'] = $ob_data->gsttype;

            if ($ob_data->gsttype == 'I') {
                $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
                $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
            } elseif ($ob_data->gsttype == 'O') {
                $this->body_vars['gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);
                $this->body_vars['idens'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
            }
        }

        $this->body_vars['save_url'] = '/gst/save_ob';
    }

    public function save_ob()
    {
        $post = $this->input->post();
        $len = sizeof($post);

        if ($post) {
            $total_items = count($post['ob_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $batch_data['date'] = date('Y-m-d', strtotime($post['doc_date']));
                $batch_data['dref'] = $post['ref_no'];                
                $batch_data['iden'] = $post['iden'][$i];
                $batch_data['rema'] = $post['remarks'];
                $batch_data['gsttype'] = $post['gst_type'];

                $batch_data['gstcate'] = $post['gst_code'][$i];
                $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $post['gst_code'][$i]]);
                $batch_data['gstperc'] = $gst_rate;

                $batch_data['amou'] = $post['amount'][$i];
                $gst_amount = $post['amount'][$i] * $gst_rate / 100;
                $batch_data['gstamou'] = round($gst_amount, 2);

                $ob_id = $post['ob_id'][$i];
                $updated[] = $this->custom->updateRow('gst_open', $batch_data, ['ob_id' => $ob_id]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'Batch Saved');
            }
            
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
        }

        redirect('gst/opening_balance');
    }

    public function reports()
    {
        is_logged_in('admin');
        has_permission();

        $this->body_vars['input_categories'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
        $this->body_vars['output_categories'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);
    }

    public function iras_api()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function get_filing_info_id()
    {
        $sql = 'SELECT fi_id FROM gst_returns_filing_info ORDER BY fi_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $fi_id = 0;
        foreach ($id_data as $key => $value) {
            $fi_id = $value->fi_id;
        }

        return $fi_id;
    }

    public function get_form_5_id()
    {
        $sql = 'SELECT fv_id FROM gst_returns_form_5 ORDER BY fv_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $fv_id = 0;
        foreach ($id_data as $key => $value) {
            $fv_id = $value->fv_id;
        }

        return $fv_id;
    }

    public function get_form_7_id()
    {
        $sql = 'SELECT fv7_id FROM gst_returns_form_7 ORDER BY fv7_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $fv7_id = 0;
        foreach ($id_data as $key => $value) {
            $fv7_id = $value->fv7_id;
        }

        return $fv7_id;
    }

    public function get_contact_info_id()
    {
        $sql = 'SELECT ci_id FROM gst_returns_contact_info ORDER BY ci_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $ci_id = 0;
        foreach ($id_data as $key => $value) {
            $ci_id = $value->ci_id;
        }

        return $ci_id;
    }

    public function get_declaration_id()
    {
        $sql = 'SELECT d_id FROM gst_returns_declaration ORDER BY d_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $d_id = 0;
        foreach ($id_data as $key => $value) {
            $d_id = $value->d_id;
        }

        return $d_id;
    }

    public function get_grp_reasons_id()
    {
        $sql = 'SELECT grp_id FROM gst_returns_grp_reasons ORDER BY grp_id DESC limit 1';
        $query = $this->db->query($sql);
        $id_data = $query->result();
        $grp_id = 0;
        foreach ($id_data as $key => $value) {
            $grp_id = $value->grp_id;
        }

        return $grp_id;
    }

    // STEP 1 :: Filing Information
    public function iras_api_filing_info()
    {
        is_logged_in('admin');
        has_permission();

        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);

        $this->body_vars['fi_id'] = $filing_data->fi_id;

        $this->body_vars['tax_ref_no'] = $filing_data->tax_ref_no;
        $this->body_vars['form_type'] = $form_type = $filing_data->form_type;
        if ($filing_data->start_date != null && $filing_data->start_date != '') {
            $this->body_vars['start_date'] = date('d-m-Y', strtotime($filing_data->start_date));
        } else {
            $this->body_vars['start_date'] = '';
        }

        if ($filing_data->end_date != null && $filing_data->end_date != '') {
            $this->body_vars['end_date'] = date('d-m-Y', strtotime($filing_data->end_date));
        } else {
            $this->body_vars['end_date'] = '';
        }

        $this->body_file = 'gst/iras_api_filing_info.php';
    }

    public function save_filing_info()
    {
        $post = $this->input->post();
        if ($post) {
            $filing_data['tax_ref_no'] = $post['tax_ref_no'];
            $filing_data['form_type'] = $post['form_type'];
            $filing_data['start_date'] = date('Y-m-d', strtotime($post['start_date']));
            $filing_data['end_date'] = date('Y-m-d', strtotime($post['end_date']));

            $fi_id = $post['fi_id'];

            if ($fi_id == '') {
                $inserted = $this->custom->insertData('gst_returns_filing_info', $filing_data);
            } else {
                $updated = $this->custom->updateRow('gst_returns_filing_info', $filing_data, ['fi_id' => $fi_id]);
            }

            if ($inserted) {
                set_flash_message('message', 'success', 'Filing Info Saved');
            } elseif ($updated) {
                set_flash_message('message', 'success', 'Filing Info Updated');
            } else {
                set_flash_message('message', 'danger', 'Filing Info Save Error');
            }

            redirect('gst/iras_api_form');
        } else {
            set_flash_message('message', 'danger', 'REQUEST ERROR');
            redirect('gst/iras_api_filing_info');
        }
    }

    public function iras_api_form()
    {
        is_logged_in('admin');
        has_permission();

        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);

        if ($filing_data->start_date != null && $filing_data->start_date != '' && $filing_data->form_type != '') {
            if ($filing_data->form_type == 'F7') {
                $this->generate_form_7();
            } else {
                $this->generate_form_5($filing_data->form_type);
            }
        } else {
            set_flash_message('message', 'success', 'START WITH FILING INFO');
            redirect('gst/iras_api_filing_info');
        }
    }

    // STEP 2: FORM 5 OR 8 Values - Generation
    public function generate_form_5($form_type)
    {
        $fv_id = $this->get_form_5_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_5', ['fv_id' => $fv_id]);
        $this->body_vars['fv_id'] = $form_data->fv_id;
        if ($form_type == 'F5') {
            $form_number = '5';
        } elseif ($form_type == 'F8') {
            $form_number = '8';
        }
        $this->body_vars['form_type'] = $form_number;

        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $start_date = $filing_data->start_date;
        $end_date = $filing_data->end_date;

        // Box 1: Total Value of Standard-Rated Supplies
        // Applicable GST Code's: 'SR', 'SRCA-S', 'SRCA-C', 'DS', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG'
        $box_1_value = 0;
        $box_1_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SR', 'SRCA-S', 'SRCA-C', 'DS', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG') GROUP BY gstcate ORDER BY gstcate = 'SR' DESC, gstcate = 'SRCA-S' DESC, gstcate = 'SRCA-C' DESC, gstcate = 'DS' DESC, gstcate = 'SRRC' DESC, gstcate = 'SROVR-RS' DESC, gstcate = 'SROVR-LVG' DESC, gstcate = 'SRLVG' DESC";
        $box_1_query = $this->db->query($box_1_sql);
        $box_1_data = $box_1_query->result();
        foreach ($box_1_data as $key => $value) {
            $box_1_value += $value->total_amount;
        }
        $this->body_vars['box_1_value'] = round($box_1_value);

        // Box 2: Total Value of Zero-Rated Supplies
        // Applicable GST Code: 'ZR'
        $box_2_value = 0;
        $box_2_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('ZR') GROUP BY gstcate";
        $box_2_query = $this->db->query($box_2_sql);
        $box_2_data = $box_2_query->result();
        foreach ($box_2_data as $key => $value) {
            $box_2_value += $value->total_amount;
        }
        $this->body_vars['box_2_value'] = round($box_2_value);

        // Box 3: Total Value of Exempt Supplies
        // Applicable GST Code: 'ES33', 'ESN33'
        $box_3_value = 0;
        $box_3_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('ES33', 'ESN33') GROUP BY gstcate ORDER BY gstcate ASC";
        $box_3_query = $this->db->query($box_3_sql);
        $box_3_data = $box_3_query->result();
        foreach ($box_3_data as $key => $value) {
            $box_3_value += $value->total_amount;
        }
        $this->body_vars['box_3_value'] = round($box_3_value);

        // Box 4: Total Supplies (Total value of (1) + (2) + (3))
        // Total Supplies = Box 1 + Box 2 + Box 3
        $total_supplies = $box_1_value + $box_2_value + $box_3_value;
        $this->body_vars['box_4_value'] = $total_supplies;

        // Box 5: Total Value of Taxable Purchases (exclude purchases where input tax is disallowed)
        // Applicable Tax Code's: 'TX', 'TXCA', 'ZP', 'IM', 'ME', 'IGDS'
        $box_5_value = 0;
        $box_5_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('TX', 'TXCA', 'ZP', 'IM', 'ME', 'IGDS') GROUP BY gstcate ORDER BY gstcate = 'TX' DESC, gstcate = 'TXCA' DESC, gstcate = 'ZP' DESC, gstcate = 'IM' DESC, gstcate = 'ME' DESC, gstcate = 'IGDS' DESC";
        $box_5_query = $this->db->query($box_5_sql);
        $box_5_data = $box_5_query->result();
        foreach ($box_5_data as $key => $value) {
            $box_5_value += $value->total_amount;
        }
        $this->body_vars['box_5_value'] = round($box_5_value);

        // Box 6: Output Tax Due
        // Applicable Tax Code's: 'SR', 'DS', 'SRCA-C', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG'
        $box_6_value = 0;
        $box_6_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN gstamou WHEN gsttype = 'OR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SR', 'DS', 'SRCA-C', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG') GROUP BY gstcate ORDER BY gstcate = 'SR' DESC, gstcate = 'DS' DESC, gstcate = 'SRCA-C' DESC, gstcate = 'SRRC' DESC, gstcate = 'SROVR-RS' DESC, gstcate = 'SROVR-LVG' DESC, gstcate = 'SRLVG' DESC";
        $box_6_query = $this->db->query($box_6_sql);
        $box_6_data = $box_6_query->result();
        foreach ($box_6_data as $key => $value) {
            $box_6_value += $value->total_gst_amount;
        }
        $this->body_vars['box_6_value'] = $box_6_value;

        // Box 7: Less: Input tax and refunds claimed (exclude disallowed input tax)
        // Applicable Tax Code's: 'TX', 'IM', 'IGDS', 'TXCA'
        $box_7_value = 0;
        $box_7_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN gstamou WHEN gsttype = 'IR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('TX', 'IM', 'IGDS', 'TXCA') GROUP BY gstcate ORDER BY gstcate ASC";
        $box_7_query = $this->db->query($box_7_sql);
        $box_7_data = $box_7_query->result();
        foreach ($box_7_data as $key => $value) {
            $box_7_value += $value->total_gst_amount;
        }
        $this->body_vars['box_7_value'] = $box_7_value;

        // Box 8: Equals: Net GST to be paid to/claimed from IRAS
        // Net GST Amount = Output Tax - Input Tax
        // If Box 6 (Output Tax Due) is greater than the Box 7 (Input Tax and Refunds Claimed) then the difference amount (Net GST) to be PAID to IRAS
        // If Box 6 (Output Tax Due) is lesser than the Box 7 (Input Tax and Refunds Claimed) then the difference amount (Net GST) to be CLAIMED from IRAS
        $net_gst_amount = 0;
        $net_gst_amount = $box_6_value - $box_7_value;
        if ($box_6_value > $box_7_value) {
            $this->body_vars['box_8_desc'] = 'Net GST to be Paid to IRAS';
            $this->body_vars['box_8_option'] = 'paid';
        } else {
            $this->body_vars['box_8_desc'] = 'Net GST to be Claimed from IRAS';
            $this->body_vars['box_8_option'] = 'claimed';
        }
        if ($box_6_value == 0 && $box_7_value == 0) {
            $this->body_vars['box_8_desc'] = 'Equals: Net GST to be paid to/claimed from IRAS';
            $this->body_vars['box_8_option'] = '';
        }

        $box_8_value = $net_gst_amount;
        if ($net_gst_amount < 0) {
            $box_8_value = (-1 * $net_gst_amount);
        }
        $this->body_vars['box_8_value'] = $box_8_value;

        // Box 9: Total value of goods imported under import GST suspension schemes (e.g. Major Exporter Scheme/Approved 3rd Party Logistics Company)
        // Applicable Tax Code's: 'ME'
        $box_9_value = 0;
        $box_9_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('ME') GROUP BY gstcate";
        $box_9_query = $this->db->query($box_9_sql);
        $box_9_data = $box_9_query->result();
        foreach ($box_9_data as $key => $value) {
            $box_9_value += $value->total_amount;
        }
        $this->body_vars['box_9_value'] = round($box_9_value);

        // Box 10 (Yes/No): Did you claim for GST you had refunded to tourists?
        if ($form_data->box_10_option == 1) {
            $this->body_vars['box_10_option'] = 1;
        } else {
            $this->body_vars['box_10_option'] = 0; // Very First Time pass 0, otherwise this field is not posting
        }
        // Box 10 (Value): Tourist Refund Amount
        $this->body_vars['box_10_value'] = $form_data->box_10_value;

        // Box 11 (Yes/No): Did you make any bad debt relief claims and/or refund for reverse charge transactions?
        if ($form_data->box_11_option == 1) {
            $this->body_vars['box_11_option'] = 1;
        } else {
            $this->body_vars['box_11_option'] = 0; // Very First Time pass 0, otherwise this field is not posting
        }
        // Box 11 (Value): Bad Debt Relief Claims and/or refund for reverse charge Amount
        $this->body_vars['box_11_value'] = $form_data->box_11_value;

        // Box 12 (Yes/No): Did you make any pre-registration claims?
        if ($form_data->box_12_option == 1) {
            $this->body_vars['box_12_option'] = 1;
        } else {
            $this->body_vars['box_12_option'] = 0; // Very First Time pass 0, otherwise this field is not posting
        }
        // Box 12 (Value): Pre-registration Claims Amount
        $this->body_vars['box_12_value'] = $form_data->box_12_value;

        // Box 13: Revenue for the accounting period
        $revenue = 0;
        $revenue_tbl = $this->custom->getSingleValue('gst_revenue_setting', 'tbl', ['process' => 'REVENUE']);
        if ($revenue_tbl == 'gst') { // revenue from GST.tbl
            $this->db->select("SUM(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS specific_revenue_amount");
            $this->db->from('gst');
            $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND gsttype in ('O', 'OR')");
            $this->db->group_by('gsttype');
            $this->db->order_by('gsttype ASC, date ASC, dref ASC');
        } else { // revenue from GL.tbl
            $this->db->select('SUM(total_amount) AS specific_revenue_amount');
            $this->db->from('gl');
            $this->db->where("doc_date BETWEEN '".$start_date."' AND '".$end_date."' AND accn in ('S0001', 'S0100')");
            $this->db->group_by('accn');
            $this->db->order_by('accn ASC, doc_date ASC, ref_no ASC');
        }
        $query = $this->db->get();
        $revenue_entries = $query->result();
        foreach ($revenue_entries as $value) {
            $revenue += $value->specific_revenue_amount;
        }
        $this->body_vars['box_13_value'] = round($revenue);

        // Box 14 (Yes/No): Did you import services and/ or low-value goods subject to GST under reverse charge?
        // Box 14 (Value): Value of imported services and/ or low-value goods subject to reverse charge
        // Applicable GST Code's : 'SRRC'
        $box_14_value = 0;
        $box_14_entry = 0;
        $box_14_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SRRC') GROUP BY gstcate";
        $box_14_query = $this->db->query($box_14_sql);
        $box_14_data = $box_14_query->result();
        foreach ($box_14_data as $key => $value) {
            $box_14_value = $value->total_amount;
            ++$box_14_entry;
        }
        if ($box_14_entry > 0) {
            $this->body_vars['box_14_option'] = '1';
            $this->body_vars['box_14_value'] = $box_14_value;
        } else {
            $this->body_vars['box_14_option'] = '0';
            $this->body_vars['box_14_value'] = 0;
        }

        // Box 15 (Yes/No): Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?
        // Box 15 (Value): Value of remote supplied by electronic marketplace operator
        // Applicable GST Code's : 'SROVR-RS'
        $box_15_value = 0;
        $box_15_entry = 0;
        $box_15_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SROVR-RS') GROUP BY gstcate";
        $box_15_query = $this->db->query($box_15_sql);
        $box_15_data = $box_15_query->result();
        foreach ($box_15_data as $key => $value) {
            $box_15_value = $value->total_amount;
            ++$box_15_entry;
        }
        if ($box_15_entry > 0) {
            $this->body_vars['box_15_option'] = '1';
            $this->body_vars['box_15_value'] = $box_15_value;
        } else {
            $this->body_vars['box_15_option'] = '0';
            $this->body_vars['box_15_value'] = 0;
        }

        // Box 16 (Yes/No): Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?
        // Box 16 (Value): Value of imported low-value goods supplied by electronic marketplace operator/ redeliverer
        // Applicable GST Code's : 'SROVR-LVG'
        $box_16_value = 0;
        $box_16_entry = 0;
        $box_16_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SROVR-LVG') GROUP BY gstcate";
        $box_16_query = $this->db->query($box_16_sql);
        $box_16_data = $box_16_query->result();
        foreach ($box_16_data as $key => $value) {
            $box_16_value = $value->total_amount;
            ++$box_16_entry;
        }
        if ($box_16_entry > 0) {
            $this->body_vars['box_16_option'] = '1';
            $this->body_vars['box_16_value'] = $box_16_value;
        } else {
            $this->body_vars['box_16_option'] = '0';
            $this->body_vars['box_16_value'] = 0;
        }

        // Box 17 (Yes/No): Did you make your own supply of imported low-value goods that is subject to GST?
        // Box 17 (Value): Value of own supply of imported low-value goods
        // Applicable GST Code's : 'SRLVG'
        $box_17_value = 0;
        $box_17_entry = 0;
        $box_17_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SRLVG') GROUP BY gstcate";
        $box_17_query = $this->db->query($box_17_sql);
        $box_17_data = $box_17_query->result();
        foreach ($box_17_data as $key => $value) {
            $box_17_value = $value->total_amount;
            ++$box_17_entry;
        }
        if ($box_17_entry > 0) {
            $this->body_vars['box_17_option'] = '1';
            $this->body_vars['box_17_value'] = $box_17_value;
        } else {
            $this->body_vars['box_17_option'] = '0';
            $this->body_vars['box_17_value'] = 0;
        }

        // Box 18: Net GST per box 8 above
        // Automatically computed after Box 8 is populated
        $box_18_value = $box_8_value;
        $this->body_vars['box_18_value'] = $box_18_value;

        // Box 19: Add: Deferred import GST payable
        // Applicable Tax Code's : 'IGDS'
        $box_19_value = 0;
        $box_19_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN gstamou WHEN gsttype = 'IR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('IGDS') GROUP BY gstcate";
        $box_19_query = $this->db->query($box_19_sql);
        $box_19_data = $box_19_query->result();
        foreach ($box_19_data as $key => $value) {
            $box_19_value += $value->total_gst_amount;
        }
        $this->body_vars['box_19_value'] = $box_19_value;

        // Box 20: Equals: Total tax to be paid to IRAS
        // Box (18) + Box (19)
        $box_20_value = $box_18_value + $box_19_value;
        $this->body_vars['box_20_value'] = $box_20_value;

        // Box 21: Total value of goods imported under Import GST Deferment Scheme
        // The net value of imports under IGDS
        // Applicable Tax Code's: 'IGDS'
        $box_21_value = 0;
        $box_21_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate = 'IGDS' GROUP BY gstcate";
        $box_21_query = $this->db->query($box_21_sql);
        $box_21_data = $box_21_query->result();
        foreach ($box_21_data as $key => $value) {
            $box_21_value = $value->total_amount;
        }
        $this->body_vars['box_21_value'] = round($box_21_value);

        $this->body_file = 'gst/iras_api_form_5.php';
    }

    // Step 2: FORM 7 Values - Generation
    public function generate_form_7()
    {
        $fv7_id = $this->get_form_7_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_7', ['fv7_id' => $fv7_id]);
        $this->body_vars['fv_id'] = $form_data->fv7_id;

        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $start_date = $filing_data->start_date;
        $end_date = $filing_data->end_date;

        // Box 1: Total Value of Standard-Rated Supplies
        // Applicable GST Code's: 'SR', 'SRCA-S', 'SRCA-C', 'DS', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG'
        $box_1_value = 0;
        $box_1_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SR', 'SRCA-S', 'SRCA-C', 'DS', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG') GROUP BY gstcate ORDER BY gstcate = 'SR' DESC, gstcate = 'SRCA-S' DESC, gstcate = 'SRCA-C' DESC, gstcate = 'DS' DESC, gstcate = 'SRRC' DESC, gstcate = 'SROVR-RS' DESC, gstcate = 'SROVR-LVG' DESC, gstcate = 'SRLVG' DESC";
        $box_1_query = $this->db->query($box_1_sql);
        $box_1_data = $box_1_query->result();
        foreach ($box_1_data as $key => $value) {
            $box_1_value += $value->total_amount;
        }
        $this->body_vars['box_1_value'] = round($box_1_value);

        // Box 2: Total Value of Zero-Rated Supplies
        // Applicable GST Code: 'ZR'
        $box_2_value = 0;
        $box_2_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('ZR') GROUP BY gstcate";
        $box_2_query = $this->db->query($box_2_sql);
        $box_2_data = $box_2_query->result();
        foreach ($box_2_data as $key => $value) {
            $box_2_value += $value->total_amount;
        }
        $this->body_vars['box_2_value'] = round($box_2_value);

        // Box 3: Total Value of Exempt Supplies
        // Applicable GST Code: 'ES33', 'ESN33'
        $box_3_value = 0;
        $box_3_sql = "SELECT sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('ES33', 'ESN33') GROUP BY gstcate ORDER BY gstcate ASC";
        $box_3_query = $this->db->query($box_3_sql);
        $box_3_data = $box_3_query->result();
        foreach ($box_3_data as $key => $value) {
            $box_3_value += $value->total_amount;
        }
        $this->body_vars['box_3_value'] = round($box_3_value);

        // Box 4: Total Supplies (Total value of (1) + (2) + (3))
        // Total Supplies = Box 1 + Box 2 + Box 3
        $total_supplies = $box_1_value + $box_2_value + $box_3_value;
        $this->body_vars['box_4_value'] = $total_supplies;

        // Box 5: Total Value of Taxable Purchases (Excluding GST)
        // Applicable Tax Code's: 'TX', 'TXCA', 'ZP', 'IM', 'ME', 'IGDS'
        $box_5_value = 0;
        $box_5_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('TX', 'TXCA', 'ZP', 'IM', 'ME', 'IGDS') GROUP BY gstcate ORDER BY gstcate = 'TX' DESC, gstcate = 'TXCA' DESC, gstcate = 'ZP' DESC, gstcate = 'IM' DESC, gstcate = 'ME' DESC, gstcate = 'IGDS' DESC";
        $box_5_query = $this->db->query($box_5_sql);
        $box_5_data = $box_5_query->result();
        foreach ($box_5_data as $key => $value) {
            $box_5_value += $value->total_amount;
        }
        $this->body_vars['box_5_value'] = round($box_5_value);

        // Box 6: Output Tax Due
        // Applicable Tax Code's: 'SR', 'DS', 'SRCA-C', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG'
        $box_6_value = 0;
        $box_6_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN gstamou WHEN gsttype = 'OR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SR', 'DS', 'SRCA-C', 'SRRC', 'SROVR-RS', 'SROVR-LVG', 'SRLVG') GROUP BY gstcate ORDER BY gstcate = 'SR' DESC, gstcate = 'DS' DESC, gstcate = 'SRCA-C' DESC, gstcate = 'SRRC' DESC, gstcate = 'SROVR-RS' DESC, gstcate = 'SROVR-LVG' DESC, gstcate = 'SRLVG' DESC";
        $box_6_query = $this->db->query($box_6_sql);
        $box_6_data = $box_6_query->result();
        foreach ($box_6_data as $key => $value) {
            $box_6_value += $value->total_gst_amount;
        }
        $this->body_vars['box_6_value'] = $box_6_value;

        // Box 7: Input Tax and Refunds Claimed
        // Applicable Tax Code's: 'TX', 'IM', 'IGDS', 'TXCA'
        $box_7_value = 0;
        $box_7_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN gstamou WHEN gsttype = 'IR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate IN ('TX', 'IM', 'IGDS', 'TXCA') GROUP BY gstcate ORDER BY gstcate ASC";
        $box_7_query = $this->db->query($box_7_sql);
        $box_7_data = $box_7_query->result();
        foreach ($box_7_data as $key => $value) {
            $box_7_value += $value->total_gst_amount;
        }
        $this->body_vars['box_7_value'] = $box_7_value;

        // Box 8. Net GST to be Paid/Claimed from IRAS
        // Net GST Amount = Output Tax - Input Tax
        // If Box 6 (Output Tax Due) is greater than the Box 7 (Input Tax and Refunds Claimed) then the difference amount (Net GST) to be PAID to IRAS
        // If Box 6 (Output Tax Due) is lesser than the Box 7 (Input Tax and Refunds Claimed) then the difference amount (Net GST) to be CLAIMED from IRAS
        $net_gst_amount = 0;
        $net_gst_amount = $box_6_value - $box_7_value;
        if ($box_6_value > $box_7_value) {
            $this->body_vars['box_8_desc'] = 'Net GST to be Paid to IRAS';
            $this->body_vars['box_8_option'] = 'paid';
        } else {
            $this->body_vars['box_8_desc'] = 'Net GST to be Claimed from IRAS';
            $this->body_vars['box_8_option'] = 'claimed';
        }
        if ($box_6_value == 0 && $box_7_value == 0) {
            $this->body_vars['box_8_desc'] = 'Equals: Net GST to be paid to/claimed from IRAS';
            $this->body_vars['box_8_option'] = '';
        }
        if ($net_gst_amount < 0) {
            $this->body_vars['box_8_value'] = (-1 * $net_gst_amount);
        } else {
            $this->body_vars['box_8_value'] = $net_gst_amount;
        }

        // Box 9 : Less: Net GST paid/ claimed previously for this accounting period
        $this->body_vars['box_9_value'] = $form_data->box_9_value;

        // Box 10 : Equals: Difference to be paid to / claimed from IRAS
        $this->body_vars['box_10_value'] = $form_data->box_10_value;

        // Box 11: Total value of goods imported under import GST suspension schemes (e.g.Major Exporter Scheme/ Approved 3rd Party Logistics Company)
        // Applicable Tax Code's: 'ME'
        $box_11_value = 0;
        $box_11_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('ME') GROUP BY gstcate";
        $box_11_query = $this->db->query($box_11_sql);
        $box_11_data = $box_11_query->result();
        foreach ($box_11_data as $key => $value) {
            $box_11_value += $value->total_amount;
        }
        $this->body_vars['box_11_value'] = round($box_11_value);

        // Box 12 : Did you claim for GST you had refunded to tourists?
        $this->body_vars['box_12_option'] = $form_data->box_12_option;
        // Box 12 : Tourist Refund Amount
        $this->body_vars['box_12_value'] = $form_data->box_12_value;

        // Box 13 : Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?
        $this->body_vars['box_13_option'] = $form_data->box_13_option;
        // Box 13 : Bad Debt Relief Claims and/or refund for reverse charge Amount
        $this->body_vars['box_13_value'] = $form_data->box_13_value;

        // Box 14 : Did you make any pre-registration claims?
        $this->body_vars['box_14_option'] = $form_data->box_14_option;
        // Box 14 : Pre-registration Claims Amount
        $this->body_vars['box_14_value'] = $form_data->box_14_value;

        // Box 15: Revenue for the accounting period
        $revenue = 0;
        $revenue_tbl = $this->custom->getSingleValue('gst_revenue_setting', 'tbl', ['process' => 'REVENUE']);
        if ($revenue_tbl == 'gst') { // // revenue from GST.tbl
            $this->db->select("SUM(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS specific_revenue_amount");
            $this->db->from('gst');
            $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND gsttype in ('O', 'OR')");
            $this->db->group_by('gsttype');
            $this->db->order_by('gsttype ASC, date ASC, dref ASC');
        } else { // revenue from GL.tbl
            $this->db->select('SUM(total_amount) AS specific_revenue_amount');
            $this->db->from('gl');
            $this->db->where("doc_date BETWEEN '".$start_date."' and '".$end_date."' and accn in ('S0001', 'S0100') ");
            $this->db->group_by('accn');
            $this->db->order_by('accn ASC, doc_date ASC, ref_no ASC');
        }
        $query = $this->db->get();
        $revenue_entries = $query->result();
        foreach ($revenue_entries as $value) {
            $revenue += $value->specific_revenue_amount;
        }
        $this->body_vars['box_15_value'] = round($revenue);

        // Box 16 (Yes/No): Did you import services and/ or low-value goods subject to GST under reverse charge?
        // Box 16 (value): Value of imported services and low-value goods subject to reverse charge
        // Applicable GST Code's : 'SRRC'
        $box_16_value = 0;
        $box_16_entry = 0;
        $box_16_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SRRC') GROUP BY gstcate";
        $box_16_query = $this->db->query($box_16_sql);
        $box_16_data = $box_16_query->result();
        foreach ($box_16_data as $key => $value) {
            $box_16_value = $value->total_amount;
            ++$box_16_entry;
        }
        if ($box_16_entry > 0) {
            $this->body_vars['box_16_option'] = '1';
            $this->body_vars['box_16_value'] = $box_16_value;
        } else {
            $this->body_vars['box_16_option'] = '0';
            $this->body_vars['box_16_value'] = 0;
        }

        // Box 17 (Yes/No): Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?
        // Box 17 (value): Value of remote services supplied by electronic marketplace operator
        // Applicable GST Code's : 'SROVR-RS'
        $box_17_value = 0;
        $box_17_entry = 0;
        $box_17_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SROVR-RS') GROUP BY gstcate";
        $box_17_query = $this->db->query($box_17_sql);
        $box_17_data = $box_17_query->result();
        foreach ($box_17_data as $key => $value) {
            $box_17_value = $value->total_amount;
            ++$box_17_entry;
        }
        if ($box_17_entry > 0) {
            $this->body_vars['box_17_option'] = '1';
            $this->body_vars['box_17_value'] = $box_17_value;
        } else {
            $this->body_vars['box_17_option'] = '0';
            $this->body_vars['box_17_value'] = 0;
        }

        // Box 18 (Yes/No): Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?
        // Box 18 (Value): Value of imported low-value goods supplied by electronic marketplace operator/redeliverer
        // Applicable GST Code's : 'SROVR-LVG'
        $box_18_value = 0;
        $box_18_entry = 0;
        $box_18_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SROVR-LVG') GROUP BY gstcate";
        $box_18_query = $this->db->query($box_18_sql);
        $box_18_data = $box_18_query->result();
        foreach ($box_18_data as $key => $value) {
            $box_18_value = $value->total_amount;
            ++$box_18_entry;
        }
        if ($box_18_entry > 0) {
            $this->body_vars['box_18_option'] = '1';
            $this->body_vars['box_18_value'] = $box_18_value;
        } else {
            $this->body_vars['box_18_option'] = '0';
            $this->body_vars['box_18_value'] = 0;
        }

        // Box 19 (Yes/No): Did you make your own supply of imported low-value goods that is subject to GST?
        // Box 19 (Value): Value of own supply of imported low-value goods
        // Applicable GST Code's : 'SRLVG'
        $box_19_value = 0;
        $box_19_entry = 0;
        $box_19_sql = "SELECT *, sum(CASE WHEN gsttype = 'O' THEN amou WHEN gsttype = 'OR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('SRLVG') GROUP BY gstcate";
        $box_19_query = $this->db->query($box_19_sql);
        $box_19_data = $box_19_query->result();
        foreach ($box_19_data as $key => $value) {
            $box_19_value = $value->total_amount;
            ++$box_19_entry;
        }
        if ($box_19_entry > 0) {
            $this->body_vars['box_19_option'] = '1';
            $this->body_vars['box_19_value'] = $box_19_value;
        } else {
            $this->body_vars['box_19_option'] = '0';
            $this->body_vars['box_19_value'] = 0;
        }

        // Box 20: Revised deferred import GST payable
        $box_20_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN gstamou WHEN gsttype = 'IR' THEN -gstamou END) AS total_gst_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate in ('IGDS') GROUP BY gstcate";
        $box_20_query = $this->db->query($box_20_sql);
        $box_20_data = $box_20_query->result();
        $box_20_value = 0;
        foreach ($box_20_data as $key => $value) {
            $box_20_value += $value->total_gst_amount;
        }
        $this->body_vars['box_20_value'] = $box_20_value;

        // Box 21. Less: Deferred import GST payable previously declared for this accounting period
        $this->body_vars['box_21_value'] = $form_data->box_21_value;

        // Box 22: Equals: Diffference in deferred import GST payable
        // Box 20 - Box 21
        if ($box_20_value > $form_data->box_21_value) {
            $box_22_value = $box_20_value - $form_data->box_21_value;
        } else {
            $box_22_value = $form_data->box_21_value - $box_20_value;
        }
        $this->body_vars['box_22_value'] = $box_22_value;

        // Box 23: Add: Difference in Net GST (per box 10 above)
        $this->body_vars['box_23_value'] = $form_data->box_10_value;

        // Box 24: Equal: Difference in total tax to be paid
        $this->body_vars['box_24_value'] = $form_data->box_24_value;

        // Box 25: Revised total tax to be paid to / claimed from IRAS (Box 8 + Box 20)
        $box_25_value = $net_gst_amount + $box_20_value;
        if ($net_gst_amount < 0) {
            $box_25_value = (-1 * $net_gst_amount) + $box_20_value;
        }
        $this->body_vars['box_25_value'] = $box_25_value;

        // Box 26: Total value of goods imported under the Import GST Deferment Scheme
        // Applicable Tax Code's: 'IGDS'
        $box_26_sql = "SELECT *, sum(CASE WHEN gsttype = 'I' THEN amou WHEN gsttype = 'IR' THEN -amou END) AS total_amount FROM gst WHERE date BETWEEN '".$start_date."' AND '".$end_date."' AND gstcate = 'IGDS' GROUP BY gstcate";
        $box_26_query = $this->db->query($box_26_sql);
        $box_26_data = $box_26_query->result();
        $box_26_value = 0;
        foreach ($box_26_data as $key => $value) {
            $box_26_value += $value->total_amount;
        }
        $this->body_vars['box_26_value'] = round($box_26_value);

        $this->body_vars['error_description'] = $form_data->error_description;

        $this->body_file = 'gst/iras_api_form_7.php';
    }

    public function save_form_5()
    {
        $post = $this->input->post();
        if ($post) {
            $gst_data['box_1_value'] = $post['box_1_value'];
            $gst_data['box_2_value'] = $post['box_2_value'];
            $gst_data['box_3_value'] = $post['box_3_value'];
            $gst_data['box_4_value'] = $post['box_4_value'];
            $gst_data['box_5_value'] = $post['box_5_value'];
            $gst_data['box_6_value'] = $post['box_6_value'];
            $gst_data['box_7_value'] = $post['box_7_value'];
            $gst_data['box_8_option'] = $post['box_8_option'];
            $gst_data['box_8_value'] = $post['box_8_value'];
            $gst_data['box_9_value'] = $post['box_9_value'];

            $gst_data['box_10_option'] = $post['box_10_option'];
            $gst_data['box_10_value'] = $post['box_10_value'];

            $gst_data['box_11_option'] = $post['box_11_option'];
            $gst_data['box_11_value'] = $post['box_11_value'];

            $gst_data['box_12_option'] = $post['box_12_option'];
            $gst_data['box_12_value'] = $post['box_12_value'];

            $gst_data['box_13_value'] = $post['box_13_value'];

            $gst_data['box_14_option'] = $post['box_14_option'];
            $gst_data['box_14_value'] = $post['box_14_value'];

            $gst_data['box_15_option'] = $post['box_15_option'];
            $gst_data['box_15_value'] = $post['box_15_value'];

            $gst_data['box_16_option'] = $post['box_16_option'];
            $gst_data['box_16_value'] = $post['box_16_value'];

            $gst_data['box_17_option'] = $post['box_17_option'];
            $gst_data['box_17_value'] = $post['box_17_value'];

            $gst_data['box_18_value'] = $post['box_18_value'];
            $gst_data['box_19_value'] = $post['box_19_value'];
            $gst_data['box_20_value'] = $post['box_20_value'];
            $gst_data['box_21_value'] = $post['box_21_value'];

            $fv_id = $post['fv_id'];

            if ($fv_id == '') {
                $inserted = $this->custom->insertData('gst_returns_form_5', $gst_data);
            } else {
                $updated = $this->custom->updateRow('gst_returns_form_5', $gst_data, ['fv_id' => $fv_id]);
            }

            if ($inserted) {
                set_flash_message('message', 'success', 'Form Values Saved');
            } elseif ($updated) {
                set_flash_message('message', 'success', 'Form Values Updated');
            } else {
                set_flash_message('message', 'danger', 'Form Values Save Error');
            }

            redirect('gst/iras_api_declaration');
        } else {
            set_flash_message('message', 'danger', 'REQUEST ERROR');
            redirect('gst/iras_api_form');
        }
    }

    public function save_form_7()
    {
        $post = $this->input->post();
        if ($post) {
            $data['box_1_value'] = $post['box_1_value'];
            $data['box_2_value'] = $post['box_2_value'];
            $data['box_3_value'] = $post['box_3_value'];
            $data['box_4_value'] = $post['box_4_value'];
            $data['box_5_value'] = $post['box_5_value'];
            $data['box_6_value'] = $post['box_6_value'];
            $data['box_7_value'] = $post['box_7_value'];
            $data['box_8_option'] = $post['box_8_option'];
            $data['box_8_value'] = $post['box_8_value'];
            $data['box_9_value'] = $post['box_9_value'];
            $data['box_10_value'] = $post['box_10_value'];
            $data['box_11_value'] = $post['box_11_value'];

            $data['box_12_option'] = $post['box_12_option'];
            $data['box_12_value'] = $post['box_12_value'];

            $data['box_13_option'] = $post['box_13_option'];
            $data['box_13_value'] = $post['box_13_value'];

            $data['box_14_option'] = $post['box_14_option'];
            $data['box_14_value'] = $post['box_14_value'];

            $data['box_15_value'] = $post['box_15_value'];

            $data['box_16_option'] = $post['box_16_option'];
            $data['box_16_value'] = $post['box_16_value'];

            $data['box_17_option'] = $post['box_17_option'];
            $data['box_17_value'] = $post['box_17_value'];

            $data['box_18_option'] = $post['box_18_option'];
            $data['box_18_value'] = $post['box_18_value'];

            $data['box_19_option'] = $post['box_19_option'];
            $data['box_19_value'] = $post['box_19_value'];

            $data['box_20_value'] = $post['box_20_value'];
            $data['box_21_value'] = $post['box_21_value'];
            $data['box_22_value'] = $post['box_22_value'];
            $data['box_23_value'] = $post['box_23_value'];
            $data['box_24_value'] = $post['box_24_value'];
            $data['box_25_value'] = $post['box_25_value'];
            $data['box_26_value'] = $post['box_26_value'];

            $data['error_description'] = $post['error_description'];

            $fv_id = $post['fv7_id'];

            if ($fv_id == '') {
                $inserted = $this->custom->insertData('gst_returns_form_7', $data);
            } else {
                $updated = $this->custom->updateRow('gst_returns_form_7', $data, ['fv7_id' => $fv_id]);
            }

            if ($inserted) {
                set_flash_message('message', 'success', 'Form Values Saved');
            } elseif ($updated) {
                set_flash_message('message', 'success', 'Form Values Updated');
            } else {
                set_flash_message('message', 'danger', 'Form Values Save Error');
            }

            redirect('gst/iras_api_declaration');
        } else {
            set_flash_message('message', 'danger', 'REQUEST ERROR');
            redirect('gst/iras_api_form');
        }
    }

    public function iras_api_declaration()
    {
        is_logged_in('admin');
        has_permission();

        $d_id = $this->get_declaration_id();
        $declaration_data = $this->custom->getSingleRow('gst_returns_declaration', ['d_id' => $d_id]);
        $this->body_vars['d_id'] = $declaration_data->d_id;
        $this->body_vars['declaration_item_1'] = $declaration_data->declaration_item_1;
        $this->body_vars['declaration_item_2'] = $declaration_data->declaration_item_2;
        $this->body_vars['designation'] = $declaration_data->designation;
        $this->body_vars['tax_agent_name'] = $declaration_data->tax_agent_name;

        $this->body_file = 'gst/iras_api_declaration.php';
    }

    public function save_declaration()
    {
        $post = $this->input->post();
        if ($post) {
            $data['declaration_item_1'] = $post['declaration_item_1'];
            $data['declaration_item_2'] = $post['declaration_item_2'];
            $data['designation'] = $post['designation'];
            $data['tax_agent_name'] = $post['tax_agent_name'];
            $d_id = $post['d_id'];

            if ($d_id == '') {
                $inserted = $this->custom->insertData('gst_returns_declaration', $data);
            } else {
                $updated = $this->custom->updateRow('gst_returns_declaration', $data, ['d_id' => $d_id]);
            }

            if ($inserted) {
                set_flash_message('message', 'success', 'Declaration Saved');
            } elseif ($updated) {
                set_flash_message('message', 'success', 'Declaration Updated');
            } else {
                set_flash_message('message', 'danger', 'Declaration Save Error');
            }

            redirect('gst/iras_api_contact_info');
        } else {
            set_flash_message('message', 'danger', 'REQUEST ERROR');
            redirect('gst/iras_api_declaration');
        }
    }

    public function iras_api_contact_info()
    {
        is_logged_in('admin');
        has_permission();

        $ci_id = $this->get_contact_info_id();
        $contact_data = $this->custom->getSingleRow('gst_returns_contact_info', ['ci_id' => $ci_id]);
        $this->body_vars['ci_id'] = $contact_data->ci_id;
        $this->body_vars['name'] = $contact_data->name;
        $this->body_vars['phone'] = $contact_data->phone;
        $this->body_vars['email'] = $contact_data->email;

        $this->body_file = 'gst/iras_api_contact_info.php';
    }

    public function save_contact_info()
    {
        $post = $this->input->post();
        if ($post) {
            $data['name'] = $post['contact_person'];
            $data['phone'] = $post['contact_number'];
            $data['email'] = $post['contact_email'];
            $ci_id = $post['ci_id'];

            if ($ci_id == '') {
                $inserted = $this->custom->insertRow('gst_returns_contact_info', $data);
            } else {
                $updated = $this->custom->updateRow('gst_returns_contact_info', $data, ['ci_id' => $ci_id]);
            }

            if ($inserted) {
                set_flash_message('message', 'success', 'Contact Information Saved');
            } elseif ($updated) {
                set_flash_message('message', 'success', 'Contact Information Updated');
            } else {
                set_flash_message('message', 'danger', 'Contact Information Save Error');
            }

            redirect('gst/iras_api_fe_validation');
        } else {
            set_flash_message('message', 'danger', 'REQUEST ERROR');
            redirect('gst/iras_api_contact_info');
        }
    }

    // Front-end Validation
    public function iras_api_fe_validation()
    {
        is_logged_in('admin');
        has_permission();

        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);

        if ($filing_data->start_date != null && $filing_data->start_date != '' && $filing_data->form_type != '') {
            if ($filing_data->form_type == 'F7') {
                $this->validate_form_7();
            } else {
                $this->validate_form_5();
            }
        } else {
            set_flash_message('message', 'success', 'START WITH FILING INFO');
            redirect('gst/iras_api_filing_info');
        }
    }

    public function validate_form_5()
    {
        // Group Reasons

        $grp_id = $this->get_grp_reasons_id();
        $grp_data = $this->custom->getSingleRow('gst_returns_grp_reasons', ['grp_id' => $grp_id]);
        $this->body_vars['grp_id'] = $grp_data->grp_id;
        $this->body_vars['grp1BadDebtRecoveryChk'] = $grp_data->grp1BadDebtRecoveryChk;
        $this->body_vars['grp1PriorToRegChk'] = $grp_data->grp1PriorToRegChk;
        $this->body_vars['grp1OtherReasonChk'] = $grp_data->grp1OtherReasonChk;
        $this->body_vars['grp1OtherReasons'] = $grp_data->grp1OtherReasons;

        $this->body_vars['grp2TouristRefundChk'] = $grp_data->grp2TouristRefundChk;
        $this->body_vars['grp2AppvBadDebtReliefChk'] = $grp_data->grp2AppvBadDebtReliefChk;
        $this->body_vars['grp2CreditNotesChk'] = $grp_data->grp2CreditNotesChk;
        $this->body_vars['grp2OtherReasonsChk'] = $grp_data->grp2OtherReasonsChk;
        $this->body_vars['grp2OtherReasons'] = $grp_data->grp2OtherReasons;

        $this->body_vars['grp3CreditNotesChk'] = $grp_data->grp3CreditNotesChk;
        $this->body_vars['grp3OtherReasonsChk'] = $grp_data->grp3OtherReasonsChk;
        $this->body_vars['grp3OtherReasons'] = $grp_data->grp3OtherReasons;

        // Filing Info
        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $start_date = $filing_data->start_date;
        $end_date = $filing_data->end_date;
        $form_type = $filing_data->form_type;

        $this->body_vars['start_date'] = date('d-m-Y', strtotime($start_date));
        $this->body_vars['end_date'] = date('d-m-Y', strtotime($end_date));
        $this->body_vars['form_type'] = $form_type;

        // Form Values
        $fv_id = $this->get_form_5_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_5', ['fv_id' => $fv_id]);
        $box_1_value = $form_data->box_1_value;
        $box_2_value = $form_data->box_2_value;
        $box_3_value = $form_data->box_3_value;
        $box_4_value = $form_data->box_4_value;
        $box_5_value = $form_data->box_5_value;
        $box_6_value = $form_data->box_6_value;
        $box_7_value = $form_data->box_7_value;
        $box_8_value = $form_data->box_8_value;
        $box_9_value = $form_data->box_9_value;
        $box_10_option = $form_data->box_10_option;
        $box_10_value = $form_data->box_10_value;
        $box_11_option = $form_data->box_11_option;
        $box_11_value = $form_data->box_11_value;
        $box_12_option = $form_data->box_12_option;
        $box_12_value = $form_data->box_12_value;
        $box_13_value = $form_data->box_13_value;
        $box_14_option = $form_data->box_14_option;
        $box_14_value = $form_data->box_14_value;
        $box_15_option = $form_data->box_15_option;
        $box_15_value = $form_data->box_15_value;
        $box_16_option = $form_data->box_16_option;
        $box_16_value = $form_data->box_16_value;
        $box_17_option = $form_data->box_17_option;
        $box_17_value = $form_data->box_17_value;
        $box_18_value = $form_data->box_18_value;
        $box_19_value = $form_data->box_19_value;
        $box_20_value = $form_data->box_20_value;
        $box_21_value = $form_data->box_21_value;
        $this->body_vars['box_1_value'] = $box_1_value;
        $this->body_vars['box_2_value'] = $box_2_value;
        $this->body_vars['box_3_value'] = $box_3_value;
        $this->body_vars['box_4_value'] = $box_4_value;
        $this->body_vars['box_5_value'] = $box_5_value;
        $this->body_vars['box_6_value'] = $box_6_value;
        $this->body_vars['box_7_value'] = $box_7_value;
        $this->body_vars['box_8_value'] = $box_8_value;
        $this->body_vars['box_9_value'] = $box_9_value;
        $this->body_vars['box_10_option'] = $box_10_option;
        $this->body_vars['box_10_value'] = $box_10_value;
        $this->body_vars['box_11_option'] = $box_11_option;
        $this->body_vars['box_11_value'] = $box_11_value;
        $this->body_vars['box_12_option'] = $box_12_option;
        $this->body_vars['box_12_value'] = $box_12_value;
        $this->body_vars['box_13_value'] = $box_13_value;
        $this->body_vars['box_14_option'] = $box_14_option;
        $this->body_vars['box_14_value'] = $box_14_value;
        $this->body_vars['box_15_option'] = $box_15_option;
        $this->body_vars['box_15_value'] = $box_15_value;
        $this->body_vars['box_16_option'] = $box_16_option;
        $this->body_vars['box_16_value'] = $box_16_value;
        $this->body_vars['box_17_option'] = $box_17_option;
        $this->body_vars['box_17_value'] = $box_17_value;
        $this->body_vars['box_18_value'] = $box_18_value;
        $this->body_vars['box_19_value'] = $box_19_value;
        $this->body_vars['box_20_value'] = $box_20_value;
        $this->body_vars['box_21_value'] = $box_21_value;

        // Front-end Validation
        // Auto summation/ pre-population
        // Box 4: Total value of (1) + (2) + (3)
        // Automatically computed after filing in the amounts for Box 1, Box 2 and Box 3

        // Front-end Validation
        // Auto summation
        // Box 8: Equals: Net GST to be paid to/ claimed from IRAS
        // Automatically computed after filing in the amounts for Box 6 and Box 7

        // Front-end Validation
        // Pre-population
        // Box 16: Net GST per box 8 above
        // Automatically computed after Box 8 is populated

        // Front-end Validation
        // Auto summation
        // Box 18: Equals: Total tax to be paid to/ claimed from IRAS
        // Automatically computed after filing in the amount for Box 17

        // SNO :: 5 || Front-end Validation ** 2 **
        // Box 7 > 0 && Box 3 <> 0 && any of the supplies contains Non-Regulation 33 Exempt Supplies (Tax Code ESN33)
        $fe_val_2_sql = "SELECT gstcate FROM gst WHERE date BETWEEN '".$star_date."' AND '".$end_date."' AND gstcate = 'ESN33'";
        $fe_val_2_query = $this->db->query($fe_val_2_sql);
        $fe_val_2_data = $fe_val_2_query->result();
        $esn33_count = 0;
        foreach ($fe_val_2_data as $key => $value) {
            ++$esn33_count;
        }
        $fe_val_2 = false;
        if ($box_7_value > 0 && $box_3_value != 0 && $esn33_count > 0) {
            $fe_val_2 = true;
        }
        $this->body_vars['fe_val_2'] = $fe_val_2;

        // SNO :: 6 || Front-end Validation ** 3 **
        // GST F5/F8 :: Box 13 is null
        $fe_val_3 = false;
        if ($box_13_value == null) {
            $fe_val_3 = true;
        }
        $this->body_vars['fe_val_3'] = false;

        // SNO :: 7 to 10 || Front-end Validation ** 4 **
        // 1) Box 1 > 0 & Box 6 < 0
        // 2) Box 1 <> 0 & Box 6 = 0
        // 3) Box 1 = 0 & Box 6 <> 0
        // 4) Box 1 < Box 6
        // 5) Box 1 = Box 6
        $fe_val_4 = false;
        $fe_val_4_text = '';
        $fe_val_4_rule = '';
        if ($box_1_value > 0 && $box_6_value < 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 > 0 & Box 6 < 0';
            $fe_val_4_text = 'Standard-rated supplies is in positive value, output tax should not be in negative value. Please re-enter the value of output tax due.';
        } elseif ($box_1_value != 0 && $box_6_value == 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 <> 0 & Box 6 = 0';
            $fe_val_4_text = 'Standard-rated supplies is completed, output tax should not be NIL. Please re-enter the value of output tax due.';
        } elseif ($box_1_value == 0 && $box_6_value != 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 = 0 & Box 6 <> 0';
            $fe_val_4_text = 'As output tax is completed, standard-rated supplies should not be NIL. Please re-enter the value of standard-rated supplies.';
        } elseif ($box_1_value < $box_6_value) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 < Box 6';
            $fe_val_4_text = 'Output tax should be less than standard-rated supplies. Please re-enter the value of output tax due.';
        } elseif ($box_1_value == $box_6_value) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 = Box 6';
            $fe_val_4_text = 'Output tax should be less than standard-rated supplies. Please re-enter the value of output tax due.';
        }

        $this->body_vars['fe_val_4'] = $fe_val_4;
        $this->body_vars['fe_val_4_rule'] = $fe_val_4_rule;
        $this->body_vars['fe_val_4_text'] = $fe_val_4_text;

        // SNO :: 11 to 13 || Front-end Validation ** 5 **
        // GST F5 / GSTF8
        // 1. Box 5 < Box 7
        // 2. Box 7 <> 0 & Box 5 = 0
        // 3. Box 7 <> 0 & Box 9 = Box 5
        $fe_val_5 = false;
        $fe_val_5_text = '';
        $fe_val_5_rule = '';
        $fe_val_5_box_display = '';
        if ($box_5_value < $box_7_value) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 5 < Box 7';
            $fe_val_5_text = 'Input tax and refunds claimed should be less than taxable purchases. Please re-enter the value of input tax and refunds claimed.';
        } elseif ($box_7_value != 0 && $box_5_value == 0) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 7 <> 0 & Box 5 = 0';
            $fe_val_5_text = 'Input tax and refunds claimed has been completed, taxable purchases should not be NIL. Please re-enter the value of taxable purchases.';
        } elseif ($box_7_value != 0 && $box_9_value == $box_5_value) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 7 <> 0 & Box 9 = Box 5';
            $fe_val_5_text = 'Input tax and refunds claimed should be NIL as you had declared the same value in taxable purchases and MES/3PL/Other Approves Schemes. Please re-enter the value of input tax and refunds claimed.';
            $fe_val_5_box_display = 'Box_9';
        }

        $this->body_vars['fe_val_5'] = $fe_val_5;
        $this->body_vars['fe_val_5_rule'] = $fe_val_5_rule;
        $this->body_vars['fe_val_5_text'] = $fe_val_5_text;
        $this->body_vars['fe_val_5_box_display'] = $fe_val_5_box_display;

        // SNO :: 14 || Front-end Validation ** 6 **
        // GST F5/F8 :: Box 9 <> 0 & Box 5 < Box 9
        $fe_val_6 = false;
        $fe_val_6_rule = '';
        if ($box_9_value != 0 && $box_5_value < $box_9_value) {
            $fe_val_6 = true;
            $fe_val_6_rule = 'Box 9 <> 0 & Box 5 < Box 9';
        }
        $this->body_vars['fe_val_6'] = $fe_val_6;
        $this->body_vars['fe_val_6_rule'] = $fe_val_6_rule;

        // SNO :: 15 || Front-end Validation ** 7 **
        // If taxpayer selects “Yes” for the
        // 1. Box 10 : Tourist Refund  OR
        // 2. Box 11 : Pre-registration claim OR
        // 3. Box 12 : Bad debt relief claim when BOX 7 is NIL
        $fe_val_7 = false;
        $fe_val_7_rule = '';
        $fe_val_7_text = '';
        $fe_val_7_box_display = '';

        if ($box_10_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 10: Did you claim for GST you had refunded to tourists? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Tourist Refund.';
            $fe_val_7_box_display = 'Box_10';
        } elseif ($box_11_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 11: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Bad debt relief.';
            $fe_val_7_box_display = 'Box_11';
        } elseif ($box_12_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 12: Did you make any pre-registration claims? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Pre-registration.';
            $fe_val_7_box_display = 'Box_12';
        }

        $this->body_vars['fe_val_7'] = $fe_val_7;
        $this->body_vars['fe_val_7_rule'] = $fe_val_7_rule;
        $this->body_vars['fe_val_7_text'] = $fe_val_7_text;
        $this->body_vars['fe_val_7_box_display'] = $fe_val_7_box_display;

        // SNO :: 16 || Front-end Validation ** 8 **
        // If taxpayer selects “No” for
        // GST F5 / F8
        // - Box 10: Did you claim for GST you had refunded to tourists? when Box 10 <> 0;
        // Or
        // - Box 11: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 11 <> 0;
        // Or
        // - Box 12: Did you make any pre-registration claims? when Box 12 is <> 0

        $fe_val_8 = false;
        $fe_val_8_rule = '';
        $fe_val_8_text = '';
        $fe_val_8_box_display = '';
        if ($box_10_option != '1' && $box_10_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 10: Did you claim for GST you had refunded to tourists? when Box 10 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Tourist Refund.';
            $fe_val_8_box_display = 'Box_10';
        } elseif ($box_11_option != '1' && $box_11_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 11: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 11 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Bad debt relief';
            $fe_val_8_box_display = 'Box_11';
        } elseif ($box_12_option != '1' && $box_12_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 12: Did you make any pre-registration claims? When Box 12 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Pre-registration';
            $fe_val_8_box_display = 'Box_12';
        }

        $this->body_vars['fe_val_8'] = $fe_val_8;
        $this->body_vars['fe_val_8_rule'] = $fe_val_8_rule;
        $this->body_vars['fe_val_8_text'] = $fe_val_8_text;
        $this->body_vars['fe_val_8_box_display'] = $fe_val_8_box_display;

        // SNO :: 17 to 18 || Front-end Validation ** 9 **
        // If taxpayer selects “Yes” for return with accounting ending before/after 1 Jan 2023:
        // GST F5/F8
        // - Box 14: Did you import services and/or low-value goods subject to GST under reverse charge?
        //   OR
        // - Box 15: Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?
        $fe_val_9 = false;
        $fe_val_9_rule = '';
        $fe_val_9_box_display = '';
        if ($end_date < '2023-01-01') {
            if ($box_14_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you import services subject to GST under Reverse Charge?';
                $fe_val_9_box_display = 'Box_14';
            } elseif ($box_15_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you operate an electronic marketplace to supply digital services subject to GST on behalf of third-party suppliers?';
                $fe_val_9_box_display = 'Box_15';
            }
            $fe_val_9_txt = 'If taxpayer selects “Yes” for return with accounting period before 1 Jan 2023 : ';
        } elseif ($end_date >= '2023-01-01') {
            if ($box_14_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you import services and/or low-value goods subject to GST under reverse charge?';
                $fe_val_9_box_display = 'Box_14';
            } elseif ($box_15_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?';
                $fe_val_9_box_display = 'Box_15';
            }
            $fe_val_9_txt = 'If taxpayer selects “Yes” for return with accounting period on/after 1 Jan 2023 : ';
        }

        if ($start_date < '2020-01-01') {
            // System should prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_9_error_display = true;
        } else {
            // System should not prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_9_error_display = false;
        }

        $this->body_vars['fe_val_9'] = $fe_val_9;
        $this->body_vars['fe_val_9_txt'] = $fe_val_9_txt;
        $this->body_vars['fe_val_9_rule'] = $fe_val_9_rule;
        $this->body_vars['fe_val_9_box_display'] = $fe_val_9_box_display;
        $this->body_vars['fe_val_9_error_display'] = $fe_val_9_error_display;

        // SNO :: 19 || Front-end Validation ** 10 **
        // If taxpayer selects “Yes” for return with accounting ending before/after 1 Jan 2023:
        // GST F5/F8
        // - Box 16: Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?
        //   OR
        // - Box 17: Did you make your own supply of imported low-value goods that is subject to GST?
        $fe_val_10 = false;
        $fe_val_10_use = false;
        $fe_val_10_rule = '';
        $fe_val_10_box_display = '';
        if ($end_date < '2023-01-01') {
            $fe_val_10_use = false;
            if ($box_16_option == '1') {
                $fe_val_10 = true;
            } elseif ($box_17_option == '1') {
                $fe_val_10 = true;
            }

            // NOT USED SINCE THIS iS NEW FROM JAN 2023 -- System should prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_10_error_display = true;
        } elseif ($end_date >= '2023-01-01') {
            $fe_val_10_use = true;
            if ($box_16_option == '1') {
                $fe_val_10 = true;
                $fe_val_10_rule = 'Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?';
                $fe_val_10_box_display = 'Box_16';
            } elseif ($box_17_option == '1') {
                $fe_val_10 = true;
                $fe_val_10_rule = 'Did you make your own supply of imported low-value goods that is subject to GST?';
                $fe_val_10_box_display = 'Box_17';
            }

            // System should not prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_10_error_display = false;
        }

        $this->body_vars['fe_val_10'] = $fe_val_10;
        $this->body_vars['fe_val_10_use'] = $fe_val_10_use;
        $this->body_vars['fe_val_10_rule'] = $fe_val_10_rule;
        $this->body_vars['fe_val_10_box_display'] = $fe_val_10_box_display;
        $this->body_vars['fe_val_10_error_display'] = $fe_val_10_error_display;

        // SNO :: 20 || Front-end Validation ** 11 **
        // GST F5/F8
        // Box 19 < 0 and Box 21 > 0
        $fe_val_11 = false;
        $fe_val_11_rule = '';
        if ($box_19_value < 0 && $box_21_value > 0) {
            $fe_val_11 = true;
            $fe_val_11_rule = 'Box 19 < 0 and Box 21 > 0';
        }
        $this->body_vars['fe_val_11'] = $fe_val_11;
        $this->body_vars['fe_val_11_rule'] = $fe_val_11_rule;

        // SNO :: 21 || Front-end Validation ** 12 **
        // GST F5/F8
        // Box 19 = 0 && (Box 21 > 0 || Box 21 < 0)
        $fe_val_12 = false;
        $fe_val_12_rule = '';
        if ($box_19_value == 0 && $box_21_value > 0) {
            $fe_val_12 = true;
            $fe_val_12_rule = 'Box 19 = 0 and Box 21 > 0';
        } elseif ($box_19_value == 0 && $box_21_value < 0) {
            $fe_val_12 = true;
            $fe_val_12_rule = 'Box 19 = 0 and Box 21 < 0';
        }
        $this->body_vars['fe_val_12'] = $fe_val_12;
        $this->body_vars['fe_val_12_rule'] = $fe_val_12_rule;

        // SNO :: 22 || Front-end Validation 13
        // GST F5/F8
        // Box 21 = 0 && (Box 19 < 0 || Box 19 > 0)
        $fe_val_13 = false;
        $fe_val_13_rule = '';
        if ($box_21_value == 0 && $box_19_value < 0) {
            $fe_val_13 = true;
            $fe_val_13_rule = 'Box 21 = 0 and Box 19 < 0';
        } elseif ($box_21_value == 0 && $box_19_value > 0) {
            $fe_val_13 = true;
            $fe_val_13_rule = 'Box 21 = 0 and Box 19 > 0';
        }
        $this->body_vars['fe_val_13'] = $fe_val_13;
        $this->body_vars['fe_val_13_rule'] = $fe_val_13_rule;

        // SNO :: 23 || Front-end Validation 14
        // GST F5/F8
        // i) Box 21 < 0 && Box 19 > 0
        // Or
        // ii) Box 21 < 0 && Box 19 < 0 && Box 19 <= Box 21
        // Or
        // iii) Box 21 > 0 && Box 19 > 0 && Box 19 >= Box 21

        $fe_val_14 = false;
        $fe_val_14_rule = '';
        if ($box_21_value < 0 && $box_19_value > 0) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 21 < 0 and Box 19 > 0';
        } elseif ($box_21_value < 0 && $box_19_value < 0 && $box_19_value <= $box_21_value) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 21 < 0 and Box 19 < 0 and Box 19 <= Box 21';
        } elseif ($box_21_value > 0 && $box_19_value > 0 && $box_19_value >= $box_21_value) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 21 > 0 and Box 19 > 0 and Box 19 >= Box 21';
        }
        $this->body_vars['fe_val_14'] = $fe_val_14;
        $this->body_vars['fe_val_14_rule'] = $fe_val_14_rule;

        // SNO :: 24 || Front-end Validation 15
        // If Contact Telephone Number is not in 8-digit format
        $ci_id = $this->get_contact_info_id();
        $contact_info = $this->custom->getSingleRow('gst_returns_contact_info', ['ci_id' => $ci_id]);
        $fe_val_15 = false;
        if (strlen($contact_info->phone) != 8) {
            $fe_val_15 = true;
        }
        $this->body_vars['fe_val_15'] = $fe_val_15;

        $fe_val_success = true;
        if ($fe_val_2 || $fe_val_3 || $fe_val_4 || $fe_val_5 || $fe_val_6 || $fe_val_7 || $fe_val_8 || $fe_val_9 || $fe_val_10 || $fe_val_11 || $fe_val_12 || $fe_val_13 || $fe_val_14 || $fe_val_15) {
            $fe_val_success = false;
        }
        $this->body_vars['fe_val_success'] = $fe_val_success;

        // If the Front-End Validation is failed, then update the status so that user can not generate JSON with errors
        $json_status = '';
        if ($fe_val_success) {
            $json_status = 'VALID';
        }
        $updated = $this->custom->updateRow('gst_returns_filing_info', ['json' => $json_status], ['fi_id' => $fi_id]);

        $this->body_file = 'gst/iras_api_fe_validation.php';
    }

    public function validate_form_7()
    {
        // Group Reasons
        $grp_id = $this->get_grp_reasons_id();
        $grp_reasons = $this->custom->getSingleRow('gst_returns_grp_reasons', ['grp_id' => $grp_id]);

        $this->body_vars['grp_id'] = $grp_id = $grp_reasons->grp_id;
        $this->body_vars['grp1BadDebtRecoveryChk'] = $grp_reasons->grp1BadDebtRecoveryChk;
        $this->body_vars['grp1PriorToRegChk'] = $grp_reasons->grp1PriorToRegChk;
        $this->body_vars['grp1OtherReasonChk'] = $grp_reasons->grp1OtherReasonChk;
        $this->body_vars['grp1OtherReasons'] = $grp_reasons->grp1OtherReasons;

        $this->body_vars['grp2TouristRefundChk'] = $grp_reasons->grp2TouristRefundChk;
        $this->body_vars['grp2AppvBadDebtReliefChk'] = $grp_reasons->grp2AppvBadDebtReliefChk;
        $this->body_vars['grp2CreditNotesChk'] = $grp_reasons->grp2CreditNotesChk;
        $this->body_vars['grp2OtherReasonsChk'] = $grp_reasons->grp2OtherReasonsChk;
        $this->body_vars['grp2OtherReasons'] = $grp_reasons->grp2OtherReasons;

        $this->body_vars['grp3CreditNotesChk'] = $grp_reasons->grp3CreditNotesChk;
        $this->body_vars['grp3OtherReasonsChk'] = $grp_reasons->grp3OtherReasonsChk;
        $this->body_vars['grp3OtherReasons'] = $grp_reasons->grp3OtherReasons;

        // Filing Info
        $fi_id = $this->get_filing_info_id();
        $gst_returns_filing_info = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $start_date = $gst_returns_filing_info->start_date;
        $end_date = $gst_returns_filing_info->end_date;
        $form_type = 'F7';

        $this->body_vars['start_date'] = date('d-m-Y', strtotime($start_date));
        $this->body_vars['end_date'] = date('d-m-Y', strtotime($end_date));
        $this->body_vars['form_type'] = $form_type;

        // Form Values
        $fv7_id = $this->get_form_7_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_7', ['fv7_id' => $fv7_id]);
        $box_1_value = $form_data->box_1_value;
        $box_2_value = $form_data->box_2_value;
        $box_3_value = $form_data->box_3_value;
        $box_4_value = $form_data->box_4_value;
        $box_5_value = $form_data->box_5_value;
        $box_6_value = $form_data->box_6_value;
        $box_7_value = $form_data->box_7_value;
        $box_8_value = $form_data->box_8_value;
        $box_9_value = $form_data->box_9_value;
        $box_10_value = $form_data->box_10_value;
        $box_11_value = $form_data->box_11_value;

        $box_12_option = $form_data->box_12_option;
        $box_12_value = $form_data->box_12_value;

        $box_13_option = $form_data->box_13_option;
        $box_13_value = $form_data->box_13_value;

        $box_14_option = $form_data->box_14_option;
        $box_14_value = $form_data->box_14_value;

        $box_15_value = $form_data->box_15_value;

        $box_16_option = $form_data->box_16_option;
        $box_16_value = $form_data->box_16_value;

        $box_17_option = $form_data->box_17_option;
        $box_17_value = $form_data->box_17_value;

        $box_18_option = $form_data->box_18_option;
        $box_18_value = $form_data->box_18_value;

        $box_19_option = $form_data->box_19_option;
        $box_19_value = $form_data->box_19_value;

        $box_20_value = $form_data->box_20_value;
        $box_21_value = $form_data->box_21_value;
        $box_22_value = $form_data->box_22_value;
        $box_23_value = $form_data->box_23_value;
        $box_24_value = $form_data->box_24_value;
        $box_25_value = $form_data->box_25_value;
        $box_26_value = $form_data->box_26_value;

        $this->body_vars['box_1_value'] = $box_1_value;
        $this->body_vars['box_2_value'] = $box_2_value;
        $this->body_vars['box_3_value'] = $box_3_value;
        $this->body_vars['box_4_value'] = $box_4_value;
        $this->body_vars['box_5_value'] = $box_5_value;
        $this->body_vars['box_6_value'] = $box_6_value;
        $this->body_vars['box_7_value'] = $box_7_value;
        $this->body_vars['box_8_value'] = $box_8_value;
        $this->body_vars['box_9_value'] = $box_9_value;

        $this->body_vars['box_10_value'] = $box_10_value;
        $this->body_vars['box_11_value'] = $box_11_value;

        $this->body_vars['box_12_option'] = $box_12_option;
        $this->body_vars['box_12_value'] = $box_12_value;
        $this->body_vars['box_13_option'] = $box_13_option;
        $this->body_vars['box_13_value'] = $box_13_value;
        $this->body_vars['box_14_option'] = $box_14_option;
        $this->body_vars['box_14_value'] = $box_14_value;

        $this->body_vars['box_15_value'] = $box_15_value;

        $this->body_vars['box_16_option'] = $box_16_option;
        $this->body_vars['box_16_value'] = $box_16_value;
        $this->body_vars['box_17_option'] = $box_17_option;
        $this->body_vars['box_17_value'] = $box_17_value;

        $this->body_vars['box_18_option'] = $box_18_option;
        $this->body_vars['box_18_value'] = $box_18_value;

        $this->body_vars['box_19_option'] = $box_19_option;
        $this->body_vars['box_19_value'] = $box_19_value;

        $this->body_vars['box_20_value'] = $box_20_value;
        $this->body_vars['box_21_value'] = $box_21_value;
        $this->body_vars['box_22_value'] = $box_22_value;
        $this->body_vars['box_23_value'] = $box_23_value;
        $this->body_vars['box_24_value'] = $box_24_value;
        $this->body_vars['box_25_value'] = $box_25_value;
        $this->body_vars['box_26_value'] = $box_26_value;

        // Front-end Validation
        // Auto summation/ pre-population
        // Box 4: Total value of (1) + (2) + (3)
        // Automatically computed after filing in the amounts for Box 1, Box 2 and Box 3

        // Front-end Validation
        // Auto summation
        // Box 8: Equals: Net GST to be paid to/ claimed from IRAS
        // Automatically computed after filing in the amounts for Box 6 and Box 7

        // Front-end Validation
        // Pre-population
        // Box 16: Net GST per box 8 above
        // Automatically computed after Box 8 is populated

        // Front-end Validation
        // Auto summation
        // Box 18: Equals: Total tax to be paid to/ claimed from IRAS
        // Automatically computed after filing in the amount for Box 17

        // SNO :: 5 || Front-end Validation ** 2 **
        // Box 7 > 0 && Box 3 <> 0 && any of the supplies contains Non-Regulation 33 Exempt Supplies (Tax Code ESN33)
        $fe_val_2_sql = "SELECT gstcate FROM gst WHERE date BETWEEN '".$star_date."' AND '".$end_date."' AND gstcate = 'ESN33'";
        $fe_val_2_query = $this->db->query($fe_val_2_sql);
        $fe_val_2_data = $fe_val_2_query->result();
        $esn33_count = 0;
        foreach ($fe_val_2_data as $key => $value) {
            ++$esn33_count;
        }
        $fe_val_2 = false;
        if ($box_7_value > 0 && $box_3_value != 0 && $esn33_count > 0) {
            $fe_val_2 = true;
        }
        $this->body_vars['fe_val_2'] = $fe_val_2;

        // SNO :: 6 || Front-end Validation ** 3 **
        // GST F7 :: Box 15 is null
        $fe_val_3 = false;
        if ($box_15_value == null) {
            $fe_val_3 = true;
        }
        $this->body_vars['fe_val_3'] = $fe_val_3;

        // SNO :: 7 to 10 || Front-end Validation ** 4 **
        // 7) Box 1 > 0 & Box 6 < 0
        // 8) Box 1 <> 0 & Box 6 = 0
        // 9) Box 1 = 0 & Box 6 <> 0
        // 10) Box 1 < Box 6 OR Box 1 = Box 6
        $fe_val_4 = false;
        $fe_val_4_text = '';
        $fe_val_4_rule = '';
        if ($box_1_value > 0 && $box_6_value < 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 > 0 & Box 6 < 0';
            $fe_val_4_text = 'Standard-rated supplies is in positive value, output tax should not be in negative value. Please re-enter the value of output tax due.';
        } elseif ($box_1_value != 0 && $box_6_value == 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 <> 0 & Box 6 = 0';
            $fe_val_4_text = 'Standard-rated supplies is completed, output tax should not be NIL. Please re-enter the value of output tax due.';
        } elseif ($box_1_value == 0 && $box_6_value != 0) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 = 0 & Box 6 <> 0';
            $fe_val_4_text = 'As output tax is completed, standard-rated supplies should not be NIL. Please re-enter the value of standard-rated supplies.';
        } elseif ($box_1_value < $box_6_value) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 < Box 6';
            $fe_val_4_text = 'Output tax should be less than standard-rated supplies. Please re-enter the value of output tax due.';
        } elseif ($box_1_value == $box_6_value) {
            $fe_val_4 = true;
            $fe_val_4_rule = 'Box 1 = Box 6';
            $fe_val_4_text = 'Output tax should be less than standard-rated supplies. Please re-enter the value of output tax due.';
        }
        $this->body_vars['fe_val_4'] = $fe_val_4 = $fe_val_4;
        $this->body_vars['fe_val_4_rule'] = $fe_val_4_rule = $fe_val_4_rule;
        $this->body_vars['fe_val_4_text'] = $fe_val_4_text = $fe_val_4_text;

        // SNO :: 11 to 13 || Front-end Validation ** 5 **
        // GST F7
        // 1. Box 5 < Box 7
        // 2. Box 7 <> 0 & Box 5 = 0
        // 3. Box 7 <> 0 & Box 11 = Box 5
        $fe_val_5 = false;
        $fe_val_5_text = '';
        $fe_val_5_rule = '';
        $fe_val_5_box_display = '';
        if ($box_5_value < $box_7_value) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 5 < Box 7';
            $fe_val_5_text = 'Input tax and refunds claimed should be less than taxable purchases. Please re-enter the value of input tax and refunds claimed.';
        } elseif ($box_7_value != 0 && $box_5_value == 0) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 7 <> 0 & Box 5 = 0';
            $fe_val_5_text = 'Input tax and refunds claimed has been completed, taxable purchases should not be NIL. Please re-enter the value of taxable purchases.';
        } elseif ($box_7_value != 0 && $box_11_value == $box_5_value) {
            $fe_val_5 = true;
            $fe_val_5_rule = 'Box 7 <> 0 & Box 11 = Box 5';
            $fe_val_5_text = 'Input tax and refunds claimed should be NIL as you had declared the same value in taxable purchases and MES/3PL/Other Approves Schemes. Please re-enter the value of input tax and refunds claimed.';
            $fe_val_5_box_display = 'Box_11';
        }
        $this->body_vars['fe_val_5'] = $fe_val_5;
        $this->body_vars['fe_val_5_rule'] = $fe_val_5_rule;
        $this->body_vars['fe_val_5_text'] = $fe_val_5_text;
        $this->body_vars['fe_val_5_box_display'] = $fe_val_5_box_display;

        // SNO :: 14 || Front-end Validation ** 6 **
        // GST F7 :: Box 11 <> 0 & Box 5 < Box 11
        $fe_val_6 = false;
        $fe_val_6_rule = '';
        if ($box_11_value != 0 && $box_5_value < $box_11_value) {
            $fe_val_6 = true;
            $fe_val_6_rule = 'Box 11 <> 0 & Box 5 < Box 11';
        }
        $this->body_vars['fe_val_6'] = $fe_val_6;
        $this->body_vars['fe_val_6_rule'] = $fe_val_6_rule;

        // SNO :: 15 || Front-end Validation ** 7 **
        // If taxpayer selects “Yes” for the
        // 1. Box 12 : Did you claim for GST you had refunded to tourists? OR
        // 2. Box 13 : Did you make any bad debt relief claims and/or refund claims for reverse charge transactions? OR
        // 3. Box 14 : Did you make any pre-registration claims?
        // WHEN BOX 7 is NIL
        $fe_val_7 = false;
        $fe_val_7_rule = '';
        $fe_val_7_text = '';
        $fe_val_7_box_display = '';
        if ($box_12_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 12: Did you claim for GST you had refunded to tourists? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Tourist Refund.';
            $fe_val_7_box_display = 'Box_12';
        } elseif ($box_13_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 13: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Bad debt relief.';
            $fe_val_7_box_display = 'Box_13';
        } elseif ($box_14_option == '1' && $box_7_value == 0) {
            $fe_val_7 = true;
            $fe_val_7_rule = 'If user selects “Yes” for <br />Box 14: Did you make any pre-registration claims? When Box 7 = 0';
            $fe_val_7_text = 'Input tax and refund claims should not be Nil if you are claiming for Pre-registration.';
            $fe_val_7_box_display = 'Box_14';
        }

        $this->body_vars['fe_val_7'] = $fe_val_7;
        $this->body_vars['fe_val_7_rule'] = $fe_val_7_rule;
        $this->body_vars['fe_val_7_text'] = $fe_val_7_text;
        $this->body_vars['fe_val_7_box_display'] = $fe_val_7_box_display;

        // SNO :: 16 || Front-end Validation ** 8 **
        // If taxpayer selects “No” for
        // GST F7
        // - Box 12: Did you claim for GST you had refunded to tourists? when Box 12 <> 0;
        // Or
        // - Box 13: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 13 <> 0;
        // Or
        // - Box 14: Did you make any pre-registration claims? when Box 14 <> 0

        $fe_val_8 = false;
        $fe_val_8_rule = '';
        $fe_val_8_text = '';
        $fe_val_8_box_display = '';
        if ($box_12_option != '1' && $box_12_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 12: Did you claim for GST you had refunded to tourists? When Box 12 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Tourist Refund.';
            $fe_val_8_box_display = 'Box_12';
        } elseif ($box_13_option != '1' && $box_13_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 13: Did you make any bad debt relief claims and/ or refund claims for reverse charge transactions? When Box 13 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Bad debt relief.';
            $fe_val_8_box_display = 'Box_13';
        } elseif ($box_14_option != '1' && $box_14_value != 0) {
            $fe_val_8 = true;
            $fe_val_8_rule = 'If user selects “No” for <br />Box 14: Did you make any pre-registration claims? When Box 14 <> 0';
            $fe_val_8_text = 'Please select Yes if you are claiming for Pre-registration.';
            $fe_val_8_box_display = 'Box_14';
        }

        $this->body_vars['fe_val_8'] = $fe_val_8 = $fe_val_8;
        $this->body_vars['fe_val_8_rule'] = $fe_val_8_rule;
        $this->body_vars['fe_val_8_text'] = $fe_val_8_text;
        $this->body_vars['fe_val_8_box_display'] = $fe_val_8_box_display;

        // SNO :: 17 to 18 || Front-end Validation ** 9 **
        // If taxpayer selects “Yes” for return covering accounting period ending before/after 1 Jan 2023:
        // GST F7
        // - Box 16: Did you import services subject to GST under reverse charge?;
        //   OR
        // - Box 17: Did you operate an electronic marketplace to supply digital services subject to GST on behalf of third-party suppliers?
        $fe_val_9 = false;
        $fe_val_9_rule = '';
        $fe_val_9_box_display = '';
        $fe_val_9_txt = '';
        if ($end_date < '2023-01-01') {
            if ($box_16_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you import services subject to GST under Reverse Charge?';
                $fe_val_9_box_display = 'Box_16';
            } elseif ($box_17_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you operate an electronic marketplace to supply digital services subject to GST on behalf of third-party suppliers?';
                $fe_val_9_box_display = 'Box_17';
            }
            $fe_val_9_txt = 'If taxpayer selects “Yes” for return with accounting period before 1 Jan 2023: ';
        } elseif ($end_date >= '2023-01-01') {
            if ($box_16_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you import services and/ or low-value goods subject to GST under Reverse Charge?';
                $fe_val_9_box_display = 'Box_16';
            } elseif ($box_17_option == '1') {
                $fe_val_9 = true;
                $fe_val_9_rule = 'Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?';
                $fe_val_9_box_display = 'Box_17';
            }
            $fe_val_9_txt = 'If taxpayer selects “Yes” for return with accounting period on/after 1 Jan 2023: ';
        }

        if ($start_date < '2020-01-01') {
            // System should prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_9_error_display = true;
        } else {
            // System should not prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_9_error_display = false;
        }

        $this->body_vars['fe_val_9'] = $fe_val_9;
        $this->body_vars['fe_val_9_rule'] = $fe_val_9_rule;
        $this->body_vars['fe_val_9_txt'] = $fe_val_9_txt;
        $this->body_vars['fe_val_9_box_display'] = $fe_val_9_box_display;
        $this->body_vars['fe_val_9_error_display'] = $fe_val_9_error_display;

        // SNO :: 19 || Front-end Validation ** 10 **
        // If taxpayer selects “Yes” for return with accounting period ending before 1 Jan 2023:
        // GST F7
        // - Box 18: Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?
        // - Box 19: Did you make your own supply of imported low-value goods that is subject to GST?
        $fe_val_10 = false;
        $fe_val_10_use = false;
        $fe_val_10_rule = '';
        $fe_val_10_box_display = '';
        if ($end_date < '2023-01-01') {
            $fe_val_10_use = false;
            if ($box_18_option == '1') {
                $fe_val_10 = true;
            } elseif ($box_19_option == '1') {
                $fe_val_10 = true;
            }

            // NOT USED SINCE THIS iS NEW FROM JAN 2023 -- System should prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_10_error_display = true;
        } elseif ($end_date >= '2023-01-01') {
            $fe_val_10_use = true;
            if ($box_18_option == '1') {
                $fe_val_10 = true;
                $fe_val_10_rule = 'Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?';
                $fe_val_10_box_display = 'Box_18';
            } elseif ($box_19_option == '1') {
                $fe_val_10 = true;
                $fe_val_10_rule = 'Did you make your own supply of imported low-value goods that is subject to GST?';
                $fe_val_10_box_display = 'Box_19';
            }

            // System should not prompt “invalid input” as reverse charge has taken effect from 1 Jan 2020.
            $fe_val_10_error_display = false;
        }
        $this->body_vars['fe_val_10'] = $fe_val_10;
        $this->body_vars['fe_val_10_use'] = $fe_val_10_use;
        $this->body_vars['fe_val_10_rule'] = $fe_val_10_rule;
        $this->body_vars['fe_val_10_box_display'] = $fe_val_10_box_display;
        $this->body_vars['fe_val_10_error_display'] = $fe_val_10_error_display;

        // SNO :: 20 || Front-end Validation ** 11 **
        // GST F7
        // Box 20 < 0 and Box 26 > 0
        $fe_val_11 = false;
        $fe_val_11_rule = '';
        if ($box_20_value < 0 && $box_26_value > 0) {
            $fe_val_11 = true;
            $fe_val_11_rule = 'Box 20 < 0 and Box 26 > 0';
        }
        $this->body_vars['fe_val_11'] = $fe_val_11;
        $this->body_vars['fe_val_11_rule'] = $fe_val_11_rule;

        // SNO :: 21 || Front-end Validation 12
        // GST F7
        // Box 20 = 0 && (Box 26 < 0 || Box 26 > 0)
        $fe_val_12 = false;
        $fe_val_12_rule = '';
        if ($box_20_value == 0 && $box_26_value > 0) {
            $fe_val_12 = true;
            $fe_val_12_rule = 'Box 20 = 0 && Box 26 > 0';
        } elseif ($box_20_value == 0 && $box_26_value < 0) {
            $fe_val_12 = true;
            $fe_val_12_rule = 'Box 20 = 0 && Box 26 < 0';
        }
        $this->body_vars['fe_val_12'] = $fe_val_12;
        $this->body_vars['fe_val_12_rule'] = $fe_val_12_rule;

        // SNO :: 22 || Front-end Validation 13
        // GST F7
        // Box 26 = 0 and (Box 20 < 0 or Box 20 > 0)
        $fe_val_13 = false;
        $fe_val_13_rule = '';
        if ($box_26_value == 0 && $box_20_value < 0) {
            $fe_val_13 = true;
            $fe_val_13_rule = 'Box 26 = 0 and Box 20 < 0';
        } elseif ($box_26_value == 0 && $box_20_value > 0) {
            $fe_val_13 = true;
            $fe_val_13_rule = 'Box 26 = 0 and Box 20 > 0';
        }
        $this->body_vars['fe_val_13'] = $fe_val_13;
        $this->body_vars['fe_val_13_rule'] = $fe_val_13_rule;

        // SNO :: 23 || Front-end Validation 14
        // GST F7
        // i) Box 26 < 0 and Box 20 > 0
        // Or
        // ii) Box 26 < 0 and Box 20 < 0 and Box 20 <= Box 26
        // Or
        // iii) Box 26 > 0 and Box 20 > 0 and Box 20 >= Box 26

        $fe_val_14 = false;
        $fe_val_14_rule = '';
        if ($box_26_value < 0 && $box_20_value > 0) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 26 < 0 and Box 20 > 0';
        } elseif ($box_26_value < 0 && $box_20_value < 0 && $box_20_value <= $box_26_value) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 26 < 0 and Box 20 < 0 and Box 20 <= Box 26';
        } elseif ($box_26_value > 0 && $box_20_value > 0 && $box_20_value >= $box_26_value) {
            $fe_val_14 = true;
            $fe_val_14_rule = 'Box 26 > 0 and Box 20 > 0 and Box 20 >= Box 26';
        }

        $this->body_vars['fe_val_14'] = $fe_val_14;
        $this->body_vars['fe_val_14_rule'] = $fe_val_14_rule;

        $ci_id = $this->get_contact_info_id();
        $contact_info = $this->custom->getSingleRow('gst_returns_contact_info', ['ci_id' => $ci_id]);
        $fe_val_15 = false;
        if (strlen($contact_info->phone) != 8) {
            $fe_val_15 = true;
        }
        $this->body_vars['fe_val_15'] = $fe_val_15;

        $fe_val_success = true;
        if ($fe_val_2 || $fe_val_3 || $fe_val_4 || $fe_val_5 || $fe_val_6 || $fe_val_7 || $fe_val_8 || $fe_val_9 || $fe_val_10 || $fe_val_11 || $fe_val_12 || $fe_val_13 || $fe_val_14 || $fe_val_15) {
            $fe_val_success = false;
        }
        $this->body_vars['fe_val_success'] = $fe_val_success;

        // If the Front-End Validation is failed, then update the status so that user can not generate JSON with errors
        $json_status = '';
        if ($fe_val_success) {
            $json_status = 'VALID';
        }
        $updated = $this->custom->updateRow('gst_returns_filing_info', ['json' => $json_status], ['fi_id' => $fi_id]);

        $this->body_file = 'gst/iras_api_fe_validation.php';
    }

    public function iras_api_generate_json()
    {
        $fi_id = $this->get_filing_info_id();
        $filing_data = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);

        if ($filing_data->json == null || $filing_data->json == '') {
            set_flash_message('message', 'danger', 'Please fix the validation issues before generating JSON');
            redirect('gst/iras_api_fe_validation');
        }

        if ($filing_data->start_date != null && $filing_data->start_date != '' && $filing_data->form_type != '') {
            if ($filing_data->form_type == 'F7') {
                $this->generate_form_7_json();
            } else {
                $this->generate_form_5_json();
            }
        } else {
            set_flash_message('message', 'success', 'START WITH FILING INFO');
            redirect('gst/iras_api_filing_info');
        }
    }

    public function generate_form_5_json()
    {
        // GST Returns Filing Info
        $fi_id = $this->get_filing_info_id();
        $gst_returns_filing_info = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $tax_ref_no = $gst_returns_filing_info->tax_ref_no;
        $start_date = $gst_returns_filing_info->start_date;
        $end_date = $gst_returns_filing_info->end_date;
        $form_type = $gst_returns_filing_info->form_type;

        // GST Returns Form Values
        $fv_id = $this->get_form_5_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_5', ['fv_id' => $fv_id]);

        $touristRefundChk = 'FALSE';
        $touristRefundAmt = $form_data->box_10_value;
        if ($form_data->box_10_option == '1') {
            $touristRefundChk = 'TRUE';
        }

        $badDebtChk = 'FALSE';
        $badDebtReliefClaimAmt = $form_data->box_11_value;
        if ($form_data->box_11_option == '1') {
            $badDebtChk = 'TRUE';
        }

        $preRegistrationChk = 'FALSE';
        $preRegistrationClaimAmt = $form_data->box_12_value;
        if ($form_data->box_12_option == '1') {
            $preRegistrationChk = 'TRUE';
        }

        $RCLVGChk = 'FALSE';
        $totImpServLVGAmt = $form_data->box_14_value;
        if ($form_data->box_14_option == '1') {
            $RCLVGChk = 'TRUE';
        }

        $OVRRSChk = 'FALSE';
        $totRemServAmt = $form_data->box_15_value;
        if ($form_data->box_15_option == '1') {
            $OVRRSChk = 'TRUE';
        }

        $RedlvrMktOprLVGChk = 'FALSE';
        $totRedlvrMktOprLVGAmt = $form_data->box_16_value;
        if ($form_data->box_16_option == '1') {
            $RedlvrMktOprLVGChk = 'TRUE';
        }

        $OwnImpLVGChk = 'FALSE';
        $totOwnImpLVGAmt = round($form_data->box_17_value);
        if ($form_data->box_17_option == '1') {
            $OwnImpLVGChk = 'TRUE';
        }

        // GST Returns Declaration
        $d_id = $this->get_declaration_id();
        $declaration = $this->custom->getSingleRow('gst_returns_declaration', ['d_id' => $d_id]);
        $declareTrueCompleteChk = 'FALSE';
        if ($declaration->declaration_item_1 == 'yes') {
            $declareTrueCompleteChk = 'TRUE';
        }

        $declareIncRtnFalseInfoChk = 'FALSE';
        if ($declaration->declaration_item_2 == 'yes') {
            $declareIncRtnFalseInfoChk = 'TRUE';
        }

        // GST Returns Contact Info
        $ci_id = $this->get_contact_info_id();
        $contact_info = $this->custom->getSingleRow('gst_returns_contact_info', ['ci_id' => $ci_id]);

        // GST Returns Group Reasons
        $grp_id = $this->get_grp_reasons_id();
        $grp_reasons = $this->custom->getSingleRow('gst_returns_grp_reasons', ['grp_id' => $grp_id]);

        $grp1BadDebtRecoveryChk = 'FALSE';
        if ($grp_reasons->grp1BadDebtRecoveryChk == '1') {
            $grp1BadDebtRecoveryChk = 'TRUE';
        }

        $grp1PriorToRegChk = 'FALSE';
        if ($grp_reasons->grp1PriorToRegChk == '1') {
            $grp1PriorToRegChk = 'TRUE';
        }

        $grp1OtherReasonChk = 'FALSE';
        $grp1OtherReasons = '';
        if ($grp_reasons->grp1OtherReasonChk == '1') {
            $grp1OtherReasonChk = 'TRUE';
            $grp1OtherReasons = $grp_reasons->grp1OtherReasons;
        }

        $grp2TouristRefundChk = 'FALSE';
        if ($grp_reasons->grp2TouristRefundChk == '1') {
            $grp2TouristRefundChk = 'TRUE';
        }

        $grp2AppvBadDebtReliefChk = 'FALSE';
        if ($grp_reasons->grp2AppvBadDebtReliefChk == '1') {
            $grp2AppvBadDebtReliefChk = 'TRUE';
        }

        $grp2CreditNotesChk = 'FALSE';
        if ($grp_reasons->grp2CreditNotesChk == '1') {
            $grp2CreditNotesChk = 'TRUE';
        }

        $grp2OtherReasonsChk = 'FALSE';
        $grp2OtherReasons = '';
        if ($grp_reasons->grp2OtherReasonsChk == '1') {
            $grp2OtherReasonsChk = 'TRUE';
            $grp2OtherReasons = $grp_reasons->grp2OtherReasons;
        }

        $grp3CreditNotesChk = 'FALSE';
        if ($grp_reasons->grp3CreditNotesChk == '1') {
            $grp3CreditNotesChk = 'TRUE';
        }

        $grp3OtherReasonsChk = 'FALSE';
        $grp3OtherReasons = '';
        if ($grp_reasons->grp3OtherReasonsChk == '1') {
            $grp3OtherReasonsChk = 'TRUE';
            $grp3OtherReasons = $grp_reasons->grp3OtherReasons;
        }

        $json_request_data = '{
                "filingInfo": {
                    "taxRefNo": "'.$tax_ref_no.'",
                    "formType": "'.$form_type.'",
                    "dtPeriodStart": "'.date('Y-m-d', strtotime($start_date)).'",
                    "dtPeriodEnd": "'.date('Y-m-d', strtotime($end_date)).'"
                },
                "supplies": {
                    "totStdSupply": "'.$form_data->box_1_value.'",
                    "totZeroSupply": "'.$form_data->box_2_value.'",
                    "totExemptSupply": "'.$form_data->box_3_value.'"
                },
                "purchases": {
                    "totTaxPurchase": "'.$form_data->box_5_value.'"
                },
                "taxes": {
                    "outputTaxDue": "'.$form_data->box_6_value.'",
                    "inputTaxRefund": "'.$form_data->box_7_value.'"
                },
                "schemes": {
                    "totValueScheme": "'.$form_data->box_9_value.'",
                    "touristRefundChk": "'.$touristRefundChk.'",
                    "touristRefundAmt": "'.$touristRefundAmt.'",
                    "badDebtChk": "'.$badDebtChk.'",
                    "badDebtReliefClaimAmt": "'.$badDebtReliefClaimAmt.'",
                    "preRegistrationChk": "'.$preRegistrationChk.'",
                    "preRegistrationClaimAmt": "'.$preRegistrationClaimAmt.'"
                },
                "revenue": {
                    "revenue": "'.$form_data->box_13_value.'"
                },
                "RevChargeLVG": {
                    "RCLVGChk": "'.$RCLVGChk.'",
                    "totImpServLVGAmt": "'.$totImpServLVGAmt.'"
                },
                "ElectronicMktplaceOprRedlvr": {
                    "OVRRSChk": "'.$OVRRSChk.'",
                    "totRemServAmt": "'.$totRemServAmt.'",
                    "RedlvrMktOprLVGChk": "'.$RedlvrMktOprLVGChk.'",
                    "totRedlvrMktOprLVGAmt": "'.$totRedlvrMktOprLVGAmt.'"
                },
                "SupplierOfImpLVG": {
                    "OwnImpLVGChk": "'.$OwnImpLVGChk.'",
                    "totOwnImpLVGAmt": "'.$totOwnImpLVGAmt.'"
                },
                "igdScheme": {
                    "defImpPayableAmt": "'.$form_data->box_19_value.'",
                    "defTotalGoodsImp": "'.$form_data->box_21_value.'"
                },
                "declaration": {
                    "declareTrueCompleteChk": "'.$declareTrueCompleteChk.'",
                    "declareIncRtnFalseInfoChk": "'.$declareIncRtnFalseInfoChk.'",
                    "declarantDesgtn": "'.$declaration->designation.'",
                    "contactPerson": "'.$contact_info->name.'",
                    "contactNumber": "'.$contact_info->phone.'",
                    "contactEmail": "'.$contact_info->email.'"
                },
                "reasons": {
                    "grp1BadDebtRecoveryChk": "'.$grp1BadDebtRecoveryChk.'",
                    "grp1PriorToRegChk": "'.$grp1PriorToRegChk.'",
                    "grp1OtherReasonChk": "'.$grp1OtherReasonChk.'",
                    "grp1OtherReasons": "'.$grp1OtherReasons.'",
                    "grp2TouristRefundChk": "'.$grp2TouristRefundChk.'",
                    "grp2AppvBadDebtReliefChk": "'.$grp2AppvBadDebtReliefChk.'",
                    "grp2CreditNotesChk": "'.$grp2CreditNotesChk.'",
                    "grp2OtherReasonsChk": "'.$grp2OtherReasonsChk.'",
                    "grp2OtherReasons": "'.$grp2OtherReasons.'",
                    "grp3CreditNotesChk": "'.$grp3CreditNotesChk.'",
                    "grp3OtherReasonsChk": "'.$grp3OtherReasonsChk.'",
                    "grp3OtherReasons": "'.$grp3OtherReasons.'"
                }
            }';

        $this->body_vars['json_request_data'] = $json_request_data = $json_request_data;
        $this->body_file = 'gst/iras_api_generate_json.php';
    }

    public function generate_form_7_json()
    {
        // GST Returns Filing Info
        $fi_id = $this->get_filing_info_id();
        $gst_returns_filing_info = $this->custom->getSingleRow('gst_returns_filing_info', ['fi_id' => $fi_id]);
        $tax_ref_no = $gst_returns_filing_info->tax_ref_no;
        $start_date = $gst_returns_filing_info->start_date;
        $end_date = $gst_returns_filing_info->end_date;
        $form_type = $gst_returns_filing_info->form_type;

        // GST Returns Form Values
        $fv7_id = $this->get_form_7_id();
        $form_data = $this->custom->getSingleRow('gst_returns_form_7', ['fv7_id' => $fv7_id]);

        $touristRefundChk = 'FALSE';
        $touristRefundAmt = $form_data->box_12_value;
        if ($form_data->box_12_option == '1') {
            $touristRefundChk = 'TRUE';
        }

        $badDebtChk = 'FALSE';
        $badDebtReliefClaimAmt = $form_data->box_13_value;
        if ($form_data->box_13_option == '1') {
            $badDebtChk = 'TRUE';
        }

        $preRegistrationChk = 'FALSE';
        $preRegistrationClaimAmt = $form_data->box_14_value;
        if ($form_data->box_14_option == '1') {
            $preRegistrationChk = 'TRUE';
        }

        $RCLVGChk = 'FALSE';
        $totImpServLVGAmt = $form_data->box_16_value;
        if ($form_data->box_16_option == '1') {
            $RCLVGChk = 'TRUE';
        }

        $OVRRSChk = 'FALSE';
        $totRemServAmt = $form_data->box_17_value;
        if ($form_data->box_17_option == '1') {
            $OVRRSChk = 'TRUE';
        }

        $RedlvrMktOprLVGChk = 'FALSE';
        $totRedlvrMktOprLVGAmt = $form_data->box_18_value;
        if ($form_data->box_18_option == '1') {
            $RedlvrMktOprLVGChk = 'TRUE';
        }

        $OwnImpLVGChk = 'FALSE';
        $totOwnImpLVGAmt = $form_data->box_19_value;
        if ($form_data->box_19_option == '1') {
            $OwnImpLVGChk = 'TRUE';
        }

        // GST Returns Declaration
        $d_id = $this->get_declaration_id();
        $declaration = $this->custom->getSingleRow('gst_returns_declaration', ['d_id' => $d_id]);
        $declareTrueCompleteChk = 'FALSE';
        if ($declaration->declaration_item_1 == 'yes') {
            $declareTrueCompleteChk = 'TRUE';
        }

        $declareIncRtnFalseInfoChk = 'FALSE';
        if ($declaration->declaration_item_2 == 'yes') {
            $declareIncRtnFalseInfoChk = 'TRUE';
        }

        // GST Returns Contact Info
        $ci_id = $this->get_contact_info_id();
        $contact_info = $this->custom->getSingleRow('gst_returns_contact_info', ['ci_id' => $ci_id]);

        // GST Returns Group Reasons
        $grp_id = $this->get_grp_reasons_id();
        $grp_reasons = $this->custom->getSingleRow('gst_returns_grp_reasons', ['grp_id' => $grp_id]);

        $grp1BadDebtRecoveryChk = 'FALSE';
        if ($grp_reasons->grp1BadDebtRecoveryChk == '1') {
            $grp1BadDebtRecoveryChk = 'TRUE';
        }

        $grp1PriorToRegChk = 'FALSE';
        if ($grp_reasons->grp1PriorToRegChk == '1') {
            $grp1PriorToRegChk = 'TRUE';
        }

        $grp1OtherReasonChk = 'FALSE';
        $grp1OtherReasons = '';
        if ($grp_reasons->grp1OtherReasonChk == '1') {
            $grp1OtherReasonChk = 'TRUE';
            $grp1OtherReasons = $grp_reasons->grp1OtherReasons;
        }

        $grp2TouristRefundChk = 'FALSE';
        if ($grp_reasons->grp2TouristRefundChk == '1') {
            $grp2TouristRefundChk = 'TRUE';
        }

        $grp2AppvBadDebtReliefChk = 'FALSE';
        if ($grp_reasons->grp2AppvBadDebtReliefChk == '1') {
            $grp2AppvBadDebtReliefChk = 'TRUE';
        }

        $grp2CreditNotesChk = 'FALSE';
        if ($grp_reasons->grp2CreditNotesChk == '1') {
            $grp2CreditNotesChk = 'TRUE';
        }

        $grp2OtherReasonsChk = 'FALSE';
        $grp2OtherReasons = '';
        if ($grp_reasons->grp2OtherReasonsChk == '1') {
            $grp2OtherReasonsChk = 'TRUE';
            $grp2OtherReasons = $grp_reasons->grp2OtherReasons;
        }

        $grp3CreditNotesChk = 'FALSE';
        if ($grp_reasons->grp3CreditNotesChk == '1') {
            $grp3CreditNotesChk = 'TRUE';
        }

        $grp3OtherReasonsChk = 'FALSE';
        $grp3OtherReasons = '';
        if ($grp_reasons->grp3OtherReasonsChk == '1') {
            $grp3OtherReasonsChk = 'TRUE';
            $grp3OtherReasons = $grp_reasons->grp3OtherReasons;
        }

        $json_request_data = '{
                "filingInfo": {
                    "taxRefNo": "'.$tax_ref_no.'",
                    "formType": "'.$form_type.'",
                    "dtPeriodStart": "'.date('Y-m-d', strtotime($start_date)).'",
                    "dtPeriodEnd": "'.date('Y-m-d', strtotime($end_date)).'"
                },                
                "supplies": {
                    "totStdSupply": "'.$form_data->box_1_value.'",
                    "totZeroSupply": "'.$form_data->box_2_value.'",
                    "totExemptSupply": "'.$form_data->box_3_value.'"
                },                
                "purchases": {
                    "totTaxPurchase": "'.$form_data->box_5_value.'"
                },                
                "taxes": {
                    "outputTaxDue": "'.$form_data->box_6_value.'",
                    "inputTaxRefund": "'.$form_data->box_7_value.'",
                    "prevGSTPaid": "'.$form_data->box_9_value.'",
                    "netDifference": "'.$form_data->box_10_value.'"
                },                
                "schemes": {
                    "totValueScheme": "'.$form_data->box_11_value.'",
                    "touristRefundChk": "'.$touristRefundChk.'",
                    "touristRefundAmt": "'.$touristRefundAmt.'",
                    "badDebtChk": "'.$badDebtChk.'",
                    "badDebtReliefClaimAmt": "'.$badDebtReliefClaimAmt.'",
                    "preRegistrationChk": "'.$preRegistrationChk.'",
                    "preRegistrationClaimAmt": "'.$preRegistrationClaimAmt.'"
                },                
                "revenue": {
                    "revenue": "'.$form_data->box_15_value.'"
                },                
                "RevChargeLVG": {
                    "RCLVGChk": "'.$RCLVGChk.'",
                    "totImpServLVGAmt": "'.$totImpServLVGAmt.'"
                },
                "ElectronicMktplaceOprRedlvr": {
                    "OVRRSChk": "'.$OVRRSChk.'",
                    "totRemServAmt": "'.$totRemServAmt.'",
                    "RedlvrMktOprLVGChk": "'.$RedlvrMktOprLVGChk.'",
                    "totRedlvrMktOprLVGAmt": "'.$totRedlvrMktOprLVGAmt.'"
                },
                "SupplierOfImpLVG": {
                    "OwnImpLVGChk": "'.$OwnImpLVGChk.'",
                    "totOwnImpLVGAmt": "'.$totOwnImpLVGAmt.'"
                },
                "igdScheme": {
                    "defImpPayableAmt": "'.$form_data->box_20_value.'",
                    "prevDefImpGSTPayable": "'.$form_data->box_21_value.'",
                    "diffDefImpGSTPayable": "'.$form_data->box_22_value.'",
                    "defTotalGoodsImp": "'.$form_data->box_26_value.'"
                },
                "ErrorDescription": {
                    "descriptionOfError": "'.$form_data->error_description.'"
                },                
                "declaration": {
                    "declareTrueCompleteChk": "'.$declareTrueCompleteChk.'",
                    "declareIncRtnFalseInfoChk": "'.$declareIncRtnFalseInfoChk.'",
                    "declarantDesgtn": "'.$declaration->designation.'",
                    "contactPerson": "'.$contact_info->name.'",
                    "contactNumber": "'.$contact_info->phone.'",
                    "contactEmail": "'.$contact_info->email.'"
                },                
                "reasons": {
                    "grp1BadDebtRecoveryChk": "'.$grp1BadDebtRecoveryChk.',
                    "grp1PriorToRegChk": "'.$grp1PriorToRegChk.'",
                    "grp1OtherReasonChk": "'.$grp1OtherReasonChk.'",
                    "grp1OtherReasons": "'.$grp1OtherReasons.'",
                    "grp2TouristRefundChk": "'.$grp2TouristRefundChk.'",
                    "grp2AppvBadDebtReliefChk": "'.$grp2AppvBadDebtReliefChk.'",
                    "grp2CreditNotesChk": "'.$grp2CreditNotesChk.'",
                    "grp2OtherReasonsChk": "'.$grp2OtherReasonsChk.'",
                    "grp2OtherReasons": "'.$grp2OtherReasons.'",
                    "grp3CreditNotesChk": "'.$grp3CreditNotesChk.'",
                    "grp3OtherReasonsChk": "'.$grp3OtherReasonsChk.'",
                    "grp3OtherReasons": "'.$grp3OtherReasons.'"
                }
            }';

        $this->body_vars['json_request_data'] = $json_request_data = $json_request_data;
        $this->body_file = 'gst/iras_api_generate_json.php';
    }

    public function data_patch($gst_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($gst_id != '') {
            $gst_data = $this->custom->getSingleRow('gst', ['gst_id' => $gst_id]);
            $this->body_vars['doc_date'] = $gst_data->date;
            $this->body_vars['ref_no'] = $gst_data->dref;
            $this->body_vars['remarks'] = $gst_data->rema;
            $this->body_vars['tran_type'] = $gst_data->tran_type;

            $this->body_vars['purchase_gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'purchase']);
            $this->body_vars['supply_gsts'] = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply']);

            $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
            $this->body_vars['suppliers'] = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);

            $this->body_file = 'gst/data_patch.php';
        }
    }

    public function save_patched_data()
    {
        $post = $this->input->post();
        $len = sizeof($post);
        if ($post) {
            $total_items = count($post['gst_id']);
            for ($i = 0; $i <= $total_items - 1; ++$i) {
                $gst_data['date'] = date('Y-m-d', strtotime($post['doc_date']));
                $gst_data['dref'] = $post['ref_no'];
                $gst_data['iden'] = $post['iden'][$i];
                $gst_data['rema'] = $post['remarks'];

                if ($post['gst_type'][$i] == 'S' || $post['gst_type'][$i] == 'R') {
                    $gst_data['gstcate'] = '';
                    $gst_data['gstperc'] = 0.00;
                    $gst_data['amou'] = 0.00;
                } else {
                    $gst_data['gstcate'] = $post['gst_category'][$i];
                    $gst_data['gstperc'] = $post['gst_percentage'][$i];
                    $gst_data['amou'] = $post['amount'][$i];
                }

                $gst_data['gstamou'] = $post['gst_amount'][$i];

                $updated[] = $this->custom->updateRow('gst', $gst_data, ['gst_id' => $post['gst_id'][$i]]);
            }

            if (in_array('error', $updated)) {
                set_flash_message('message', 'danger', 'Save Error');
            } else {
                set_flash_message('message', 'success', 'GST Datapatch Completed');
            }

            redirect('/gst');
        } else {
            set_flash_message('message', 'danger', 'BATCH POST ERROR');
            redirect('/gst/data_patch/'.$post['gst_id'][0]);
        }
    }

    public function print_summary()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $start_date = date('Y-m-d', strtotime($post['from_sr']));
            $end_date = date('Y-m-d', strtotime($post['to_sr']));

            // Company header & report title
            $company = $this->custom->getSingleValue('company_profile', 'company_name', ['code' => 'CP']);
            $html .= '<table style="width: 100%;">';
            $html .= '<tr>
                            <td style="border: none; text-align: center;">
                                <h2>'.$company.'</h2>
                            </td>
                        </tr>';
            $html .= '<tr>
                        <td style="border: none; text-align: center;">
                            <h3>Summary GST Report</h3>
                        </td>
                    </tr>';
            $html .= '<tr>
                <td style="border: none; border-bottom: 2px solid gainsboro; text-align: right;">
                    <strong>Period:</strong> '.$post['from_sr'].' <i>to</i> '.$post['to_sr'].'
                </td>
            </tr>';
            $html .= '</table>';

            // OUTPUT TAX
            $html .= '<table style="width: 100%;">';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none;" height="20"></td>';
            $html .= '</tr>';

            $html .= '<tr>
                            <td colspan="6" style="border: none; color: blue;">
                                <h4>Output Tax</h4>
                            </td>
                        </tr>';

            $html .= '<tr>
            <th>#</th>
            <th>Category</th>
            <th>Description</th>
            <th style="text-align: right;">Amount</th>
            <th style="text-align: right;">Rate</th>
            <th style="text-align: center;">GST Amount</th>
            </tr>';

            $gt_amount = 0;
            $gt_gst_amount = 0;
            $sno = 1;
            $this->db->select('*, sum(CASE WHEN gsttype = "O" THEN amou WHEN gsttype = "OR" THEN -amou END) AS total_amount, sum(CASE WHEN gsttype = "O" THEN gstamou WHEN gsttype = "OR" THEN -gstamou END) AS total_gst_amount');
            $this->db->from('gst');
            $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'O' || gsttype = 'OR') and gstcate != 'OS'");
            $this->db->group_by('gstcate');
            $this->db->order_by('gstcate', 'ASC');
            $query = $this->db->get();
            $gst_ot_cts = $query->result();
            foreach ($gst_ot_cts as $value) {
                $gst_description = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gstcate]);
                $html .= '<tr>';
                $html .= '<td>'.$sno.'</td>';
                $html .= '<td>'.$value->gstcate.'</td>';
                $html .= '<td>'.$gst_description.'</td>';
                $html .= '<td style="text-align: right;">'.number_format($value->total_amount, 2).'</td>';
                $html .= '<td style="text-align: center;">'.$value->gstperc.'</td>';
                $html .= '<td style="text-align: right;">'.number_format($value->total_gst_amount, 2).'</td>';
                $html .= '</tr>';

                $gt_amount += $value->total_amount;
                $gt_gst_amount += $value->total_gst_amount;
                ++$sno;
            }
            $ot_payable = $gt_gst_amount;

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none;" height="20"></td>';
            $html .= '</tr>';

            if ($sno > 1) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="border: none; text-align: right;">Grand Total</td>';
                $html .= '<td style="border:none; border-top: 1px dotted #000; border-bottom: 1px dotted #000; text-align: right"><strong>$'.number_format($gt_amount, 2).'</strong></td>';
                $html .= '<td style="border:none;"></td>';
                $html .= '<td style="border:none; border-top: 1px dotted #000; border-bottom: 1px dotted #000; text-align: right"><strong>$'.number_format($gt_gst_amount, 2).'</strong></td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: center; color: red; font-weight: bold;">No Transactions</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            // INPUT TAX
            $html .= '<table style="width: 100%;">';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none;" height="20"></td>';
            $html .= '</tr>';

            $html .= '<tr>
                            <td colspan="6" style="border: none; color: blue;">
                                <h4>Input Tax</h4>
                            </td>
                        </tr>';

            $html .= '<tr>
            <th>#</th>
            <th>Category</th>
            <th>Description</th>
            <th style="text-align: right;">Amount</th>
            <th style="text-align: right;">Rate</th>
            <th style="text-align: center;">GST Amount</th>
            </tr>';

            $gt_amount = 0;
            $gt_gst_amount = 0;
            $sno = 1;
            $this->db->select('*, sum(CASE WHEN gsttype = "I" THEN amou WHEN gsttype = "IR" THEN -amou END) AS total_amount, sum(CASE WHEN gsttype = "I" THEN gstamou WHEN gsttype = "IR" THEN -gstamou END) AS total_gst_amount');
            $this->db->from('gst');
            $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'I' || gsttype = 'IR') and gstcate != 'BL' and gstcate != 'NR' and gstcate != 'EP' and gstcate != 'OP'");
            $this->db->group_by('gstcate');
            $this->db->order_by('gstcate', 'ASC');
            $query = $this->db->get();
            $gst_ot_cts = $query->result();
            foreach ($gst_ot_cts as $value) {
                $gst_description = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gstcate]);
                $html .= '<tr>';
                $html .= '<td>'.$sno.'</td>';
                $html .= '<td>'.$value->gstcate.'</td>';
                $html .= '<td>'.$gst_description.'</td>';
                $html .= '<td style="text-align: right;">'.number_format($value->total_amount, 2).'</td>';
                $html .= '<td style="text-align: center;">'.$value->gstperc.'</td>';
                $html .= '<td style="text-align: right;">'.number_format($value->total_gst_amount, 2).'</td>';
                $html .= '</tr>';

                $gt_amount += $value->total_amount;
                $gt_gst_amount += $value->total_gst_amount;
                ++$sno;
            }

            $it_receivable = $gt_gst_amount;

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none;" height="20"></td>';
            $html .= '</tr>';

            if ($sno > 1) {
                $html .= '<tr>';
                $html .= '<td colspan="3" style="border: none; text-align: right;">Grand Total</td>';
                $html .= '<td style="border:none; border-top: 1px dotted #000; border-bottom: 1px dotted #000; text-align: right"><strong>$'.number_format($gt_amount, 2).'</strong></td>';
                $html .= '<td style="border:none;"></td>';
                $html .= '<td style="border:none; border-top: 1px dotted #000; border-bottom: 1px dotted #000; text-align: right"><strong>$'.number_format($gt_gst_amount, 2).'</strong></td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: center; color: red; font-weight: bold;">No Transactions</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            $html .= '<br />';
            $html .= '<br />';

            // Output Tax is always PAYABLE
            // Input Tax is always RECEIVABLE
            $html .= '<table style="width: 300px;">';

            $html .= '<tr>';
            $html .= '<td style="border: none;">PAYABLE</td>';
            $html .= '<td style="border: none; text-align: right">'.number_format($ot_payable, 2).'</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td style="border: none;">RECEIVABLE</td>';
            $html .= '<td style="border: none; text-align: right">'.number_format($it_receivable, 2).'</td>';
            $html .= '</tr>';

            // if output tax is more than input tax then the Tax PAYABLE
            // if input tax is more than output tax then the TAX RECEIVABLE
            $gst_text = '';
            if ($ot_payable > $gst_input_grand_total) {
                $gst_text = 'NET GST PAYABLE';
            } elseif ($it_receivable > $ot_payable) {
                $gst_text = 'NET GST RECEIVABLE';
            } elseif ($it_receivable = $ot_payable) {
                $gst_text = 'NET GST';
            }

            $net_gst = $ot_payable - $it_receivable;
            if ($net_gst < 0) {
                $net_gst *= (-1);
            }

            $html .= '<tr>';
            $html .= '<td style="border: none; font-weight: bold">'.$gst_text.'</td>';
            $html .= '<td style="border:none; border-top: 1px dotted #000; border-bottom: 1px dotted #000; text-align: right; font-weight: bold">'.number_format($net_gst, 2).'</td>';
            $html .= '</tr>';

            $html .= '</table>';

            // Revenue
            $revenue = 0;
            $this->db->select('total_amount');
            $this->db->from('gl');
            $this->db->where("doc_date BETWEEN '".$start_date."' AND '".$end_date."' AND accn LIKE 'S0%'");
            $revenue_query = $this->db->get();
            $revenue_data = $query->result();
            foreach ($revenue_data as $key => $value) {
                $revenue += $value->total_amount;
            }

            $html .= '<br />';
            $html .= '<table style="width: 100%;">';
            $html .= '<tr>';
            $html .= '<td style="border: none;"><i>REVENUE FOR THE ABOVE PERIOD =</i> <span style="color: red;">$'.number_format($revenue, 2).'</span><strong></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="border: none;"><strong>NOTE :</strong> THE GST REPORT IS NOT MEANT FOR PARTIALLY-EXEMPT TRADER</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<br />';
            $html .= '<table style="width: 100%; page-break-inside: avoid">';
            $html .= '<tr>';
            $html .= '<td colspan="2">Categories of supplies/purchases not reported in the GST Return:</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>Supplies :</td><td>Out-of-Scope Suplies (Tax Code : OS)</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>Purchases :</td>';
            $html .= '<td>Disallowed Expenses (Tax Code : BL) <br />
				Purchases from Non-GST Registered Suppliers (Tax Code : NR) <br />
				Exempt Purchases (Tax Code : EP) <br />
				Out-of-Scope Purchases (Tax Code : OP)</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gst_dltd_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/gst/reports');
        }
    }

    public function get_output_tax($gstcate, $start_date, $end_date)
    {
        $ot = 0;
        $gt_amount = 0;
        $gt_gst_amount = 0;

        $this->db->select('gstcate');
        $this->db->from('gst');
        $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'O' || gsttype = 'OR') AND gstcate != 'OS' $gstcate");
        $this->db->group_by('gstcate');
        $this->db->order_by('gstcate', 'ASC');
        $query = $this->db->get();
        $gst_ot_cts = $query->result();

        $html = '<table style="width: 100%;">';
        $html .= '<tr>
                        <td style="border: none; color: blue;">
                            <h4>Output Tax</h4>
                        </td>
                    </tr>';

        foreach ($gst_ot_cts as $rec) {
            $html .= '<tr><td colspan="7"><strong>GST Category:</strong> '.$rec->gstcate.'</td></tr>';

            $html .= '<tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Remarks</th>
                <th>IDEN</th>
                <th style="text-align: right;">Amount $</th>
                <th style="text-align: center;">GST %</th>
                <th style="text-align: right;">GST $</th>
                </tr>';

            $st_amount = 0;
            $st_gst_amount = 0;
            $this->db->select('*');
            $this->db->from('gst');
            $this->db->where("gstcate = '".$rec->gstcate."' AND date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'O' || gsttype = 'OR') AND gstcate != 'OS' ");
            $this->db->order_by('date, dref', 'ASC, ASC');
            $query = $this->db->get();
            $gst_ot_data = $query->result();
            foreach ($gst_ot_data as $value) {
                $gst_type = strtoupper($value->gsttype);
                $doc_date = implode('/', array_reverse(explode('-', $value->date)));
                $name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $value->iden]);
                if ($name == '') {
                    $name = $value->iden;
                }

                $html .= '<tr>';
                $html .= '<td>'.$doc_date.'</td>';
                $html .= '<td>'.$value->dref.'</td>';
                $html .= '<td>'.$value->rema.'</td>';
                $html .= '<td>'.$name.'</td>';

                $html .= '<td style="text-align: right;">';
                if ($gst_type == 'OR') {
                    $html .= '-';
                }
                $html .= number_format($value->amou, 2).'</td>';

                $html .= '<td style="text-align: center;">'.$value->gstperc.'</td>';

                $html .= '<td style="text-align: right;">';
                if ($gst_type == 'OR') {
                    $html .= '-';
                }
                $html .= number_format($value->gstamou, 2).'</td>';

                $html .= '</tr>';

                if ($gst_type == 'O') {
                    $st_amount += $value->amou;
                    $st_gst_amount += $value->gstamou;
                } elseif ($gst_type == 'OR') {
                    $st_amount -= $value->amou;
                    $st_gst_amount -= $value->gstamou;
                }
            }

            // sub total
            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="color: red">Sub Total</td>';
            $html .= '<td style="text-align: right;">'.number_format($st_amount, 2).'</td>';
            $html .= '<td></td>';
            $html .= '<td style="text-align: right;">'.number_format($st_gst_amount, 2).'</td>';
            $html .= '</tr>';

            $html .= '<tr><td colspan="7" height="20" style="border: none"></tr>';

            $gt_amount += $st_amount;
            $gt_gst_amount += $st_gst_amount;

            ++$ot;
        }

        // Grand total
        if ($ot > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="border: none;"><strong>Grand Total</strong></td>';
            $html .= '<td style="text-align: right; border: none; border-top: 1px dotted #000; border-bottom: 1px dotted #000;">'.number_format($gt_amount, 2).'</td>';
            $html .= '<td style="border: none"></td>';
            $html .= '<td style="text-align: right; border: none; border-top: 1px dotted #000; border-bottom: 1px dotted #000;">'.number_format($gt_gst_amount, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="7" style="border: none; text-align: center; color: red; font-weight: bold;">No Transactions</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function get_input_tax($gstcate, $start_date, $end_date)
    {
        $html = '<table style="width: 100%;">';
        $html .= '<tr>
                        <td style="border: none; color: blue;">
                            <h4>Input Tax</h4>
                        </td>
                    </tr>';

        $ot = 0;
        $gt_amount = 0;
        $gt_gst_amount = 0;

        $this->db->select('gstcate');
        $this->db->from('gst');
        $this->db->where("date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'I' || gsttype = 'IR') $gstcate");
        $this->db->group_by('gstcate');
        $this->db->order_by('gstcate', 'ASC');
        $query = $this->db->get();
        $gst_it_cts = $query->result();
        foreach ($gst_it_cts as $rec) {
            $html .= '<tr><td colspan="7"><strong>GST Category:</strong> '.$rec->gstcate.'</td></tr>';

            $html .= '<tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Remarks</th>
                <th>IDEN</th>
                <th style="text-align: right;">Amount $</th>
                <th style="text-align: center;">GST %</th>
                <th style="text-align: right;">GST $</th>
                </tr>';

            $st_amount = 0;
            $st_gst_amount = 0;
            $this->db->select('*');
            $this->db->from('gst');
            $this->db->where("gstcate = '".$rec->gstcate."' AND date BETWEEN '".$start_date."' AND '".$end_date."' AND (gsttype = 'I' || gsttype = 'IR') ");
            $this->db->order_by('date, dref', 'ASC, ASC');
            $query = $this->db->get();
            $gst_ot_data = $query->result();
            foreach ($gst_ot_data as $value) {
                $gst_type = strtoupper($value->gsttype);
                $doc_date = implode('/', array_reverse(explode('-', $value->date)));
                $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $value->iden]);
                if ($name == '') {
                    $name = $value->iden;
                }

                $html .= '<tr>';
                $html .= '<td>'.$doc_date.'</td>';
                $html .= '<td>'.$value->dref.'</td>';
                $html .= '<td>'.$value->rema.'</td>';
                $html .= '<td>'.$name.'</td>';

                $html .= '<td style="text-align: right;">';
                if ($gst_type == 'OR') {
                    $html .= '-';
                }
                $html .= number_format($value->amou, 2).'</td>';

                $html .= '<td style="text-align: center;">'.$value->gstperc.'</td>';

                $html .= '<td style="text-align: right;">';
                if ($gst_type == 'OR') {
                    $html .= '-';
                }
                $html .= number_format($value->gstamou, 2).'</td>';

                $html .= '</tr>';

                if ($gst_type == 'I') {
                    $st_amount += $value->amou;
                    $st_gst_amount += $value->gstamou;
                } elseif ($gst_type == 'IR') {
                    $st_amount -= $value->amou;
                    $st_gst_amount -= $value->gstamou;
                }
            }

            // sub total
            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="color: red">Sub Total</td>';
            $html .= '<td style="text-align: right;">'.number_format($st_amount, 2).'</td>';
            $html .= '<td></td>';
            $html .= '<td style="text-align: right;">'.number_format($st_gst_amount, 2).'</td>';
            $html .= '</tr>';

            $html .= '<tr><td colspan="7" height="20" style="border: none"></tr>';
            $gt_amount += $st_amount;
            $gt_gst_amount += $st_gst_amount;

            ++$ot;
        }

        // Grand total
        if ($ot > 0) {
            $html .= '<tr>';
            $html .= '<td colspan="4" align="right" style="border: none;"><strong>Grand Total</strong></td>';
            $html .= '<td style="text-align: right; border: none; border-top: 1px dotted #000; border-bottom: 1px dotted #000;">'.number_format($gt_amount, 2).'</td>';
            $html .= '<td style="border: none"></td>';
            $html .= '<td style="text-align: right; border: none; border-top: 1px dotted #000; border-bottom: 1px dotted #000;">'.number_format($gt_gst_amount, 2).'</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="7" style="border: none; text-align: center; color: red; font-weight: bold;">No Transactions</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    public function print_io_tax()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $start_date = date('Y-m-d', strtotime($post['from_io']));
            $end_date = date('Y-m-d', strtotime($post['to_io']));
            $gst_type = $post['gst_type'];
            $input_tax = $post['input_tax'];
            $output_tax = $post['output_tax'];

            // Company header & report title
            $company = $this->custom->getSingleValue('company_profile', 'company_name', ['code' => 'CP']);
            $html .= '<table style="width: 100%;">';
            $html .= '<tr>
                        <td style="border: none; text-align: center;">
                            <h2>'.$company.'</h2>
                        </td>
                    </tr>';
            $html .= '<tr>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: right;">
                            <strong>Period:</strong> '.$post['from_io'].' <i>to</i> '.$post['to_io'].'
                        </td>
                    </tr>';
            $html .= '</table>';

            $html .= '<br />';

            $gstcate = '';
            if ($output_tax != '') {
                $gstcate = ' AND gstcate = "'.$output_tax.'"';
            } elseif ($input_tax != '') {
                $gstcate = ' AND gstcate = "'.$input_tax.'"';
            }

            // output tax
            if ($gst_type == 'O' || $gst_type == 'B') {
                $html .= $this->get_output_tax($gstcate, $start_date, $end_date);
            }

            // input tax
            if ($gst_type == 'I' || $gst_type == 'B') {
                $html .= $this->get_input_tax($gstcate, $start_date, $end_date);
            }

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gst_io_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/gst/reports');
        }
    }

    public function print_detailed()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();

        if ($post) {
            $start_date = date('Y-m-d', strtotime($post['from']));
            $end_date = date('Y-m-d', strtotime($post['to']));

            // Company header & report title
            $company = $this->custom->getSingleValue('company_profile', 'company_name', ['code' => 'CP']);
            $html .= '<table style="width: 100%;">';
            $html .= '<tr>
                            <td style="border: none; text-align: center;">
                                <h2>'.$company.'</h2>
                            </td>
                        </tr>';
            $html .= '<tr>
                        <td style="border: none; text-align: center;">
                            <h3>Detailed GST Report</h3>
                        </td>
                    </tr>';
            $html .= '<tr>
                        <td style="border: none; border-bottom: 1px solid #000; text-align: right;">
                            <strong>Period:</strong> '.$post['from'].' <i>to</i> '.$post['to'].'
                        </td>
                    </tr>';
            $html .= '</table>';

            $html .= '<br />';

            // output tax
            $html .= $this->get_output_tax('', $start_date, $end_date);

            // input tax
            $html .= $this->get_input_tax('', $start_date, $end_date);

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gst_dltd_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/gst/reports');
        }
    }

    public function print_audit()
    {
        is_logged_in('admin');
        has_permission();
        $post = $this->input->post();
        if ($post) {
            $transaction_type = $post['transaction'];
            $transaction_text = $post['transaction_desc'];
            $transaction_order = $post['order'];
            $cut_off_date = date('d-m-Y');

            $html = '';

            $html .= '<table style="width: 100%; border: none">';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding-left: 0;">';
            $html .= $this->custom->populateCompanyHeader();
            $html .= '</td>';
            $html .= '<td style="border: none; text-align: right; padding-right: 0;"><h3>GST AUDIT LISTING</h3></td>';
            $html .= '</tr>';
            $html .= '<tr style="border: none; border-bottom: 2px solid brown">';
            $html .= '<td style="border: none; color: blue; padding-left: 0;"><h4>'.$transaction_text.'</h4></td>';
            $html .= '<td style="border: none; text-align: right; padding-right: 0;"><strong>Date:</strong> <i>'.$cut_off_date.'</i></td>';
            $html .= '</tr>';
            $html .= '</table><br /><br />';

            $html .= '<table style="width: 100%; border: none">';

            $i = 0;
            $bf_total_per_subledger = 0;

            $table = 'gst';
            $columns = '*';
            $where = ['tran_type' => $transaction_type];
            $group_by = null;
            $order_by = null;
            $order_by_many = 'date '.$transaction_order.', dref ASC';
            $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
            $list = $query->result();
            foreach ($list as $record) {
                if ($i == 0) {
                    $html .= '<thead>
							<tr>
								<th style="width: 90px;">Date</th>
								<th style="width: 110px;">Reference</th>
                                <th style="width: 200px;">Remarks</th>
								<th style="width: 200px;">Iden</th>
                                <th>Tax</th>
                                <th>Code</th>
                                <th style="text-align: right">Amount</th>
                                <th style="text-align: right">GST Amount</th>
							</tr>
						</thead>';
                    $html .= '<tbody>';
                }

                $iden_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $record->iden]);
                $gst_type = strtoupper($record->gsttype);
                $document_date = implode('/', array_reverse(explode('-', $record->date)));

                $html .= '<tr>';
                $html .= '<td>'.$document_date.'</td>';
                $html .= '<td>'.$record->dref.'</td>';
                $html .= '<td>'.$record->rema.'</td>';

                $html .= '<td>'.$iden_name.' ('.$record->iden.')</td>';
                if ($gst_type == 'I') {
                    $html .= '<td>INPUT</td>';
                } elseif ($gst_type == 'OR') {
                    $html .= '<td>REVERSE OUTPUT</td>';
                } elseif ($gst_type == 'S') {
                    $html .= '<td>SETTLEMENT</td>';
                } elseif ($gst_type == 'O') {
                    $html .= '<td>OUTPUT</td>';
                } elseif ($gst_type == 'IR') {
                    $html .= '<td>REVERSE INPUT</td>';
                } elseif ($gst_type == 'R') {
                    $html .= '<td>REBATE</td>';
                }
                $html .= '<td>'.$record->gstcate.'</td>';
                $html .= '<td style="text-align: right">'.$record->amou.'</td>';
                $html .= '<td style="text-align: right">'.$record->gstamou.'</td>';
                $html .= '</tr>';

                $bf_total_per_subledger += $record->gstamou;
                ++$i;
            }

            if ($i > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">GRAND TOTAL <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';
                $html .= '<td colspan="2" style="border: none; text-align: right">';
                if ($bf_total_per_subledger < 0) {
                    $html .= number_format((-1) * $bf_total_per_subledger, 2).' CR';
                } else {
                    $html .= number_format($bf_total_per_subledger, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $table = 'gl';
                $columns = 'sign, total_amount';
                $where = ['accn' => 'CL300', 'tran_type' => $transaction_type];
                $group_by = null;
                $order_by = null;
                $order_by_many = null;
                $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by, $order_by_many);
                $list = $query->result();
                foreach ($list as $record) {
                    if ($record->sign == '+') {
                        $bf_total_per_gl += $record->total_amount;
                    } else {
                        $bf_total_per_gl -= $record->total_amount;
                    }
                }

                // balance per gl
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">';
                if ($transaction_type == 'OPBAL') {
                    $html .= 'Balance b/f per GL';
                } else {
                    $html .= 'Total per CL300';
                }
                $html .= ' <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';

                $html .= '<td colspan="2" style="border: none; text-align: right">';
                if ($bf_total_per_gl < 0) {
                    $html .= number_format((-1) * $bf_total_per_gl, 2).' CR';
                } else {
                    $html .= number_format($bf_total_per_gl, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';

                $diff_amount = $this->abs_diff($bf_total_per_subledger < 0 ? $bf_total_per_subledger : (-1) * $bf_total_per_subledger, $bf_total_per_gl < 0 ? $bf_total_per_gl : (-1) * $bf_total_per_gl);
                $html .= '<tr>';
                $html .= '<td colspan="6" style="border: none; text-align: right">DIFFERENCE <strong>('.$this->custom->getDefaultCurrency().')</strong></td>';
                $html .= '<td colspan="2" style="border: none; text-align: right">';
                if ($diff_amount < 0) {
                    $html .= number_format((-1) * $diff_amount, 2).' CR';
                } else {
                    $html .= number_format($diff_amount, 2).' DR';
                }
                $html .= '</td>';
                $html .= '</tr>';
                $html .= '</tbody>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="8" align="center" style="border: none; text-align: center; color: red">No transactions found</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'gst_audit_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);

        } else {
            redirect('/gst/audit_listing');
        }
    }

    public function abs_diff($v1, $v2)
    {
        $diff = $v1 - $v2;

        return $diff < 0 ? (-1) * $diff : $diff;
    }

    public function print_ob()
    {
        if (isset($_GET['ob_type'])) {
            $ob_type = $_GET['ob_type'];
        } else {
            $ob_type = 'C';
        }

        $html = '<div style="width: 100%; margin: auto;text-align: center;"><h3>GST Opening Balance</h3></div>';

        $html .= '<table style="width: 100%;">';

        $i = 0;
        $table = 'gst_open';
        $columns = 'date, dref, rema, gsttype';
        $group_by = 'date, dref';
        $order_by = null;
        $order_by_many = 'date ASC, dref ASC';
        $where = ['status' => $ob_type];
        if ($_GET['rowID'] !== null) {
            $ob_id = $_GET['rowID'];
            $where = ['ob_id' => $ob_id, 'status' => $ob_type];
        }
        $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by);
        // print_r($this->db->last_query());
        $list = $query->result();
        foreach ($list as $row) {
            $html .= '<tr>';
            $html .= '<td colspan="5" style="border: none; height: 20px;"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>';
            $html .= '<strong>Date : </strong>'.date('d-m-Y', strtotime($row->date));
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<strong>Reference : </strong>'.$row->dref.'<br />';
            $html .= '</td>';
            $html .= '<td colspan="3">';
            $html .= '<strong>Tax : </strong>';
            if ($row->gsttype == 'I') {
                $html .= 'Input';
            } elseif ($row->gsttype == 'O') {
                $html .= 'Output';
            }
            $html .= '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="5">';
            $html .= '<strong>Remarks : </strong>'.$row->rema.'<br />';
            $html .= '</td>';
            $html .= '</tr>';

            $i = 0;
            $table = 'gst_open';
            $columns = null;
            $group_by = null;
            $order_by = 'date';
            $where = ['date' => $row->date, 'dref' => $row->dref];
            $query = $this->custom->get_tbl_data($table, $columns, $where, $group_by, $order_by);
            // print_r($this->db->last_query());
            $record_list = $query->result();
            foreach ($record_list as $value) {
                if ($i == 0) {
                    $html .= '<tr>';
                    $html .= '<th>Category</th>';
                    $html .= '<th>Iden</th>';
                    $html .= '<th style="text-align: right">Amount</th>';
                    $html .= '<th style="text-align: right">Rate</th>';
                    $html .= '<th style="text-align: right">GST Amount</th>';
                    $html .= '</tr>';
                }

                $gst_desc = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gstcate]);

                if ($value->gsttype == 'I') {
                    $iden_name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $value->iden]);
                } elseif ($value->gsttype == 'O') {
                    $iden_name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $value->iden]);
                }

                $html .= '<tr>';
                $html .= '<td>'.$value->gstcate.' : '.$gst_desc.'</td>';
                $html .= '<td>'.$value->iden.' : '.$iden_name.'</td>';
                $html .= '<td style="text-align: right">'.$value->amou.'</td>';
                $html .= '<td style="text-align: right">'.$value->gstperc.'</td>';
                $html .= '<td style="text-align: right">'.$value->gstamou.'</td>';
                $html .= '</tr>';

                ++$i;
            }
        }

        if ($i == 0) {
            $html .= '<tr>
					<td colspan="2" style="color: red; text-align: center">No Transactions</td>
				</tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'gst_ob_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'gst_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['gst', 'gst_open', 'gst_returns_filing_info', 'gst_returns_contact_info', 'gst_returns_declaration', 'gst_returns_form_5', 'gst_returns_form_7', 'gst_returns_grp_reasons'],
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
            set_flash_message('message', 'success', 'GST Datafiles Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('gst/', 'refresh');        
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'gst/blank.php';
        zapGST();
        redirect('gst/', 'refresh');
    }
}
