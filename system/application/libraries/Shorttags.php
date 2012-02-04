<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shorttags {

	private $CI;

	private $_tags = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		// {{ site:url url='XYZ' }}
		$this->register('site:url', array($this, 'shorttag__site_url'));

		// {{ site:base url='XYZ' }}
		$this->register('site:base', array($this, 'shorttag__base_url'));

		// {{ site:theme url='XYZ' }}
		$this->register('site:theme', array($this, 'shorttag__site_theme'));

		// {{ site:logo }}
		$this->register('site:logo', array($this, 'shorttag__site_logo'));

		// {{ page:bodyclass }}
		$this->register('page:bodyclass', array($this, 'shorttag__page_bodyclass'));

		// {{ page:head:title }}
		$this->register('page:head:title', array($this, 'shorttag__page_head_title'));

		// {{ page:content }}
		$this->register('page:content', array($this, 'shorttag__page_content'));

		// {{ page:breadcrumbs }}
		$this->register('page:breadcrumbs', array($this, 'shorttag__page_breadcrumbs'));

		// {{ theme:partial file='XYZ' }}
		$this->register('theme:partial', array($this, 'shorttag__theme_partial'));

		// {{ translate lang='XYZ' }}
		$this->register('translate', array($this, 'shorttag__translate'));

		// {{ is type='XYZ' }}
		$this->register('is', array($this, 'shorttag__is'));

		// {{ can action='XYZ' }}
		$this->register('can', array($this, 'shorttag__can'));

		// {{ widget type='XYZ' }}
		$this->register('widget', array($this, 'shorttag__widget'));

		// {{ page:head:meta }}
		$this->register('page:head:meta', array($this, 'shorttag__page_head_meta'));

		// {{ page:head:links }}
		$this->register('page:head:links', array($this, 'shorttag__page_head_links'));

		// {{ page:head:scripts }}
		$this->register('page:head:scripts', array($this, 'shorttag__page_head_scripts'));

		// {{ page:foot:scripts }}
		$this->register('page:foot:scripts', array($this, 'shorttag__page_foot_scripts'));
	}

	// --------------------------------------------------------------------

	/**
	 * All shorttags
	 *
	 * @return  array
	 */
	public function get_shorttags()
	{
		return $this->_tags;
	}

	// --------------------------------------------------------------------

	/**
	 * Register a trigger
	 *
	 * @param   string  $trigger
	 * @param   array   $callback
	 * @return  Shorttags
	 */
	public function register($trigger = '', $callback = array())
	{
		$this->_tags[$trigger] = $callback;

		// Return
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Unregister a shorttag
	 *
	 * @param   array   $callback
	 * @return  Shorttags
	 */
	public function unregister($trigger = '')
	{
		unset($this->_tags[$trigger]);

		// Return
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Site URL
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__site_url($attributes = array(), $content = '')
	{
		$url = '';

		if (array_key_exists('url', $attributes))
		{
			$url = $attributes['url'];
		}

		return site_url($url);
	}

	// --------------------------------------------------------------------

	/**
	 * Base URL
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__base_url($attributes = array(), $content = '')
	{
		$url = '';

		if (array_key_exists('url', $attributes))
		{
			$url = $attributes['url'];
		}

		return base_url($url);
	}

	// --------------------------------------------------------------------

	/**
	 * Site theme
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__site_theme($attributes = array(), $content = '')
	{
		$url = theme_url();

		if (array_key_exists('file', $attributes))
		{
			$url = theme_url($attributes['file']);
		}

		return $url;
	}

	// --------------------------------------------------------------------

	/**
	 * Site logo
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__site_logo($attributes = array(), $content = '')
	{
		$logo = $this->CI->registry->get_item('logo');

		// Logo specified
		if ($logo)
		{
			$logo = 'uploads/logo/'.$logo;
		}

		// Default logo
		else
		{
			$logo = $this->CI->config->item('default_logo');
		}

		return base_url($logo);
	}

	// --------------------------------------------------------------------

	/**
	 * Body tag classes
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_bodyclass($attributes = array(), $content = '')
	{
		// Load user agent class
		$this->CI->load->library('user_agent');

		// Array of body class elements
		$body = array();

		// Platform (OS)
		$body[] = $this->_lower_dash($this->CI->agent->platform());

		// Browser
		if ($this->CI->agent->is_browser())
		{
			$browser = $this->_lower_dash($this->CI->agent->browser());

			if ($browser == 'internet-explorer')
			{
				$browser = 'ie';
			}

			$body[] = $browser;
		}

		// Mobile
		if ($this->CI->agent->is_mobile('iphone'))
		{
			$body[] = $this->_lower_dash($this->CI->agent->mobile());
		}

		// Maintenance mode
		if ($this->CI->registry->get_item('in-maintenance-mode'))
		{
			$body[] = 'maintenance';
		}

		// 'is'
		foreach ($this->CI->is->all() as $is)
		{
			$body[] = $is;
		}

		// Logged in
		if ($this->CI->auth->logged_in())
		{
			$body[] = 'logged-in';
		}

		// Not logged in
		else
		{
			$body[] = 'not-logged-in';
		}

		return implode(' ', $body);
	}

	// --------------------------------------------------------------------

	/**
	 * Page title
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_head_title($attributes = array(), $content = '')
	{
		return $this->CI->template->template['title'];
	}

	// --------------------------------------------------------------------

	/**
	 * Page content
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_content($attributes = array(), $content = '')
	{
		return $this->CI->template->template['body'];
	}

	// --------------------------------------------------------------------

	/**
	 * Page breadcrumbs
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_breadcrumbs($attributes = array(), $content = '')
	{
		$crumbs = $this->CI->template->template['breadcrumbs'];

		if ( empty($crumbs))
		{
			$crumbs = '';
		}

		return $crumbs;
	}

	// --------------------------------------------------------------------

	/**
	 * Load theme partial
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__theme_partial($attributes = array(), $content = '')
	{
		if (array_key_exists('file', $attributes))
		{
			$partial = $attributes['file'];

			return $this->CI->template->_load_view('partials/'.$partial, array(), TRUE, $this->CI->template->_find_view_folder());
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Translation
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__translate($attributes = array(), $content = '')
	{
		if (array_key_exists('lang', $attributes))
		{
			$lang = $attributes['lang'];

			return lang($lang);
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Test is user has permission
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__can($attributes = array(), $content = '')
	{
		if (array_key_exists('action', $attributes))
		{
			$can = $attributes['action'];

			return $this->CI->user_model->has_permission($can);
		}

		return FALSE;

	}

	// --------------------------------------------------------------------

	/**
	 * Test for 'is'
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  bool
	 */
	public function shorttag__is($attributes = array(), $content = '')
	{
		if (array_key_exists('type', $attributes))
		{
			$type = $attributes['type'];

			$this->CI->is->query($type);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
		 * Return widget
		 *
		 * @param   array   $attributes
		 * @param   string  $content
		 * @return  bool
		 */
	public function shorttag__widget($attributes = array(), $content = '')
	{
		if (array_key_exists('type', $attributes))
		{
			$module = $attributes['type'];

			return Modules::run('widgets/'.$module, $attributes);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * META content
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_head_meta($attributes = array(), $content = '')
	{
		return $this->CI->assets->render_meta();
	}

	// --------------------------------------------------------------------

	/**
	 * Link tags
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_head_links($attributes = array(), $content = '')
	{
		return $this->CI->assets->render_all_css();
	}

	// --------------------------------------------------------------------

	/**
	 * Scripts for the top of the page
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_head_scripts($attributes = array(), $content = '')
	{
		return $this->CI->assets->render_all_js('head');
	}

	// --------------------------------------------------------------------

	/**
	 * Scripts for the bottom of the page
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function shorttag__page_foot_scripts($attributes = array(), $content = '')
	{
		return $this->CI->assets->render_all_js();
	}

	// --------------------------------------------------------------------

	/**
	 * Make a string lower case and replace spaces with a dash
	 *
	 * @param   string  $str
	 * @return  string
	 */
	private function _lower_dash($str = '')
	{
		return strtolower(url_title($str));
	}

}