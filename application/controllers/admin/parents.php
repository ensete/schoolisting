<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parents extends MY_Controller {

    var $data = array();

    function __construct ()
    {
        parent::__construct();
        $this->data['head']['css'][] = "admin.css";
        $this->data['admin_header'] = load("admin/header", null, TRUE);
        $this->data['admin_sidebar'] = admin_sidebar(2);
        $this->data['page'] = "admin/main";
    }

    function index()
    {
        $this->data['admin_title'] = LocalizedString("Parents");
        $this->data['head']['title'] = LocalizedString("Parents");
        $this->data['admin_breadcrumb'] = breadcrumb(array("parents"));

        $error = $this->session->flashdata('error');
        $success = $this->session->flashdata('success');

        $this->load->library('tablemanagement/table_management');
        $content = "";
        if($error) {
            $content .= $this->lbplusbuilder->get_element('schoolisting/error', array('message' => $error));
        } elseif($success) {
            $content .= $this->lbplusbuilder->get_element('schoolisting/success', array('message' => $success));
        }

        $this->load->library('tablemanagement/table_management');
        $content .= $this->table_management->getTable("all-parents");
        $content .= $this->get_import_button();

        $this->data['admin_content'] = load("tablemanagement/libre_elements/index", array('content' => $content), true);

        load('basic_elements/index', $this->data);
    }

    function get_import_button() {
        $content = '<label for="file"><div style="margin-left: 10px" class="btn btn-primary">Import Excel/CSV</div></label>';
        $content .= '<form action="'.base_url("admin/parents/import_excel").'" enctype="multipart/form-data" method="post" style="display: none">
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
                redirect(base_url("admin/parents"));
            }
            $file_name = md5(uniqid(mt_rand(), true)).'.'.$file_extension;
            $config['upload_path'] = "assets/uploaded_files/";
            $config['allowed_types'] = '*';
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(base_url("admin/parents"));
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

                    if(!isset($data['password']) || !isset($data['email']) || !isset($data['username'])) {
                        $this->session->set_flashdata('error', "Missing some of the required columns");
                        redirect(base_url("admin/parents"));
                    }

                    $code = $this->lb_authentication->register($data['username'], $data['email'], $data['password'], array('user_type' => "school"));
                    if($code['code'] == 200) {
                        add_to_agile($data['email']);
                        $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));
                        $this->parent_model->update_user($user['id'], array('active' => 1));
                        $parent_data = array();
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
                            $parent_data['city'] = $location['city_id'];
                            $parent_data['district'] = $location['district_id'];
                        }
                        $parent_data['user_id'] = $user['id'];
                        $parent_data['full_name'] = $data['full_name'];
                        $parent_data['telephone'] = $data['telephone'];
                        $parent_data['children_numbers'] = $data['children_numbers'];

                        $this->parent_model->insert_parent($parent_data);
                        $success_records++;
                    }

                } else {
                    foreach($rowData[0] as $header) {
                        $data[$header] = "";
                    }
                }
            }
            unlink("assets/uploaded_files/$file_name");
            if($success_records == 0) {
                $this->session->set_flashdata('error', "No Parent was added to the database due to some errors");
            } else {
                $this->session->set_flashdata('success', $success_records . " parent(s) was added to the database successfully");
            }
        }
        redirect(base_url("admin/parents"));
    }

    function add_parent() {
        $this->data['admin_title'] = LocalizedString("Insert Parent");
        $this->data['head']['title'] = LocalizedString("Insert Parent");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Insert Parent"));

        $data['flashdata'] = $this->session->flashdata("data");
        $data['location'] = $this->render_location($data['flashdata']['city'], $data['flashdata']['district']);
        $data['appearance'] = $this->lbplusbuilder->get_element('schoolisting/profile-status', array('checked' => ""));

        $this->data['admin_content'] = load("admin/parents/register", $data, true);
        load('basic_elements/index', $this->data);
    }

    function edit_parent() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = LocalizedString("Edit Parent");
            $this->data['head']['title'] = LocalizedString("Edit Parent");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Edit Parent"));

            $data['flashdata'] = $parent;
            $data['location'] = $this->render_location($data['flashdata']['city'], $data['flashdata']['district'], "profile-location", "");
            $checked = "";
            if($parent['appearance'] == 1) {
                $checked = "checked";
            }
            $data['appearance'] = $this->lbplusbuilder->get_element('schoolisting/profile-status', array('checked' => $checked));

            $this->data['admin_content'] = load("admin/parents/edit", $data, true);
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/parents"));
        }
    }

    function handle_edit() {
        if($data = $this->input->post()) {
            $update_user = array();
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('error', "Please enter a valid email");
                redirect(base_url("admin/parents/edit_parent/{$data['parent_id']}"));
            }
            if(isset($data['password'])) {
                if($data['password']) {
                    if(strlen($data['password']) < 6 || strlen($data['confirm_password']) < 6) {
                        $this->session->set_flashdata("error_pass_empty", LocalizedString("Your password length must be 6 characters at least"));
                        redirect(base_url("admin/parents/edit_parent/{$data['parent_id']}"));
                    }
                    if($data['password'] != $data['confirm_password']) {
                        $message = LocalizedString("There is something wrong in your password confirmation");
                        $this->session->set_flashdata('error_confirm', $message);
                        redirect(base_url("admin/parents/edit_parent/{$data['parent_id']}"));
                    } else {
                        $update_user['password'] = md5($data['password']);
                    }
                }
            }
            $all_users = $this->parent_model->get_all_users();
            foreach($all_users as $user) {
                if($data['username'] == $user['username'] && $data['user_id'] != $user['id']) {
                    $this->session->set_flashdata('error', LocalizedString("Your username you has just typed in is unavailable"));
                    redirect(base_url("admin/parents/edit_parent/{$data['parent_id']}"));
                }

                if($data['email'] == $user['email'] && $data['user_id'] != $user['id']) {
                    $this->session->set_flashdata('error', LocalizedString("Your email you has just typed in is unavailable"));
                    redirect(base_url("admin/parents/edit_parent/{$data['parent_id']}"));
                }
            }
            $update_user['username'] = $data['username'];
            $update_user['email'] = $data['email'];
            $this->parent_model->update_user($data['user_id'], $update_user);
            if(!isset($data['appearance'])) {
                $data['appearance'] = 0;
            }
            $update_parent = array(
                'full_name' => $data['full_name'],
                'telephone' => $data['telephone'],
                'children_numbers'=> $data['children_numbers'],
                'city'=> $data['city'],
                'district'=> $data['district'],
                'appearance' => $data['appearance'],
            );
            $location = $this->parent_model->get_parent_location($data['district']);
            $latlnt = convert_to_latlng($location['district_name'] . ", " . $location['city_name']);
            $update_parent['lat'] = @$latlnt['lat'];
            $update_parent['lng'] = @$latlnt['lng'];

            $this->parent_model->update_parent($data['user_id'], $update_parent);
        }
        redirect(base_url("admin/parents"));
    }

    function handle_register() {
        if($data = $this->input->post()) {
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->session->set_flashdata('data', $data);
                $this->session->set_flashdata('error', LocalizedString("Please enter a valid email"));
            } elseif(strpos($data['username'], " ") !== false) {
                $this->session->set_flashdata('data', $data);
                $message = LocalizedString("Your username must not contain any whitespace");
                $this->session->set_flashdata('error', $message);
            } elseif($data['password'] != $data['confirm_password']) {
                $this->session->set_flashdata('data', $data);
                $message = LocalizedString("There is something wrong in your password confirmation");
                $this->session->set_flashdata('error_confirm', $message);
            } else {
                $this->load->library('plusauthentication/lb_authentication');
                $register_code = $this->lb_authentication->register($data['username'], $data['email'], $data['password']);
                if($register_code['code'] == 200) {
                    $user = $this->parent_model->get_by_attribute(array('email' => $data['email']));
                    $this->parent_model->update_user($user['id'], array('active' => 1));

                    $parent['user_id'] = $user['id'];
                    $parent['full_name'] = $data['full_name'];
                    $parent['city'] = $data['city'];
                    $parent['district'] = $data['district'];
                    $parent['telephone'] = $data['telephone'];

                    $location = $this->parent_model->get_parent_location($data['district']);
                    $latlnt = convert_to_latlng($location['district_name'] . ", " . $location['city_name']);
                    $data['lat'] = @floatval($latlnt['lat']);
                    $data['lng'] = @floatval($latlnt['lng']);

                    $this->parent_model->insert_parent($parent);
                    add_to_agile($data['email']);

                    redirect(base_url("admin/parents"));
                } else {
                    $this->session->set_flashdata('data', $data);
                    $this->session->set_flashdata('error', $register_code['description']);
                }
            }
            //echo"<pre>";var_dump($this->session->all_userdata());die;
            redirect(base_url("admin/parents/add_parent"));
        } else {
            redirect(base_url("admin/parents"));
        }
    }

    function bookmarks() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = $parent['full_name'] . "'s " . LocalizedString("Bookmarks");
            $this->data['head']['title'] = LocalizedString("Bookmarks");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Bookmarks"));
            $this->data['head']['css'][] = "pages-profile.min.css";

            $bookmarked_results = $this->parent_model->get_bookmarks($parent['id'], 0, 0);
            if($bookmarked_results) {
                foreach($bookmarked_results as $k => $v) {
                    $bookmarked_results[$k]['create_time'] = "Bookmarked in " . date("M d, Y", strtotime($v['create_time']));
                }
                $bookmarked_schools = '<div class="tab-pane" id="posts-bookmark"><div class="posts-wrapper" id="mediaObject-bookmark">';
                $bookmarked_schools .= $this->load_results($bookmarked_results, 0);
                $bookmarked_schools .= '</div></div>';
            } else {
                $bookmarked_schools = '<div class="posts-wrapper"><div class="post-item">No Bookmark Available</div></div>';
            }

            $this->data['admin_content'] = $bookmarked_schools;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url('admin/parents'));
        }
    }

    function recommendations() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = $parent['full_name'] . "'s " . LocalizedString("Recommendations");
            $this->data['head']['title'] = LocalizedString("Recommendations");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Recommendations"));
            $this->data['head']['css'][] = "pages-profile.min.css";

            $recommended_results = $this->parent_model->get_recommendations($parent['id'], 0, 0);
            if($recommended_results) {
                foreach($recommended_results as $k => $v) {
                    $recommended_results[$k]['create_time'] = LocalizedString("Referred in") . " " . date("M d, Y", strtotime($v['create_time']));
                }
                $recommended_schools = '<div class="tab-pane" id="posts-recommendation"><div class="posts-wrapper" id="mediaObject-recommendation">';
                $recommended_schools .= $this->load_results($recommended_results, 0);
                $recommended_schools .= '</div></div>';
            } else {
                $recommended_schools = '<div class="posts-wrapper"><div class="post-item">No Recommendation Available</div></div>';
            }

            $this->data['admin_content'] = $recommended_schools;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url('admin/parents'));
        }
    }

    function reviews() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = $parent['full_name'] . "'s " . LocalizedString("Reviews");
            $this->data['head']['title'] = LocalizedString("Reviews");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Reviews"));
            $this->data['head']['css'][] = "pages-profile.min.css";

            $tbreview = new tbReview();
            $reviews = $tbreview->get_parent_reviews($parent['id']);

            $review_html = '<div class="tab-pane" id="posts-reviews"><div class="posts-wrapper" id="mediaObject-review">';
            if($reviews) {
                foreach($reviews as $review) {
                    $review_data = array(
                        'link' => base_url("parents/school_details/{$review['uuid']}") . "/" . refine_name($review['school_name']),
                        'name' => $review['school_name'],
                        'date' => date("M d, Y", strtotime($review['create_time'])),
                        'content' => $review['review'],
                    );
                    $review_html .= $this->lbplusbuilder->get_element('schoolisting/parent-review', $review_data);
                }
            } else {
                $review_html .= '<div class="post-item">No Review Available</div>';
            }
            $review_html .= '</div></div>';

            $this->data['admin_content'] = $review_html;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url('admin/parents'));
        }
    }

    function rates() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = $parent['full_name'] . "'s " . LocalizedString("Rates");
            $this->data['head']['title'] = LocalizedString("Rates");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Rates"));
            $this->data['head']['css'][] = "pages-profile.min.css";

            $tbrate = new tbRate();
            $evaluations = $tbrate->get_all_parent_evaluation($parent['id']);
            $evaluation_html = "<div class='posts-wrapper'>";
            if($evaluations) {
                foreach($evaluations as $evaluation) {
                    $evaluation_html .= '<div class="post-item">';
                    $evaluation_html .= '<p style="font-size: 15px">'.date('d M, Y', strtotime($evaluation['createtime'])).'</p>';
                    $evaluation_html .= '<hr><div class="post-content"><div class="media"><div class="media-body">';
                    $evaluation_html .= '<p>The parent rated <a target="_blank" href="'.base_url("parents/school_details/{$evaluation['uuid']}") . "/" .refine_name($evaluation['school_name']).'">'.$evaluation['school_name'].'</a> with average mark of <b style="font-size: 24px"> '.number_format($evaluation['average_rate'], 1).'</b></p>';
                    $evaluation_html .= '</div></div></div></div>';
                }
            } else {
                $evaluation_html .= '<div class="post-item">No Rate Available</div>';
            }
            $evaluation_html .= "</div>";

            $this->data['admin_content'] = $evaluation_html;
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url('admin/parents'));
        }
    }

    function map() {
        $parent_id = $this->uri->segment(4);
        if($parent = $this->parent_model->get_by_attribute(array('t2.id' => $parent_id))) {
            $this->data['admin_title'] = LocalizedString("Map");
            $this->data['head']['title'] = LocalizedString("Map");
            $this->data['admin_breadcrumb'] = breadcrumb(array("Parents", "Map"));

            $data['name'] = $parent['full_name'];
            $data['lat']= $parent['lat'];
            $data['lng']= $parent['lng'];
            $data['js'][] = "gmaps.min.js";
            $this->data['admin_content'] = load("admin/map", $data, true);
            load('basic_elements/index', $this->data);
        } else {
            redirect(base_url("admin/parents"));
        }
    }

    function mass_delete() {
        if($data = $this->input->post()) {
            $this->parent_model->delete_parent($data['id']);
        }
        redirect(base_url("admin/parents"));
    }
    
    function mass_approve() {
        if($data = $this->input->post()) {
            $ids = $this->parent_model->get_user_id_by_parent_id($data['id']);
            $user_ids = array();
            foreach($ids as $id) {
                $user_ids[] = $id['id'];
            }
            $this->parent_model->approve_user($user_ids);
        }
        redirect(base_url("admin/parents"));
    }

}