<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('about', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * About
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'About Summit',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('about/about', lang('about_title_general'), $data, $breadcrumbs);
	}

}