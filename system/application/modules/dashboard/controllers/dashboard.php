<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('dashboard', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Dashboard
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array();

		// Build the page
		$this->build('dashboard/dashboard', lang('dashboard_title'), $data);
	}

}