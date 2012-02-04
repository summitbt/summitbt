<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Priority extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-priority', TRUE);

		// Load model
		$this->load->model('priority_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Priority list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Priority list page
	 *
	 * @param   int     $page
	 * @return  View
	 */
	public function page($page = 1)
	{
		// Load pagination library
		$this->load->library('pagination');

		// Pass to view
		$data = array();

		// Priorities
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->priority_model->get_total();
		$data['priorities'] = $this->priority_model->get_segmented($page, $count);
		$data['priority_results'] = count($data['priorities']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/priority/page'),
			'first_url' => site_url('administration/priority'),
			'total_rows' => $data['total'],
			'per_page' => $count,
			'num_links' => 3,
			'uri_segment' => 4,
			'cur_page' => $page
		);

		$data['pagination'] = $this->pagination->generate($pagination_config);

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Administration',
				'link' => 'administration'
			),
			array(
				'label' => 'Issue Priorities',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/priority/list', lang('administration_title_priorities_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create priority page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-priority-create', TRUE);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Priority')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique[priorities.priority_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->text('color', 'Color', 'trim|required|xss_clean')
				->file('icon', 'Icon', 'trim|required|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Priority', array('class' => 'button positive'))
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
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Priorities',
					'link' => 'administration/priority'
				),
				array(
					'label' => 'New Issue Priority',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/priority/create', lang('administration_title_priorities_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create priority
			$this->priority_model->create_priority($post);

			// Add a message
			$message = sprintf(lang('administration_success_priorities_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/priority', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit priority page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Priority ID not defined');
			show_error($error, 406);
		}

		// Get priority info
		$priority = $this->priority_model->get_priority_by_id($id);

		// Priority not found
		if ( ! $priority)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Priority ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-priority-edit', $id);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Priority')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique_except[priorities.priority_name.priority_id.'.$id.']', $priority['name'])
				->textarea('description', 'Description', 'trim|xss_clean', $priority['description'])
				->text('color', 'Color', 'trim|required|xss_clean', $priority['color'])
				->file('icon', 'Icon', 'trim|required|xss_clean', $priority['icon'])
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Priority', array('class' => 'button positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'edit' => $form->generate()
				)
			);

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Priorities',
					'link' => 'administration/priority'
				),
				array(
					'label' => 'Edit '.$priority['name'].' Priority',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_priorities_edit'), $priority['name']);
			$this->build('administration/priority/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Update database
			$this->priority_model->update_priority_by_id($id, $post);

			// Add a message
			$message = sprintf(lang('administration_success_priorities_edit'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/priority', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete priority page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Priority ID not defined');
			show_error($error, 406);
		}

		// Get priority info (for the success message)
		$priority = $this->priority_model->get_priority_by_id($id);

		// How many issues are using this priority?
		$count = $this->priority_model->get_issue_count_in_priority_id($id);

		// If there are no products with this priority, delete the priority
		if ( ! $count)
		{
			// Delete priority
			$this->priority_model->delete_priority_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_priorities_delete'), $priority['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/priority', $this->registry->get_item('redirect'));
		}

		// If there are users with this priority, notify the user that the priority cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_priorities_cannot_delete'), $priority['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Priorites',
					'link' => 'administration/priority'
				),
				array(
					'label' => 'Cannot Delete Issue Priority',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_priorities_delete'), $priority['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}