<?php
$this->load->view('basic_elements/head', (isset($head)) ? $head : "");
if (isset($has_header)) {
    $this->load->view("basic_elements/header_page", array('user_type' => @$user_type));
}
$this->load->view("$page");
if (isset($has_footer)) {
    $this->load->view("basic_elements/footer_page");
}
$this->load->view('basic_elements/footer', (isset($js)) ? $js : "");