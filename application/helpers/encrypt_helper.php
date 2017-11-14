<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function encrypt($src) {
  $CI =& get_instance();
  if (!$CI->load->is_loaded('encryption')) $CI->load->library('encryption');
  $CI->encryption->initialize(array('cipher' => 'des'));
  $delimeter = config_item('encryption_delimeter');
  $serialized = $src;
  
  if (is_array($src)) {
    $serialized = implode($delimeter, $src);
  }
  
  return strtr($CI->encryption->encrypt($serialized), '+/=', '-_:');
}

function decrypt($encoded) {
  $CI =& get_instance();
  if (!$CI->load->is_loaded('encryption')) $CI->load->library('encryption');
  $CI->encryption->initialize(array('cipher' => 'des'));
  $delimeter = config_item('encryption_delimeter');
  
  if (!is_string($encoded)) {
    return NULL;
  }
  
  $decoded = $CI->encryption->decrypt(strtr($encoded, '-_:', '+/='));
  if (count($decoded) == 1) {
    return $decoded;
  }
  else {
    return explode($delimeter, $decoded);
  }
}