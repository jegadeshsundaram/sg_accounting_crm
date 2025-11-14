<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Combo_tables extends MY_Controller
{
    public function currency()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1);
        $this->body_file = 'combo_tables/currency/listing.php';
    }

    public function add_currency()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'combo_tables/currency/add.php';
    }

    public function edit_currency($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_currency', ['currency_id' => $row_id]);
            if ($row) {
                $this->body_vars['currency'] = $currency = $row;
            }
            $this->body_file = 'combo_tables/currency/edit.php';
        }
    }

    public function view_currency($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_currency', ['currency_id' => $row_id]);
            if ($row) {
                $this->body_vars['currency'] = $currency = $row;
            }
            $this->body_file = 'combo_tables/currency/edit.php';
        }
    }

    public function gst()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1);
        $this->body_file = 'combo_tables/gst/listing.php';
    }

    public function add_gst()
    {
        is_logged_in('admin');
        has_permission();
        $gst_type_arr = ['purchase', 'supply'];
        $this->body_vars['gst_types'] = createSimpleDropdown($gst_type_arr, 'GST Type');
        $this->body_file = 'combo_tables/gst/add.php';
    }

    public function edit_gst($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_gst', ['gst_id' => $row_id]);
            if ($row) {
                $this->body_vars['gst'] = $gst = $row;
                $gst_type_arr = ['purchase', 'supply'];
                $this->body_vars['gst_types'] = createSimpleDropdown($gst_type_arr, 'GST Type', mb_strtolower($row->gst_type));
            }
            $this->body_file = 'combo_tables/gst/edit.php';
        }
    }

    public function view_gst($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_gst', ['gst_id' => $row_id]);
            if ($row) {
                $this->body_vars['gst'] = $gst = $row;
                $gst_type_arr = ['purchase', 'supply'];
                $this->body_vars['gst_types'] = createSimpleDropdown($gst_type_arr, 'GST Type', mb_strtolower($row->gst_type));
            }
            $this->body_file = 'combo_tables/gst/edit.php';
        }
    }

    public function country()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1);
        $this->body_file = 'combo_tables/country/listing.php';
    }

    public function add_country()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'combo_tables/country/add.php';
    }

    public function edit_country($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_country', ['country_id' => $row_id]);
            if ($row) {
                $this->body_vars['country'] = $country = $row;
            }
            $this->body_file = 'combo_tables/country/edit.php';
        }
    }

    public function view_country($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ct_country', ['country_id' => $row_id]);
            if ($row) {
                $this->body_vars['country'] = $country = $row;
            }
            $this->body_file = 'combo_tables/country/edit.php';
        }
    }

    public function ye_revision_options()
    {
        is_logged_in('admin');
        has_permission();
    }

    public function ye_revision()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 0, 0, 1);
    }

    public function add_ye_revision()
    {
        is_logged_in('admin');
        has_permission();

        $currency_list = $this->currency_list();

        $this->body_vars['currency_list'] = createSimpleDropdown($currency_list, 'Currency');

        $this->body_file = 'combo_tables/ye_revision/add.php';
    }

    public function currency_list()
    {
        // Bind - Currency Codes from AR
        $sql_currency_list_AR = 'SELECT currency FROM accounts_receivable group by currency';
        $query_currency_list_AR = $this->db->query($sql_currency_list_AR);
        $currency_list_AR = $query_currency_list_AR->result();
        $currency_codes_array = [];

        foreach ($currency_list_AR as $key => $value) {
            if ($value->currency !== 'SGD') {
                $currency_codes_array[] = $value->currency;
            }
        }

        $sql_currency_list_AP = 'SELECT currency FROM accounts_payable group by currency';
        $query_currency_list_AP = $this->db->query($sql_currency_list_AP);
        $currency_list_AP = $query_currency_list_AP->result();

        foreach ($currency_list_AP as $key => $value) {
            if ($value->currency !== 'SGD') {
                $currency_codes_array[] = $value->currency;
            }
        }

        $sql_currency_list_FB = 'SELECT currency FROM foreign_bank group by currency';
        $query_currency_list_FB = $this->db->query($sql_currency_list_FB);
        $currency_list_FB = $query_currency_list_FB->result();

        foreach ($currency_list_FB as $key => $value) {
            if ($value->currency !== 'SGD') {
                $currency_codes_array[] = $value->currency;
            }
        }

        $unique_currency_codes = array_unique($currency_codes_array);
        sort($unique_currency_codes);

        return $unique_currency_codes;
    }

    public function edit_ye_revision($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ye_revision', ['r_id' => $row_id]);
            if ($row) {
                $this->body_vars['revision_data'] = $revision_data = $row;
                $currency_list = $this->currency_list();
                $this->body_vars['currency_list'] = createSimpleDropdown($currency_list, 'Currency', $row->currency);
            }

            $this->body_file = 'combo_tables/ye_revision/edit.php';
        }
    }

    public function view_ye_revision($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('ye_revision', ['r_id' => $row_id]);
            if ($row) {
                $this->body_vars['revision_data'] = $revision_data = $row;
                $currency_list = $this->currency_list();
                $this->body_vars['currency_list'] = createSimpleDropdown($currency_list, 'Currency', $row->currency);
            }

            $this->body_file = 'combo_tables/ye_revision/edit.php';
        }
    }

    public function print_currencies()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>CURRENCIES</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table style="width: 500px;" align="center">
            <tr>
                <th style="width: 100px">Code</th>
                <th>Description</th>
                <th style="width: 140px; text-align: right">Rate</th>
            </tr>
        ';

        $this->db->select('*');
        $this->db->from('ct_currency');
        $query = $this->db->get();
        $currency_data = $query->result();
        foreach ($currency_data as $key => $value) {
            $html .= '<tr>
                    <td>'.$value->code.'</td>
                    <td>'.$value->description.'</td>
                    <td style="text-align: right">'.$value->rate.'</td>
                </tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'currencies_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_gst()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>GST</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
            <tr>
                <th style="width: 75px">Code</th>
                <th style="width: 85px">Rate (%)</th>
                <th style="width: 85px">Type</th>
                <th>Description</th>
            </tr>
        ';

        $this->db->select('*');
        $this->db->from('ct_gst');
        $query = $this->db->get();
        $gst_data = $query->result();
        foreach ($gst_data as $key => $value) {
            $html .= '<tr>
                    <td>'.$value->gst_code.'</td>
                    <td align="center">'.$value->gst_rate.'</td>
                    <td>'.$value->gst_type.'</td>
                    <td>'.$value->gst_description.'</td>
                </tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'gst_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }

    public function print_country()
    {
        is_logged_in('admin');
        has_permission();

        $html = '<table style="border: none; width: 100%; border-bottom: 1px solid #f5f5f5">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>COUNTRIES</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Report Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
            <tr>
                <th width="100">Code</th>
                <th>Name</th>
            </tr>
        ';

        $this->db->select('*');
        $this->db->from('ct_country');
        $query = $this->db->get();
        $country_data = $query->result();
        foreach ($country_data as $key => $value) {
            $html .= '<tr>
                    <td>'.$value->country_code.'</td>
                    <td>'.$value->country_name.'</td>
                </tr>';
        }

        $html .= '</table>';

        $style = $this->custom->populateMPDFStyle();
        $document = $style.$html;

        $file = 'countries_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $document);
    }
}
