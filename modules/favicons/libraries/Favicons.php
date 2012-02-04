<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Favicons extends Module {

	/*
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		log_message('debug', 'Favicons Class Initialized');

		// Favicon
		$this->favicon($config);

		// Apple Touch Icon (57x57)
		$this->apple_favicon_57($config);

		// Favicon (7x72)
		$this->apple_favicon_72($config);

		// Favicon (114x114)
		$this->apple_favicon_114($config);
	}

	// --------------------------------------------------------------------

	/**
	 * Module activated
	 */
	public function activate()
	{
		// Empty
	}

	// --------------------------------------------------------------------

	/**
	 * Module deactivated
	 */
	public function deactivate()
	{
		// Empty
	}

	// --------------------------------------------------------------------

	/**
	 * Custom favicon (.ico)
	 *
	 * @param   array   $config
	 */
	public function favicon($config = array())
	{
		$key = 'favicon';

		if ($favicon = $config[$key])
		{
			$this->CI->assets->link($key, array('rel' => 'shortcut icon', 'href' => $favicon));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Custom Apple Touch icon (57x57)
	 *
	 * @param   array   $config
	 */
	public function apple_favicon_57($config = array())
	{
		$key = 'apple-touch-icon';

		if ($favicon = $config[$key])
		{
			$this->CI->assets->link($key, array('rel' => 'apple-touch-icon', 'href' => $favicon));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Custom Apple Touch icon (72x72)
	 *
	 * @param   array   $config
	 */
	public function apple_favicon_72($config = array())
	{
		$key = 'apple-touch-icon-72';

		if ($favicon = $config[$key])
		{
			$this->CI->assets->link($key, array('rel' => 'apple-touch-icon', 'sizes' => '72x72', 'href' => $favicon));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Custom Apple Touch icon (114x114)
	 *
	 * @param   array   $config
	 */
	public function apple_favicon_114($config = array())
	{
		$key = 'apple-touch-icon-114';

		if ($favicon = $config[$key])
		{
			$this->CI->assets->link($key, array('rel' => 'apple-touch-icon', 'sizes' => '114x114', 'href' => $favicon));
		}
	}

}
