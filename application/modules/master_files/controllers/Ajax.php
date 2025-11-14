<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    public $logged_id;

    public function __construct()
    {
        parent::__construct();
        $this->logged_id = $this->session->user_id;
    }

    public function save_customer()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $customer['code'] = $post['code'];
            $customer['name'] = $post['name'];
            $customer['currency_id'] = $post['currency_id'];

            $id = $this->custom->insertRow('master_customer', $customer);

            $data['customer_id'] = $id;
            $data['currency'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $post['currency_id']]);

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function save_supplier()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $supplier['code'] = $post['code'];
            $supplier['name'] = $post['name'];
            $supplier['currency_id'] = $post['currency_id'];

            $id = $this->custom->insertRow('master_supplier', $supplier);

            $data['supplier_id'] = $id;
            $data['currency'] = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $post['currency_id']]);

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function save_employee()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $employee['code'] = $post['employee_code'];
            $employee['name'] = $post['employee_name'];
            $employee['department_id'] = $post['department_id'];

            $id = $this->custom->insertRow('master_employee', $employee);

            $data['employee_id'] = $id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function save_department()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $department['code'] = $post['code'];
            $department['name'] = $post['name'];

            $id = $this->custom->insertRow('master_department', $department);

            $data['department_id'] = $id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function save_currency()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $currency['code'] = $post['code'];
            $currency['description'] = $post['description'];
            $currency['rate'] = $post['rate'];

            $id = $this->custom->insertRow('ct_currency', $currency);

            $data['currency_id'] = $id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function save_country()
    {
        is_ajax();
        $post = $this->input->post();
        if ($post) {
            $country['country_code'] = $post['country_code'];
            $country['country_name'] = $post['country_name'];

            $id = $this->custom->insertRow('ct_country', $country);

            $data['country_id'] = $id;

            echo json_encode($data);
        } else {
            echo 'post error';
        }
    }

    public function double_customer()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_customer', ['code' => $post['code']]);
        echo $cnt;
    }

    public function double_supplier()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_supplier', ['code' => $post['code']]);
        echo $cnt;
    }

    public function double_billing()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_billing', ['stock_code' => $post['code']]);
        echo $cnt;
    }

    public function double_department()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_department', ['code' => $post['code']]);
        echo $cnt;
    }

    public function double_employee()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_employee', ['code' => $post['code']]);
        echo $cnt;
    }

    public function double_accountant()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_accountant', ['code' => $post['code']]);
        echo $cnt;
    }

    public function double_fb()
    {
        is_ajax();
        $post = $this->input->post();
        $cnt = $this->custom->getCount('master_foreign_bank', ['fb_code' => $post['code']]);
        echo $cnt;
    }

    public function consultAccountant()
    {
        is_ajax();
        $code = $this->input->post('code');
        $id = $this->custom->getSingleValue('master_accountant', 'ac_id', ['code' => $code]);

        $flag = 0;
        $checkAcc = $this->custom->checkTableValues('sac_job', ['accountant_id' => $id]);
        if ($checkAcc) {
            $flag = 1;
        }

        echo $flag;
    }

    public function consultCustomer()
    {
        is_ajax();
        $code = $this->input->post('code');
        $id = $this->custom->getSingleValue('master_customer', 'customer_id', ['code' => $code]);
        $where = ['customer_id' => $id];
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkQuatationResult = $this->custom->checkTableValues('quotation_master', $where);
                if ($checkQuatationResult) {
                    $flag = 1;
                }
            } elseif ($i == 1) {
                ++$i;
                $checkInvoiceResult = $this->custom->checkTableValues('invoice_master', $where);
                if ($checkInvoiceResult) {
                    $flag = 1;
                }
            } elseif ($i == 2) {
                ++$i;
                $checkOpenResult = $this->custom->checkTableValues('ar_open', $where);
                if ($checkOpenResult) {
                    $flag = 1;
                }
            } elseif ($i == 3) {
                ++$i;
                $receipt_master = $this->custom->checkTableValues('receipt_master', $where);
                if ($receipt_master) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        echo $flag;
    }

    public function consultSupplier()
    {
        is_ajax();
        $code = $this->input->post('code');
        $id = $this->custom->getSingleValue('master_supplier', 'supplier_id', ['code' => $code]);
        $where = ['supplier_id' => $id];
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkPaymentMaster = $this->custom->checkTableValues('payment_master', $where);
                if ($checkPaymentMaster) {
                    $flag = 1;
                }
            } elseif ($i == 2) {
                ++$i;
                $checkPurchaseMaster = $this->custom->checkTableValues('stock_purchase', $where);
                if ($checkPurchaseMaster) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        echo $flag;
    }

    public function consultStock()
    {
        is_ajax();
        $code = $this->input->post('stock_code');
        $billing_id = $this->custom->getSingleValue('master_billing', 'billing_id', ['stock_code' => $code]);

        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkInvoiceProductMaster = $this->custom->checkTableValues('invoice_product_master', ['billing_id' => $billing_id]);
                if ($checkInvoiceProductMaster) {
                    $flag = 1;
                }
            } elseif ($i == 1) {
                ++$i;
                $checkQuotationProductMaster = $this->custom->checkTableValues('quotation_product_master', ['billing_id' => $billing_id]);
                if ($checkQuotationProductMaster) {
                    $flag = 1;
                }
            } elseif ($i == 2) {
                ++$i;
                $checkOpenStockTable = $this->custom->checkTableValues('stock_open', ['product_id' => $billing_id]);
                if ($checkOpenStockTable) {
                    $flag = 1;
                }
            } elseif ($i == 3) {
                ++$i;
                $checkStockAdjusmtent = $this->custom->checkTableValues('stock_adjustment', ['product_id' => $billing_id]);
                if ($checkStockAdjusmtent) {
                    $flag = 1;
                }
            } elseif ($i == 4) {
                ++$i;
                $checkStockPurchaseMaster = $this->custom->checkTableValues('stock_purchase', ['product_id' => $billing_id]);
                if ($checkStockPurchaseMaster) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        echo $flag;
    }

    public function consultEmployee()
    {
        is_ajax();
        $code = $this->input->post('code');
        $id = $this->custom->getSingleValue('master_employee', 'e_id', ['code' => $code]);
        $where = ['employee_id' => $id];
        $i = 0;
        $flag = 0;
        do {
            if ($i == 0) {
                ++$i;
                $checkQuatationResult = $this->custom->checkTableValues('quotation_master', $where);
                if ($checkQuatationResult) {
                    $flag = 1;
                }
            } elseif ($i == 1) {
                ++$i;
                $checkInvoiceResult = $this->custom->checkTableValues('invoice_master', $where);
                if ($checkInvoiceResult) {
                    $flag = 1;
                }
            } else {
                break;
            }
        } while ($flag == 0);
        echo $flag;
    }

    public function consultDepartment()
    {
        is_ajax();
        $id = $this->input->post('id');

        $flag = 0;
        $checkDept = $this->custom->checkTableValues('master_employee', ['department_id' => $id]);
        if ($checkDept) {
            $flag = 1;
        }

        echo $flag;
    }

    public function consultForeignBank()
    {
        is_ajax();
        $code = $this->input->post('fb_code');

        $flag = 0;
        $checkFBOpen = $this->custom->checkTableValues('fb_open', ['fb_code' => $code]);
        if ($checkFBOpen) {
            $flag = 1;
        }

        $checkFB = $this->custom->checkTableValues('foreign_bank', ['fb_code' => $code]);
        if ($checkFB) {
            $flag = 1;
        }

        echo $flag;
    }
}
