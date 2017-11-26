<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Check if user logged in.
 *
 * @param bool
 * @return  bool
 */
function get_user_id()
{
  return get_session_data('user_id');
}

function get_user_name()
{
  return get_session_data('user_name');
}

function get_role()
{
  return get_session_data('role');
}

function get_timezone()
{
  return get_session_data('timezone');
}

function get_preferred_currency()
{
  return get_session_data('preferred_currency');
}

function get_currency($from_cn)
{
  $c = get_session_data('currency');
  return isset($c[$from_cn]) ? $c[$from_cn]: 0;
}

function get_auth_code()
{
  return get_session_data('auth_code');
}

function get_partner_id()
{
  return get_session_data('partner_id');
}

function is_logged_on()
{
  return strlen(get_user_id()) > 0;
}

function get_session_data($key)
{
  $ci = &get_instance();
  return $ci->session->userdata($key);
}

function is_verified_user()
{
  return get_session_data('is_verified');
  /*
  $ci = &get_instance();
  $ci->load->model('auth_model', 'auth_model', TRUE);
  return $ci->auth_model->is_verified_user(get_user_id()) > 0;
  */
}

// Function to get the client IP address
function get_client_ip()
{
    /*$ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = '0.0.0.0';
  */
  $ipaddress = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR')?:
    '0.0.0.0';

    return $ipaddress;
}

function fz_float($str, $len)
{
  if ($pos = strpos($str, '.'))
  {
    for ($i = strlen($str)-1 ; $i > $pos ; $i--)
    {
      $str .= '0';
    }
  }
  else
  {
    $str .= '.';

    for ($i = 0 ; $i < $len ; $i++)
    {
      $str .= '0';
    }
  }

  return $str;
}
