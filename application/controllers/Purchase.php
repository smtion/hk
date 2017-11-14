<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'purchase/_sidebar';
  }

	public function index()
	{
    $this->aluminum();
	}

  public function aluminum()
	{
    $this->cdata->template = 'purchase/aluminum';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option_detail()
	{
    $this->cdata->template = 'purchase/option_detail';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option_list()
	{
    $this->cdata->template = 'purchase/option_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option_price()
	{
    $this->cdata->template = 'purchase/option_price';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
