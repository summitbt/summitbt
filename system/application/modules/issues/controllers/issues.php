<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Issues extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('issues', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List page
	 *
	 * @return  View
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Issue listing
	 *
	 * @return  View
	 */
	public function page($page = 1)
	{
		// Paged 'is'
		if ($page != 1)
		{
			// Set 'is'
			$this->is->query('issues-paged', TRUE);
			$this->is->query('issues-page', $page);
		}

		// Load pagination library
		$this->load->library('pagination');

		// Results to display per page
		$count = $this->registry->get_item('result_count');

		// Total records
		$total = $this->issues_m->get_total();

		// Get pages records
		$segmented = $this->issues_m->get_issues($page, $count);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('issues/page'),
			'first_url' => site_url('issues'),
			'total_rows' => $total,
			'per_page' => $count,
			'num_links' => 3,
			'uri_segment' => 3,
			'cur_page' => $page
		);

		// Pass to view
		$data = array(
			'total' => $total,
			'issues' => $segmented,
			'num_results' => count($segmented),
			'pagination' => $this->pagination->generate($pagination_config),
			'no_results' => lang('issues_no_results')
		);

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Projects',
				'link' => $this->uri->uri_string()
			)
		);

		// Page title
		$title = lang('issues_title');

		if ($page != 1)
		{
			$title = sprintf(lang('issues_title_paged'), $page);
		}

		// Build the page
		$this->build('issues/issues', $title, $data, $breadcrumbs);
	}

}
