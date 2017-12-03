<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends EX_Controller {

	public function index()
	{
		if (is_logged_on()) {
			if (is_admin()) {
				redirect('/admin');
			} elseif (has_permission('sales')) {
				redirect('/sales');
			} elseif (has_permission('purchase')) {
				redirect('/purchase');
			} elseif (has_permission('production')) {
				redirect('/production');
			} elseif (has_permission('finance')) {
				redirect('/finance');
			} elseif (has_permission('admin')) {
				redirect('/admin');
			}
		} else {
			redirect('auth/login');
		}
	}
}
