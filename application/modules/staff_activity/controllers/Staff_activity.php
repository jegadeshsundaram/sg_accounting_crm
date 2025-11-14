<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Staff_activity extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }    

    public function index()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'staff_activity/options.php';
    }

    public function manage()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_vars['employees'] = $this->custom->createDropdownSelect('master_employee', ['e_id', 'name', 'code'], '', ['(', ')']);
    }

    public function print_tasks()
    {
        $post = $this->input->post();
        if ($post) {

            $html = '';

            $html .= '<table style="width: 100%">';

            $company_where = ['code' => 'CP'];
            $company_profile = $this->custom->getSingleRow('company_profile', $company_where);
            $html .= '<tr><td colspan="2" style="border: none; text-align: center;"><h2>'.$company_profile->company_name.'</h2></td></tr>';

            $html .= '<tr><td colspan="2" style="border: none; text-align: center;"><h4>STAFF ACTIVITY REPORT</h4></td></tr>';

            $html .= '<tr>';
            $html .= '<td style="border: none;">';
            if($post['start_date'] != '' && $post['end_date'] != '') {
                $html .= '<strong>Period:</strong> '.$post['start_date'].' <i>to</i> '.$post['end_date'];
            }
            $html .= '</td>';
            $html .= '<td style="border: none;" align="right"><strong>Date:</strong> '.date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '</table> <hr />';

            $html .= '<table style="width: 100%">';

            $i = 0;
            $start_date = date('Y-m-d', strtotime($post['start_date']));
            $end_date = date('Y-m-d', strtotime($post['end_date']));

            // get each employee 
            $this->db->select('employee_id, name, code');
            $this->db->from('staff_activity, master_employee');
            $where = 'staff_activity.employee_id = master_employee.e_id';
            if($post['staff'] !== '' && $post['start_date'] !== '' && $post['end_date'] !== '') {
                $where .= ' AND employee_id = "'.$post['staff'].'" AND activity_date >= "'.$start_date.'" AND activity_date <= "'.$end_date.'"';
            } elseif ($post['staff'] !== '') {
                $where .= ' AND employee_id = "'.$post['staff'].'"';
            } elseif($post['start_date'] !== '' && $post['end_date'] !== '') {
                $where .= ' AND activity_date >= "'.$start_date.'" AND activity_date <= "'.$end_date.'"';
            }
            $this->db->where($where);
            $this->db->group_by('employee_id');
            $this->db->order_by('master_employee.name', 'ASC');
            $query = $this->db->get();
            $staffs = $query->result();
            foreach ($staffs as $staff) {
                $employee_id = $staff->employee_id;
                $employee_name_code = $staff->name.' ('.$staff->code.')';

                $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';

                $html .= '<tr>';
                $html .= '<td colspan="4" style="background: tomato; color: #fff"><strong>Staff Name & Code :</strong> '.$employee_name_code.'</td>';
                $html .= '</tr>';

                // get each dats under every employee
                $this->db->select('activity_date');
                $this->db->from('staff_activity');
                $where = 'employee_id = "'.$employee_id.'"';
                if($post['start_date'] !== '' && $post['end_date'] !== '') {
                    $where .= 'AND activity_date >= "'.$start_date.'" AND activity_date <= "'.$end_date.'"';
                }
                $this->db->where($where);
                $this->db->group_by('activity_date');
                $this->db->order_by('activity_date', 'DESC');
                $query = $this->db->get();
                $dates = $query->result();
                foreach ($dates as $day) {
                    $task_date = date('d-m-Y', strtotime($day->activity_date));
                    $html .= '<tr>';
                    $html .= '<td colspan="4" style="background: gainsboro;"><strong>Date :</strong> '.$task_date.'</td>';
                    $html .= '</tr>';

                    $html .= '<tr><td colspan="4" height="10" style="border: none;"></td></tr>';
                    
                    $html .= '<tr>';
                    $html .= '<td><strong>Task Details</strong></td>';
                    $html .= '<td><strong>Time</strong></td>';
                    $html .= '<td><strong>Minutes</strong></td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';

                    $total_daily_minutes = 0;

                    // get tasks under every date on each employee
                    $this->db->select('*');
                    $this->db->from('staff_activity');
                    $this->db->where('employee_id = "'.$employee_id.'" AND activity_date = "'.$day->activity_date.'"');
                    $query = $this->db->get();
                    $tasks = $query->result();
                    foreach ($tasks as $task) {
                        $total_daily_minutes += $task->minutes;
                        
                        $html .= '<tr>';
                        $html .= '<td style="min-width: 300px">'.$task->task_description.'</td>';
                        $html .= '<td style="min-width: 100px">'.$task->start_time.' <i>to</i> '.$task->end_time.'</td>';
                        $html .= '<td style="text-align: center">'.$task->minutes.'</td>';
                        $html .= '<td style="min-width: 200px">';
                        if ($task->remarks !== '') {
                            $html .= '<i><u>Remarks:</u></i> '.$task->remarks;
                        }
                        if ($task->supervisor_comments !== '') {
                            $html .= '<br /><br /><i><u>Comments:</u></i> '.$task->supervisor_comments;
                        }
                        $html .= '</td>';
                        $html .= '</tr>';
                    }

                    $html .= '<tr>';
                    $html .= '<td colspan="2" style="text-align: right; color: red; font-weight: bold">Sub Total</td>';
                    $html .= '<td colspan="2" style="font-weight: bold; padding-left: 25px">'.$total_daily_minutes.'</td>';
                    $html .= '</tr>';

                    $html .= '<tr><td colspan="4" height="15" style="border: none;"></td></tr>';

                    ++$i;
                }
            }

            if ($i == 0) {
                $html .= '<tr>';
                $html .= '<td colspan="4" height="20" style="border: none; color: red; text-align: center">No Activities Found</td>';
                $html .= '</tr>';
            }

            $html .= '</table><br /><br />';

            $style = $this->custom->populateMPDFStyle();
            $document = $style.$html;

            $file = 'staff_activities_'.date('YmdHis').'.pdf';
            $this->custom->printMPDF($file, $document);
        } else {
            set_flash_message('message', 'danger', 'Request Error!');
            redirect('staff_activity/');
        }
    }

    public function df_backup($tables = [])
    {
        $CI = &get_instance();
        // Load the DB utility class
        $file_name = 'staff_activity_'.date('j-F-Y_H-i-s').'.sql';
        $CI->load->dbutil();
        $prefs = [
            'tables' => ['staff_activity'],
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
            set_flash_message('message', 'success', 'Staff Activities Restored');
        } else {
            set_flash_message('message', 'warning', $data['error']);
        }
        redirect('staff_activity/', 'refresh');
    }

    public function df_zap()
    {
        is_logged_in('admin');
        has_permission();
        $this->body_file = 'staff_activity/blank.php';
        zapStaffActivity();
        redirect('staff_activity/', 'refresh');
    }
}
