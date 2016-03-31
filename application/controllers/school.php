<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class School extends MY_Controller {

    private $display = 20;

    public function index() {
        $data['head']['css'][] = "pages-landing.min.css";
        $data['school'] = get_school_by_token();
        $data['page'] = "school/home_page";
        $data['has_footer'] = TRUE;
        $data['head']['title'] = "School";
        $this->load->view('basic_elements/index', $data);
    }

    public function forgot_password() {
        if($email = $this->input->post('email')) {
            $schools = $this->parent_model->get_all_users(array('user_type' => 'school'));
            $is_exist = FALSE;
            foreach($schools as $school) {
                if($email == $school['email']) {
                    $is_exist = TRUE;
                    break;
                }
            }
            if(!$is_exist) {
                $this->session->set_flashdata("recover_error", "The email has not been registered yet");
                $this->session->set_flashdata("recover_code", TRUE);
            } else {
                $this->load->library('plusauthentication/lb_authentication');
                $status = $this->lb_authentication->forgot_password_generate_code($email, null, 1);
                if($status['code'] == 200 ) {
                    $this->session->set_flashdata("success", "Please check your email box for reset password code");
                } else {
                    $this->session->set_flashdata("error", "The email has not been sent due to some errors");
                }
            }
            redirect(base_url("school/login"));
        } else {
            redirect(base_url());
        }
    }

    function change_password() {
        if($school = get_school_by_token()) {
            if($data = $this->input->post()) {
                $data['current_pass'] = md5($data['current_pass']);

                if(strlen($data['password']) < 6) {
                    $this->session->set_flashdata("error_new", "The length of your password is required with at least 6 characters");
                    redirect(base_url('school/change_password'));
                } elseif(strlen($data['confirm_pass']) < 6) {
                    $this->session->set_flashdata("error_match", "The length of your password is required with at least 6 characters");
                    redirect(base_url('school/change_password'));
                }

                if($data['current_pass'] == $school['password']) {
                    if($data['password'] == $data['confirm_pass']) {
                        $this->parent_model->update_user($school['id'], array('password' => md5($data['password'])));
                        $this->session->set_userdata("school", "");
                        $this->session->set_userdata("school_token", "");
                        $this->session->set_flashdata('success', 'Your password was changed successfully. Please re-login to your account.');
                        $this->session->set_userdata('rejected_url', base_url("school/profile/{$school['uuid']}/dashboard"."/".refine_name($school['school_name'])));
                        redirect(base_url('school/login'));
                    } else {
                        $this->session->set_flashdata("error_match", "Passwords do not match");
                    }
                } else {
                    $this->session->set_flashdata("error_pass", "Your password was incorrect");
                }
                redirect(base_url("school/change_password"));
            } else {
                $data['school'] = $school;
                $data['page'] = "school/profile_change_pass";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = "Change Password";
                $this->load->view('basic_elements/index', $data);
            }
        } else {
            redirect(base_url("school"));
        }
    }

    function load_statistics() {
        $category_id = $this->input->post('category_id');
        $user_id = $this->input->post('user_id');
        $target_type = $this->input->post('target_type');

        $tbcategory = new tbCategory();

        //column data
        $column_data = "";
        for ($i = 11; $i >= 0; $i--) {
            $time = strtotime( date( 'Y-m-01' )." -$i months");
            $date = date("m-M-Y", $time);
            $date = explode('-', $date);

            if($target_type == 0) {
                $total_marks = $tbcategory->get_category_mark($user_id ,$category_id, $date[0], $date[2]);
                $column_data .= "{device: '{$date[0]}-{$date[2]}', Total: {$total_marks['total']}},";
            } else {
                $school_rating = $tbcategory->get_school_rating($user_id ,$category_id, $date[0], $date[2]);
                if(!@$school_rating['total']) {
                    $school_rating['total'] = 0;
                }
                $column_data .= "{device: '{$date[0]}-{$date[2]}', Total: {$school_rating['total']}},";
            }
        }
        $column_array = array(
            'id' => "column-$target_type",
            'caption' => 'Mark',
            'label' => 'Total',
            'data' => $column_data
        );
        $column = $this->lbplusbuilder->get_element('schoolisting/column-chart', $column_array);

        //pie chart
        if($target_type == 0) {
            $total_mark = $tbcategory->get_category_mark($user_id ,$category_id);
        } else {
            $total_mark = $tbcategory->get_school_rating($user_id ,$category_id);
        }
        if($total_mark['total']) {
            $average_mark['total'] = number_format($total_mark['total'], 1) . " / 5";
        } else {
            $average_mark['total'] = 0 . " / 5";
        }
        $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
        $average_mark['title'] = "";
        $pie_chart = "<div>";
        $pie_chart .= $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);
        $pie_chart .= '<p style="display: inline-block;position: relative;top: 51px"><i style="position: relative;top: 2px" class="glyphicon glyphicon-chevron-left"></i>'.LocalizedString("Total Average Mark").'</p>';
        $pie_chart .= "</div>";

        $js = '<script src="'.js_url("raphael.min.js").'"></script>';
        $js .= '<script src="'.js_url("morris.min.js").'"></script>';
        $js .= '<script src="'.js_url("jquery.easypiechart.min.js").'"></script>';
        $js .= '<script src="'.js_url("jquery.sparkline.min.js").'"></script>';
        $js .= '<script src="'.js_url("demo/chart-inline-demo.js").'"></script>';
        echo $js.$pie_chart.$column;
    }

    function profile() {
        $uuid = $this->uri->segment(3);
        $tab = $this->uri->segment(4);
        $school = $this->school_model->get_by_attribute(array('uuid' => $uuid, "is_active" => 1));
        if($school['user_id']) {
            if($this->session->userdata('school_token') == $school['token']) {
                $content = "";
                if($tab == "dashboard") {
                    $data['head']['css'][] = "morris.min.css";
                    $data['head']['css'][] = "charts-inline.min.css";
                    $data['head']['css'][] = "daterangepicker.min.css";

                    $data['js'][] = "raphael.min.js";
                    $data['js'][] = "morris.min.js";
                    $data['js'][] = "jquery.easypiechart.min.js";
                    $data['js'][] = "jquery.sparkline.min.js";
                    $data['js'][] = "demo/chart-inline-demo.js";

                    $data['js'][] = "daterangepicker.min.js";
                    $data['js'][] = "moment.min.js";

                    //get details of the school
                    $details = $this->school_model->get_school_details(array('t1.user_id' => $school['id']));

                    //get all categories
                    $tbcategory = new tbCategory();
                    $parent_categories = $tbcategory->get_proper_categories($school['grade'], 0);
                    $school_categories = $tbcategory->get_proper_categories($school['grade']);

                    //column data
                    $parent_current_category = $parent_categories[0];
                    $school_current_category = $school_categories[0];

                    $column_data = "";
                    $school_data = "";
                    for ($i = 11; $i >= 0; $i--) {
                        $time = strtotime( date( 'Y-m-01' )." -$i months");
                        $date = date("m-M-Y", $time);
                        $date = explode('-', $date);
                        $total_marks = $tbcategory->get_category_mark($school['user_id'] ,$parent_current_category['id'], $date[0], $date[2]);
                        $column_data .= "{device: '{$date[0]}-{$date[2]}', Total: {$total_marks['total']}},";

                        $school_rating = $tbcategory->get_school_rating($school['user_id'] ,$school_current_category['id'], $date[0], $date[2]);
                        if(!@$school_rating['total']) {
                            $school_rating['total'] = 0;
                        }

                        $school_data .= "{device: '{$date[0]}-{$date[2]}', Total: {$school_rating['total']}},";
                    }
                    $column_array = array(
                        'id' => 'category-column',
                        'caption' => 'Mark',
                        'label' => 'Total',
                        'data' => $column_data
                    );
                    $column = $this->lbplusbuilder->get_element('schoolisting/column-chart', $column_array);

                    $school_array = array(
                        'id' => 'school-column',
                        'caption' => 'Mark',
                        'label' => 'Total',
                        'data' => $school_data
                    );
                    $school_column = $this->lbplusbuilder->get_element('schoolisting/column-chart', $school_array);

                    //get average mark of each category for parent pie chart
                    $total_mark = $tbcategory->get_category_mark($school['user_id'] ,$parent_current_category['id']);
                    if($total_mark['total']) {
                        $average_mark['total'] = (number_format($total_mark['total'], 1)) . " / 5";
                    } else {
                        $average_mark['total'] = 0 . " / 5";
                    }
                    $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
                    $average_mark['title'] = "";
                    $pie_chart = $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);

                    //get average mark of each category for school pie chart
                    $total_mark = $tbcategory->get_school_rating($school['user_id'] ,$school_current_category['id']);
                    if($total_mark['total']) {
                        $average_mark['total'] = number_format($total_mark['total'], 1) . " / 5";
                    } else {
                        $average_mark['total'] = 0 . " / 5";
                    }
                    $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
                    $average_mark['title'] = "";
                    $pie_school = $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);

                    $active_plans = $this->school_model->get_plans($school['user_id'], 0);
                    $completed_plans = $this->school_model->get_plans($school['user_id'], 2);

                    //get average rating
                    $tbrate = new tbRate();
                    $parent_rating = $tbrate->get_parent_rating($school['user_id']);
                    $school_rating = $tbrate->get_school_rating($school['user_id']);

                    $dashboard_data['parent_rating'] = @$parent_rating['average_rating'];
                    $dashboard_data['school_rating'] = @$school_rating['average_rating'];;
                    $dashboard_data['active_plans'] = $active_plans;
                    $dashboard_data['completed_plans'] = $completed_plans;
                    $dashboard_data['pie_chart'] = $pie_chart;
                    $dashboard_data['pie_school'] = $pie_school;
                    $dashboard_data['column'] = $column;
                    $dashboard_data['school_column'] = $school_column;
                    $dashboard_data['parent_categories'] = $parent_categories;
                    $dashboard_data['school_categories'] = $school_categories;
                    $dashboard_data['parent_current_category'] = $parent_current_category;
                    $dashboard_data['school_current_category'] = $school_current_category;
                    $dashboard_data['school']= $school;
                    $dashboard_data['details'] = $details;
                    $content .= load("school/dashboard", $dashboard_data, true);
                } elseif($tab == "plan-actions") {
                    $plans = tbPlan::get_model()->get_plans_with_basic_info($school["id"]);
                    $content .= html("div", "", array(
                        "id" => "modal",
                        "class" => "modal fade",
                        "tabindex" => "-1",
                        "role" => "dialog",
                        "aria-labelledby" => "mySmallModalLabel",
                        "aria-hidden" => "true"
                    ));

                    $content .= load("school/plan_action", array("plans"=>$plans), true);
                } elseif($tab == "files") {
                    $tbcategory = new tbCategory();
                    $school_categories = $tbcategory->get_proper_categories($school['grade']);
                    $data['head']['css'][] = "jstree.min.css";
                    $data['js'][] = "jstree.min.js";
                    $data['js'][] = "demo/jstree-demo.js";
                    $file_folder = tbSchool_folder::get_model()->get_folder_n_file($school["id"]);
                    $content .= load("school/school_file", array("file_folder" => $file_folder, "categories" => $school_categories), true);
                } elseif ($tab == "reviews") {
                    $tbreview = new tbReview();
                    $total = $tbreview->total_reviews($school['school_id']);
                    $total_groups = ceil($total / $this->display);
                    $reviews = $tbreview->get_school_reviews($school['school_id'], $this->display);
                    $content .= '<div id="review"><div class="posts-wrapper">';
                    if($reviews) {
                        foreach($reviews as $review) {
                            $content .= $this->render_school_reviews($review);
                        }
                        if($total_groups > 1) {
                            $content .= '<div class="text-center"><button type="button" id="more_reviews" class="btn btn-sm btn-default" data-position="'.count($reviews).'">Load more...</button></div>';
                        }
                    } else {
                        $content .= '<blockquote><p class="lead">'.LocalizedString("Your school have no review available").'</p></blockquote>';
                    }
                    $content .= '</div></div>';
                } elseif($tab == "rates") {
                    $tbrate = new tbRate();
                    $total = $tbrate->get_parent_evaluation($school['user_id']);
                    $total_groups = ceil(count($total) / $this->display);

                    $evaluations = $tbrate->get_parent_evaluation($school['user_id'], $this->display);
                    $content .= "<div id='rate'><div class='posts-wrapper'>";
                    if($evaluations) {
                        $content .= $this->render_evaluation($evaluations);
                        if($total_groups > 1) {
                            $content .= '<div class="text-center"><button type="button" id="more_rates" class="btn btn-sm btn-default" data-position="'.count($evaluations).'">Load more...</button></div>';
                        }
                    } else {
                        $content .= '<blockquote><p class="lead">'.LocalizedString("Your school have no rate available").'</p></blockquote>';
                    }
                    $content .= "</div></div>";
                } elseif ($tab == "upload-customer-data") {
                    $content .= load("school/upload_customer_data", array("customers_emails" => $school["customers_emails"]), true);
                } elseif ($tab == "saved-answers") {
                    $s = new tbSaved_answer();
                    $saved_answers = $s->get_saved_answers($school['user_id'], 2);
                    $content .= load("school/saved_answers", array("saved_answers" => $saved_answers), true);
                } else {
                    redirect(base_url("school"));
                }

                $data['content'] = $content;
                $data['school'] = $school;
                $data['tab'] = $tab;

                $data['head']['css'][] = "pages-profile.min.css";
                $data['page'] = "school/profile_page";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = "Profile";
                $this->load->view('basic_elements/index', $data);
            } else {
                $this->logout();
            }
        } else {
            redirect(base_url("school"));
        }
    }

    function edit_profile() {
        if($school = get_school_by_token()) {
            if($data = $this->input->post()) {
                if(!isset($data['appearance'])) {
                    $data['appearance'] = 0;
                }
                $error_web = FALSE;
                $error_fb = FALSE;
                $error_email = FALSE;
                if($data['web']) {
                    if(!filter_var($data['web'], FILTER_VALIDATE_URL)) {
                        $this->session->set_flashdata('error_web', "Please enter a valid url for your website");
                        $error_web = TRUE;
                    }
                }
                if($data['facebook']) {
                    if(!filter_var($data['facebook'], FILTER_VALIDATE_URL)) {
                        $this->session->set_flashdata('error_fb', "Please enter a valid url for your facebook page");
                        $error_fb = TRUE;
                    }
                }
                if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->session->set_flashdata('error_email', "Please enter a valid email");
                    $error_email = TRUE;
                }
                if($error_email || $error_web || $error_fb) {
                    redirect(base_url("school/edit_profile"));
                }

                if(!is_numeric($data['telephone']) || strlen($data['telephone']) < 10 || strlen($data['telephone']) > 11) {
                    $message = "Phone number must not contain any alphabetic character and its length must range from 10 to 11 numbers";
                    $this->session->set_flashdata('error_phone', $message);
                    redirect(base_url("school/edit_profile"));
                }

                $users = $this->parent_model->get_all_users();
                foreach($users as $u) {
                    if($data['email'] == $u['email'] && $school['id'] != $u['id']) {
                        $this->session->set_flashdata('error_email', "Your email you has just typed in is unavailable");
                        redirect(base_url("school/edit_profile"));
                    }
                }
                //update email
                $this->parent_model->update_user($school['id'], array('email' => $data['email']));
                unset($data['email']);

                //update multiple choices
                $this->school_model->delete_menu($school['school_id']);
                $insert_menu['school_id'] = $school['school_id'];
                foreach($data['menus'] as $menu) {
                    $insert_menu['menu'] = $menu;
                    $this->school_model->insert_school_menu($insert_menu);
                }
                $this->school_model->delete_language($school['school_id']);
                $insert_language['school_id'] = $school['school_id'];
                foreach($data['languages'] as $language) {
                    $insert_language['language'] = $language;
                    $this->school_model->insert_school_language($insert_language);
                }
                unset($data['menus']);unset($data['languages']);

                //$location = $this->parent_model->get_parent_location($data['district']);
                $latlnt = convert_to_latlng($data['address']);
                $data['lat'] = @$latlnt['lat'];
                $data['lng'] = @$latlnt['lng'];

                //update main school
                $data['latest_update'] = date("Y-m-d H:i:s");
                $this->school_model->update_school($school['id'], $data);

                $school = $this->school_model->get_by_attribute(array('username' => $school['username']));
                redirect(base_url("school/profile/{$school['uuid']}/dashboard/{$school['username']}"));
            } else {
                $data['all'] = $this->render_all_input($school);
                $data['location'] = $this->render_location($school['city'], $school['district']);

                //render status
                $checked = "";
                if($school['appearance'] == 1) {
                    $checked = "checked";
                }
                $data['appearance'] = $this->lbplusbuilder->get_element('schoolisting/profile-status', array('checked' => $checked));

                $data['school'] = $school;
                $data['page'] = "school/profile_edit_page";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = "Edit Profile";
                $this->load->view('basic_elements/index', $data);
            }
        } else {
            redirect(base_url('school'));
        }
    }

    function delete_account() {
        if($school = get_school_by_token()) {
            $this->school_model->update_school($school['id'], array('is_active' => 0));
            $this->session->set_userdata('school', "");
            $this->session->set_userdata('school_token', "");
        }
        redirect(base_url("school"));
    }

    function avatar_upload() {
        $this->load->library('plusgallery/lbgallery');
        $image_id = $this->lbgallery->upimage('avatar');

        $user_id = $this->uri->segment(3);
        $data['avatar'] = $image_id;
        $this->parent_model->update_user($user_id, $data);

        $image = image_render($image_id);
        echo $image;
    }

    function register() {
        redirect_after_login('school');
        $flashdata = $this->session->flashdata("data");
        $data['head']['title'] = "School Register";
        $data['head']['css'][] = "typeahead.min.css";
        $data['head']['css'][] = "pages-signin.min.css";

        $data['all'] = $this->render_all_input($flashdata, 'login-dropdown', 'login-multiplechoice', 'login-radio');
        $data['location'] = $this->render_location($flashdata['city'], $flashdata['district'], "login-location");

        $data['js'][] = "jquery.vegas.min.js";
        $data['js'][] = "jquery.validate.min.js";
        $data['js'][] = "additional-methods.min.js";
        $data['js'][] = "handlebars.min.js";
        $data['js'][] = "typeahead.bundle.min.js";
        $data['js'][] = "demo/pages-signin-demo.js";

        $data['page'] = "school/register_page";
        $this->load->view('basic_elements/index', $data);
    }

    function handle_register() {
        if($data = $this->input->post()) {
            $this->session->set_flashdata('data', $data);
            if(!isset($data['menus']) || !isset($data['languages'])) {
                if(!isset($data['languages'])) {
                    $data['languages'] = array();
                } elseif(!isset($data['menus'])) {
                    $data['menus'] = array();
                }
                $message = "Please choose your school's menu(s) and language(s)";
                $this->session->set_flashdata('error', $message);
                redirect(base_url("school/register"));
            }

            $schools = $this->school_model->get_all_schools();
            foreach($schools as $school) {
                if($data['school_name'] == $school['school_name']) {
                    $message = "Your school name existed";
                    $this->session->set_flashdata('error', $message);
                    redirect(base_url("school/register"));
                }
            }

            if(strpos(trim($data['username']), " ") !== false) {
                $message = "Your username must not contain any whitespace";
                $this->session->set_flashdata('error', $message);
            } elseif(!is_numeric($data['telephone']) || strlen($data['telephone']) < 10 || strlen($data['telephone']) > 11) {
                $message = "Phone number must not contain any alphabetic character and its length must range from 10 to 11 numbers";
                $this->session->set_flashdata('error', $message);
            } elseif($data['password'] != $data['confirm_password']) {
                $message = "There is something wrong in your password confirmation";
                $this->session->set_flashdata('error', $message);
            } else {
                $this->load->library('plusauthentication/lb_authentication');
                $register_code = $this->lb_authentication->register($data['username'], $data['email'], $data['password'], array('user_type' => "school"));
                if($register_code['code'] == 200) {
                    $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));

                    send_activate_mail($data['username'], $data['email'], base_url("school/activate_account/{$user['uuid']}"));

                    add_to_agile($data['email']);

                    unset($data['username']);unset($data['password']);unset($data['confirm_password']);
                    unset($data['email']);unset($data['location']);
                    $menus = $data['menus'];unset($data['menus']);
                    $languages = $data['languages'];unset($data['languages']);

                    //$location = $this->parent_model->get_parent_location($data['district']);
                    $latlnt = convert_to_latlng($data['address']);
                    $data['lat'] = @$latlnt['lat'];
                    $data['lng'] = @$latlnt['lng'];

                    $data['user_id'] = $user['id'];
                    $school_id = $this->school_model->insert_school($data);

                    $menu_data['school_id'] = $school_id;
                    foreach($menus as $menu) {
                        $menu_data['menu'] = $menu;
                        $this->school_model->insert_school_menu($menu_data);
                    }

                    $language_data['school_id'] = $school_id;
                    foreach($languages as $language) {
                        $language_data['language'] = $language;
                        $this->school_model->insert_school_language($language_data);
                    }

                    $this->session->set_flashdata('success', "Please check your email to activate your school");
                } else {
                    $this->session->set_flashdata('data', $data);
                    $this->session->set_flashdata('error', $register_code['description']);
                }
            }
            redirect(base_url("school/register"));
        } else {
            redirect(base_url("school"));
        }
    }

    function activate_account() {
        $uuid = $this->uri->segment(3);
        $user = $this->parent_model->get_by_attribute(array("uuid" => $uuid));
        if($user) {
            if($user['active'] = -1) {
                $this->session->set_flashdata('success', "Your account has been activated.");
                $this->parent_model->update_user($user['id'], array("active" => 1));
            }
        }
        redirect(base_url("school/login"));
    }

    function login() {
        if(!$this->session->userdata("school_token")) {
            if(isset($_SERVER['HTTP_REFERER'])) {
                if($rejected_url = $this->session->userdata('rejected_url')) {
                    $this->session->set_userdata('last_url', $rejected_url);
                } else {
                    $this->session->set_userdata('last_url', $_SERVER['HTTP_REFERER']);
                }
            }
            $data['head']['title'] = "Login";
            $data['head']['css'][] = "typeahead.min.css";
            $data['head']['css'][] = "pages-signin.min.css";

            $data['js'][] = "jquery.vegas.min.js";
            $data['js'][] = "jquery.validate.min.js";
            $data['js'][] = "additional-methods.min.js";
            $data['js'][] = "handlebars.min.js";
            $data['js'][] = "typeahead.bundle.min.js";
            $data['js'][] = "demo/pages-signin-demo.js";

            $data['page'] = "school/login_page";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect(base_url("school"));
        }
    }

    function handle_login() {
        $this->load->library('plusauthentication/lb_authentication');
        if($data = $this->input->post()) {
            $this->session->set_flashdata('username', $data['username']);
            $user = $this->school_model->get_by_attribute(array('username' => $data['username'], 'password' => md5($data['password']), 'user_type' => 'school'));
           if($user['user_id']) {
               if($user['is_active'] == 0) {
                   $this->session->set_flashdata('error', "Your school has been deleted");
               } elseif($user['active'] == 1) {
                    $user_token = $this->session->userdata('user_token');
                    $login_code = $this->lb_authentication->login($data['username'], $data['password']);
                    if($login_code['code'] == 200) {
                        $school_token = $this->session->userdata('user_token');
                        $school = $this->school_model->get_by_attribute(array('token' => $school_token));
                        $this->school_model->update_school($school['id'], array('latest_login' => date("Y-m-d H:i:s")));

                        //$this->session->set_userdata('user_token', $user_token);
                        $this->session->unset_userdata('user_token');
                        $this->session->set_userdata('school_token', $school_token);
                        $this->session->unset_userdata('flashdata');
                        if($last_url = $this->session->userdata('last_url')) {
                            $this->session->unset_userdata('last_url');
                            $this->session->unset_userdata('rejected_url');
                            redirect($last_url);
                        }
                        redirect(base_url("school"));
                    }
                } else {
                    $this->session->set_flashdata('error', "Your school has not been activated yet. Please check your email.");
                }
            } else {
                $this->session->set_flashdata('error', "Login credentials is invalid");
            }
            redirect(base_url('school/login'));
        }
    }

    function load_more_reviews() {
        $data = $this->input->post();
        $reviews = $this->render_reviews($data['school_id'], $data['position']);
        print json_encode($reviews);
    }

    function load_more_rates() {
        $data = $this->input->post();
        $tbrate = new tbRate();
        $evaluations = $tbrate->get_parent_evaluation($data['school_user_id'], $this->display, $data['position']);

        $rates['position'] = $data['position'] + count($evaluations);
        $evaluation_html = '<div class="posts-wrapper">';
        $evaluation_html .= $this->render_evaluation($evaluations);
        $evaluation_html .= '</div>';
        $rates['rates'] = $evaluation_html;
        print json_encode($rates);
    }

    function render_reviews($school_id, $position = 0) {
        $tbreview = new tbReview;
        $reviews = $tbreview->get_school_reviews($school_id, $this->display, $position);
        $review_position = $position + count($reviews);

        $review_html = "";
        foreach($reviews as $review) {
            $review_html .= '<div class="posts-wrapper">';
            $review_html .= $this->render_school_reviews($review);
            $review_html .= '</div>';
        }
        return array('reviews' => $review_html, 'position' => $review_position);
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url("school"));
    }
	/**
	 * 
	 */
	function get_file($id = -1)
	{
		$file = new tbSchool_file($id);
		if ($file->exists())
		{
			$this->load->helper('download');
			
			$data = file_get_contents("./assets/upload/{$file->name}"); // Read the file's contents
			$name = $file->display_name;
			
			force_download($name, $data);
		}
	}
	/**
	 * 
	 */
	function delete_credential ()
	{
		$id = $this->input->post("id");
		$type = $this->input->post("type");
		if ($type == "folder")
		{
			$folder = new tbSchool_folder($id);
			if ($folder->exists())
			{
				$files = tbSchool_file::get_model()->delete_record(array("folder_id"=>$folder->id));
				$folder->delete();
			}
		}
		else if ($type == "file")
		{
			$file = new tbSchool_file($id);
			if ($file->exists())
			{
				$file->delete();
			}
		}
		else
		{
			
		}
	}
	/**
	 * 
	 */
	function rename_credential ()
	{
		$id = $this->input->post("id");
		$type = $this->input->post("type");
		$name = $this->input->post("name");
		if ($type == "folder")
		{
			$folder = new tbSchool_folder($id);
			if ($folder->exists())
			{
				$folder->name = $name;
				$folder->save();
			}
		}
		else if ($type == "file")
		{
			$file = new tbSchool_file($id);
			if ($file->exists())
			{
				$file->display_name = $name;
				$file->save();
			}
		}
		else
		{
			
		}
	}
	/**
	 * 
	 */
	public function add_category()
	{
		$school = check_login("school");
		$name = $this->input->post("name");
		if (strlen($name) > 0)
		{
			$check_folder = tbSchool_folder::get_model(array("name"=>$name, "school_id"=>$school["id"]));
			if ($check_folder->exists())
			{
				$error = LocalizedString("This folder's name currently exists");
				$this->session->set_flashdata('error', $error);	
			}
			else
			{
				$folder = tbSchool_folder::get_model()->add(array("name"=>$name, "school_id"=>$school["id"]));
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * 
	 */
	public function add_file()
	{
		$school = check_login("school");
		$folder = $this->input->post("folder");
		
		if (strlen($folder) > 0)
		{
			$this->load->library('upload', $this->configUpload());
	        if ($this->upload->do_upload("files")) {
	            $data = $this->upload->data();
				
				$file = new tbSchool_file;
	            $file->name = $data['file_name'];
				$file->display_name = $data['file_name'];
	            $file->folder_id = $folder;
	            $file->save();
	        } else {
	        	echo $this->upload->display_errors();
				return;
	        }
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * 
	 */
	function configUpload($path = './assets/upload/') {
        $ci = & get_instance();
        $ci->load->helper('string');
        $config['upload_path'] = $path;
        $config['allowed_types'] = '*';
        $config['max_size'] = '1000000000';
        return $config;
    }

	/**
	 * 
	 */
	function upload_customer_data ()
	{
		$customers_emails = str_replace(" ", "",$this->input->post("customers_emails"));
        $school = check_login("school");
        if($this->input->post('save_form') == "save_form") {
            $emails = array_unique(explode(",", $customers_emails));
            $this->load->helper('email');
            foreach ($emails as $email) {
                if (!valid_email($email))
                {
                    $notification = LocalizedString("There's one of your customers' email that is invalid.", "school/profile");
                    $this->session->set_flashdata('error', $notification);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                add_to_agile($email, "School Customer Email");
            }
            $customers_emails = implode(',', $emails);
            $this->school_model->update_school($school["id"], array("customers_emails"=>$customers_emails));
            $notification = LocalizedString("Your email list has been saved");
            $this->session->set_flashdata('success', $notification);
            redirect($_SERVER['HTTP_REFERER']);
        }  else {
            redirect(base_url('school'));
        }
	} 
}

