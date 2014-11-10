<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct() {

        parent::__construct();

        if ((int) $this->session->userdata("admin_id") <= 0)
            redirect("users/login");

        $this->load->model("collections_model", "collections");
    }

	public function index() {

        $page['collections'] = $this->collections->get_collections_menu();

        $headers = array(
                    "meta_title" => $this->config->item("site_title"),
                    "meta_description" => $this->config->item("site_title"),
                    "meta_keywords" => "");

        $this->load->view('headers.tpl', $headers);

        $page['body_class'] = "index";
		$this->load->view('index.tpl', $page);
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */