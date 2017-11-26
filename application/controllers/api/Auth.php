<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller {

  public function login_post()
  {
    $email = $this->post('email');
    $this->post('password');

    // @TODO get user info

    $this->session->set_userdata('user_id', $email);
    $this->session->set_userdata('name', '관리자');
    $this->session->set_userdata('email', $email);
    $this->session->set_userdata('role', 'admin');
    $this->session->set_userdata('level', '100');
    $this->session->set_userdata('permission', ['a'=>1, 'b'=>2, 'c'=>3]);

    $response = [
      // 'email' => $this->post('email'),
      // 'password' => $this->post('password'),
      // 'message' => 'Added a resource'
    ];

    $http_status = REST_Controller::HTTP_OK;
    // $http_status = REST_Controller::HTTP_UNAUTHORIZED;

    $this->set_response($response, $http_status); // CREATED (201) being the HTTP response code
  }
}
