<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error404 extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('error-404', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List page
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array(
			'error' => array(
				'headline' => lang('error404_headline'),
				'message' => lang('error404_message')
			)
		);

		// Build the page
		$this->build('error/404', lang('error404_title'), $data);
	}

}
