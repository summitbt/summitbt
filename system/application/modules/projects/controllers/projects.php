<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('projects', TRUE);
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
	 * Project listing
	 *
	 * @return  View
	 */
	public function page($page = 1)
	{
		// Paged 'is'
		if ($page != 1)
		{
			// Set 'is'
			$this->is->query('projects-paged', TRUE);
			$this->is->query('projects-page', $page);
		}

		// Load pagination library
		$this->load->library('pagination');

		// Results to display per page
		$count = $this->registry->get_item('result_count');

		// Total records
		$total = $this->projects_m->get_total();

		// Get pages records
		$segmented = $this->projects_m->get_segmented($page, $count);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('projects/page'),
			'first_url' => site_url('projects'),
			'total_rows' => $total,
			'per_page' => $count,
			'num_links' => 3,
			'uri_segment' => 3,
			'cur_page' => $page
		);

		// Pass to view
		$data = array(
			'total' => $total,
			'projects' => $segmented,
			'num_results' => count($segmented),
			'pagination' => $this->pagination->generate($pagination_config)
		);

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Projects',
				'link' => $this->uri->uri_string()
			)
		);

		// Page title
		$title = lang('projects_title');

		if ($page != 1)
		{
			$title = sprintf(lang('projects_title_paged'), $page);
		}

		// Build the page
		$this->build('projects/projects', $title, $data, $breadcrumbs);
	}

}
