<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller
{

    var $data = array();

    function __construct()
    {
        parent::__construct();
        $this->data['head']['css'][] = "admin.css";
        $this->data['admin_header'] = load("admin/header", null, TRUE);
        $this->data['admin_sidebar'] = admin_sidebar(6);
        $this->data['page'] = "admin/main";
    }

    function index()
    {
        $this->data['admin_title'] = LocalizedString("Settings");
        $this->data['head']['title'] = LocalizedString("Settings");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Settings"));

        $content = "";
        $this->load->library('tablemanagement/table_management');
        $this->load->library('tablemanagement/table_management');
        $content .= $this->table_management->getTable("all-settings");

        $this->data['admin_content'] = load("tablemanagement/libre_elements/index", array('content' => $content), true);
        load('basic_elements/index', $this->data);
    }
}