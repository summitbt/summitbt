<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resolution extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-resolution', TRUE);

		// Load model
		$this->load->model('resolution_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Resolution list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Resolution list page
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

		// Resolutions
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->resolution_model->get_total();
		$data['resolutions'] = $this->resolution_model->get_segmented($page, $count);
		$data['resolution_results'] = count($data['resolutions']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/resolution/page'),
			'first_url' => site_url('administration/resolution'),
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
				'label' => 'Issue Resolutions',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/resolution/list', lang('administration_title_resolutions_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create resolution page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-resolution-create', TRUE);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Resolution')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique[resolutions.resolution_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->file('icon', 'Icon', 'trim|required|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Resolution', array('class' => 'button positive'))
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
					'label' => 'Issue Resolutions',
					'link' => 'administration/resolution'
				),
				array(
					'label' => 'New Issue Resolution',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/resolution/create', lang('administration_title_resolutions_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create resolution
			$this->resolution_model->create_resolution($post);

			// Add a message
			$message = sprintf(lang('administration_success_resolutions_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/resolution', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit resolution page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Resolution ID not defined');
			show_error($error, 406);
		}

		// Get resolution info
		$resolution = $this->resolution_model->get_resolution_by_id($id);

		// Resolution not found
		if ( ! $resolution)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Resolution ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-resolution-edit', $id);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Resolution')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique_except[resolutions.resolution_name.resolution_id.'.$id.']', $resolution['name'])
				->textarea('description', 'Description', 'trim|xss_clean', $resolution['description'])
				->file('icon', 'Icon', 'trim|required|xss_clean', $resolution['icon'])
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Resolution', array('class' => 'button positive'))
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
					'label' => 'Issue Resolutions',
					'link' => 'administration/resolution'
				),
				array(
					'label' => 'Edit '.$resolution['name'].' Resolution',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_resolutions_edit'), $resolution['name']);
			$this->build('administration/resolution/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Update database
			$this->resolution_model->update_resolution_by_id($id, $post);

			// Add a message
			$message = sprintf(lang('administration_success_resolutions_edit'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/resolution', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete resolution page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Resolution ID not defined');
			show_error($error, 406);
		}

		// Get resolution info (for the success message)
		$resolution = $this->resolution_model->get_resolution_by_id($id);

		// How many issues are using this resolution?
		$count = $this->resolution_model->get_issue_count_in_resolution_id($id);

		// If there are no products with this resolution, delete the resolution
		if ( ! $count)
		{
			// Delete resolution
			$this->resolution_model->delete_resolution_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_resolutions_delete'), $resolution['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/resolution', $this->registry->get_item('redirect'));
		}

		// If there are users with this resolution, notify the user that the resolution cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_resolutions_cannot_delete'), $resolution['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Resolutions',
					'link' => 'administration/resolution'
				),
				array(
					'label' => 'Cannot Delete Issue Resolution',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_resolutions_delete'), $resolution['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}