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
    redirect('sales/currency');
	}

  public function currency()
	{
    $this->cdata->template = 'sales/currency';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option()
	{
    $this->cdata->template = 'sales/option';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function material()
	{
    $this->cdata->template = 'sales/material';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost()
	{
    $this->cdata->template = 'sales/cost';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function product()
	{
    $this->cdata->template = 'sales/product';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function customer()
	{
    $this->cdata->template = 'sales/customer';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function project()
	{
    $this->cdata->template = 'sales/project';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function quotation($id = 0)
	{
    $this->cdata->id = $id;
    $this->cdata->template = $id ? 'sales/quotation_view' : 'sales/quotation';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function quotation_create()
	{
    $this->cdata->template = 'sales/quotation_create';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function approval()
	{
    $this->cdata->template = 'sales/approval';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function request()
	{
    $this->cdata->template = 'sales/request';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
