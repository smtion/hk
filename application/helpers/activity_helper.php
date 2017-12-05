<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function activity_log()
{
	$CI =& get_instance();
  $trace = debug_backtrace();

  $CI->db->insert('HK_activity_log', ['user_id' => get_user_id(), 'class' => $trace[1]['class'], 'function' => $trace[1]['function']]);
}
