<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property mixed lo
 * @property mixed loa
 */
class Issue extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('issue', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List page
	 *
	 * @return  View
	 */
	public function index($code = '')
	{
//		// Paged 'is'
//		if ($page != 1)
//		{
//			// Set 'is'
//			$this->is->query('projects-paged', TRUE);
//			$this->is->query('projects-page', $page);
//		}
//
//		// Load pagination library
//		$this->load->library('pagination');
//
//		// Results to display per page
//		$count = $this->registry->get_item('result_count');
//
//		// Total records
//		$total = $this->projects_m->get_total();
//
//		// Get pages records
//		$segmented = $this->projects_m->get_segmented($page, $count);
//
//		// Pagination
//		$pagination_config = array(
//			'base_url' => site_url('projects/page'),
//			'first_url' => site_url('projects'),
//			'total_rows' => $total,
//			'per_page' => $count,
//			'num_links' => 3,
//			'uri_segment' => 3,
//			'cur_page' => $page
//		);

		// Pass to view
		$data = array(

		);

		// Breadcrumbs
		$breadcrumbs = array(
//			array(
//				'label' => 'Projects',
//				'link' => $this->uri->uri_string()
//			)
		);

		// Page title
		$title = lang('issue_title');

//		if ($page != 1)
//		{
//			$title = sprintf(lang('projects_title_paged'), $page);
//		}

		// Build the page
		$this->build('issue/issue', $title, $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create new issue
	 *
	 * @param   string  $project
	 * @return  View
	 */
	public function create($project = '')
	{
		// Set 'is'
		$this->is->query('issue-create', TRUE);

		// Form
		$form = $this->formation->action()
			->honeypot('user', 'is_empty')
			->fieldset_open(lang('issue_fieldset_create'))
//				->text('username', lang('access_field_username'), 'trim|login|required|xss_clean')
//				->password('password', lang('access_field_password'), 'trim|required|xss_clean')
//				->checkbox('remember', lang('access_field_remember'), '1', 'trim')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', lang('issue_button_create'), array('class' => 'button pill positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'issue' => array(
						'create' => $form->generate()
					)
				)
			);

			$breadcrumbs = array();

			// Build the page
			$this->build('issue/new', lang('issue_new_title'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			print_r( $this->input->post() );

			// Send the user information to auth to create session
			// Custom form validation "login" has already created this for us. Happy day!

			// Redirect
			//redirect('');
		}
	}

}
