<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zend {

	/**
	 * Constructor
	 *
	 * @param   string  $class
	 */
	public function __construct($class = NULL)
	{
		// Define the path separator if not already defined
		if ( ! defined("PATH_SEPARATOR"))
		{
			$separator = (strpos($_ENV['OS'], 'Win') !== FALSE) ? ';' : ':';

			define('PATH_SEPARATOR', $separator);
		}

		ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.realpath(APPPATH.'third_party/Zend/libraries'));

		log_message('debug', 'Zend Class Initialized');

		if ($class)
		{
			$this->load($class);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Load Zend library
	 *
	 * @param   string  $class
	 */
	public function load($class = NULL)
	{
		$class = (string)$class;

		if ($class)
		{
			require_once $class.'.php';

			log_message('debug', "Zend Class $class Loaded");
		}
	}

}