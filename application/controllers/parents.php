<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parents extends MY_Controller {

    private $display = 20;

    public function index()
    {
        $data['head']['title'] = "Home";
        $data['head']['css'][] = "pages-landing.min.css";
        $data['js'][] = "jquery.counterup.min.js";

        $data['page'] = "parents/home_page";
        $data['has_footer'] = TRUE;
        $data['user'] = get_user_by_token();
        $this->load->view('basic_elements/index', $data);
    }

    public function forgot_password() {
        if($email = $this->input->post('email')) {
            $parents = $this->parent_model->get_all_users(array('user_type' => 'parent'));
            $is_exist = FALSE;
            foreach($parents as $parent) {
                if($email == $parent['email']) {
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
            redirect(base_url("parents/login"));
        } else {
            redirect(base_url());
        }
    }

    function change_password() {
        if($user = get_user_by_token()) {
            if($data = $this->input->post()) {
                $data['current_pass'] = md5($data['current_pass']);

                if(strlen($data['password']) < 6) {
                    $this->session->set_flashdata("error_new", "The length of your password is required with at least 6 characters");
                    redirect(base_url('parents/change_password'));
                } elseif(strlen($data['confirm_pass']) < 6) {
                    $this->session->set_flashdata("error_match", "The length of your password is required with at least 6 characters");
                    redirect(base_url('parents/change_password'));
                }

                if($data['current_pass'] == $user['password']) {
                    if($data['password'] == $data['confirm_pass']) {
                        $this->parent_model->update_user($user['id'], array('password' => md5($data['password'])));
                        $this->session->unset_userdata("user");
                        $this->session->unset_userdata("user_token");
                        $this->session->set_flashdata('success', 'Your password was changed successfully. Please re-login to your account.');
                        $this->session->set_userdata('rejected_url', base_url("parents/profile/{$user['uuid']}/{$user['username']}"));
                        redirect(base_url('parents/login'));
                    } else {
                        $this->session->set_flashdata("error_match", "Passwords do not match");
                    }
                } else {
                    $this->session->set_flashdata("error_pass", "Your password was incorrect");
                }
                redirect(base_url("parents/change_password"));
            } else {
                $data['user'] = $user;
                $data['page'] = "parents/profile_change_pass";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = "Change Password";
                $this->load->view('basic_elements/index', $data);
            }
        } else {
            redirect(base_url());
        }
    }

    function load_more_bookmarks() {
        $data = $this->input->post();

        $bookmarks = $this->parent_model->get_bookmarks($data['user_id'], $data['current_id'], $this->display, $data['position']);
        $bookmark_position = $data['position'] + count($bookmarks);
        foreach($bookmarks as $k => $v) {
            $bookmarks[$k]['create_time'] = LocalizedString("Bookmarked in") . " " . date("d-m-Y", strtotime($v['create_time']));
        }
        $bookmarked_schools = $this->load_results($bookmarks, $data['current_id']);
        print json_encode(array('schools' => $bookmarked_schools, 'position' => $bookmark_position));
    }

    function load_more_recommendations() {
        $data = $this->input->post();

        $recommendations = $this->parent_model->get_recommendations($data['user_id'], $data['current_id'], $this->display, $data['position']);
        $recommendations_position = $data['position'] + count($recommendations);
        foreach($recommendations as $k => $v) {
            $recommendations[$k]['create_time'] = LocalizedString("Referred in") . " " . date("M d, Y", strtotime($v['create_time']));
        }
        $bookmarked_schools = $this->load_results($recommendations, $data['current_id']);
        print json_encode(array('schools' => $bookmarked_schools, 'position' => $recommendations_position));
    }

    function load_more_reviews() {
        $data = $this->input->post();
        $reviews = $this->render_reviews($data['user_id'], $data['position']);
        print json_encode($reviews);
    }

    function render_reviews($user_id, $position = 0) {
        $tbreview = new tbReview;
        $reviews = $tbreview->get_parent_reviews($user_id, $this->display, $position);
        $review_position = $position + count($reviews);

        $review_html = "";
        foreach($reviews as $review) {
            $review_data = array(
                'link' => base_url("school/profile/{$review['uuid']}/dashboard") . "/" . refine_name($review['school_name']),
                'name' => $review['school_name'],
                'date' => date("M d, Y", strtotime($review['create_time'])),
                'content' => $review['review'],
            );
            $review_html .= $this->lbplusbuilder->get_element('schoolisting/parent-review', $review_data);
        }
        return array('reviews' => $review_html, 'position' => $review_position);
    }

    function profile() {
        $uuid = $this->uri->segment(3);
        $user = $this->parent_model->get_by_attribute(array('uuid' => $uuid, "is_active" => 1));
        if($user) {
            //check if user is a newcomer
            $data['is_newcomer'] = FALSE;
            if($this->session->userdata('newcomer')) {
                $data['is_newcomer'] = TRUE;
                $this->session->unset_userdata('newcomer');
            }

            $is_owner = FALSE;
            if($this->session->userdata('user_token') == $user['token']) {
                $is_owner = TRUE;
            }
            $current_user = $this->parent_model->get_by_attribute(array('token' => $this->session->userdata('user_token')));
            $current_id = 0;
            if($current_user) {
                $current_id = $current_user['id'];
            }
            $tbrate = new tbRate();

            $s = new tbSaved_answer();
            $saved_answers = $s->get_saved_answers($user['id'], 1);
            $data['questionnaire_type'] = "questionnaire_for_parent";
            $data['saved_answers'] = $saved_answers;

            $bookmarked_results = $this->parent_model->get_bookmarks($user['id'], $current_id, 0);
            $bookmarked_count = count($bookmarked_results);
            $bookmarked_total_groups = ceil($bookmarked_count / $this->display);
            $bookmarked = $this->parent_model->get_bookmarks($user['id'], $current_id, $this->display);
            $bookmarked_position = count($bookmarked);
            foreach($bookmarked as $k => $v) {
                $bookmarked[$k]['create_time'] = LocalizedString("Bookmarked in") . " " . date("d-m-Y", strtotime($v['create_time']));
                $bookmarked[$k]['parent_rating'] = $tbrate->get_parent_rating($v['user_id']);
                $bookmarked[$k]['school_rating'] = $tbrate->get_school_rating($v['user_id']);
            }
            $bookmarked_schools = $this->load_results($bookmarked, $current_id);

            $recommended_results = $this->parent_model->get_recommendations($user['id'], $current_id, 0);
            $recommend_count = count($recommended_results);
            $recommend_total_groups = ceil($recommend_count / $this->display);
            $recommendations = $this->parent_model->get_recommendations($user['id'], $current_id, $this->display);
            $recommendation_position = count($recommendations);
            foreach($recommendations as $k => $v) {
                $recommendations[$k]['create_time'] = LocalizedString("Referred in") . " " . date("M d, Y", strtotime($v['create_time']));
                $recommendations[$k]['parent_rating'] = $tbrate->get_parent_rating($v['user_id']);
                $recommendations[$k]['school_rating'] = $tbrate->get_school_rating($v['user_id']);
            }
            $recommendation_schools = $this->load_results($recommendations, $current_id);

            $review_html = "";
            $tbreview = new tbReview();
            $total_reviews = $tbreview->total_parent_reviews($user['id']);
            $review_total_groups = ceil($total_reviews / $this->display);
            $reviews = $tbreview->get_parent_reviews($user['id'], $this->display);
            //echo "<pre>";var_dump($reviews);die;
            foreach($reviews as $review) {
                $review_data = array(
                    'link' => base_url("parents/school_details/{$review['uuid']}") . "/" . refine_name($review['school_name']),
                    'name' => $review['school_name'],
                    'date' => date("M d, Y", strtotime($review['create_time'])),
                    'content' => $review['review'],
                );
                $review_html .= $this->lbplusbuilder->get_element('schoolisting/parent-review', $review_data);
            }
            if($review_total_groups > 1) {
                $review_html .= '<div class="text-center"><button type="button" id="more_reviews" class="btn btn-sm btn-default" data-position="'.count($reviews).'">Load more...</button></div>';
            }

            $tbrate = new tbRate();
            $evaluations = $tbrate->get_all_parent_evaluation($user['id']);

            $rate_html = "";
            if($evaluations) {
                foreach($evaluations as $evaluation) {
                    $rate_html .= '<div class="post-item">';
                    $rate_html .= '<p style="font-size: 15px">'.date('d M, Y', strtotime($evaluation['createtime'])).'</p>';
                    $rate_html .= '<hr><div class="post-content"><div class="media"><div class="media-body">';
                    $rate_html .= '<p>Rated <a target="_blank" href="'.base_url("parents/school_details/{$evaluation['uuid']}") . "/" .refine_name($evaluation['school_name']).'">'.$evaluation['school_name'].'</a> with average mark of <b style="font-size: 24px"> '.number_format($evaluation['average_rate'], 1).'</b></p>';
                    $rate_html .= '</div></div></div></div>';
                }
            }

            $user['location'] = $this->parent_model->get_parent_location(@$user['district']);
            $data['user'] = $user;
            $data['rate_html'] = $rate_html;
            $data['current_id'] = $current_id;
            $data['bookmarked_schools'] = $bookmarked_schools;
            $data['bookmarked_position'] = $bookmarked_position;
            $data['bookmarked_total_groups'] = $bookmarked_total_groups;
            $data['recommended_schools'] = $recommendation_schools;
            $data['recommendation_position'] = $recommendation_position;
            $data['recommendation_total_groups'] = $recommend_total_groups;
            $data['review_html'] = $review_html;
            $data['is_owner'] = $is_owner;
            $data['head']['css'][] = "pages-profile.min.css";
            $data['page'] = "parents/profile_page";
            $data['has_footer'] = TRUE;
            $data['has_header'] = TRUE;
            $data['head']['title'] = "Profile";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect(base_url());
        }
    }

    function edit_profile() {
        if($user = get_user_by_token()) {
            if($data = $this->input->post()) {
                if(!isset($data['appearance'])) {
                    $data['appearance'] = 0;
                }
                $users = $this->parent_model->get_all_users();
                if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->session->set_flashdata('error', "Please enter a valid email");
                    redirect(base_url("parents/edit_profile"));
                }
                foreach($users as $u) {
                    if($data['email'] == $u['email'] && $user['id'] != $u['id']) {
                        $this->session->set_flashdata('error', "Your email you has just typed in is unavailable");
                        redirect(base_url("parents/edit_profile"));
                    }
                }
                $this->parent_model->update_user($user['id'], array('email' => $data['email']));
                unset($data['email']);

                $location = $this->parent_model->get_parent_location($data['district']);
                $latlnt = convert_to_latlng($location['district_name'] . ", " . $location['city_name']);
                $data['lat'] = @$latlnt['lat'];
                $data['lng'] = @$latlnt['lng'];

                $this->parent_model->update_parent($user['id'], $data);
                $user = $this->parent_model->get_by_attribute(array('username' => $user['username']));
                $this->session->set_userdata('user', $user);
                redirect(base_url("parents/profile/{$user['uuid']}/{$user['username']}"));
            } else {
                $data['location'] = $this->render_location($user['city'], $user['district']);
                $data['user'] = $user;
                $data['page'] = "parents/profile_edit_page";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = "Edit Profile";
                $this->load->view('basic_elements/index', $data);
            }
        } else {
            redirect(base_url());
        }
    }

    function get_districts() {
        $city_id = $this->input->post('city_id');
        $districts = $this->parent_model->get_districts($city_id);
        $html = "";
        foreach($districts as $district) {
            $html .= "<option value='{$district['id']}'>{$district['name']}</option>";
        }
        echo $html;
    }

    function delete_account() {
        if($user = get_user_by_token()) {
            $this->parent_model->update_parent($user['id'], array('is_active' => 0));
            $this->session->set_userdata('user', "");
            $this->session->set_userdata('user_token', "");
            $this->load->helper('cookie');
            delete_cookie("user_token");
        }
        redirect(base_url());
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
        redirect_after_login('parents');
        $data['head']['title'] = "Register";
        $data['head']['css'][] = "typeahead.min.css";
        $data['head']['css'][] = "pages-signin.min.css";

        $data['js'][] = "jquery.vegas.min.js";
        $data['js'][] = "jquery.validate.min.js";
        $data['js'][] = "additional-methods.min.js";
        $data['js'][] = "typeahead.bundle.min.js";
        $data['js'][] = "handlebars.min.js";
        $data['js'][] = "demo/pages-signin-demo.js";
        $flashdata = $this->session->flashdata('data');
        $data['methods'] = $this->render_methods($flashdata['methods']);

        $data['page'] = "parents/register_page";
        $this->load->view('basic_elements/index', $data);
    }

    function handle_register() {
        if($data = $this->input->post()) {
            $this->session->set_flashdata('full_name', $data['full_name']);
            $this->session->set_flashdata('username', $data['username']);
            $this->session->set_flashdata('email', $data['email']);
            if(!isset($data['methods'])) {
                $message = "Please help us finding out the ways you know about us by choose at least one of our options";
                $this->session->set_flashdata('error', $message);
            } elseif(strpos(trim($data['username']), " ") !== false) {
                $message = "Your username must not contain any whitespace";
                $this->session->set_flashdata('error', $message);
            } elseif(strlen($data['full_name']) < 5 || strlen($data['full_name']) > 70) {
                $message = "Your full name must be ranged from 5 to 70 characters";
                $this->session->set_flashdata('error', $message);
            } elseif($data['password'] != $data['confirm_password']) {
                $message = "There is something wrong in your password confirmation";
                $this->session->set_flashdata('error', $message);
            } elseif(strlen($data['full_name']) < 5 || strlen($data['full_name']) > 70) {
                $message = "Your full name must be ranged from 5 to 70 characters";
                $this->session->set_flashdata('error', $message);
            } elseif($data['password'] != $data['confirm_password']) {
                $message = "There is something wrong in your password confirmation";
                $this->session->set_flashdata('error', $message);
            } else {
                $this->load->library('plusauthentication/lb_authentication');
                $register_code = $this->lb_authentication->register($data['username'], $data['email'], $data['password']);
                if($register_code['code'] == 200) {
                    $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));

                    $methods['user_id'] = $user['id'];
                    foreach($data['methods'] as $value) {
                        $methods['method'] = $value;
                        $this->parent_model->insert_method($methods);
                    }

                    $parent['user_id'] = $user['id'];
                    $parent['full_name'] = $data['full_name'];
                    $this->parent_model->insert_parent($parent);

                    //send activation email
                    send_activate_mail($data['username'], $data['email'], base_url("parents/activate_account/{$user['uuid']}"));

                    add_to_agile($data['email'], "", array("name"=>"first_name", "value"=> $data['full_name'], "type"=>"SYSTEM"));

                    //notify the success massage for the user
                    $this->session->set_flashdata('success', "Please check your email to activate the account");

                    $this->session->set_flashdata('full_name', "");
                    $this->session->set_flashdata('username', "");
                    $this->session->set_flashdata('email', "");
                } else {
                    $this->session->set_flashdata('error', $register_code['description']);
                }
            }
            redirect(base_url("parents/register"));
        }
    }

    function activate_account() {
        $uuid = $this->uri->segment(3);
        $user = $this->parent_model->get_by_attribute(array("uuid" => $uuid));
        if($user) {
            if($user['active'] = -1) {
                $this->session->set_flashdata('success', "Your account has been activated.");
                $this->session->set_userdata('newcomer', TRUE);
                $this->parent_model->update_user($user['id'], array("active" => 1));
            }
        }
        redirect(base_url("parents/login"));
    }

    function login() {
        if(!$user = get_user_by_token()) {
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

            $data['page'] = "parents/login_page";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect(base_url());
        }
    }

    function handle_login() {
        $this->load->library('plusauthentication/lb_authentication');
        if($data = $this->input->post()) {
            $this->session->set_flashdata('username', $data['username']);
            $user = $this->parent_model->get_by_attribute(array('username' => $data['username'], 'password' => md5($data['password']), 'user_type' => 'parent'));
            if($user) {
                if($user['is_active'] == 0) {
                    $this->session->set_flashdata('error', "Your account has been deleted");
                } elseif($user['active'] == 1) {
                    $login_code = $this->lb_authentication->login($data['username'], $data['password']);
                    if($login_code['code'] == 200) {
                        $this->session->unset_userdata('school_token');
                        if($this->session->userdata('newcomer')) {
                            redirect(base_url("parents/profile/{$user['uuid']}/{$user['username']}"));
                        } elseif($last_url = $this->session->userdata('last_url')) {
                            $this->session->unset_userdata('last_url');
                            $this->session->unset_userdata('rejected_url');
                            redirect($last_url);
                        }
                        redirect(base_url());
                    }
                } else {
                    $this->session->set_flashdata('error', "Your account has not been activated yet. Please check your email.");
                }
            } else {
                $this->session->set_flashdata('error', "Login credentials is invalid");
            }
            redirect(base_url('parents/login'));
        }
    }

    function login_by_google() {
        $this->load->library('plusauthentication/lb_authentication');
        $this->lb_authentication->google_login(base_url('parents/after_google_login'));
    }

    function after_google_login() {
        $this->session->unset_userdata('school_token');
        $last_url = base_url();
        if($this->session->userdata('last_url')) {
            $last_url = $this->session->userdata('last_url');
            $this->session->unset_userdata('last_url');
            $this->session->unset_userdata('rejected_url');
        }
        $token = $this->session->userdata('user_token');
        $u = new tbUser();
        $u->get_by_token($token);
        if($u->exists()) {
            add_to_agile($u->email);
            $parent = $this->parent_model->get_by_attribute(array('user_id' => $u->id));
            if(!$parent) {
                $data = array(
                    'user_id' => $u->id,
                    'full_name' => $u->username,
                    'appearance' => 1,
                    'is_active' => 1
                );
                $this->parent_model->insert_parent($data);
            }
        } else {
            $this->session->sess_destroy();
        }
        redirect($last_url);
    }

    private function handle_search($key, $display, $position, $get_data = array(), $user_id = 0) {
        if($key) {
            $schools = $this->school_model->search_school_by_location($key, "city", $display, $position, $user_id);
            if(!$schools) {
                $schools = $this->school_model->search_school_by_location($key, "district", $display, $position, $user_id);
            }
			
            if(!$schools) {
                $schools = $this->school_model->search_school_by_name($key, $display, $position, $user_id);
            }
			
            if(!$schools) {
                $schools = $this->school_model->search_school_by_address($key, $display, $position, $user_id);
            }
            
        } else {
            $school_data = array();
            unset($get_data['rating_sort']);
            foreach($get_data as $key => $val) {
                if($key == "languages" || $key == "menus") {
                    continue;
                }
                if($val != "") {
                    $school_data[$key] = $val;
                }
            }
            $schools = $this->school_model->advanced_search($school_data, @$get_data['languages'], @$get_data['menus'], $display, $position, $user_id);
        }
        return $schools;
    }

    function search_submit() {
        $get_data = $this->input->get();
        $get_url = "";
        $count = 1;
        foreach($get_data as $key => $val) {
            if($val != "") {
                if($count == 1) {
                    $get_url .= $key . "=" . $val;
                } else {
                    if($key == "languages" || $key == "menus") {
                        foreach($get_data[$key] as $k => $v) {
                            $get_url .= "&" . $key . "[]=" . $v;
                        }
                    } else {
                        $get_url .= "&" . $key . "=" . $val;
                    }
                }
                $count++;
            }
        }
        redirect(base_url("parents/search_results?$get_url"));
    }

    function search_results() {
        if($get_data = $this->input->get()) {
            $key = $this->input->get('key');

            $results = $this->handle_search($key, 0, 0, $get_data);

            $total_results = count($results);
            $total_groups = ceil($total_results / $this->display);

            $all_input['private_playground'] = $this->input->get('private_playground');
            $all_input['extended_hours'] = $this->input->get('extended_hours');
            $all_input['languages'] = $this->input->get('languages');
            $all_input['menus'] = $this->input->get('menus');
            foreach($this->dropdown_elements as $k => $v) {
                $all_input[$k] = $this->input->get($k);
            }
            $data['all'] = $this->render_all_input($all_input, 'login-dropdown', 'login-multiplechoice', 'search-radio', "");

            $city = $this->input->get('city');
            $district = $this->input->get('district');
            $data['location'] = $this->render_location($city, $district, "login-location", "");

            $data['school_name'] = $this->input->get('school_name');
            $data['establishment_year'] = $this->input->get('establishment_year');

            $data['get_data']= $get_data;
            $data['total_results'] = $total_results;
            $data['total_groups'] = $total_groups;
            $data['results'] = $results;
            $data['key'] = $key;

            $data['head']['css'][] = "animate.min.css";
            $data['js'][] = "viewportchecker.js";
            $data['head']['title'] = "School Search Results";
            $data['head']['css'][] = "pages-profile.min.css";
            $data['has_footer'] = TRUE;
            $data['has_header'] = TRUE;
            $data['page'] = "search_results";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function autoload()
    {
        $key = $this->input->post('key');
        $get_data = $this->input->post('get_data');
        $group_number = $this->input->post('group_no');
        $position = ($group_number * $this->display);
        $user_id = 0;
        if($user = get_user_by_token()) {
            $user_id = $user['id'];
        }
        $results = $this->handle_search($key, $this->display, $position, $get_data, $user_id);
        $tbrate = new tbRate();
        for($i=0;$i<count($results);$i++) {
            $results[$i]['parent_rating'] = $tbrate->get_parent_rating($results[$i]['user_id']);
            if(!$school_rating = $tbrate->get_school_rating($results[$i]['user_id'])) {
                $school_rating = NULL;
            }
            $results[$i]['school_rating'] = $school_rating;
        }

        if(isset($get_data['rating_sort'])) {
            $rating_sort = $get_data['rating_sort'];
            $rating = array();
            if($rating_sort == 1) {
                foreach ($results as $key => $row) {
                    $rating[$key] = $row['parent_rating']['average_rating'];
                }
                array_multisort($rating, SORT_DESC, $results);
            } elseif($rating_sort == 2) {
                foreach ($results as $key => $row) {
                    $rating[$key] = $row['parent_rating']['average_rating'];
                }
                array_multisort($rating, SORT_ASC, $results);
            } elseif($rating_sort == 3) {
                foreach ($results as $key => $row) {
                    $rating[$key] = $row['school_rating']['average_rating'];
                }
                array_multisort($rating, SORT_DESC, $results);
            } else {
                foreach ($results as $key => $row) {
                    $rating[$key] = $row['school_rating']['average_rating'];
                }
                array_multisort($rating, SORT_ASC, $results);
            }
        }

        $html = $this->load_results($results, $user_id, $group_number);
        if(!$html && $group_number < 1) {
            $html = '<div class="panel panel-minimalize" style="text-align: center"><div class="panel-body"><div class="tab-content sr-wrapper">';
            $html .= '<b>'.LocalizedString("No record matching your criteria in our database").'</b>';
            $html .= '</div></div></div>';
        }
        echo $html;
    }

    function school_details() {
        $uuid = $this->uri->segment(3);
        $current_user = get_user_by_token();
        $current_id = 0;
        if($current_user) {
            $current_id = $current_user['id'];
            add_to_agile($current_user['email'], "View a school");
        }
        $school = $this->school_model->get_school_details(array('is_active' => 1, 'appearance' => 1,'uuid' => $uuid), $current_id);
        if($school) {
            if($school['is_active'] == 1 && $school['appearance'] == 1 && $school['active'] == 1) {
                $this->school_model->update_school($school['user_id'], array('total_views' => $school['total_views'] + 1));

                //multiple
                $multiplechoice_html = "";
                foreach($this->multiplechoice_elements as $k => $v) {
                    $multiplechoice_html .= '<p style="margin-bottom: 20px"><i class="'.$v['icon'].' fa-2x"></i>';
                    $multiplechoice_html .= '<span><b class="school-details-title"> '.LocalizedString($v['label']).':</b><br>';
                    foreach($v['values'] as $key => $value) {
                        $multiplechoice = explode(',', $school[$k]);
                        $multi_total = count($multiplechoice);
                        for($i=0;$i<$multi_total;$i++) {
                            $comma = ", ";
                            if($i == $multi_total - 1) {
                                $comma = "";
                            }
                            if($multiplechoice[$i] == $key) {
                                $multiplechoice_html .= '<span class="school-details-text"> '.LocalizedString($value).$comma.'</span>';
                                break;
                            }
                        }
                    }
                    $multiplechoice_html .= '</span></p>';
                }

                //dropdown
                $details_html = '<div class="col-md-4">';
                $count = 1;
                foreach($this->dropdown_elements as $k => $v) {
                    if($count == 6) {
                        $details_html .= '<div class="col-md-4">';
                        $details_html .= $multiplechoice_html;
                    }
                    $details_html .= '<p style="margin-bottom: 20px"><i class="'.$v['icon'].' fa-2x"></i>';
                    $details_html .= '<span><b class="school-details-title"> '.LocalizedString($v['label']).':</b><br>';
                    foreach($v['values'] as $key => $value) {
                        if($key == $school[$k]) {
                            $details_html .= '<span class="school-details-text"> '.LocalizedString($value).'</span></span></p>';
                            if($count == 5) {
                                $details_html .= "</div>";
                            }
                            break;
                        }
                    }
                    $count++;
                }
                $details_html .= "</div>";
                //average_rating
                $tbrate = new tbRate();
                $parent_rating = $tbrate->get_parent_rating($school['user_id']);
                $school_rating = $tbrate->get_school_rating($school['user_id']);

                //nearby school on google map
                $nearby_schools = $this->school_model->get_nearby_schools($school['lat'], $school['lng'], 1);
                $school_ids = array();
                foreach($nearby_schools as $k => $v) {
                    $school_ids[] = $v['id'];
                    $nearby_schools[$k]['type'] = $this->get_name_from_dropdown('type', $v['type']);
                    $nearby_schools[$k]['grade'] = $this->get_name_from_dropdown('grade', $v['grade']);
                    $nearby_schools[$k]['rating'] = $tbrate->get_parent_rating($v['user_id']);
                    if($nearby_schools[$k]['rating']) {
                        $nearby_schools[$k]['rating_mark'] = $nearby_schools[$k]['rating']['average_rating'];
                    } else {
                        $nearby_schools[$k]['rating_mark'] = 0;
                    }
                }

                //nearby school for listing
                if($school_ids) {
                    $nearby_school_html = "";
                    for($i=0;$i<count($nearby_schools);$i++) {
                        if($school_ids[$i] == $school['id']) {
                            continue;
                        } else {
                            $nearby_school_result = $this->school_model->get_school_by_id($school_ids[$i], $current_id);
                            $nearby_school_result[0]['rating'] = $nearby_schools[$i]['rating'];
                            $nearby_school_html .= $this->load_results($nearby_school_result, $current_id, 1);
                        }
                    }
                    $data['nearby_school_html'] = $nearby_school_html;
                }

                //render reviews
                $tbreview = new tbReview();
                $reviews = $tbreview->get_school_reviews($school['id'], 10);
                $review_html = "";
                foreach($reviews as $review) {
                    $review_html .= $this->render_school_reviews($review);
                }

                $tbcategory = new tbCategory();
                $school_categories = $tbcategory->get_proper_categories($school['grade']);
                $parent_categories = $tbcategory->get_proper_categories($school['grade'], 0);

                //render pie-chart
                $pie_parent = "";
                $pie_school = "";
                foreach($parent_categories as $category) {
                    //parent_rating
                    $parent_pie_rating = $tbcategory->get_category_mark($school['user_id'] ,$category['id']);
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
                foreach($school_categories as $category) {
                    //school_rating
                    $school_pie_rating = $tbcategory->get_school_rating($school['user_id'] ,$category['id']);
                    if($school_pie_rating['total']) {
                        $average_mark['total'] = number_format($school_pie_rating['total'], 1) . " / 5";
                    } else {
                        $average_mark['total'] = 0 . " / 5";
                    }
                    $average_mark['percentage'] = ($average_mark['total'] * 100) / 5;
                    $average_mark['title'] = LocalizedString($category['name']);
                    $average_mark['width'] = 150;
                    $pie_school .= $this->lbplusbuilder->get_element('schoolisting/pie-chart', $average_mark);
                }

                //get line-chart data
                $line_chart = array();
                for ($i = 11; $i >= 0; $i--) {
                    $time = strtotime( date( 'Y-m-01' )." -$i months");
                    $date = date("m-M-Y", $time);
                    $date = explode('-', $date);
                    $line_chart['time'][] = $date[0] . " - " . $date[2];
                    $count = 0;
                    foreach($parent_categories as $category) {
                        $total_marks = $tbcategory->get_category_mark($school['user_id'] , $category['id'], $date[0], $date[2]);
                        $line_chart['parent_data'][$count]['name'] = LocalizedString($category['name']);
                        $line_chart['parent_data'][$count]['data'][] = floatval($total_marks['total']);
                        $count++;
                    }
                    $count = 0;
                    foreach($school_categories as $category) {
                        $school_marks = $tbcategory->get_school_rating($school['user_id'] , $category['id'], $date[0], $date[2]);
                        if(!@$school_marks['total']) {
                            $school_marks['total'] = 0;
                        }
                        $line_chart['school_data'][$count]['name'] = LocalizedString($category['name']);
                        $line_chart['school_data'][$count]['data'][] = floatval($school_marks['total']);
                        $count++;
                    }
                }
                $line_chart['time'] = json_encode($line_chart['time']);
                $line_chart['parent_data'] = json_encode($line_chart['parent_data']);
                $line_chart['school_data'] = json_encode($line_chart['school_data']);

                //get unique parent rates
                $evaluations = $tbrate->get_unique_parent_evaluation($school['user_id']);
                if($evaluations) {
                    $evaluation_content = $this->render_evaluation($evaluations);
                    $parent_rate_modal = $this->lbplusbuilder->get_element('schoolisting/show_rates-modal', array('title'=>LocalizedString("Parent Rates"),'content'=>$evaluation_content));
                    $data['parent_rate_modal'] = $parent_rate_modal;
                }

                $data['parent_rating'] = @$parent_rating['average_rating'];
                $data['total_rate'] = @$parent_rating['total_rate'];
                $data['school_rating'] = @$school_rating['average_rating'];
                $data['pie_parent'] = $pie_parent;
                $data['pie_school'] = $pie_school;
                $data['line_chart'] = $line_chart;
                $data['nearby_schools'] = $nearby_schools;
                $data['current_id'] = $current_id;
                $data['school'] = $school;
                $data['school_url'] = base_url("parents/school_details/{$school['uuid']}") . "/" . refine_name($school['school_name']);
                $data['details_html'] = $details_html;
                $data['review_html'] = $review_html;
                $data['head']['css'][] = "pages-landing.min.css";
                $data['head']['css'][] = "pages-profile.min.css";
                $data['head']['css'][] = "morris.min.css";
                $data['head']['css'][] = "charts-inline.min.css";
                $data['js'][] = "raphael.min.js";
                $data['js'][] = "morris.min.js";
                $data['js'][] = "jquery.easypiechart.min.js";
                $data['js'][] = "jquery.sparkline.min.js";
                $data['js'][] = "demo/chart-inline-demo.js";
                $data['js'][] = "custom/highcharts.js";
                $data['js'][] = "gmaps.min.js";
                $data['page'] = "school/school_page";
                $data['has_footer'] = TRUE;
                $data['has_header'] = TRUE;
                $data['head']['title'] = $school['school_name'];
				$data['head']['opg_title'] = $school['school_name'];
				$data['head']['opg_image'] = base_url("/plusgallery/services/showbyid?id=" . $school['avatar']);
				
                $this->load->view('basic_elements/index', $data);
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }

    function activity_like() {
        $this->load->model('activity_model');
        $data = $this->input->post();
        //unlike
        if(@$data['un']) {
            unset($data['un']);
            $this->activity_model->unlike($data);
            //like
        } else {
            $this->activity_model->like($data);
        }
    }

    function activity_bookmark() {
        $this->load->model('activity_model');
        $data = $this->input->post();
        //unbookmark
        if(@$data['un']) {
            unset($data['un']);
            $this->activity_model->unbookmark($data);
            //bookmark
        } else {
            $this->activity_model->bookmark($data);
            $u = new tbUser($data['user_id']);
            add_to_agile($u->email, "Bookmark a school");
        }
    }

    function activity_refer() {
        $this->load->model('activity_model');
        $data = $this->input->post();
        $user_token = $this->session->userdata('user_token');
        $user = get_user_by_token($user_token);
        if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->activity_model->refer($data);
            $school = $this->school_model->get_by_attribute(array('t2.id' => $data['school_id']));
            $params = array("link" => base_url("parents/school_details/{$school['uuid']}" . "/" . refine_name($school['school_name'])));
            $config = array(
                "send_from" => "general",
                "send_to" => $data['email'],
                "subject" => "School Recommendation from your Friend - {$user['email']}",
                "template" => "school_recommendation",
                "sender" => 'Admin'
            );
            $this->load->library('plusmailer/mail_library');
            $this->mail_library->send_mail($config, $params);
            add_to_agile($user['email'], "Refer a school");
        } else {
            echo 1;
        }
    }

    function activity_share() {
        $data = $this->input->post();
        $this->load->library('plusmailer/mail_library');
        if(isset($data['is_school'])) {
            $customers_emails = str_replace(" ", "", $data["email"]);
            $params = array("message" => $data['message'], 'link' => $data['current_url']);
            $config = array(
                "send_from" => "general",
                "send_to" => $data['email'],
                "subject" => LocalizedString("Schoolisting - Review and Rate Sharing"),
                "template" => "email_share",
                "sender" => 'Admin'
            );
            $this->mail_library->send_mail($config, $params);
            $school = check_login("school");
            $this->school_model->update_school($school["id"], array("customers_emails"=>$customers_emails));
        } else {
            if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $params = array("message" => $data['message'], 'link' => $data['current_url']);
                $config = array(
                    "send_from" => "general",
                    "send_to" => $data['email'],
                    "subject" => LocalizedString("Schoolisting - Review and Rate Sharing"),
                    "template" => "email_share",
                    "sender" => 'Admin'
                );
                $this->mail_library->send_mail($config, $params);
            } else {
                echo 1;
            }
        }
    }

    function logout() {
        $this->session->sess_destroy();
        $this->load->helper('cookie');
        delete_cookie("user_token");
        redirect(base_url());
    }

}
