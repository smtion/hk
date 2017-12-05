<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends EX_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_admin()) {
      redirect('mypage');
    }
    $this->cdata->sidebar = 'admin/_sidebar';
  }

	public function index()
	{
    redirect('admin/user');
	}

  public function user()
	{
    $this->cdata->template = 'admin/user';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function dept()
	{
    $this->cdata->template = 'admin/dept';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function position()
	{
    $this->cdata->template = 'admin/position';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function login()
	{
    $this->cdata->template = 'admin/login';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function activity()
	{
    $this->cdata->template = 'admin/activity';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function company()
	{
    $this->cdata->template = 'admin/company';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
