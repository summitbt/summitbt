<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('search', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * General search
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array();

		// Build the page
		$this->build('search/search', lang('search_title_general'), $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Search results
	 *
	 * @return  View
	 */
	public function results($q = '', $page = 1)
	{
		// Pass to view
		$data = array();

		// Page title
		$title = sprintf(lang('search_title_results'), $q);

		// Build the page
		$this->build('search/results', $title, $data);
	}

}