<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends EX_Controller {

	public function index()
	{
		redirect('auth/login');
	}

	public function login()
	{
		if (is_logged_on()) {
			redirect('main');
		} else {
	    $this->cdata->template = 'auth/login';
			$this->load->view(LAYOUT, $this->cdata);
		}
	}

	public function logout()
	{
    $this->session->sess_destroy();
		redirect('auth/login');
	}
}
