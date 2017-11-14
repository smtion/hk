<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function errorHandlerTransaction($errno, $errstr, $errfile, $errline) {
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function printErrorLog($e) {
  log_message('ERROR', sprintf("[%s] %s %s:%d \n", $e->getMessage(), get_class($e), $e->getFile(), $e->getLine()));
}
