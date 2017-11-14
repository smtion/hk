<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function async_request($url, $method='GET', $payload='') {
  
  $cmd = "curl -X " . strtoupper($method) . " -H 'Content-Type: application/json'";
  $cmd.= " -d '" . $payload . "'" . " -k '" . $url . "'";

  //if (!$this->debug()) {
    $cmd .= " > /dev/null 2>&1 &";
  //}

  log_message('info', $cmd);
  exec($cmd, $output, $exit);
  //
  //echo print_r($output, 1);
  return $exit == 0;
}