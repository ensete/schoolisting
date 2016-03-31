<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Review extends MY_Controller
{

    public function find_your_school()
    {
        $data['head']['css'][] = "pages-landing.min.css";
        $data['user'] = get_user_by_token();
        $data['search_text'] = LocalizedString("Review a School");
        $data['search_url'] = "review/ajax_search_school";
        $data['page'] = "review/search";
        $data['has_footer'] = TRUE;
        $data['head']['title'] = "Review a School";
        $this->load->view('basic_elements/index', $data);
    }

    public function ajax_search_school() {
        $key = $this->input->post('key');
        if($key) {
            $schools = $this->school_model->search_school_by_name($key, 10);
            $school_return = "";
            foreach($schools as $school) {
                $school_return .= "<a href='".base_url("review/write_a_review/{$school['uuid']}") . "/" . refine_name($school['school_name']) ."'>";
                $school_return .= "<div>{$school['school_name']}</div></a>";
            }
            echo $school_return;
        }
    }

    public function write_a_review() {
        $uuid = $this->uri->segment(3);
        $school = $this->school_model->get_by_attribute(array('uuid' => $uuid));
        if($school) {
            $parent = check_parents();

            $data['parent'] = $parent;
            $school['type'] = $this->get_name_from_dropdown('type', $school['type']);
            $school['grade'] = $this->get_name_from_dropdown('grade', $school['grade']);
            $data['school'] = $school;

            $data['head']['css'][] = "pages-landing.min.css";
            $data['page'] = "review/review";
            $data['has_header'] = TRUE;
            $data['has_footer'] = TRUE;
            $data['head']['title'] = "Review";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect(base_url('review/find_your_school'));
        }
    }

    public function review_submit() {
        if($data = $this->input->post()) {
            $tbreview = new tbReview();
            $tbreview->user_id = $data['user_id'];
            $tbreview->school_id = $data['school_id'];
            $tbreview->review = $data['review'];
            $tbreview->save();

            $review = $tbreview->get_review(array('t1.user_id' => $data['user_id'], 'school_id' => $data['school_id']));

            $this->session->set_flashdata('school_id', $review['school_id']);
            $this->session->set_flashdata('review_id', $review['id']);

            $u = new tbUser($data['user_id']);
            add_to_agile($u->email, "Review a school");

            redirect(base_url("review/share_review"));
        } else {
            redirect(base_url("review/find_your_school"));
        }
    }

    public function share_review() {
        if($school_id = $this->session->flashdata('school_id')) {
            $review_html = "";
            $school = $this->school_model->get_by_attribute(array('t2.id' => $school_id));
            $user = get_user_by_token($this->session->userdata('user_token'));
            if($review_id = $this->session->flashdata('review_id')) {
                $tbreview = new tbReview();
                $review = $tbreview->get_review(array('t1.id' => $review_id));
                $review_html .= $this->render_school_reviews($review);
                $data['user'] = $user;
                $data['content'] = $review['review'];
            } elseif($rate_id = $this->session->flashdata('rate_id')) {
                $data['user_type'] = "school";
                $data['school_profile_url'] = base_url("school/profile/{$school['uuid']}/dashboard") . "/" . refine_name($school['school_name']);
                if($this->session->flashdata('user_type') == 1) {
                    $data['user_type'] = "parents";
                    $data['user'] = $user;
                    $data['school_profile_url'] = NULL;
                }
                $tbrate_cat = new tbRate_category();
                $average_rate = $tbrate_cat->get_average_rate($rate_id);
                $average_rate['mark'] = number_format($average_rate['mark'], 1);
                if($average_rate['username']) {
                    $data['content'] = "I ".LocalizedString("rated ") . $school['school_name'] . LocalizedString(" with average mark of ") . $average_rate['mark'];
                    $average_rate['review'] = "You have just rated <b style='font-size: 25px'>{$average_rate['mark']}</b> for this school.";
                    $review_html .= $this->render_school_reviews($average_rate);
                } else {
                    $data['customers_emails'] = $school['customers_emails'];
                    $data['content'] = "{$school['school_name']} ".LocalizedString("has just rated") ." {$average_rate['mark']} " . LocalizedString("for themselves.");
                    $review_html .= "You have just rated <b style='font-size: 25px'>{$average_rate['mark']}</b> for your school.";
                }
            }

            $data['school_url'] = base_url("parents/school_details/{$school['uuid']}") . "/" . refine_name($school['school_name']);
            $data['school'] = $school;
            $data['review_html'] = $review_html;
            $data['head']['css'][] = "pages-landing.min.css";
            $data['page'] = "share_page";
            $data['has_header'] = TRUE;
            $data['has_footer'] = TRUE;
            $data['head']['title'] = "Share your Review";
            $this->load->view('basic_elements/index', $data);
        } else {
            redirect(base_url());
        }
    }

    public function submit_survey() {
        $data = $this->input->post();
        $tbsurvey = new tbSurvey();
        $tbsurvey->rate = $data['rate'];
        $tbsurvey->school_id = $data['school_id'];
        if(!$data['user_id']) {
            $tbsurvey->user_id = $data['user_id'];
            $school_token = $this->session->userdata('school_token');
            $school = get_school_by_token($school_token);
            $data['url'] = base_url("school/profile/{$school['uuid']}/dashboard") . "/" .refine_name($school['school_name']);
        }
        $tbsurvey->content = $data['content'];
        $tbsurvey->save();
        redirect($data['url']);
    }
}