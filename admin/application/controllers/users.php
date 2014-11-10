<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function login() {

        if ($this->session->userdata("admin_id") > 0)
            redirect(site_url());

        if ($this->input->post()) {
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_rules('username', "Username", 'required|xss_clean');
            $this->form_validation->set_rules('password', "Password", 'required|xss_clean');

            if ($this->form_validation->run()) {
                $login = $this->users_model->login($this->input->post("username"), $this->input->post("password"));
                if (!$login) {
                    $content['login_err'] = true;
                }
                else {
                    redirect(site_url());
                }
            }
            else {
                $content['login_err'] = true;
            }

            $content['username'] = $this->input->post('username');
            $content['password'] = $this->input->post('password');
        }
        else {
            $content['username'] = "";
            $content['password'] = "";
            $content['login_err'] = "";
        }

        $headers = array("meta_title" => "Please sign in to continue");
        $this->load->view("headers.tpl", $headers);
        $this->load->view("users/login.tpl", $content);
    }

    public function logout() {
        $this->users_model->logout();

        redirect(site_url());
    }
}

/* End of file users.php */
/* Location: ./application/controllers/users.php */