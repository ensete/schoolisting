<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oauth extends CI_Controller {

    public function index() {
        if($data = $this->input->post()) {
            $user = new Admin;
            $admin = $user->get_admin(array('username' => $data['username'], 'password' => md5($data['password'])));
            if($admin) {
                $token = md5(uniqid(mt_rand(), true));
                $user->update_admin($admin['id'], array('token' => $token));
                // $this->session->sess_destroy();
                $this->session->set_userdata('admin_token', $token);
                redirect(base_url("admin/index"));
            } else {
                $this->session->set_flashdata('error', "Login credentials is invalid");
                $this->session->set_flashdata('username', $data['username']);
                redirect(base_url("admin/oauth"));
            }
        } else {
            $data['page'] = "admin/login";
            $data['head']['title'] = "Admin Login";
            $this->load->view('basic_elements/index', $data);
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

}