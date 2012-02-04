<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seo extends Module {

	/*
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		log_message('debug', 'SEO Class Initialized');
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

}
