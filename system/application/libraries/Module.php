<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module {

	protected $CI;

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI = &get_instance();

		log_message('debug', 'Module Base Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder function to call module is activated
	 */
	public function activate()
	{
		// Empty
	}

	// --------------------------------------------------------------------

	/**
	 * Placeholder function to call module is deactivated
	 */
	public function deactivate()
	{
		// Empty
	}

}