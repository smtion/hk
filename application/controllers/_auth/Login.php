<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends EX_Controller {

	public function index()
	{
		// echo get_user_id();
		// return;
		if (is_logged_on()) {
			redirect('main');
		} else {
	    $this->cdata->template = 'auth/login';
			$this->load->view(LAYOUT, $this->cdata);
		}
	}
}
