<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends EX_Controller {

	public function index()
	{
		if (is_logged_on()) {
			redirect('sales');
		} else {
			redirect('auth/login');
		}
	}
}
