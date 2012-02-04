<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-users', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * General administration
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array();

		// Build the page
		$this->build('administration/users/users', lang('administration_title_general'), $data);
	}

}