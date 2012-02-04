<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('project', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List page
	 *
	 * @return  View
	 */
	public function index($code = '')
	{
		// Make sure the project exists
		$project = $this->project_model->get_project_by_code($code);

		// Make sure the project exists
		if (empty($project))
		{
			show_error('Project doesn\'t exist');
		}

		// Set 'is'
		$this->is->query('project-code', $code);

		// Pass to view
		$data = array(
//			'total' => $total,
//			'projects' => $segmented,
//			'num_results' => count($segmented),
//			'pagination' => $this->pagination->generate($pagination_config)
		);

		// Breadcrumbs
		$breadcrumbs = array(
//			array(
//				'label' => 'Projects',
//				'link' => $this->uri->uri_string()
//			)
		);

		// Page title
		$title = lang('project_title_general');

//		if ($page != 1)
//		{
//			$title = sprintf(lang('projects_title_paged'), $page);
//		}

		// Build the page
		$this->build('project/project', $title, $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * List page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('project-create', TRUE);

		// Users
		$users = array('' => '- Select User -')+$this->user_model->get_all_users();

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Project')
				->text('key', 'Key', 'trim|xss_clean|required|is_unique[projects.project_key]')
				->text('name', 'Name', 'trim|xss_clean|required|is_unique[projects.project_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->text('url', 'URL', 'trim|valid_url|required|xss_clean')
				->file('icon', 'Icon', 'trim|xss_clean')
				->select('lead_id', 'Project Lead', $users, 'trim|xss_clean|required')
				->select('default_assignee_id', 'Default Assignee', $users, 'trim|xss_clean|required')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Project', array('class' => 'button positive'))
			->block('</div>');
		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'create' => $form->generate()
				)
			);

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Projects',
					'link' => 'projects'
				),
				array(
					'label' => 'Create Project',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('project/create', lang('project_title_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create priority
			$this->project_model->create_project($post);

			// Add a message
			$message = sprintf(lang('project_success_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('projects', $this->registry->get_item('redirect'));
		}
	}

}
