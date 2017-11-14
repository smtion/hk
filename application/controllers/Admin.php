<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'admin/_sidebar';
  }

	public function index()
	{
    $this->users();
	}

  public function users()
	{
    $this->cdata->template = 'admin/users';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
