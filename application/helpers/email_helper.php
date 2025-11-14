<?php
function send_email($from, $to, $subject, $html)
{
    // Get current CodeIgniter instance
    $CI =& get_instance();
    $CI->load->library("Emogrifier");
    $css = file_get_contents(CSS_PATH . 'adminlte.css');
    $css .= file_get_contents(CSS_PATH . 'bootstrap/css/bootstrap.min.css');

    $CI->emogrifier->setHtml($html);
    $CI->emogrifier->setCss($css);
    $message = $CI->emogrifier->emogrify();
    d($message);
    $CI->load->library('email');

    $config['protocol'] = 'sendmail';
    $config['mailpath'] = '/usr/sbin/sendmail';
    $config['charset'] = 'iso-8859-1';
    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';


    $CI->email->initialize($config);

    $CI->email->from($from);
    $CI->email->to($to);

    $CI->email->subject('subject');
    $CI->email->message($message);

    $CI->email->send();

    echo $CI->email->print_debugger();
}

?>