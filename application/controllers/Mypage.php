<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mypage extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'mypage/_sidebar';
  }

	public function index()
	{
    redirect('mypage/info');
	}

  public function info()
	{
    $this->cdata->template = 'mypage/info';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function user_list()
	{
    $this->cdata->template = 'mypage/user_list';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
