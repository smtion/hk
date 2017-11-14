<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'production/_sidebar';
  }

	public function index()
	{
    $this->sample();
	}

  public function sample()
	{
    $this->cdata->template = 'production/sample';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
