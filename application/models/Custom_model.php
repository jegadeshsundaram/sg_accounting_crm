 <?php
 defined('BASEPATH') or exit('No direct script access allowed');
class Custom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function replaceLogo($name)
    {
        $this->db->where('code', 'CP');
        $this->db->set('company_logo', $name);
        $this->db->update('company_profile');
    }

    public function get_tbl_data($table, $columns = null, $where, $group_by = null, $order_by = null, $order_by_many = null)
    {
        $this->db->select($columns)->from($table);

        if (!is_null($where)) {
            $this->db->where($where);
        }

        if (!is_null($group_by)) {
            $this->db->group_by($group_by);
        }

        if (!is_null($order_by)) {
            $this->db->order_by($order_by, 'ASC');
        }

        if (!is_null($order_by_many)) {
            $this->db->order_by($order_by_many);
        }

        return $this->db->get();
    }

    public function getRows($table, $where = [], $or_where = [], $group_by = null)
    {
        $this->db->select('*');
        $this->db->where($where);
        if (is_array($or_where)) {
            $this->db->or_where($or_where);
        }
        if (!is_null($group_by)) {
            $this->db->group_by($group_by);
        }
        $query = $this->db->get($table);

        return $query->result();
    }

    public function getRowsSorted($table, $where = [], $or_where = [], $sort_column = 'id', $sort = 'ASC', $limit = '')
    {
        $this->db->select('*');
        $this->db->where($where);
        if (is_array($or_where)) {
            $this->db->or_where($or_where);
        }
        if ($limit != '') {
            $this->db->limit($limit);
        }
        $this->db->order_by($sort_column.' '.$sort);
        $query = $this->db->get($table);

        // print_r($this->db->last_query());

        return $query->result();
    }

    public function getOpenStocks($table, $where = [], $sort_column = '', $sort = 'ASC')
    {
        $this->db->select('*');
        $this->db->where($where);
        if (is_array($or_where)) {
            $this->db->or_where($or_where);
        }

        $this->db->order_by($sort_column.' '.$sort);
        $query = $this->db->get($table);

        return $query->result();
    }

    public function getColumnRowsSorted($table, $column, $where = [], $or_where = [], $sort_column = 'id', $sort = 'ASC', $limit = '')
    {
        $this->db->select($column);
        $this->db->where($where);
        if (is_array($or_where)) {
            $this->db->or_where($or_where);
        }
        if ($limit != '') {
            $this->db->limit($limit);
        }
        $this->db->order_by($sort_column.' '.$sort);
        $query = $this->db->get($table);

        return $query->result();
    }

    public function getRowsWhereJoin($table, $where, $join, $join_condition)
    {
        $this->db->select(' * ')->from($table)->where($where);
        for ($i = 0; $i < count($join); ++$i) {
            $this->db->join($join[$i], $join_condition[$i]);
        }
        $query = $this->db->get();

        return $query->result();
        // d($this->db->last_query());
    }

    public function getDistinctRows($table, $where = [], $or_where = [], $distinct_column)
    {
        $this->db->select("DISTINCT($distinct_column)");
        // $this->db->select("*");
        $this->db->where($where);
        if (is_array($or_where)) {
            $this->db->or_where($or_where);
        }

        $query = $this->db->get($table);

        return $query->result();
    }

    public function getSingleRow($table, $where = []) /* get a single row from a table */
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();

        //print_r($this->db->last_query());
        return $query->row();
    }

    public function getDefaultCurrency()
    {
        $default_currency = $this->getSingleValue('company_profile', 'default_currency', ['code' => 'CP']);

        return $default_currency;
    }

    public function getLastInsertedRow($table, $grp_by)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->order_by($grp_by, 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        // $query = $this->db->select($column)->from($table)->where($where)->get();

        return $query->row();
    }

    public function populateCOAByCode($selected = '')
    {
        $this->db->select('accn, description');
        $this->db->from('chart_of_account');
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($value->accn == $selected) {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->accn.'">';
            }
            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateCOAList($selected = '')
    {
        $this->db->select('*');
        $this->db->from('chart_of_account');
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($value->coa_id == $selected) {
                $coa_options .= '<option value="'.$value->coa_id.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->coa_id.'">';
            }
            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateCOASalesList($selected = '')
    {
        $this->db->select('*');
        $this->db->from('chart_of_account');
        $this->db->where(['accn like' => 'S0%', 'accn !=' => 'S0100']);
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($value->accn == $selected) {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } elseif ($value->accn == 'S0001') {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->accn.'">';
            }
            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateCOAPurchasesList($selected = '')
    {
        $this->db->select('*');
        $this->db->from('chart_of_account');
        $this->db->where("(accn like 'C0%' || accn like 'E0%') AND accn != 'C0999' AND accn != 'C0100'");
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($value->accn == $selected) {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } elseif ($value->accn == 'C0001') {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->accn.'">';
            }

            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateCOABankList($selected = '')
    {
        $this->db->select('*');
        $this->db->from('chart_of_account');
        $this->db->where("accn in ('CA101', 'CA102', 'CA103', 'CA104', 'CA105', 'CA106', 'CA107', 'CA108', 'CA109')"); // CA110 is removed as Per David Comment on FEB 15
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($selected == $value->accn) {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } elseif ($value->accn == 'CA101') {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->accn.'">';
            }
            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateCOABankListWithFB($selected = '')
    {
        $this->db->select('*');
        $this->db->from('chart_of_account');
        $this->db->where("accn in ('CA101', 'CA102', 'CA103', 'CA104', 'CA105', 'CA106', 'CA107', 'CA108', 'CA109', 'CA110')");
        $this->db->order_by('accn', 'ASC');
        $query = $this->db->get();
        $coa_data = $query->result();

        $coa_options = '<option value="">-- Select --</option>';
        foreach ($coa_data as $key => $value) {
            if ($selected == $value->accn) {
                $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
                // } elseif ($value->accn == 'CA101') {
                //    $coa_options .= '<option value="'.$value->accn.'" selected="selected">';
            } else {
                $coa_options .= '<option value="'.$value->accn.'">';
            }
            $coa_options .= $value->accn.' : '.str_replace("'", '', $value->description);
            $coa_options .= '</option>';
        }

        return $coa_options;
    }

    public function populateFBAccounts($selected = '')
    {
        $this->db->select('*');
        $this->db->from('master_foreign_bank');
        $this->db->order_by('fb_code, fb_name', 'ASC, ASC');
        $query = $this->db->get();
        $fb_accn_data = $query->result();

        $fb_options = '<option value="">-- Select --</option>';
        foreach ($fb_accn_data as $key => $value) {
            if ($selected == $value->fb_code) {
                $fb_options .= '<option value="'.$value->fb_code.'" selected="selected">';
            } else {
                $fb_options .= '<option value="'.$value->fb_code.'">';
            }
            $fb_options .= $value->fb_code.' : '.$value->fb_name;
            $fb_options .= '</option>';
        }

        return $fb_options;
    }

    public function populateMPDFStyle()
    {
        $style = '<style type="text/css">
                    body {
                        font-size: 1rem;
                        font-family: sans-serif;
                    }
                    table { width: 100%; border-collapse: collapse; }
                    table th {
                        font-weight: bold;
                        background: gainsboro;
                        padding: 7px;
                        font-variant: petite-caps;
                        border: 1px solid gainsboro;
                        text-align: left;
                    }			
                    table td {
                        border: 1px solid gainsboro;
                        padding: 7px; text-align: left;
                    }
                    tfoot td {
                        font-weight: bold;
                        border: none;
                        border-top: 2px solid gainsboro;
                    }
                    .dummy-row th {
                        background: #fff !important;
                        border: none !important;
                        color: #fff !important;
                    }                    
                </style>';

        return $style;
    }

    public function printMPDF($file, $document)
    {
        include 'application/third_party/mpdf/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
        'margin_left' => '10',
        'margin_right' => '10',
        'margin_top' => '10',
        'margin_bottom' => '20',
        ]);
        $mpdf->showWatermarkText = true;
        $mpdf->setFooter('Page {PAGENO} of {nb}');
        $mpdf->SetHeader();
        $mpdf->WriteHTML($document);
        $mpdf->Output($file, 'I');

        exit;
    }

    public function populateCompanyHeader_old()
    {
        $company_where = ['code' => 'CP'];
        $company_details = $this->getSingleRow('company_profile', $company_where);

        $html = '';
        $html .= '<h3>'.$company_details->company_name.'</h3>';
        $html .= $company_details->company_address;
        $html .= '<br />';

        if (strlen($company_details->gst_reg_no) != 0) {
            $html .= '<u>GST Register Number:</u> ';
            $html .= $company_details->gst_reg_no;
        }

        if (strlen($company_details->gst_reg_no) != 0 && strlen($company_details->uen_no) != 0) {
            $html .= ' | ';
        } elseif (strlen($company_details->gst_reg_no) != 0 && strlen($company_details->uen_no) == 0) {
            $html .= '<br /> ';
        }

        if (strlen($company_details->uen_no) != 0) {
            $html .= '<u>UEN No:</u> ';
            $html .= $company_details->uen_no;
            $html .= '<br />';
        }

        if (strlen($company_details->phone) != 0) {
            $html .= '<u>Phone:</u> ';
            $html .= $company_details->phone;
        }

        if (strlen($company_details->phone) != 0 && strlen($company_details->fax) != 0) {
            $html .= ' | ';
        }

        if (strlen($company_details->fax) != 0) {
            $html .= '<u>Fax:</u> ';
            $html .= $company_details->fax;
            $html .= '<br />';
        }

        if (strlen($company_details->company_email) != 0) {
            $html .= '<u>Email:</u> ';
            $html .= $company_details->company_email;
            $html .= '<br />';
        }

        return $html;
    }

    public function populateCompanyHeader()
    {
        $details = '';
        $company_data = $this->getSingleRow('company_profile', ['code' => 'CP']);
        if ($company_data->company_logo !== '') {
            $img_src = UPLOAD_PATH.'site/'.$company_data->company_logo;
            $details .= '<img src="'.$img_src.'" height="70px" width="170px" />';
        }

        if ($company_data->company_logo !== '' && $company_data->company_name !== '') {
            $details .= '<br /><br />';
        }

        if ($company_data->company_name !== '') {
            $details .= '<h3>'.$company_data->company_name.'</h3>';
        }

        if ($company_data->company_address !== '') {
            $details .= $company_data->company_address;
        }

        if ($company_data->gst_reg_no != '') {
            $details .= '<br />';
            $details .= 'GST Register No: '.$company_data->gst_reg_no;
        }

        if ($company_data->gst_reg_no != '' && $company_data->uen_no != '') {
            $details .= ' | ';
        }

        if ($company_data->gst_reg_no == '') {
            $details .= '<br />';
        }

        if ($company_data->uen_no != '') {
            $details .= 'UEN No: '.$company_data->uen_no;
        }

        if ($company_data->phone != '') {
            $details .= '<br />';
            $details .= 'Phone: '.$company_data->phone;
        }

        if ($company_data->phone != '' && $company_data->fax != '') {
            $details .= ' | ';
        }

        if ($company_data->fax != '') {
            $details .= 'Fax: '.$company_data->fax;
        }

        if ($company_data->company_email != '') {
            $details .= '<br />';
            $details .= 'Email: '.$company_data->company_email;
        }

        return $details;
    }

    public function populateCustomerAddress($data)
    {
        $address = '';
        $address_count = 0;
        if ($data->bldg_number !== null && $data->bldg_number !== '') {
            $address .= $data->bldg_number.', ';
            ++$address_count;
        }

        if ($data->street_name !== null && $data->street_name !== '') {
            $address .= $data->street_name;
            ++$address_count;
        }

        if ($data->address_line_2 !== null && $data->address_line_2 !== '') {
            $address .= '<br />'.$data->address_line_2;
            ++$address_count;
        }

        if ($address_count > 0) {
            $address .= '<br />';
        }

        $address .= 'SINGAPORE';

        if ($data->postal_code !== null && $data->postal_code !== '') {
            $address .= ', '.$data->postal_code;
            ++$address_count;
        }

        if ($data->phone != null && $data->phone !== '') {
            $address .= '<br />';
            $address .= $data->phone;
        }

        if ($data->email != null && $data->email !== '') {
            $address .= '<br />';
            $address .= $data->email;
        }

        $currency = $this->getSingleValue('ct_currency', 'code', ['currency_id' => $data->currency_id]);
        $address .= '<br />';
        $address .= '<u>Currency:</u> '.$currency;

        return $address;
    }

    public function getStdGSTRate() {
        $gst = $this->getLastInsertedRow('gst_std_rate', 'gsr_id');
        return $gst->rate;
    }

    public function populateSupplierAddress($data)
    {
        $address = '';
        $address_count = 0;
        if ($data->bldg_number !== null && $data->bldg_number !== '') {
            $address .= $data->bldg_number.', ';
            ++$address_count;
        }

        if ($data->street_name !== null && $data->street_name !== '') {
            $address .= $data->street_name;
            ++$address_count;
        }

        if ($data->address_line_2 !== null && $data->address_line_2 !== '') {
            $address .= '<br />'.$data->address_line_2;
            ++$address_count;
        }

        if ($address_count > 0) {
            $address .= '<br />';
        }

        $address .= 'SINGAPORE';

        if ($data->postal_code !== null && $data->postal_code !== '') {
            $address .= ', '.$data->postal_code;
            ++$address_count;
        }

        if ($data->phone != null && $data->phone !== '') {
            $address .= '<br />';
            $address .= $data->phone;
        }

        if ($data->email != null && $data->email !== '') {
            $address .= '<br />';
            $address .= $data->email;
        }

        $currency = $this->getSingleValue('ct_currency', 'code', ['currency_id' => $data->currency_id]);
        $address .= '<br />';
        $address .= '<u>Currency:</u> '.$currency;

        return $address;
    }

    public function populateEmailHeaders() {
        // Load PHPMailer library
        $this->load->library('phpmailer_lib');

        // PHPMailer object
        $mail = $this->phpmailer_lib->load();

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'topjeg.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'topform@topjeg.com';
        $mail->Password = 'Thamayanthy0!*';
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPDebug = 4;
        $mail->Port = 465;

        $where = ['code' => 'CP'];
        $company_data = $this->getSingleRow('company_profile', $where);

        $mail->setFrom('support@topjeg.com', $company_data->company_name);
        $mail->addReplyTo($company_data->company_email, $company_data->company_name);

        $mail->isHTML(true);

        return $mail;
    }

    public function getGstRow($table, $column, $where = []) /* get a single row from a table */
    {
        $query = $this->db->select($column)->from($table)->where($where)->get();

        // d($this->db->last_query());
        return $query->row();
    }

    public function getTotalCount($table)
    {
        /* get total no.of records count of given table */
        return $this->db->count_all_results($table);
    }

    public function getCount($table, $where = [])
    {
        $this->db->from($table);
        if (!is_null($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function insertRow($table, $data) /* insert new row into a table */
    {
        $result = $this->db->insert($table, $data);

        if ($result) {
            return $this->db->insert_id();
        } else {
            $error = $this->db->error(); // Has keys 'code' and 'message'
            echo '<BR><BR>';
            print_r($error);
            echo '<BR><BR>';

            return $error;
        }
    }

    public function insertData($table, $data)
    {
        $this->db->insert($table, $data);
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateRow($table, $data, $where, $or_where = []) /* update existing row in a table */
    {
        $this->db->where($where);
        $this->db->or_where($or_where);
        $result = $this->db->update($table, $data);
        // echo $this->db->last_query();
        if ($result) {
            return 'updated';
        } else {
            return 'error';
        }
    }

    public function updateRowWithoutWhere($table, $data) /* update existing row in a table */
    {
        $result = $this->db->update($table, $data);
        if ($result) {
            return 'updated';
        } else {
            return 'error';
        }
    }

    public function findRow($table, $where) /* update existing row in a table */
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();

        return $query->result();
    }

    public function deleteRow($table, $where, $or_where = []) /* delete a row from table */
    {
        $this->db->where($where);
        $this->db->or_where($or_where);
        $result = $this->db->delete($table);
        if ($result) {
            return 'deleted';
        } else {
            return 'error';
        }
    }

    public function checkTableValues($table, $where) /* get single column value from table */
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();
        $res = !empty($query->result());
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function getSingleValue($table, $column, $where) /* get single column value from table */
    {
        $query = $this->db->select($column)->from($table)->where($where)->get();
        $res = !empty($query->result());
        if ($res) {
            return $query->row()->$column;
        } else {
            return null;
        }
    }

    public function getMultiValues($table, $columns, $where) /* get single column value from table */
    {
        $query = $this->db->select($columns)->from($table)->where($where)->get();
        // print_r($this->db->last_query());

        return $query->row();
    }

    public function customQuery($query, $return_result = false) /* runs custom query */
    {
        $qry = $this->db->query($query);
        if ($return_result) {
            return $this->getResult($qry);
        } else {
            return $qry;
        }
    }

    public function getResult($query_result, $array = false)
    {
        if (!empty($query_result->result())) {
            if ($array) {
                return $query_result->result_array();
            } else {
                return $query_result->result();
            }
        } else {
            return null;
        }
    }

    public function createDropdown($table, $columns = [], $caption = 'Value', $where = null, $selected = null) // $columns = array with two columns name of table , from one column will be key and other value of options of dropdown. $selected = key value from $columns array.
    {
        $key = $columns[0];
        if ($where != null) {
            $query = $this->db->select($columns)->from($table)->where($where)->get();
        } else {
            $query = $this->db->select($columns)->from($table)->get();
        }
        if (isset($columns[2])) {
            $value1 = $columns[1];
            $value2 = $columns[2];
        } else {
            $value = $columns[1];
        }
        $rows = $this->getResult($query);
        $drop_options = "<option value='' >-- Select ".$caption.' --</option>';
        if ($rows) {
            if ($selected != null) {
                foreach ($rows as $row) {
                    if ($selected == $row->$key) {
                        if (isset($columns[2])) {
                            $drop_options .= "<option value='".$row->$key."' selected='selected'>".$row->$value1.' '.$row->$value2.'</option>';
                        } else {
                            $drop_options .= "<option value='".$row->$key."' selected='selected'>".$row->$value.'</option>';
                        }
                    } else {
                        if (isset($columns[2])) {
                            $drop_options .= "<option value='".$row->$key."'>".$row->$value1.' '.$row->$value2.'</option>';
                        } else {
                            $drop_options .= "<option value='".$row->$key."'>".$row->$value.'</option>';
                        }
                    }
                }
            } else {
                foreach ($rows as $row) {
                    // d($row->$value2);
                    if (isset($columns[2])) {
                        $drop_options .= "<option value='".$row->$key."'>".$row->$value1.' '.$row->$value2.'</option>';
                    } else {
                        $drop_options .= "<option value='".$row->$key."'>".$row->$value.'</option>';
                    }
                }
            }
        }

        return $drop_options;
    }

    public function getMaxID_six_digit($table, $column) // get six digit number from specified table and column
    {
        $sql = "SELECT MAX(CAST(SUBSTRING($column,4)AS SIGNED)) AS max_field FROM `$table`";
        $result = $this->customQuery($sql);
        $row = $this->getResult($result);
        $count = $row[0]->max_field;
        $MaxIDVN = $count + 1;
        $MaxIDVN = str_pad($MaxIDVN, 6, '0', STR_PAD_LEFT);

        return $MaxIDVN;
    }

    public function get_column_sum($table, $column, $where)
    {
        $this->db->select_sum($column);
        $this->db->where($where);
        $sum_result = $this->db->from($table)->get();
        if (!empty($sum_result->result())) {
            return $sum_result->row($column);
        } else {
            return null;
        }
    }

    public function checkAvailability($table, $where)
    {
        $query = $this->db->select('*')->from($table)->where($where)->get();
        $count = $query->num_rows();
        if ($count >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function findInSet($table, $column, $value, $where = null)
    {
        $this->db->select('*');
        $this->db->from($table);
        $find_in_set = [
           "FIND_IN_SET('$value',$column) !=" => '0',
        ];
        if ($where) {
            $where = array_merge($where, $find_in_set);
        } else {
            $where = $find_in_set;
        }
        $this->db->where($where);
        $query = $this->db->get();

        // return $this->db->last_query();
        return $query->result();
    }

    public function login($username, $password)
    {
        $query = $this->db->select('username, email, id, password')
        ->where(['username' => $username, 'password' => encrypt($password, ENCRYPTION_KEY)])
        ->limit(1)
        ->order_by('id', 'desc')
        ->get('admin');
        // echo $this->db->last_query();
        if ($query->num_rows() === 1) {
            return true;
        } else {
            return false;
        }
    }

    public function createDropdownSelect2($table, $columns = [], $caption = 'Value', $separator = [' '], $where = null, $selected = [], $sort_column = '', $sort = 'ASC')
    {
        if ($sort_column == '') {
            $sort_column = $columns[1];
        }
        if ($where != null && !is_null($where)) {
            $query = $this->db->select($columns)->from($table)->where($where)->order_by($sort_column.' '.$sort)->group_by('currency')->get();
        } else {
            $query = $this->db->select($columns)->from($table)->order_by($sort_column.' '.$sort)->group_by('currency')->get();
        }
        $id = $columns[0];
        unset($columns[0]);
        $rows = $this->getResult($query);
        $drop_options = "<option value=''>-- Select ".$caption.' --</option>';
        if ($rows) {
            if (!empty($selected)) {
                foreach ($rows as $row) {
                    if (in_array($row->$id, $selected)) {
                        $drop_options .= "<option value='".$row->$id."' selected='selected'>";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    } else {
                        $drop_options .= "<option value='".$row->$id."' >";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    }
                }
            } else {
                foreach ($rows as $row) {
                    $drop_options .= "<option value='".$row->$id."'>";
                    for ($i = 1; $i <= count($columns); ++$i) {
                        $col_value = $columns[$i];
                        $col_sep = $separator[$i - 1];
                        $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                    }
                    $drop_options .= '</option>';
                }
            }
        }

        return $drop_options;
    }

    public function createDropdownSelectJoin($table, $columns = [], $caption = 'Value', $separator = [' '], $join = null, $join_condition = null, $where = null, $selected = [], $sort_column = '', $sort = 'ASC')
    {
        if ($sort_column == '') {
            $sort_column = $columns[1];
        }
        if ($where != null && !is_null($where)) {
            $query = $this->db->select($columns)->from($table)->where($where)->order_by($sort_column.' '.$sort);
        } elseif ($join) {
            $query = $this->db->select($columns)->from($table)->order_by($sort_column.' '.$sort);
        }

        if ($join != null && !is_null($join)) {
            $this->db->join($join, $join_condition);
        }
        $query = $this->db->get();
        $id = $columns[0];
        unset($columns[0]);
        $rows = $this->getResult($query);
        if ($caption == 'Value') {
            $drop_options = '<option value="">-- Select One --</option>';
        } else {
            $drop_options = '<option value="">';
            $drop_options .= '-- Select '.$caption;
            $drop_options .= ' --</option>';
        }

        if ($rows) {
            if (!empty($selected)) {
                foreach ($rows as $row) {
                    if (in_array($row->$id, $selected)) {
                        $drop_options .= '<option value="'.$row->$id.'" selected="selected">';
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            // $drop_options .= ''.str_replace("'", "\'", $row->$col_value)." $col_sep";
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    } else {
                        $drop_options .= '<option value="'.$row->$id.'" >';
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            // $drop_options .= ''.str_replace("'", "\'", $row->$col_value)." $col_sep";
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    }
                }
            } else {
                foreach ($rows as $row) {
                    $drop_options .= '<option value="'.$row->$id.'" >';
                    for ($i = 1; $i <= count($columns); ++$i) {
                        $col_value = $columns[$i];
                        $col_sep = $separator[$i - 1];
                        // $drop_options .= ''.str_replace("'", "\'", $row->$col_value)." $col_sep";
                        $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                    }
                    $drop_options .= '</option>';
                }
            }
        }

        return $drop_options;
    }

    public function createDropdownSelect1($table, $columns = [], $caption = 'Value', $separator = [' '], $where = null, $selected = [], $sort_column = '', $sort = 'ASC')
    {
        // d($where != null);
        if ($sort_column == '') {
            $sort_column = $columns[1];
        }
        if ($where != null && !is_null($where)) {
            $query = $this->db->select($columns)->from($table)->where($where)->order_by($sort_column.' '.$sort)->get();
        } else {
            $query = $this->db->select($columns)->from($table)->order_by($sort_column.' '.$sort)->get();
        }
        // echo $this->db->last_query();die;
        $id = $columns[0];
        unset($columns[0]);
        $rows = $this->getResult($query);
        $drop_options = "<option value=''>-- Select ".$caption.' --</option>';
        if ($rows) {
            if (!empty($selected)) {
                foreach ($rows as $row) {
                    if (in_array($row->$id, $selected)) {
                        $drop_options .= "<option value='".$row->$id."' selected='selected'>";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    } else {
                        $drop_options .= "<option value='".$row->$id."' >";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    }
                }
            } else {
                foreach ($rows as $row) {
                    $drop_options .= "<option value='".$row->$id."'>";
                    for ($i = 1; $i <= count($columns); ++$i) {
                        $col_value = $columns[$i];
                        $col_sep = $separator[$i - 1];
                        $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                    }
                    $drop_options .= '</option>';
                }
            }
        }

        return $drop_options;
    }

    public function createDropdownSelect_1($table, $columns = [], $table2, $column_2, $where_2, $caption = 'Value', $separator = [' '], $where = null, $selected = [], $sort_column = '', $sort = 'ASC')
    {
        // d($where != null);
        if ($sort_column == '') {
            $sort_column = $columns[1];
        }
        if ($where != null && !is_null($where)) {
            $query = $this->db->query("SELECT quotation_master.quotation_id, quotation_master.quotation_ref_no, master_customer.name, master_customer.code,ct_currency.code
from quotation_master  join master_customer join ct_currency where quotation_master.customer_id = master_customer.customer_id AND quotation_master.invoice =0 AND quotation_master.quotation_status = 'SUCCESSFUL' AND master_customer.currency_id= ct_currency.currency_id ORDER BY quotation_master.quotation_ref_no DESC");

            // $query = $this->db->select($columns)->from($table)->where($where)->order_by($sort_column . ' ' . $sort)->get();
            // echo $this->db->last_query();die;
        } else {
            $query = $this->db->select($columns)->from($table)->order_by($sort_column.' '.$sort)->get();
            // die();
        }
        // echo $this->db->last_query();die;
        $id = $columns[0];
        unset($columns[0]);
        $rows = $this->getResult($query);
        $drop_options = "<option value=''>-- Select ".$caption.' --</option>';
        if ($rows) {
            if (!empty($selected)) {
                foreach ($rows as $row) {
                    if (in_array($row->$id, $selected)) {
                        $drop_options .= "<option value='".$row->$id."' selected='selected'>";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    } else {
                        $drop_options .= "<option value='".$row->$id."' >";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                        }
                        $drop_options .= '</option>';
                    }
                }
            } else {
                foreach ($rows as $row) {
                    $drop_options .= "<option value='".$row->$id."'>";
                    for ($i = 1; $i <= count($columns); ++$i) {
                        $col_value = $columns[$i];
                        $col_sep = $separator[$i - 1];
                        $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                    }
                    $drop_options .= '</option>';
                }
            }
        }

        return $drop_options;
    }

    public function populateCurrencyList($selected = '')
    {
        $this->db->select('*');
        $this->db->from('ct_currency');
        $this->db->order_by('code, description', 'ASC, ASC');
        $query = $this->db->get();
        $currency_data = $query->result();
        $ddm_options = "<option value=''>Select Currency</option>";
        foreach ($currency_data as $key => $value) {
            if (!empty($selected) && $selected == $value->currency_id) {
                $ddm_options .= "<option value='".$value->currency_id."' selected='selected'>";
            } else {
                $ddm_options .= "<option value='".$value->currency_id."'>";
            }

            $ddm_options .= $value->code.' : '.$value->description.' (Rate: '.$value->rate.')';
            $ddm_options .= '</option>';
        }

        return $ddm_options;
    }

    // this function is to create age Group dropdown it is customize we can not use it in any other place
    public function createDropdownSelect($table, $columns = [], $caption = 'Value', $separator = [' '], $where = null, $selected = [], $sort_column = '', $sort = 'ASC')
    {
        // d($where != null);
        if ($sort_column == '') {
            $sort_column = $columns[1];
        }
        if ($where != null && !is_null($where)) {
            $query = $this->db->select($columns)->from($table)->where($where)->order_by($sort_column.' '.$sort)->get();
        } else {
            $query = $this->db->select($columns)->from($table)->order_by($sort_column.' '.$sort)->get();
        }

        $id = $columns[0];
        $code = $columns[1];
        unset($columns[0]);
        $rows = $this->getResult($query);

        if ($caption === 'Document Ref No') {
            $drop_options = '<option value="" disabled>-- Select --</option>';
        } else {
            $drop_options = '<option value="">-- Select --</option>';
        }

        if ($rows) {
            if (!empty($selected) && $table != 'ct_gst') {
                foreach ($rows as $row) {
                    if (in_array($row->$id, $selected)) {
                        $drop_options .= "<option value='".$row->$id."' selected='selected'>";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            if ($i == 3) {
                                $customer_result = $this->getRows('master_customer', ['customer_id' => $selected[0]]);
                                $currency_id = $customer_result[0]->currency_id;

                                $currency_result = $this->getRows('ct_currency', ['currency_id' => $currency_id]);

                                $col_value = $currency_result[0]->code;
                                $col_sep = $separator[$i - 1];

                                $drop_options .= ''.str_replace("'", '', $col_value)." $col_sep";
                            } else {
                                $col_value = $columns[$i];
                                $col_sep = $separator[$i - 1];
                                $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                            }
                        }
                        $drop_options .= '</option>';
                    } else {
                        $drop_options .= "<option value='".$row->$id."' >";
                        for ($i = 1; $i <= count($columns); ++$i) {
                            if ($i == 3) {
                                $currency_id = $columns[$i];
                                $currency_result = $this->getRows('ct_currency', ['currency_id' => $row->$currency_id]);

                                $col_value = $currency_result[0]->code;
                                $col_sep = $separator[$i - 1];

                                $drop_options .= ''.str_replace("'", '', $col_value)." $col_sep";
                            } else {
                                $col_value = $columns[$i];
                                $col_sep = $separator[$i - 1];
                                $drop_options .= ''.str_replace("'", '', $row->$col_value)." $col_sep";
                            }
                        }
                        $drop_options .= '</option>';
                    }
                }
            } elseif (!empty($selected) && $table == 'ct_gst') {
                foreach ($rows as $row) {
                    if (in_array($row->$code, $selected)) {
                        $drop_options .= '<option value="'.$row->$id.'" selected="selected">';
                    } else {
                        $drop_options .= '<option value="'.$row->$id.'">';
                    }

                    for ($i = 1; $i <= count($columns); ++$i) {
                        if ($i == 1) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];

                            $drop_options .= ''.substr($row->$col_value, 0, 250)." $col_sep";
                        } elseif ($i == 2) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.substr($row->$col_value, 0, 250)." $col_sep";
                        } elseif ($i == 3) {
                            $currency_id = $columns[$i];
                            $currency_result = $this->getRows('ct_currency', ['currency_id' => $row->$currency_id]);
                            // var_dump($currency_result[0]->code); exit;
                            // $col_value = $currency_result[0]->code;
                            $col_value = $currency_result[0]->code;
                            $col_sep = $separator[$i - 1];
                            $drop_options .= ''.substr($row->gst_rate, 0, 3)."$col_sep";
                        }
                    }
                    $drop_options .= '</option>';
                }
            } else {
                foreach ($rows as $row) {
                    $drop_options .= '<option value="'.$row->$id.'">';
                    for ($i = 1; $i <= count($columns); ++$i) {
                        if ($i == 1) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];

                            $drop_options .= ''.substr(str_replace("'", '', $row->$col_value), 0, 250)." $col_sep";
                        } elseif ($i == 2) {
                            $col_value = $columns[$i];
                            $col_sep = $separator[$i - 1];
                            if ($table != 'master_billing') {
                                if ($table == 'ct_gst') {
                                    $drop_options .= ''.substr($row->$col_value, 0, 250)." $col_sep";
                                } else {
                                    $drop_options .= ''.substr($row->$col_value, 0, 11)." $col_sep";
                                }
                            } else {
                                $drop_options .= ''.substr(strip_tags($row->$col_value), 0, 26)." $col_sep";
                            }
                        } elseif ($i == 3) {
                            $currency_id = $columns[$i];
                            $currency_result = $this->getRows('ct_currency', ['currency_id' => $row->$currency_id]);
                            // var_dump($currency_result[0]->code); exit;
                            // $col_value = $currency_result[0]->code;

                            $col_value = $currency_result[0]->code;
                            $col_sep = $separator[$i - 1];
                            if ($table != 'master_billing') {
                                if ($table == 'ct_gst') {
                                    $drop_options .= ''.substr($row->gst_rate, 0, 3)."$col_sep";
                                } else {
                                    $drop_options .= ''.substr($col_value, 0, 3)." $col_sep";
                                }
                            } else {
                                // if($table=='ct_gst') print_r($row->$col_value);
                                $drop_options .= ''.substr($row->$col_value, 0, 11)." $col_sep";
                            }
                        }
                    }
                    $drop_options .= '</option>';
                }
            }
        }

        return $drop_options;
    }

    public function setAutoIncrement($table, $table_id, $increment_value = null)
    {
        $value = 1;
        if ($increment_value == null) {
            $result = $this->db->select_max($table_id, 'value')->get($table)->row();
            if ($result) {
                $value = $result->value + 1;
            }
        } else {
            $value = $increment_value;
        }
        $alterQry = "ALTER TABLE $table AUTO_INCREMENT = $value";

        return $this->customQuery($alterQry);
    }

    public function convert_number_to_words($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion',
      ];

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.$this->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.$this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}
