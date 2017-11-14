<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'finance/_sidebar';
  }

	public function index()
	{
    $this->sample();
	}

  public function sample()
	{
    $this->cdata->template = 'finance/sample';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
