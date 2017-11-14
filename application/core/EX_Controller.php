<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EX_Controller extends CI_Controller {

  var $cdata;

  function __construct()
  {
    parent::__construct();

    $this->cdata = new stdClass();

    if (uri_string() != 'auth/login' && !is_logged_on()) {
      redirect('auth/login');
    }
  }
}
