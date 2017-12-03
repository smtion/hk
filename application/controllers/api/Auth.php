<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller {

  public function login_post()
  {
    $email = $this->post('email');
    $password = hash('sha256', $this->post('password'));

    $user = $this->db->where('email', $email)->where('password', $password)
            // ->where('password', "HEX(AES_ENCRYPT('{$password}', '{$password}'))", FALSE)
            ->get('HK_users')->row_array();

    if ($user) {
      $this->session->set_userdata('user_id', $user['id']);
      $this->session->set_userdata('name', $user['name']);
      $this->session->set_userdata('email', $user['email']);
      $this->session->set_userdata('admin', $user['admin']);
      $this->session->set_userdata('permission', json_decode($user['permission'], true));

      //
      $data = [
        'user_id' => $user['id'],
        'ip' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
      ];
      $this->db->insert('HK_login_history', $data);

      $this->response(null, REST_Controller::HTTP_OK);
    } else {
      $this->response(null, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }
}
