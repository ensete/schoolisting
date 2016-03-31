<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rate extends MY_Controller
{

    public function find_a_school()
    {
        $data['head']['css'][] = "pages-landing.min.css";
        $data['user'] = get_user_by_token();
        $data['search_text'] = LocalizedString("Rate a School");
        $data['search_url'] = "rate/ajax_search_school";
        $data['page'] = "review/search";
        $data['has_footer'] = TRUE;
        $data['head']['title'] = LocalizedString("Rate a School");
        $this->load->view('basic_elements/index', $data);
    }

    public function ajax_search_school()
    {
        $key = $this->input->post('key');
        if ($key) {
            $schools = $this->school_model->search_school_by_name($key, 10);
            $school_return = "";
            foreach ($schools as $school) {
                $school_return .= "<a href='" . base_url("questionnaire/questionnaire_for_parent?name=") . urlencode($school['school_name']) . "'>";
                $school_return .= "<div>{$school['school_name']}</div></a>";
            }
            echo $school_return;
        }
    }

}