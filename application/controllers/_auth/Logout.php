<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends EX_Controller {

	public function index()
	{
    $this->session->sess_destroy();
		redirect('auth/login');
	}
}