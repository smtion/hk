<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'sales/_sidebar';
  }

	public function index()
	{
    $this->currency();
	}

  public function currency()
	{
    $this->cdata->template = 'sales/currency';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
