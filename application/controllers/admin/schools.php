<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schools extends MY_Controller {

    var $data = array();

    function __construct ()
    {
        parent::__construct();
        $this->data['head']['css'][] = "admin.css";
        $this->data['admin_header'] = load("admin/header", null, TRUE);
        $this->data['admin_sidebar'] = admin_sidebar(3);
        $this->data['page'] = "admin/main";
    }

    function index()
    {
        $this->data['admin_title'] = LocalizedString("Schools");
        $this->data['head']['title'] = LocalizedString("Schools");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Schools"));

        $error = $this->session->flashdata('error');
        $success = $this->session->flashdata('success');

        $this->load->library('tablemanagement/table_management');
        $content = "";
        if($error) {
            $content .= $this->lbplusbuilder->get_element('schoolisting/error', array('message' => $error));
        } elseif($success) {
            $content .= $this->lbplusbuilder->get_element('schoolisting/success', array('message' => $success));
        }

        $content .= $this->table_management->getTable("all-schools");
        $content .= $this->get_import_button();

        $this->data['admin_content'] = load("tablemanagement/libre_elements/index", array('content' => $content), true);
        load('basic_elements/index', $this->data);
    }

    function get_import_button() {
        $content = '<label for="file"><div style="margin-left: 10px" class="btn btn-primary">Import Excel/CSV</div></label>';
        $content .= '<form action="'.base_url("admin/schools/import_excel").'" enctype="multipart/form-data" method="post" style="display: none">
        <div class="form-group">
            <input type="file" name="file" id="file" size="150" accept=".xls,.xlsx,.csv" onchange="this.form.submit()">
            <p class="help-block">Only Excel/CSV File Import.</p>
        </div></form>';
        return $content;
    }

    function import_excel() {
        if($_FILES) {
            $file_array = explode('.', $_FILES["file"]['name']);
            $file_extension = $file_array[1];
            $allowed_types = array('xlsx', 'xls', 'csv');
            if(!in_array($file_extension, $allowed_types)) {
                $error = "The File is not allowed to upload";
                $this->session->set_flashdata('error', $error);
                redirect(base_url("admin/schools"));
            }
            $file_name = md5(uniqid(mt_rand(), true)).'.'.$file_extension;
            $config['upload_path'] = "assets/uploaded_files/";
            $config['allowed_types'] = '*';
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(base_url("admin/schools"));
            }
            $this->load->library('plusauthentication/lb_authentication');
            $this->load->library('excel');
            $inputFileName = "assets/uploaded_files/$file_name";
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }

            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $data = array();
            $success_records = 0;
            //  Loop through each row of the worksheet in turn
            for ($row = 1; $row <= $highestRow; $row++){
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if($row != 1) {
                    $count = 0;
                    foreach($data as $key => $value) {
                        $data[$key] = $rowData[0][$count];
                        $count++;
                    }

                    if(!isset($data['school_name']) || !isset($data['password']) || !isset($data['email']) || !isset($data['username'])) {
                        $this->session->set_flashdata('error', LocalizedString("Missing some of the required columns"));
                        redirect(base_url("admin/schools"));
                    }

                    $schools = $this->school_model->get_all_schools();
                    $school_name_exist = FALSE;
                    if(isset($data['school_name'])) {
                        foreach($schools as $school) {
                            if($data['school_name'] == $school['school_name']) {
                                $school_name_exist = TRUE;
                                break;
                            }
                        }
                    }

                    if(!$school_name_exist) {
                        $code = $this->lb_authentication->register($data['username'], $data['email'], $data['password'], array('user_type' => "school"));
                        if($code['code'] == 200) {
                            add_to_agile($data['email']);
                            $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));
                            $this->parent_model->update_user($user['id'], array('active' => 1));
                            $data['menus'] = explode(',',$data['menus']);
                            $data['languages'] = explode(',',$data['languages']);

                            $school_data = array();
                            $school_data['user_id'] = $user['id'];
                            $school_data['school_name'] = $data['school_name'];
                            $school_data['establishment_year'] = $data['establishment_year'];

                            //process dropdown elements
                            $dropdown_elements = $this->dropdown_elements;
                            foreach($dropdown_elements as $dropdown_key => $dropdown_value) {
                                $list = $dropdown_value['values'];
                                foreach($list as $db_key => $db_value) {
                                    if($data[$dropdown_key] == $db_value) {
                                        $school_data[$dropdown_key] = $db_key;
                                        break;
                                    }
                                }
                            }

                            //process boolean elements
                            $boolean_elements = array('private_playground', 'extended_hours', 'is_switchable');
                            foreach($boolean_elements as $value) {
                                if(!isset($data[$value])) {
                                    $school_data[$value] = 1;
                                } else {
                                    if($data[$value] == "No") {
                                        $school_data[$value] = 0;
                                    } else {
                                        $school_data[$value] = 1;
                                    }
                                }
                            }

                            //process multiple choice elements
                            $multiplechoice_elements = $this->multiplechoice_elements;
                            $choices = array();
                            foreach($multiplechoice_elements as $choice_key => $choice_value) {
                                $list = $choice_value['values'];
                                $list_count = count($list);
                                $count = 1;
                                foreach($list as $db_key => $db_value) {
                                    foreach($data[$choice_key] as $chosen_value) {
                                        if($chosen_value == $db_value) {
                                            $choices[$choice_key][] = $db_key;
                                        }
                                    }
                                    if($count == $list_count && !isset($choices[$choice_key])) {
                                        $choices[$choice_key][] = 1;
                                    }
                                    $count++;
                                }
                            }
                            $menus = $choices['menus'];
                            $languages = $choices['languages'];

                            //process city and district
                            if(isset($data['district'])) {
                                $location = $this->parent_model->find_location_by_district($data['district']);
                                if(!$location) {
                                    $location = $this->parent_model->find_location_by_city($data['city']);
                                    if ($location) {
                                        $district = $this->parent_model->find_default_district_by_city($location['city_id']);
                                        $location['district_id'] = $district['district_id'];
                                    } else {
                                        $location['city_id'] = 1;
                                        $location['district_id'] = 1;
                                    }
                                }
                                $school_data['city'] = $location['city_id'];
                                $school_data['district'] = $location['district_id'];
                            }

                            $school_id = $this->school_model->insert_school($school_data);

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
                            $success_records++;
                        }
                    }

                } else {
                    foreach($rowData[0] as $header) {
                        $data[$header] = "";
                    }
                }
            }
            unlink("assets/uploaded_files/$file_name");
            if($success_records == 0) {
                $this->session->set_flashdata('error', LocalizedString("No school was added to the database due to some errors"));
            } else {
                $this->session->set_flashdata('success', $success_records . " " . LocalizedString("school(s) was added to the database successfully"));
            }
        }
        redirect(base_url("admin/schools"));
    }

    function add_school() {
        $this->data['admin_title'] = LocalizedString("Insert School");
        $this->data['head']['title'] = LocalizedString("Insert School");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Insert School"));

        $data['flashdata'] = $this->session->flashdata("data");

        $data['location'] = $this->render_location($data['flashdata']['city'], $data['flashdata']['district']);
        $data['all'] = $this->render_all_input($data['flashdata']);

        $checked = "";
        if($data['flashdata']['appearance'] == 1) {
            $checked = "checked";
        }
        $data['appearance'] = $this->lbplusbuilder->get_element('schoolisting/profile-status', array('checked' => $checked));
        $data['form_action'] = base_url("admin/schools/handle_register");
        $data['form_title'] = LocalizedString("Add School");

        $this->data['admin_content'] = load("admin/schools/school-form", $data, true);
        load('basic_elements/index', $this->data);
    }

    function edit_school() {
        $school_id = $this->uri->segment(4);
        $school = $this->school_model->get_by_attribute(array('t2.id' => $school_id));
        if($school['id']) {
            $this->data['admin_title'] = LocalizedString("Edit School");
            $this->data['head']['title'] = LocalizedString("Edit School");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Schools", "Edit School"));

            $data['flashdata'] = $school;
            $data['all'] = $this->render_all_input($data['flashdata']);
            $data['location'] = $this->render_location($data['flashdata']['city'], $data['flashdata']['district']);
            $checked = "";
            if($school['appearance'] == 1) {
                $checked = "checked";
            }
            $data['appearance'] = $this->lbplusbuilder->get_element('schoolisting/profile-status', array('checked' => $checked));
            $data['form_action'] = base_url("admin/schools/handle_edit");
            $data['form_title'] = "Edit School";
            $data['edit'] = TRUE;

            $this->data['admin_content'] = load("admin/schools/school-form", $data, true);
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function handle_edit() {
        if($data = $this->input->post()) {
            $school = $this->school_model->get_by_attribute(array('t2.id' => $data['school_id']));
            if($data['web']) {
                if(!filter_var($data['web'], FILTER_VALIDATE_URL)) {
                    $this->session->set_flashdata('error', "Please enter a valid url for your website");
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
            }
            if($data['facebook']) {
                if(!filter_var($data['facebook'], FILTER_VALIDATE_URL)) {
                    $this->session->set_flashdata('error', "Please enter a valid url for your facebook page");
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
            }
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('error', "Please enter a valid email");
                redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
            }

            if(!is_numeric($data['telephone']) || strlen($data['telephone']) < 10 || strlen($data['telephone']) > 11) {
                $message = "Phone number must not contain any alphabetic character and its length must range from 10 to 11 numbers";
                $this->session->set_flashdata('error', $message);
                redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
            }

            if($data['current_pass']) {
                if(strlen($data['password']) < 6 || strlen($data['confirm_password']) < 6) {
                    $this->session->set_flashdata("error_pass_empty", LocalizedString("Your password length must be 6 characters at least"));
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
                $data['current_pass'] = md5($data['current_pass']);
                if($data['current_pass'] == $school['password']) {
                    if(!$data['password'] || !$data['confirm_password']) {
                        $this->session->set_flashdata("error_pass_empty", LocalizedString("Please Enter your New Password and its confirmation"));
                        redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                    } else {
                        if($data['password'] == $data['confirm_password']) {
                            $this->parent_model->update_user($school['id'], array('password' => md5($data['password'])));
                        } else {
                            $this->session->set_flashdata("error_confirm", LocalizedString("Passwords do not match"));
                            redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                        }
                    }
                } else {
                    $this->session->set_flashdata("error_pass", LocalizedString("Your password was incorrect"));
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
            }

            if(!isset($data['appearance'])) {
                $data['appearance'] = 0;
            }

            $users = $this->parent_model->get_all_users();
            //echo"<pre>";var_dump($data);die;
            foreach($users as $u) {
                if($data['username'] == $u['username'] && $data['user_id'] != $u['id']) {
                    $this->session->set_flashdata('error', LocalizedString("Your username you has just typed in is unavailable"));
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }

                if($data['email'] == $u['email'] && $school['id'] != $u['id']) {
                    $this->session->set_flashdata('error', LocalizedString("Your email you has just typed in is unavailable"));
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
            }

            $schools = $this->school_model->get_all_schools();
            foreach($schools as $s) {
                if($data['school_name'] == $s['school_name'] && $school['school_id'] != $s['id']) {
                    $this->session->set_flashdata('data', $data);
                    $message = "Your school name existed";
                    $this->session->set_flashdata('error', $message);
                    redirect(base_url("admin/schools/edit_school/{$data['school_id']}"));
                }
            }

            //update email
            $this->parent_model->update_user($school['id'], array('username' => $data['username'], 'email' => $data['email']));
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

            //update main school
            unset($data['school_id']);
            unset($data['username']);
            unset($data['current_pass']);
            unset($data['password']);
            unset($data['confirm_password']);
            //$location = $this->parent_model->get_parent_location($data['district']);
            $latlnt = convert_to_latlng($data['address']);
            $data['lat'] = @$latlnt['lat'];
            $data['lng'] = @$latlnt['lng'];
            $data['latest_update'] = date("Y-m-d H:i:s");
            $this->school_model->update_school($school['id'], $data);
        }
        redirect(base_url("admin/schools"));
    }

    function handle_register() {
        if($data = $this->input->post()) {
            if(!isset($data['appearance'])) {
                $data['appearance'] = 0;
            }

            if(!isset($data['menus']) || !isset($data['languages'])) {
                if(!isset($data['languages'])) {
                    $data['languages'] = array();
                } elseif(!isset($data['menus'])) {
                    $data['menus'] = array();
                }
                $this->session->set_flashdata('data', $data);
                $message = "Please choose your school's menu(s) and language(s)";
                $this->session->set_flashdata('error', $message);
                redirect(base_url("admin/schools/add_school"));
            }

            if(!is_numeric($data['telephone']) || strlen($data['telephone']) < 10 || strlen($data['telephone']) > 11) {
                $message = "Phone number must not contain any alphabetic character and its length must range from 10 to 11 numbers";
                $this->session->set_flashdata('error', $message);
                $this->session->set_flashdata('data', $data);
                redirect(base_url("admin/schools/add_school"));
            }

            $schools = $this->school_model->get_all_schools();
            foreach($schools as $school) {
                if($data['school_name'] == $school['school_name']) {
                    $this->session->set_flashdata('data', $data);
                    $message = LocalizedString("Your school name existed");
                    $this->session->set_flashdata('error', $message);
                    redirect(base_url("admin/schools/add_school"));
                }
            }

            if(strpos($data['username'], " ") !== false)
            {
                $this->session->set_flashdata('data', $data);
                $message = "Your username must not contain any whitespace";
                $this->session->set_flashdata('error', $message);
            } elseif($data['password'] != $data['confirm_password']) {
                $this->session->set_flashdata('data', $data);
                $message = "There is something wrong in your password confirmation";
                $this->session->set_flashdata('error', $message);
            } else {
                $this->load->library('plusauthentication/lb_authentication');
                $register_code = $this->lb_authentication->register($data['username'], $data['email'], $data['password'], array('user_type' => "school"));
                if($register_code['code'] == 200) {
                    $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));
                    $this->parent_model->update_user($user['id'], array('active' => 1));

                    $params = array("username" => $data['username'], "link" => base_url("school/activate_account/{$user['uuid']}"));
                    $config = array(
                        "send_from" => "general",
                        "send_to" => $data['email'],
                        "subject" => LocalizedString("Activate your schoolisting account"),
                        "template" => "activate_account",
                        "sender" => 'Admin'
                    );
                    $this->load->library('plusmailer/mail_library');
                    $this->mail_library->send_mail($config, $params);

                    add_to_agile($data['email']);

                    unset($data['username']);unset($data['password']);unset($data['confirm_password']);
                    unset($data['email']);unset($data['location']);
                    $menus = $data['menus'];
                    unset($data['menus']);
                    $languages = $data['languages'];
                    unset($data['languages']);

                    //$location = $this->parent_model->get_parent_location($data['district']);
                    $latlnt = convert_to_latlng($data['address']);
                    $data['lat'] = @floatval($latlnt['lat']);
                    $data['lng'] = @floatval($latlnt['lng']);

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

                    redirect(base_url("admin/schools"));
                } else {
                    $this->session->set_flashdata('data', $data);
                    $this->session->set_flashdata('error', $register_code['description']);
                }
            }
            redirect(base_url("admin/schools/add_school"));
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function map() {
        $school_id = $this->uri->segment(4);
        if($school = $this->school_model->get_by_attribute(array('t2.id' => $school_id))) {
            $this->data['admin_title'] = LocalizedString("Map") . " - {$school['school_name']}";
            $this->data['head']['title'] = LocalizedString("Map");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Schools", "Map"));

            $data['name'] = $school['school_name'];
            $data['lat']= $school['lat'];
            $data['lng']= $school['lng'];
            $data['js'][] = "gmaps.min.js";
            $this->data['admin_content'] = load("admin/map", $data, true);
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function reviews() {
        $school_id = $this->uri->segment(4);
        if($school = $this->school_model->get_by_attribute(array('t2.id' => $school_id))) {
            $this->data['admin_title'] = LocalizedString("Reviews") . " - {$school['school_name']}";
            $this->data['head']['title'] = LocalizedString("Reviews");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Schools", "Reviews"));

            $tbreview = new tbReview();
            $reviews = $tbreview->get_school_reviews($school['school_id'], 0);
            $review_html = '<div class="posts-wrapper" id="review-main">';
            if($reviews) {
                foreach($reviews as $review) {
                    $review_html .= $this->render_school_reviews($review);
                }
            } else {
                $review_html .= '<div class="post-item">No Review Available</div>';
            }
            $review_html .= "</div>";

            $this->data['head']['css'][] = "pages-profile.min.css";
            $this->data['admin_content'] = $review_html;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function self_evaluations() {
        $school_id = $this->uri->segment(4);
        if($school = $this->school_model->get_by_attribute(array('t2.id' => $school_id))) {
            $this->data['admin_title'] = LocalizedString("School Self-Evaluations") . " - {$school['school_name']}";
            $this->data['head']['title'] = LocalizedString("School Self-Evaluations");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Schools", "Self-Evaluations"));

            $tbrate = new tbRate();
            $evaluations = $tbrate->get_self_evaluation($school['id']);
            $evaluation_html = "<div class='posts-wrapper'>";
            if($evaluations) {
                foreach($evaluations as $evaluation) {
                    $evaluation_html .= '<div class="post-item">';
                    $evaluation_html .= '<p style="font-size: 15px">'.date('d M, Y', strtotime($evaluation['createtime'])).'</p>';
                    $evaluation_html .= '<hr><div class="post-content"><div class="media"><div class="media-body">';
                    $evaluation_html .= '<p>The school rated itself with average mark of<b style="font-size: 24px"> '.number_format($evaluation['average_rate'], 1).'</b></p>';
                    $evaluation_html .= '</div></div></div></div>';
                }
            } else {
                $evaluation_html .= '<div class="post-item">No Evaluation Available</div>';
            }
            $evaluation_html .= "</div>";

            $this->data['head']['css'][] = "pages-profile.min.css";
            $this->data['admin_content'] = $evaluation_html;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function parent_evaluations() {
        $school_id = $this->uri->segment(4);
        if($school = $this->school_model->get_by_attribute(array('t2.id' => $school_id))) {
            $this->data['admin_title'] = LocalizedString("Parent Evaluations") . " - {$school['school_name']}";
            $this->data['head']['title'] = LocalizedString("Parent Evaluations");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Schools", "Parent Evaluations"));

            $tbrate = new tbRate();
            $evaluations = $tbrate->get_parent_evaluation($school['id']);
            $evaluation_html = "<div class='posts-wrapper'>";
            if($evaluations) {
                foreach($evaluations as $evaluation) {
                    $evaluation_html .= '<div class="post-item">';
                    $evaluation_html .= '<p style="font-size: 15px">'.date('d M, Y', strtotime($evaluation['createtime'])).'</p>';
                    $evaluation_html .= '<hr><div class="post-content"><div class="media"><div class="media-body">';
                    $evaluation_html .= '<p><b>'.$evaluation['username'].'</b> rated <b style="font-size: 24px"> '.number_format($evaluation['average_rate'], 1).'</b> average mark</p>';
                    $evaluation_html .= '</div></div></div></div>';
                }
            } else {
                $evaluation_html .= '<div class="post-item">No Evaluation Available</div>';
            }
            $evaluation_html .= "</div>";

            $this->data['head']['css'][] = "pages-profile.min.css";
            $this->data['admin_content'] = $evaluation_html;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/schools"));
        }
    }

    function mass_delete() {
        if($data = $this->input->post()) {
            $this->school_model->delete_school($data['id']);
        }
        redirect(base_url("admin/schools"));
    }

    function mass_approve() {
        if($data = $this->input->post()) {
            $ids = $this->parent_model->get_user_id_by_school_id($data['id']);
            $user_ids = array();
            foreach($ids as $id) {
                $user_ids[] = $id['id'];
            }
            $this->parent_model->approve_user($user_ids);
        }
        redirect(base_url("admin/schools"));
    }

}