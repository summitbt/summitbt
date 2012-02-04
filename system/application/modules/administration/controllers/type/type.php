<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Type extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-type', TRUE);

		// Load model
		$this->load->model('type_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Type list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Type list page
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

		// Types
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->type_model->get_total();
		$data['types'] = $this->type_model->get_segmented($page, $count);
		$data['type_results'] = count($data['types']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/type/page'),
			'first_url' => site_url('administration/type'),
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
				'label' => 'Issue Types',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/type/list', lang('administration_title_types_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create type page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-type-create', TRUE);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Type')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique[types.type_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->file('icon', 'Icon', 'trim|required|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Type', array('class' => 'button positive'))
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
					'label' => 'Issue Types',
					'link' => 'administration/type'
				),
				array(
					'label' => 'New Issue Type',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/type/create', lang('administration_title_types_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create type
			$this->type_model->create_type($post);

			// Add a message
			$message = sprintf(lang('administration_success_types_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/type', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit type page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Type ID not defined');
			show_error($error, 406);
		}

		// Get type info
		$type = $this->type_model->get_type_by_id($id);

		// Type not found
		if ( ! $type)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Type ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-type-edit', $id);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Type')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique_except[types.type_name.type_id.'.$id.']', $type['name'])
				->textarea('description', 'Description', 'trim|xss_clean', $type['description'])
				->file('icon', 'Icon', 'trim|required|xss_clean', $type['icon'])
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Type', array('class' => 'button positive'))
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
					'label' => 'Issue Types',
					'link' => 'administration/type'
				),
				array(
					'label' => 'Edit '.$type['name'].' Type',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_types_edit'), $type['name']);
			$this->build('administration/type/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Update database
			$this->type_model->update_type_by_id($id, $post);

			// Add a message
			$message = sprintf(lang('administration_success_types_edit'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/type', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete type page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Type ID not defined');
			show_error($error, 406);
		}

		// Get type info (for the success message)
		$type = $this->type_model->get_type_by_id($id);

		// How many issues are using this type?
		$count = $this->type_model->get_issue_count_in_type_id($id);

		// If there are no products with this type, delete the type
		if ( ! $count)
		{
			// Delete type
			$this->type_model->delete_type_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_types_delete'), $type['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/type', $this->registry->get_item('redirect'));
		}

		// If there are users with this type, notify the user that the type cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_types_cannot_delete'), $type['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Types',
					'link' => 'administration/type'
				),
				array(
					'label' => 'Cannot Delete Issue Type',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_types_delete'), $type['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}