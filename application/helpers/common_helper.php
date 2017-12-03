<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function now() {
  return date("Y-m-d H:i:s");
}

function today() {
  return date("Y-m-d");
}

function dow($w = false, $shorten = false) {
  $arr = ['일', '월', '화', '수', '목', '금', '토'];
  if (!$w) $w = date('w');
  return $shorten ? $arr[$w] : $arr[$w] . '요일';
}

function diff_date($str_date1, $str_date2, $return_type = 'days') {
  $date1 = new DateTime($str_date1);
  $date2 = new DateTime($str_date2);
  $interval = $date1->diff($date2);

  return $interval->{$return_type};
}

/**
 * @param mixed $str
 * @param string $filler
 * @param int $len Whole length
 * @param bool $direction TRUE: prepending, FALSE: appending
 * @param bool $strict TRUE: whole length of the output be strictly, FALSE: whole length of the output can be longer than $len because of $filler
 */
function fill($str, $filler, $len, $direction = TRUE, $strict = TRUE) {
  if (!is_string($str)) {
    $str = strval($str);
  }

  while (strlen($str) < $len) {
    $str = $direction ? ($filler . $str) : ($str . $filler);
  }

  if ($strict) {
    $str = substr($str, 0, $len);
  }

  return $str;
}

function get_ip() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
