<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {
    var $data = array();
    function __construct ()
    {
        parent::__construct();
        $this->data['admin_title'] = LocalizedString("Dashboard");
        $this->data['head']['title'] = LocalizedString("Dashboard");
        $this->data['head']['css'][] = "admin.css";
    }

    function index ()
    {
        $tbadmin = new Admin();

        //get latest newcomers data for column-chart
        $column_objects = array('Schools', 'Parents');
        $column_data = array();
        for ($i = 11; $i >= 0; $i--) {
            $time = strtotime( date( 'Y-m-01' )." -$i months");
            $date = date("m-M-Y", $time);
            $date = explode('-', $date);
            $column_data['categories'][] = $date[0] . " - " . $date[2];
            foreach($column_objects as $k => $v) {
                $newcomers = $tbadmin->get_latest_newccomers($k, $date['0'], $date['2']);
                $column_data['data'][$k]['name'] = LocalizedString($v);
                $column_data['data'][$k]['data'][] = (int)$newcomers;
            }
        }
        $column_data['categories'] = json_encode($column_data['categories']);
        $column_data['data'] = json_encode($column_data['data']);
        $column_data['id'] = "latest_newcomers";
        $column_data['maintitle'] = "Monthly Total Newcomers";
        $column_data['subtitle'] = "Latest 12 Months";
        $column_data['title'] = "Users";
        $column_chart = $this->lbplusbuilder->get_element("schoolisting/column-highchart", $column_data);

        //get school total view
        $total_view = $tbadmin->get_school_total_view();
        $total_view['caption'] = LocalizedString("Total View");
        $counter_views = $this->lbplusbuilder->get_element('schoolisting/counter-up', $total_view);

        //get school total recommendation
        $total_recommendation = $tbadmin->get_school_total_recommendation();
        $total_recommendation['caption'] = LocalizedString("Total Recommendation");
        $counter_recommendations = $this->lbplusbuilder->get_element('schoolisting/counter-up', $total_recommendation);

        //get inactive schools within 3 months
        $inactive_schools = $tbadmin->get_inactive_schools();

        //get number of news parents and schools joining schoolisting this year
        $new_parents = $tbadmin->get_newcomers(1);
        $new_schools = $tbadmin->get_newcomers(0);

        //get schools improved in term of their average mark in every single one of 7 categories
        $improved_objects = array('Schools');
        $improved_school_data = array();
        for ($i = 11; $i >= 0; $i--) {
            $time = strtotime(date('Y-m-01') . " -$i months");
            $date = date("m-M-Y", $time);
            $date = explode('-', $date);
            $improved_school_data['categories'][] = $date[0] . " - " . $date[2];
            foreach ($improved_objects as $k => $v) {
                $improved_school = $tbadmin->get_improved_schools($date[0], $date[2]);
                $total_improved_school = count($improved_school);
                $improved_school_data['data'][$k]['name'] = LocalizedString($v);
                $improved_school_data['data'][$k]['data'][] = $total_improved_school;
            }
        }
        $improved_school_data['categories'] = json_encode($improved_school_data['categories']);
        $improved_school_data['data'] = json_encode($improved_school_data['data']);
        $improved_school_data['id'] = "improved_school";
        $improved_school_data['maintitle'] = LocalizedString("Total Schools improving their Average Mark in comparison with the Last Month");
        $improved_school_data['subtitle'] = LocalizedString("Latest 12 Months");
        $improved_school_data['title'] = LocalizedString("Schools");
        $improved_school_chart = $this->lbplusbuilder->get_element("schoolisting/column-highchart", $improved_school_data);

        //render location
        $location = $this->render_location(0, 0, "login-location", "");

        //get plan statistics
        $active_plans = $tbadmin->get_plans(0);
        $completed_plans = $tbadmin->get_plans(2);
        $closed_plans = $tbadmin->get_plans(1);
        $school_with_completed_plan = $tbadmin->get_total_school_with_completed_plan();

        $dashboard['active_plans'] = $active_plans;
        $dashboard['completed_plans'] = $completed_plans;
        $dashboard['closed_plans'] = $closed_plans;
        $dashboard['school_with_completed_plan'] = $school_with_completed_plan;
        $dashboard['location'] = $location;
        $dashboard['new_schools'] = $new_schools;
        $dashboard['new_parents'] = $new_parents;
        $dashboard['inactive_schools'] = $inactive_schools['total'];
        $dashboard['total_view'] = $counter_views;
        $dashboard['total_recommendation'] = $counter_recommendations;
        $dashboard['column_chart'] = $column_chart;
        $dashboard['improved_school_column'] = $improved_school_chart;
        $this->data['admin_content'] = load('admin/dashboard', $dashboard, true);
        $this->data['admin_header'] = load("admin/header", null, TRUE);
        $this->data['admin_sidebar'] = admin_sidebar(1);
        $this->data['admin_breadcrumb'] = breadcrumb(array("questionaire"));
        $this->data['head']['css'][] = "wizard.min.css";
        $this->data['js'][] = "custom/highcharts.js";
        $this->data['js'][] = "custom/exporting.js";
        $this->data['js'][] = "jquery.counterup.min.js";
        $this->data['js'][] = "select2.min.js";
        $this->data['js'][] = "jquery.bootstrap.wizard.min.js";
        $this->data['js'][] = "demo/form-wizard-demo.js";
        $this->data['page'] = "admin/main";
        load('basic_elements/index', $this->data);
    }

    function search_targets() {
        $data = $this->input->post();
        $tbadmin = new Admin();
        $results = $tbadmin->get_target_numbers($data);
        $count = count($results);

        $panel['title'] = "Matching Results";
        $panel['content'] = "<b>Total {$data['user_type']}: </b>" . $count . "<hr>";
        if($results) {
            foreach($results as $result) {
                if($result['user_type'] == "parent") {
                    $panel['content'] .= '<p><a href="'.base_url("admin/parents/edit_parent/{$result['id']}").'">'. $result['name'] . "</a> - " . date('d M,Y', strtotime($result['createtime'])) . "</p>";
                } else {
                    $panel['content'] .= '<p><a href="'.base_url("admin/schools/edit_school/{$result['id']}").'">'. $result['name'] . "</a> - " . date('d M,Y', strtotime($result['createtime'])) . "</p>";
                }
            }
        }
        echo $this->lbplusbuilder->get_element('schoolisting/panel', $panel);
    }
}