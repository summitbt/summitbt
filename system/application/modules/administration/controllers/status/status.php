<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-status', TRUE);

		// Load model
		$this->load->model('status_model');
	}
	// --------------------------------------------------------------------

	/**
	 * Status list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Status list page
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

		// Statuses
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->status_model->get_total();
		$data['statuses'] = $this->status_model->get_segmented($page, $count);
		$data['status_results'] = count($data['statuses']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/status/page'),
			'first_url' => site_url('administration/status'),
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
				'label' => 'Issue Statuses',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/status/list', lang('administration_title_statuses_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create status page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-status-create', TRUE);

		// File upload configurations
		$upload_config = array(
			'upload_path' => FCPATH.'uploads/icons/',
			'allowed_types' => 'gif|jpg|png'
		);

		$this->load->library('upload', $upload_config);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Status')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique[statuses.status_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->file('icon', 'Icon', 'trim|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Status', array('class' => 'button positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate() OR ! $this->upload->do_upload('icon'))
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
					'label' => 'Issue Statuses',
					'link' => 'administration/status'
				),
				array(
					'label' => 'New Issue Status',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/status/create', lang('administration_title_statuses_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create status
			$this->status_model->create_status($post);

			// Add a message
			$message = sprintf(lang('administration_success_statuses_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/status', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit status page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Status ID not defined');
			show_error($error, 406);
		}

		// Get status info
		$status = $this->status_model->get_status_by_id($id);

		// Status not found
		if ( ! $status)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Status ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-status-edit', $id);

		// File upload configurations
		$upload_config = array(
			'upload_path' => FCPATH.'uploads/icons/',
			'allowed_types' => 'gif|jpg|png'
		);

		$this->load->library('upload', $upload_config);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Status')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique_except[statuses.status_name.status_id.'.$id.']', $status['name'])
				->textarea('description', 'Description', 'trim|xss_clean', $status['description'])
				->file('icon', 'Icon', 'trim|xss_clean', $status['icon'])
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Status', array('class' => 'button positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate() OR ! $this->upload->do_upload('icon'))
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
					'label' => 'Issue Statuses',
					'link' => 'administration/status'
				),
				array(
					'label' => 'Edit '.$status['name'].' Status',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_statuses_edit'), $status['name']);
			$this->build('administration/status/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Uploaded file data
			$file = $this->upload->data();

			// Update database
			$this->status_model->update_status_by_id($id, $post, $file);

			// Add a message
			$message = sprintf(lang('administration_success_statuses_edit'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/status', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete status page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Status ID not defined');
			show_error($error, 406);
		}

		// Get status info (for the success message)
		$status = $this->status_model->get_status_by_id($id);

		// How many issues are using this status?
		$count = $this->status_model->get_issue_count_in_status_id($id);

		// If there are no products with this status, delete the status
		if ( ! $count)
		{
			// Delete status
			$this->status_model->delete_status_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_statuses_delete'), $status['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/status', $this->registry->get_item('redirect'));
		}

		// If there are users with this status, notify the user that the status cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_statuses_cannot_delete'), $status['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Statuses',
					'link' => 'administration/status'
				),
				array(
					'label' => 'Cannot Delete Issue Status',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_statuses_delete'), $status['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}