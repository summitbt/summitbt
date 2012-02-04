<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('theme_url'))
{
	function theme_url($file = '')
	{
		return base_url().theme_uri($file);
	}
}

if( ! function_exists('theme_uri'))
{
	function theme_uri($file = '')
	{
		$CI =& get_instance();

		return 'theme/'.$CI->registry->get_item('theme').'/'.$file;
	}
}

// --------------------------------------------------------------------

if( ! function_exists('includes_url'))
{
	function includes_url($file = '')
	{
		return base_url().includes_uri($file);
	}
}

// --------------------------------------------------------------------

if( ! function_exists('includes_uri'))
{
	function includes_uri($file = '')
	{
		return 'assets/includes/'.$file;
	}
}

// --------------------------------------------------------------------

if( ! function_exists('uploads_url'))
{
	function uploads_url($file = '')
	{
		return base_url().uploads_uri($file);
	}
}

// --------------------------------------------------------------------

if( ! function_exists('uploads_uri'))
{
	function uploads_uri($file = '')
	{
		return 'uploads/'.$file;
	}
}

// --------------------------------------------------------------------

if( ! function_exists('assets_url'))
{
	function assets_url($file = '')
	{
		return base_url().assets_uri($file);
	}
}

// --------------------------------------------------------------------

if( ! function_exists('assets_uri'))
{
	function assets_uri($file = '')
	{
		return 'assets/'.$file;
	}
}

// ------------------------------------------------------------------------

/**
 * Redirect to referrer
 *
 * @param	string	$uri
 * @param	string	$method
 * @param   int     $http_response_code
 * @return	void
 */
if ( ! function_exists('referrer_redirect'))
{
	function referrer_redirect($uri = '', $method = 'auto', $http_response_code = 302)
	{
		$CI =& get_instance();

		$CI->load->library('user_agent');

		// is there a referrer?
		if ($CI->agent->is_referral())
		{
			$uri = $CI->agent->referrer();;
		}

		redirect($uri, $method, $http_response_code);
	}
}