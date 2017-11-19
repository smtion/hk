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
    redirect('purchase/aluminum');
	}

  public function aluminum()
	{
    $this->cdata->template = 'purchase/aluminum';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option_parts()
	{
    $this->cdata->template = 'purchase/option_parts';
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

  public function material_parts()
	{
    $this->cdata->template = 'purchase/material_parts';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function material_list()
	{
    $this->cdata->template = 'purchase/material_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function material_price()
	{
    $this->cdata->template = 'purchase/material_price';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost_parts()
	{
    $this->cdata->template = 'purchase/cost_parts';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost_list()
	{
    $this->cdata->template = 'purchase/cost_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost_price()
	{
    $this->cdata->template = 'purchase/cost_price';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function product_list()
	{
    $this->cdata->template = 'purchase/product_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function product_price()
	{
    $this->cdata->template = 'purchase/product_price';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function stock_tile()
	{
    $this->cdata->template = 'purchase/cost_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function stock_complete()
	{
    $this->cdata->template = 'purchase/cost_price';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function project()
	{
    $this->cdata->template = 'purchase/cost_list';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function request()
	{
    $this->cdata->template = 'purchase/cost_price';
    $this->load->view(LAYOUT, $this->cdata);
	}
}
