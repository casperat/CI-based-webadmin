<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function login($username, $password) {

        $this->db->select("`id` as `admin_id`, `firstname`, `lastname`, `username`, `superadmin`")
                ->from("wa_users")
                ->where(array("username" => $username, "password" => md5($password)));
        $query = $this->db->get();

        if ($query->num_rows()) {
            $user_data = $query->row_array();
            $this->session->set_userdata($user_data);

            // this is needed for CKFinder
            session_start();
            $_SESSION['admin_id'] = $user_data['admin_id'];

            $update = array('last_visit' => date('Y-m-d H:i:s'));
            $this->db->where("id", $user_data['admin_id']);
            $this->db->update('wa_users', $update);

            return true;
        }

        return null;
    }

    public function logout() {
        $user_data = array(
                        'admin_id' => '',
                        'superadmin' => '',
                        'firstname' => '',
                        'lastname' => '',
                        'username' => '');
        $this->session->unset_userdata($user_data);
    }

    public function generate_password() {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#\$%^&*()+-?";
        $pass = '';
        while (strlen($pass) < 8) {
            $char = substr($chars, rand(0, strlen($chars)-1), 1);
            if (!strpos($pass, $char)) {
                $pass .= $char;
            }
        }

        return $pass;
    }
}

/* End of file users_model.php */
/* Location ./admin/application/models/users_model.php */