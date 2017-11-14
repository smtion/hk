<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Check if user logged in.
 *
 * @param bool
 * @return  bool
 */
function array_pick($arr, $picker)
{
  $tmp = array();
  
  while (count($picker) > 0) {
    $key = array_shift($picker);
    //array_push($tmp, $arr[array_shift($picker)]);
    $tmp[$key] = $arr[$key];
  }
  
  return $tmp;
}