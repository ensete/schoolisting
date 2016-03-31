<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Welcome extends MY_Controller {

    function add_contact() {
        if($email = $this->input->post("email")) {

            add_to_agile($email, "Subscriber");

            $this->session->set_flashdata('email', $email);
            $controller = $this->uri->segment(3);
			
            redirect(base_url("$controller/register"));
        } else {
            redirect(base_url());
        }
    }

    function check_reset_code() {
        if($data = $this->input->post()) {
            $user = tbUser::get_user(array('forgot_password_code' => $data['reset_code']));
            if($user->id) {
                $new_password = substr(str_shuffle(strtolower(sha1(rand() . time() . "my salt string"))),0, 6);
                $update['password'] = md5($new_password);
                $this->parent_model->update_user($user->id, $update);
                $this->session->set_flashdata('success', LocalizedString("Your new password is") . " " . $new_password);
            } else {
                $this->session->set_flashdata('reset_error', LocalizedString("Your reset password code you have just entered is incorrect"));
                $this->session->set_flashdata("reset_code", TRUE);
            }
            if($user->user_type == "school") {
                redirect(base_url("school/login"));
            } else {
                redirect(base_url("parents/login"));
            }
        } else {
            $token = $this->uri->segment(3);
            $user = tbUser::get_user(array('token' => $token));
            if($user) {
                $this->session->set_flashdata("reset_code", TRUE);
                if($user->user_type == "school") {
                    redirect(base_url("school/login"));
                } else {
                    redirect(base_url("parents/login"));
                }
            } else {
                redirect(base_url());
            }
        }
    }

    function print_report() {
    	$base_time = $this->input->get("time");
		if (!$base_time) 
		{
			$base_time = date('Y-m-d');
		}
		else
		{
			$base_time = date('Y-m-d', strtotime($base_time));
		}
		
        $school_token = $this->session->userdata('school_token');
        $school = get_school_by_token($school_token);

        $tbcategory = new tbCategory();
        $all_categories = $tbcategory->get_all_categories();

        //get details of the school
        $details = $this->school_model->get_school_details(array('t1.user_id' => $school['id']));

        //render pie-chart
        $pie_parent = "";
        $pie_school = "";
        foreach($all_categories as $category) {
            //school_rating
            $school_pie_rating = $tbcategory->get_school_rating($school['user_id'] ,$category['id'], null, null, $base_time);
            if($school_pie_rating['total']) {
                $average_mark['total'] = number_format($school_pie_rating['total'], 1) . " / 5";
            } else {
                $average_mark['total'] = 0 . " / 5";
            }
            $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
            $average_mark['title'] = LocalizedString($category['name']);
            $average_mark['width'] = 150;
            $pie_school .= $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);

            //parent_rating
            $parent_pie_rating = $tbcategory->get_category_mark($school['user_id'] ,$category['id'], null, null, $base_time);
            if($parent_pie_rating['total']) {
                $average_mark['total'] = (number_format($parent_pie_rating['total'], 1)) . " / 5";
            } else {
                $average_mark['total'] = 0 . " / 5";
            }
            $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
            $average_mark['title'] = LocalizedString($category['name']);
            $average_mark['width'] = 150;
            $pie_parent .= $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);
        }

        //get line-chart data
        $line_chart = array();
        for ($i = 11; $i >= 0; $i--) {
            $time = strtotime( date( 'Y-m-01', strtotime($base_time) )." -$i months");
            $date = date("m-M-Y", $time);
            $date = explode('-', $date);
            $line_chart['time'][] = $date[1] . " - " . $date[2];
            $count = 0;
            foreach($all_categories as $category) {
                $total_marks = $tbcategory->get_category_mark($school['user_id'] , $category['id'], $date[0], $date[2], $base_time);
                if($total_marks['count'] == 0) {
                    $last_month = date("m-M-Y", strtotime( date( 'Y-m-01' )." -$i-1 months"));
                    $last_date = explode('-', $last_month);
                    $total_marks = $tbcategory->get_category_mark($school['user_id'] , $category['id'], $last_date[0], $last_date[2], $base_time);
                }
                $line_chart['parent_data'][$count]['name'] = LocalizedString($category['name']);
                $line_chart['parent_data'][$count]['data'][] = floatval($total_marks['total']);

                $school_marks = $tbcategory->get_school_rating($school['user_id'] , $category['id'], $date[0], $date[2], $base_time);
                $line_chart['school_data'][$count]['name'] = LocalizedString($category['name']);
                $line_chart['school_data'][$count]['data'][] = floatval($school_marks['total']);

                $count++;
            }
        }
        $line_chart['time'] = json_encode($line_chart['time']);
        $line_chart['parent_data'] = json_encode($line_chart['parent_data']);
        $line_chart['school_data'] = json_encode($line_chart['school_data']);

        //get average rating
        $tbrate = new tbRate();
        $parent_rating = $tbrate->get_parent_rating($school['user_id']);
        $school_rating = $tbrate->get_school_rating($school['user_id']);

        $active_plans = $this->school_model->get_plans($school['user_id'], 0);
        $completed_plans = $this->school_model->get_plans($school['user_id'], 2);

        $data['parent_rating'] = @$parent_rating['average_rating'];
        $data['school_rating'] = @$school_rating['average_rating'];
        $data['active_plans'] = $active_plans;
        $data['completed_plans'] = $completed_plans;
        $data['school'] = $school;
        $data['details'] = $details;
        $data['pie_parent'] = $pie_parent;
        $data['pie_school'] = $pie_school;
        $data['line_chart'] = $line_chart;
        $data['head']['css'][] = "pages-landing.min.css";
        $data['head']['css'][] = "pages-profile.min.css";
        $data['head']['css'][] = "morris.min.css";
        $data['head']['css'][] = "charts-inline.min.css";
        $data['js'][] = "custom/highcharts.js";
        $data['js'][] = "jquery.easypiechart.min.js";
        $data['js'][] = "jquery.sparkline.min.js";
        $data['js'][] = "demo/chart-inline-demo.js";
        $data['page'] = "school/print_report";
        $data['head']['title'] = "Report";
        $this->load->view('basic_elements/index', $data);
    }

    public function switch_language() {
        $tblanguage = new tbLanguage();
        $side = $tblanguage->get_side_language();
        $primary = $tblanguage->get_primary_language();
        $current_lang = $this->session->userdata('site_language');

        if(!$current_lang) {
            $this->session->set_userdata('site_language', $side['name']);
        } else {
            if($current_lang == $side['name']) {
                $this->session->set_userdata('site_language', $primary['name']);
            } else {
                $this->session->set_userdata('site_language', $side['name']);
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function sess_destroy() {
        $this->session->sess_destroy();
    }

    public function all_session() {
        echo"<pre>";var_dump($this->session->all_userdata());
    }
	function test ()
	{
		phpinfo();
	}
}
