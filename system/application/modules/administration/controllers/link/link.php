<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Link extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-link', TRUE);

		// Load model
		$this->load->model('link_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Link list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Link list page
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

		// Links
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->link_model->get_total();
		$data['links'] = $this->link_model->get_segmented($page, $count);
		$data['link_results'] = count($data['links']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/link/page'),
			'first_url' => site_url('administration/link'),
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
				'label' => 'Issue Links',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/link/list', lang('administration_title_links_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create link page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-link-create', TRUE);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Link')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique[links.link_name]')
				->text('inward', 'Inward', 'trim|required|xss_clean')
				->text('outward', 'Outward', 'trim|required|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Link', array('class' => 'button positive'))
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
					'label' => 'Issue Links',
					'link' => 'administration/link'
				),
				array(
					'label' => 'New Issue Link',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/link/create', lang('administration_title_links_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Create link
			$this->link_model->create_link($post);

			// Add a message
			$message = sprintf(lang('administration_success_link_create'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/link', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit link page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Link ID not defined');
			show_error($error, 406);
		}

		// Get link info
		$link = $this->link_model->get_link_by_id($id);

		// Link not found
		if ( ! $link)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Link ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-link-edit', $id);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Link')
				->text('name', 'Name', 'trim|required|xss_clean|is_unique_except[links.link_name.link_id.'.$id.']', $link['name'])
				->text('inward', 'Inward', 'trim|required|xss_clean', $link['inward'])
				->text('outward', 'Outward', 'trim|required|xss_clean', $link['outward'])
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Link', array('class' => 'button positive'))
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
					'label' => 'Issue Links',
					'link' => 'administration/link'
				),
				array(
					'label' => 'Edit '.$link['name'].' Link',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_links_edit'), $link['name']);
			$this->build('administration/link/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Post data
			$post = $this->input->post();

			// Update database
			$this->link_model->update_link_by_id($id, $post);

			// Add a message
			$message = sprintf(lang('administration_success_links_edit'), $post['name']);
			$this->message->success($message);

			// Redirect
			redirect('administration/link', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete link page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Link ID not defined');
			show_error($error, 406);
		}

		// Get link info (for the success message)
		$link = $this->link_model->get_link_by_id($id);

		// How many issues are using this link?
		$count = $this->link_model->get_issue_count_in_link_id($id);

		// If there are no products with this link, delete the link
		if ( ! $count)
		{
			// Delete link
			$this->link_model->delete_link_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_links_delete'), $link['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/link', $this->registry->get_item('redirect'));
		}

		// If there are users with this link, notify the user that the link cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_links_cannot_delete'), $link['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Issue Links',
					'link' => 'administration/link'
				),
				array(
					'label' => 'Cannot Delete Issue Link',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_links_delete'), $link['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}