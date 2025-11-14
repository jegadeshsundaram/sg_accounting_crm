<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Sac_job_control extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'sac_job_control/options.php';
    }

    public function manage()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['accountants'] = $this->custom->createDropdownSelect('master_accountant', ['ac_id', 'name', 'code'], 'Accountant', ['( ', ') ', ' '], ['active' => 1]);
        $this->body_vars['customers'] = $this->custom->createDropdownSelect('master_customer', ['customer_id', 'name', 'code', 'currency_id'], '', ['( ', ') ', ' '], ['active' => 1]);
    }
    
    public function print_detailed() {
		is_logged_in('admin');
		has_permission();
		$post = $this->input->post();
        if ($post) {

            $company_profile = $this->custom->getSingleRow('company_profile', ['code' => 'CP']);

            // company header and report title
            $html = '<table style="width: 100%">';
            $html .='<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';
            $html .='<tr><td style="border: none; text-align: center;"><h4>JOB DETAILS</h4></td></tr>';
            $html .= '</table>';

            // date period
            $html .='<table style="border: none; width: 100%; font-size: 11px; border-bottom: 2px solid blue">';
            $html .='<tr>';
            $html .='<td style="border: none; text-align: left;">
                        <i><strong>Report Date :</strong> '.date('d-m-Y').'</i>
                    </td>';
            $html .='<td style="border: none; text-align: right;">';
            if($post['start_date'] != '' && $post['end_date'] != '') {
                $html .='<i><strong>From :</strong> '.$post['start_date'].'&nbsp;&nbsp;&nbsp;<strong>To :</strong> '.$post['end_date'].'</span></i>';
            }
            $html .='</td>';
            $html .='</tr>';
            $html .='</table>';

            $html .= '<table style="width: 100%">';

            // request parameters

            $start_date = '';
            if($post['start_date'] !== '') {
                $start_date = date("Y-m-d", strtotime($post['start_date']));
            }
            $end_date = '';
            if($post['end_date'] !== '') {
                $end_date = date("Y-m-d", strtotime($post['end_date']));
            }
            $accountant_id = $post['accountant_id'];
            $job_status = $post['job'];

            $entry = 0;

            // get each accountant 
            $this->db->select('accountant_id, name, code');
            $this->db->from('sac_job, master_accountant');
            $where = 'sac_job.accountant_id  = master_accountant.ac_id';
            if($accountant_id !== '' && $start_date !== '' && $end_date !== '' && $job_status !== '') {
                $where .= ' AND accountant_id = "'.$post['accountant_id'].'" AND job_confirmed_date >= "'.$start_date.'" AND job_confirmed_date <= "'.$end_date.'" AND job_closed = '.$job_status;
            } elseif ($accountant_id !== '') {
                $where .= ' AND accountant_id = "'.$post['accountant_id'].'"';
            } elseif($start_date !== '' && $end_date !== '') {
                $where .= ' AND job_confirmed_date >= "'.$start_date.'" AND job_confirmed_date <= "'.$end_date.'"';
            } elseif($job_status !== '') {
                $where .= ' AND job_closed = '.$job_status;
            }
            $this->db->where($where);
            $this->db->group_by('accountant_id');
            $this->db->order_by('master_accountant.name', 'ASC');
            $query = $this->db->get();
            $accountants = $query->result();
            foreach ($accountants as $accountant) {
                $accountant_id = $accountant->accountant_id;
                $accountant_name_code = $accountant->name.' ('.$accountant->code.')';

                $html .= '<tr><td colspan="2" height="10" style="border: none;"></td></tr>';

                $html .= '<tr>';
                $html .= '<td colspan="2" style="border: none; padding: 3px 10px;"><strong>Accountant :</strong> '.$accountant_name_code.'</td>';
                $html .= '</tr>';

                $html .= '<tr><td colspan="2" height="10" style="border: none;"></td></tr>';

                $sno = 1;
                $this->db->select('*');
                $this->db->from('sac_job');
                $where = 'accountant_id = "'.$accountant_id.'"';
                if($post['start_date'] !== '' && $post['end_date'] !== '') {
                    $where .= ' AND job_confirmed_date >= "'.$start_date.'" AND job_confirmed_date <= "'.$end_date.'"';
                }
                $this->db->where($where);
                $query = $this->db->get();
                $jobs = $query->result();
                foreach ($jobs as $job) {
                    $customer = $this->custom->getMultiValues("master_customer", 'name, code', ['customer_id' => $job->customer_id]);
                    $customer_name_code = $customer->name . " (".$customer->code . ")";

                    $html .= '<tr>';
                    $html .= '<td style="border: none; vertical-align: top; padding-top: 15px;">'.$sno.'</td>';
                    $html .= '<td style="border: none">';
                    $html .= '<table>';
                    $html .= '<tr>';
                    $html .= '<td><label>Job Code: </label>'.$job->job_code.'</td>';
                    $html .= '<td><label>Job Confirmed Date: </label>'.date('d-m-Y', strtotime($job->job_confirmed_date)).'</td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td colspan="2"><label>Customer: </label>'.$customer_name_code.'</td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td><label>Agreed Completion Date: </label>';
                    if($job->agreed_completion_date !== null) {
                        $html .= date('d-m-Y', strtotime($job->agreed_completion_date));
                    } else {
                        $html .= 'N/A';
                    }
                    $html .= '</td>';
                    $html .= '<td><label>Actual Completion Date: </label>';
                    if($job->actual_completion_date !== null) {
                        $html .= date('d-m-Y', strtotime($job->actual_completion_date));
                    } else {
                        $html .= 'N/A';
                    }
                    $html .= '</td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td><label>Job Value: </label>'.number_format($job->job_value, 2).'</td>';
                    $html .= '<td><label>Payment Collected: </label>'.number_format($job->payment_collected, 2).'</td>';
                    $html .= '</tr>';

                    $html .= '<tr>';
                    $html .= '<td><label>Balance: </label>'.number_format(((float)$job->job_value - (float)$job->payment_collected), 2).'</td>';
                    $html .= '<td><label>Status: </label>';
                    if($job->job_closed == 1) {
                        $html .= 'Closed';
                    } else {
                        $html .= 'Open';
                    }
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                    $html .= '</td>';
                    $html .= '</tr>';

                    ++$sno;
                } // each job loop ends

                ++$entry;
            } // each accountant loops ends

            if($entry == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="2" height="20" style="border: none; color: red; text-align: center">No Jobs Found</td>';
                $html .= '</tr>';
            }

            $html .= '</table><br /><br />';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'sac_job_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        } else {
            set_flash_message('message', 'danger', 'Request Error!');
            redirect('sac_job_control/');
        }
	}

    public function print_kash() {
		is_logged_in('admin');
		has_permission();
		$post = $this->input->post();
        if ($post) {

            $company_where = array('profile_id' => 1);
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);

            // company header and report title
            $html = '<table style="width: 100%">';
            $html .='<tr><td style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';
            $html .='<tr><td style="border: none; text-align: center;"><h4>KASH REPORT BY ACCOUNTANT</h4></td></tr>';
            $html .= '</table>';

            // date period
            $html .='<table style="border: none; width: 100%; font-size: 11px; border-bottom: 2px solid blue">';
            $html .='<tr>';
            $html .='<td style="border: none; text-align: left;">
                        <i><strong>Report Date :</strong> '.date('d-m-Y').'</i>
                    </td>';
            $html .='<td style="border: none; text-align: right;">';
            if($post['start_date'] != '' && $post['end_date'] != '') {
                $html .='<i><strong>From :</strong> '.$post['start_date'].'&nbsp;&nbsp;&nbsp;<strong>To :</strong> '.$post['end_date'].'</span></i>';
            }
            $html .='</td>';
            $html .='</tr>';
            $html .='</table>';

            $html .= '<table style="width: 100%">';

            // request parameters

            $start_date = '';
            if($post['start_date'] !== '') {
                $start_date = date("Y-m-d", strtotime($post['start_date']));
            }

            $end_date = '';
            if($post['end_date'] !== '') {
                $end_date = date("Y-m-d", strtotime($post['end_date']));
            }

            $accountant_id = $post['accountant_id'];
            $job_status = $post['job'];
            
            $third_party_cost = 0;
            if($post['third_party_cost'] !== '') {
                $third_party_cost = $post['third_party_cost'];
            }

            $marketting_cost_percentage = 20;
            if($post['marketting_cost_percentage'] !== '') {
                $third_party_cost = $post['marketting_cost_percentage'];
            }
            
            $accountant = $this->custom->getMultiValues('master_accountant', 'name, code, basic_salary, incentives', ['ac_id' => $accountant_id]);
            $entry = 0;

            $html .= '<tr><td colspan="6" height="10" style="border: none;"></td></tr>';

            $html .= '<tr>';
            $html .= '<td colspan="6" style="border: none; padding: 3px 10px;"><strong>Accountant :</strong> '.$accountant->name.' ('.$accountant->code.')</td>';
            $html .= '</tr>';

            $html .= '<tr><td colspan="6" height="10" style="border: none;"></td></tr>';

            $html .= '<tr>';
			$html .= '<th>Date</th>';
			$html .= '<th>Job Reference</th>';
			$html .= '<th>Target Date</th>';
			$html .= '<th>Actual Date</th>';
			$html .= '<th style="width: 100px">+/- Var Days</th>';
			$html .= '<th>Job Value</th>';
		    $html .= '</tr>';

            $entry = 0;
		    $total_job_value = 0;

            $this->db->select('*');
            $this->db->from('sac_job');            
            $this->db->where('accountant_id = "'.$accountant_id.'" AND job_confirmed_date >= "'.$start_date.'" AND job_confirmed_date <= "'.$end_date.'"');
            $query = $this->db->get();
            $jobs = $query->result();
            foreach ($jobs as $job) {
                
                $html .= '<tr>';
                $html .= '<td>'.$job->job_confirmed_date.'</td>';
                $html .= '<td>'.$job->job_code.'</td>';
                if($job->agreed_completion_date !== NULL) {
					$html .= '<td>'.date('d-m-Y', strtotime($job->agreed_completion_date)).'</td>';
				} else {
					$html .= '<td>N/A</td>';
				}
                if($job->actual_completion_date !== NULL) {
					$html .= '<td>'.date('d-m-Y', strtotime($job->actual_completion_date)).'</td>';
				} else {
					$html .= '<td>N/A</td>';
				}

                if($job->agreed_completion_date !== NULL && $job->actual_completion_date !== NULL) {
					$t_date = new DateTime($job->agreed_completion_date);
					$a_date = new DateTime($job->actual_completion_date);
					$diff = $a_date->diff($t_date)->format("%r%a");
				} else {
					$diff = 0;
				}

				$html .= '<td>'.$diff.'</td>';
				$html .= '<td>'.number_format($job->job_value, 2).'</td>';
                $html .= '</tr>';

                $total_job_value += $job->job_value;
			    ++$entry;
            } // each job loop ends

            if($entry > 0) {
                
                $html .= '<tr><td colspan="6" height="20" style="border: none;"></td></tr>';

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="5" style="border: none;"><strong>Total Job Value</strong></td>';
                $html .= '<td style="border: none;">$'.number_format($total_job_value, 2).'</td>';
                $html .= '</tr>';

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="5" style="border: none;"><strong>Less</strong></td>';
                $html .= '<td style="border: none;"></td>';
                $html .= '</tr>';

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="2" style="border: none;"><strong>Account Basic (If any)</strong></td>';
                $html .= '<td colspan="4" style="border: none;">$'.number_format($accountant->basic_salary, 2).'</td>';
                $html .= '</tr>';

                $accountant_incentives_cost = $total_job_value * $accountant->incentives / 100;

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="2" style="border: none;"><strong>Account Incentives@<span style="color: red">'.number_format($accountant->incentives).'</span>%</strong></td>';
                $html .= '<td colspan="4" style="border: none;">$'.number_format($accountant_incentives_cost, 2).'</td>';
                $html .= '</tr>';

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="2" style="border: none;"><strong>Third Party Cost</strong></td>';
                $html .= '<td colspan="4" style="border: none;">$'.number_format($third_party_cost, 2).'</td>';
                $html .= '</tr>';

                $marketting_cost = $total_job_value * $marketting_cost_percentage / 100;

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="2" style="border: none;"><strong>Marketting Cost@<span style="color: red">'.$marketting_cost_percentage.'</span>%</strong></td>';
                $html .= '<td colspan="4" style="border: none;">$'.number_format($marketting_cost, 2).'</td>';
                $html .= '</tr>';

                $total_of_costs = $accountant->basic_salary + $accountant_incentives_cost + $third_party_cost + $marketting_cost;
                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="5" style="border: none;"><strong>Total Of Costs</strong></td>';
                $html .= '<td style="border: none;">$'.number_format($total_of_costs, 2).'</td>';
                $html .= '</tr>';

                $months_contribution = $total_job_value + ((-1) * $total_of_costs);

                // Calcualte no of months between the period of dates
                $s_date = strtotime($start_date);
                $e_date = strtotime($end_date);
                $datediff = $e_date - $s_date;
                $days = round($datediff / (60 * 60 * 24));
                $months = round($days / 30);

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="5" style="border: none;"><strong>Contribution for <span style="color: red">['.$months.']</span> Months</strong></td>';
                $html .= '<td style="border: none;">$'.number_format($months_contribution, 2).'</td>';
                $html .= '</tr>';

                $monthly_kash_value = $months_contribution / $months;

                $html .= '<tr style="border: 1px dotted #ccc;">';
                $html .= '<td colspan="5" style="border: none;"><strong>Monthly Kash Value</strong></td>';
                $html .= '<td style="border: none;">$'.number_format($monthly_kash_value, 2).'</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="5" height="20" style="border: none; color: red; text-align: center">No Jobs Found</td>';
                $html .= '</tr>';
            }

            $html .= '</table><br /><br />';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'sac_job_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        } else {
            set_flash_message('message', 'danger', 'Request Error!');
            redirect('sac_job_control/');
        }
	}

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'sac_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['sac_job'],
            'format' => 'sql',           // sql, txt
            'filename' => $file_name,      // File name
            'add_drop' => true,            // Whether to add DROP TABLE statements to backup file
            'add_insert' => true,            // Whether to add INSERT data to backup file
            'newline' => "\n",             // Newline character used in backup file
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
            set_flash_message('message', 'success', 'SAC Module Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('sac_job_control/', 'refresh');
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'sac_job_control/blank.php';
        zapSACJob();
        redirect('sac_job_control/', 'refresh');
    }
}
