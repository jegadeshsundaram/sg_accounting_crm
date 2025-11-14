<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Master_files extends MY_Controller
{
    public function customer()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1);
        $this->body_file = 'master_files/customer/listing.php';
    }

    public function add_customer()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->populateCurrencyList();
        $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' ']);
        $this->body_file = 'master_files/customer/add.php';
    }

    public function edit_customer($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_customer', ['customer_id' => $row_id]);
            if ($row) {
                $this->body_vars['customer'] = $row;
                $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
                $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' '], '', [$row->country_id]);
                $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            }
            $this->body_file = 'master_files/customer/edit.php';
        }
    }

    public function view_customer($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_customer', ['customer_id' => $row_id]);
            if ($row) {
                $this->body_vars['customer'] = $customer = $row;
                $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
                $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' '], '', [$row->country_id]);
                $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
                $this->body_vars['mode'] = 'view';
            }

            $this->body_file = 'master_files/customer/edit.php';
        }
    }

    public function supplier()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 1);
        $this->body_file = 'master_files/supplier/listing.php';
    }

    public function add_supplier()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->populateCurrencyList();
        $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' ']);
        $this->body_file = 'master_files/supplier/add.php';
    }

    public function edit_supplier($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $row_id]);
            $this->body_vars['supplier'] = $row;
            $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
            $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' '], '', [$row->country_id]);
            $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            $this->body_file = 'master_files/supplier/edit.php';
        }
    }

    public function view_supplier($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_supplier', ['supplier_id' => $row_id]);
            $this->body_vars['supplier'] = $row;
            $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
            $this->body_vars['country_options'] = $this->custom->createDropdownSelect('ct_country', ['country_id', 'country_name'], 'Country', [' '], '', [$row->country_id]);
            $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            $this->body_vars['mode'] = 'view';
            $this->body_file = 'master_files/supplier/edit.php';
        }
    }

    public function accountant()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 1);
        $this->body_file = 'master_files/accountant/listing.php';
    }

    public function add_accountant()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'master_files/accountant/add.php';
    }

    public function edit_accountant($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_accountant', ['ac_id' => $row_id]);
            if ($row) {
                $this->body_vars['accountant'] = $accountant = $row;
            }
            $this->body_file = 'master_files/accountant/edit.php';
        }
    }

    public function view_accountant($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_accountant', ['ac_id' => $row_id]);
            if ($row) {
                $this->body_vars['accountant'] = $accountant = $row;
                $this->body_vars['mode'] = 'view';
            }
            $this->body_file = 'master_files/accountant/edit.php';
        }
    }

    public function billing()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 1);
        $this->body_file = 'master_files/billing/listing.php';
    }

    public function add_billing()
    {
        is_logged_in('admin');
        has_permission();
        $gst_id = $this->custom->getSingleValue('ct_gst', 'gst_id', ['gst_code' => 'SR']);
        $this->body_vars['gst_options'] = $this->custom->createDropdownSelect1('ct_gst', ['gst_id', 'gst_code', 'gst_type', 'gst_rate'], 'GST', [' ( ', ' ) =>', '%'], ['gst_type'], [$gst_id]);
        $this->body_vars['stock_options'] = createSimpleDropdown(['YES', 'NO'], '');
        $this->body_vars['bill_type_options'] = createSimpleDropdown(['Service', 'Products'], 'Bill Type');
        $this->body_file = 'master_files/billing/add.php';
    }

    public function edit_billing($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_billing', ['billing_id' => $row_id]);
            if ($row) {
                $this->body_vars['billing_data'] = $billing_data = $row;
                $this->body_vars['gst_options'] = $this->custom->createDropdownSelect1('ct_gst', ['gst_id', 'gst_code', 'gst_type', 'gst_rate'], 'GST', [' ( ', ' ) =>', '%'], [], [$row->gst_id]);
                $this->body_vars['stock_options'] = createSimpleDropdown(['YES', 'NO'], '', $row->billing_update_stock);
                $this->body_vars['bill_type_options'] = createSimpleDropdown(['Service', 'Product'], 'Bill Type', $row->billing_type);
            }

            $this->body_file = 'master_files/billing/edit.php';
        }
    }

    public function view_billing($row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_billing', ['billing_id' => $row_id]);
            if ($row) {
                $this->body_vars['billing_data'] = $billing_data = $row;
                $this->body_vars['gst_options'] = $this->custom->createDropdownSelect1('ct_gst', ['gst_id', 'gst_code', 'gst_type', 'gst_rate'], 'GST', [' ( ', ' ) =>', '%'], [], [$row->gst_id]);
                $this->body_vars['stock_options'] = createSimpleDropdown(['YES', 'NO'], '', $row->billing_update_stock);
                $this->body_vars['bill_type_options'] = createSimpleDropdown(['Service', 'Product'], 'Bill Type', $row->billing_type);
                $this->body_vars['mode'] = 'view';
            }
            $this->body_file = 'master_files/billing/edit.php';
        }
    }

    public function employee()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 1);
        $this->body_file = 'master_files/employee/listing.php';
    }

    public function add_employee()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['departments'] = $this->custom->createDropdownSelect('master_department', ['d_id', 'name', 'code'], 'Department', ['( ', ') ', ' ']);
        $this->body_file = 'master_files/employee/add.php';
    }

    public function edit_employee($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_employee', ['e_id' => $row_id]);
            if ($row) {
                $this->body_vars['employee_data'] = $employee_data = $row;
                $this->body_vars['departments'] = $departments = $this->custom->createDropdownSelect('master_department', ['d_id', 'name', 'code'], 'Department', ['( ', ') ', ' '], [], [$row->department_id]);
            }

            $this->body_file = 'master_files/employee/edit.php';
        }
    }

    public function view_employee($row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_employee', ['e_id' => $row_id]);
            if ($row) {
                $this->body_vars['employee_data'] = $employee_data = $row;
                $this->body_vars['mode'] = 'view';
                $this->body_vars['departments'] = $departments = $this->custom->createDropdownSelect('master_department', ['d_id', 'name', 'code'], 'Department', ['( ', ') ', ' '], [], [$row->department_id]);
            }
            $this->body_file = 'master_files/employee/edit.php';
        }
    }

    public function department()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 1);
        $this->body_file = 'master_files/department/listing.php';
    }

    public function add_department()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'master_files/department/add.php';
    }

    public function edit_department($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_department', ['d_id' => $row_id]);
            if ($row) {
                $this->body_vars['department_data'] = $department_data = $row;
            }

            $this->body_file = 'master_files/department/edit.php';
        }
    }

    public function view_department($row_id = '')
    {
        is_logged_in('admin');
        has_permission();
        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_department', ['d_id' => $row_id]);
            if ($row) {
                $this->body_vars['department_data'] = $department_data = $row;
                $this->body_vars['mode'] = 'view';
            }
            $this->body_file = 'master_files/department/edit.php';
        }
    }

    public function foreign_bank()
    {
        is_logged_in('admin');
        has_permission();
        $print = $this->uri->segment(3);
        $this->body_vars['buttonsPanel'] = buttonsPanel(1, 1, 1, 1, 1, 1, 1, 0, 0, 0);
        $this->body_file = 'master_files/foreign_bank/listing.php';
    }

    public function add_foreign_bank()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['currency_options'] = $this->custom->populateCurrencyList();
        $this->body_file = 'master_files/foreign_bank/add.php';
    }

    public function edit_foreign_bank($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_foreign_bank', ['fb_id' => $row_id]);
            if ($row) {
                $this->body_vars['foreign_bank'] = $foreign_bank = $row;
                $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
                $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            }
            $this->body_file = 'master_files/foreign_bank/edit.php';
        }
    }

    public function view_foreign_bank($row_id = '')
    {
        is_logged_in('admin');
        has_permission();

        if ($row_id != '') {
            $row = $this->custom->getSingleRow('master_foreign_bank', ['fb_id' => $row_id]);
            if ($row) {
                $this->body_vars['foreign_bank'] = $foreign_bank = $row;
                $this->body_vars['currency_options'] = $this->custom->populateCurrencyList($row->currency_id);
                $this->body_vars['currencyCode'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $row->currency_id]);
            }
            $this->body_file = 'master_files/foreign_bank/edit.php';
        }
    }

    public function print_departments()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('*');
        $this->db->from('master_department');
        $this->db->order_by('name, code', 'ASC, ASC');
        $query = $this->db->get();
        $department_data = $query->result();

        $html = $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>DEPARTMENT'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th style="width: 120px">Code</th>
         <th>Name</th>
      </tr>
   ';

        foreach ($department_data as $key => $value) {
            $html .= '<tr>
            <td>'.$value->code.'</td>
            <td>'.$value->name.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'departments_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_accountants()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('*');
        $this->db->from('master_accountant');
        $this->db->order_by('name, code', 'ASC, ASC');
        $query = $this->db->get();
        $ac_data = $query->result();

        $html = $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= '<td style="text-align: center; border: none;"><h3>ACCOUNTANTS</h3></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th style="width: 250px">Name</th>
         <th>Code</th>
         <th>Email</th>
         <th>Category</th>
         <th style="width: 150px">Basic Salary</th>
         <th>Incentives</th>
      </tr>
   ';

        foreach ($ac_data as $key => $value) {
            $html .= '<tr>
            <td>'.$value->name.'</td>
            <td>'.$value->code.'</td>
            <td>'.$value->email.'</td>
            <td>'.$value->category.'</td>
            <td>'.$value->basic_salary.'</td>
            <td>'.$value->incentives.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'accountants_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_foreign_banks()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('*');
        $this->db->from('master_foreign_bank');
        $this->db->order_by('fb_name, fb_code', 'ASC, ASC');
        $query = $this->db->get();
        $fb_data = $query->result();

        $html = $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>FOREIGN BANK'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th>Name</th>
         <th>Code</th>
         <th>Currency</th>
         <th>Email</th>
         <th>Phone</th>
      </tr>
   ';

        foreach ($fb_data as $key => $value) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $value->currency_id]);

            $html .= '<tr>
            <td>'.$value->fb_name.'</td>
            <td>'.$value->fb_code.'</td>
            <td>'.$currency.'</td>
            <td>'.$value->email.'</td>
            <td>'.$value->phone.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'foreign_banks_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_customers()
    {
        is_logged_in('admin');
        has_permission();

        $html = $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>CUSTOMER'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">
            <thead>
               <tr>
                     <th>Name</th>
                     <th>Code</th>
                     <th>Currency</th>
                     <th>Phone</th>
               </tr>
            </thead>';

        $i = 1;

        $this->db->select('*');
        $this->db->from('master_customer');
        $this->db->where(['active' => 1]);
        $this->db->order_by('name, code', 'ASC, ASC');
        $this->db->limit(1500);
        $query = $this->db->get();
        // print_r($this->db->last_query());
        $customer = $query->result();
        foreach ($customer as $record) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $record->currency_id]);

            $html .= '<tr>
            <td>'.$record->name.'</td>
            <td>'.$record->code.'</td>
            <td>'.$currency.'</td>
            <td>'.$record->phone.'</td>
         </tr>';

            ++$i;
        }

        $html .= '</table>';

        $file = 'customers_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_suppliers()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('*');
        $this->db->from('master_supplier');
        $this->db->where(['active' => 1]);
        $this->db->order_by('name, code', 'ASC, ASC');
        $query = $this->db->get();
        $supplier = $query->result();

        $html = '';

        $html .= $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>SUPPLIER'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th width="300">Name</th>
         <th>Code</th>
         <th>Currency</th>
         <th>Email</th>
         <th>Phone</th>
      </tr>
   ';

        foreach ($supplier as $record) {
            $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $record->currency_id]);

            $html .= '<tr>
            <td>'.$record->name.'</td>
            <td>'.$record->code.'</td>
            <td align="center">'.$currency.'</td>
            <td>'.$record->email.'</td>
            <td>'.$record->phone.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'suppliers_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_billings()
    {
        is_logged_in('admin');
        has_permission();

        /*$this->db->select('*');
        $this->db->from('master_billing');
        $this->db->order_by('stock_code, billing_description', 'ASC, ASC');
        $query = $this->db->get();
        $billing_data = $query->result();
        */

        $sql = 'SELECT * FROM master_billing ORDER BY stock_code ASC, billing_description ASC';
        $query = $this->db->query($sql);
        $billing_data = $query->result();

        $html = $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>BILLING'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th style="width: 100px">Code</th>
         <th style="width: 200px">Description</th>
         <th>UOM</th>
         <th>Unit Price</th>
         <th>Billing Type</th>
         <th>GST</th>
      </tr>
   ';

        foreach ($billing_data as $key => $value) {
            $gst_code = $this->custom->getSingleValue('ct_gst', 'gst_code', ['gst_id' => $value->gst_id]);

            $html .= '<tr>
            <td>'.$value->stock_code.'</td>
            <td>'.$value->billing_description.'</td>
            <td>'.$value->billing_uom.'</td>
            <td>'.$value->billing_price_per_uom.'</td>
            <td>'.$value->billing_type.'</td>
            <td>'.$gst_code.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'billings_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }

    public function print_employees()
    {
        is_logged_in('admin');
        has_permission();

        $this->db->select('*');
        $this->db->from('master_employee');
        $this->db->order_by('name, code', 'ASC, ASC');
        $query = $this->db->get();
        $employee_data = $query->result();

        $html = '';

        $html .= $this->custom->populateMPDFStyle();

        $html .= '<table style="border: none; width: 100%; border-bottom: 1px solid gainsboro">';
        $html .= '<tr>';
        $html .= "<td style='text-align: center; border: none;'><h3>EMPLOYEE'S</h3></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align: right; border: none;"><strong>Date:</strong> '.date('d-m-Y').'</td>';
        $html .= '</tr>';
        $html .= '</table><br /><br />';

        $html .= '<table>
      <tr>
         <th>Code</th>
         <th>Name</th>
         <th>Department</th>
         <th>Email</th>
         <th>Note</th>
      </tr>
   ';

        foreach ($employee_data as $key => $value) {
            $department = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $value->department_id]);
            $html .= '<tr>
            <td>'.$value->code.'</td>
            <td>'.$value->name.'</td>
            <td>'.$department.'</td>
            <td>'.$value->email.'</td>
            <td>'.$value->note.'</td>
         </tr>';
        }

        $html .= '</table>';

        $file = 'employees_'.date('YmdHis').'.pdf';
        $this->custom->printMPDF($file, $html);
    }
}
