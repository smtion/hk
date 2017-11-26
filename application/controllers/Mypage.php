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
    $this->cdata->template = 'mypage/index';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function currency()
	{
    $this->cdata->template = 'mypage/index';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option()
	{
    $this->cdata->template = 'mypage/option';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function material()
	{
    $this->cdata->template = 'mypage/material';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost()
	{
    $this->cdata->template = 'mypage/cost';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function product()
	{
    $this->cdata->template = 'mypage/product';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function customer()
	{
    $this->cdata->template = 'mypage/customer';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function project()
	{
    $this->cdata->template = 'mypage/project';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function quotation()
	{
    $this->cdata->template = 'mypage/quotation';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function approval()
	{
    $this->cdata->template = 'mypage/approval';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function request()
	{
    $this->cdata->template = 'mypage/request';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
