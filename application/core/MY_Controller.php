<?php

class MY_Controller extends CI_Controller
{
    public function __contruct()
    {
        parent::__contruct();
    }
    protected $header_file = 'layout/header';
    protected $body_file;
    protected $footer_file = 'layout/footer';
    protected $header_vars = [];
    protected $body_vars = [];
    protected $footer_vars = [];

    public function _remap($method, $params = [])
    {
        $this->load->helper('assets_helper');
        // create an array of css files
        $arr_css = [
            'bs4/jquery-ui.min.css',

            'bs4/popper.min.css',
            'bs4/bootstrap.min.css',

            'bs4/jquery.dataTables.min.css',
            'bs4/dataTables.bootstrap4.min.css',

            'select2.min.css',

            'bs4/skins/_all-skins.min.css',

            'notify-metro.css',
            'sweetalert2.min.css',
            'animate.css',
            'bootstrap2-toggle.min.css',
            'custom.css?ver=1.1',
            ];

        // create an array of js files
        $arr_js = [
            'bs4/jquery-ui.min.js',
            'bs4/popper.min.js',
            'bs4/bootstrap.min.js',

            'bs4/bootstrap.bundle.min.js',

            'bs4/jquery.dataTables.min.js',
            'bs4/dataTables.bootstrap4.min.js',
            'select2.min.js',
            'app.min.js',

            'jQuery.print.js',
            'jquery.validate.min.js',
            'notify.js',
            'notify-metro.js',
            'jquery.confirm.min.js',
            'jquery.mask.min.js',
            'sweetalert2.min.js',
            'custom.js',
            ];

        // assign all css to any array variable
        $this->header_vars['css'] = load_css($arr_css);

        // assign all js to any array variable
        $this->footer_vars['js'] = load_js($arr_js);
        // you can set default variables to send to the template here
        $this->body_file = $method;
        if (method_exists($this, $method)) {
            $result = call_user_func_array([$this, $method], $params);
            $this->load->view($this->header_file, $this->header_vars);
            $this->load->view($this->body_file, $this->body_vars);
            $this->load->view($this->footer_file, $this->footer_vars);

            return $result;
        } else {
            show_404();
        }
    }
}
