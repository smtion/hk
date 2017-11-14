<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function return_json($res)
{
	$CI =& get_instance();
	$res = json_encode($res);
  $CI->output->set_header('Access-Control-Allow-Origin: *');
	$CI->output->set_content_type('application/json');
	echo $res;
}

function validate()
{
	
}
