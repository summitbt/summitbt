<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration-role', TRUE);

		// Load model
		$this->load->model('role_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Role list
	 *
	 * @return view
	 */
	public function index()
	{
		$this->page();
	}

	// --------------------------------------------------------------------

	/**
	 * Role list page
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

		// Roles
		$count = $this->registry->get_item('result_count');
		$data['total'] = $this->role_model->get_total();
		$data['roles'] = $this->role_model->get_segmented($page, $count);
		$data['role_results'] = count($data['roles']);

		// Pagination
		$pagination_config = array(
			'base_url' => site_url('administration/role/page'),
			'first_url' => site_url('administration/role'),
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
				'label' => 'Roles',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/role/list', lang('administration_title_roles_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Create role page
	 *
	 * @return  View
	 */
	public function create()
	{
		// Set 'is'
		$this->is->query('administration-role-create', TRUE);

		// Get all of the permissions
		$all_permissions = $this->role_model->get_all_permissions();

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Create Role')
				->text('name', 'Name', 'trim|required|is_unique[roles.role_name]')
				->textarea('description', 'Description', 'trim|xss_clean')
				->fieldset_open('Permissions');

		foreach ($all_permissions as $group => $permissions)
		{
			$perm = array();

			$form->fieldset_open($group);

			foreach ($permissions as $index => $permission)
			{
				// Group description if there is one
				if ($index == 0 AND $permission['group_description'])
				{
					$form->block('<p class="permission-description">'.$permission['group_description'].'</p>');
				}

				$perm[] = array(
					'label' => $permission['description'],
					'value' => $permission['id'],
					'checked' => FALSE
				);
			}

			$form->checkboxes('permissions', 'Permissions', $perm, 'trim')
				->fieldset_close();
		}

		$form->fieldset_close()
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Create Role', array('class' => 'button positive'))
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
					'label' => 'Roles',
					'link' => 'administration/role'
				),
				array(
					'label' => 'New Role',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('administration/role/create', lang('administration_title_roles_create'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Create role
			$this->role_model->create_role();

			// Add a message
			$message = sprintf(lang('administration_success_roles_create'), $this->input->post('name'));
			$this->message->success($message);

			// Redirect
			redirect('administration/role', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit role page
	 *
	 * @return  View
	 */
	public function edit($id = '')
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Role ID not defined');
			show_error($error, 406);
		}

		// Get role info
		$role = $this->role_model->get_role_by_id($id);

		// Role not found
		if ( ! $role)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Role ID not found');
			show_error($error, 406);
		}

		// Set 'is'
		$this->is->query('administration-role-edit', $id);

		// Get all of the permissions
		$all_permissions = $this->role_model->get_all_permissions();

		// This roles current permissions
		$role_permissions = $this->role_model->get_role_permissions($id);

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open('Edit Role')
				->text('name', 'Name', 'trim|required|is_unique_except[roles.role_name.role_id.'.$id.']', $role['name'])
				->textarea('description', 'Description', 'trim|xss_clean', $role['description'])
				->fieldset_open('Permissions');

		foreach ($all_permissions as $group => $permissions)
		{
			$perm = array();

			$form->fieldset_open($group);

			foreach ($permissions as $index => $permission)
			{
				// Group description if there is one
				if ($index == 0 AND $permission['group_description'])
				{
					$form->block('<p class="permission-description">'.$permission['group_description'].'</p>');
				}

				$selected = (in_array($permission['id'], $role_permissions)) ? TRUE : FALSE;

				$perm[] = array(
					'label' => $permission['description'],
					'value' => $permission['id'],
					'checked' => $selected
				);
			}

			$form->checkboxes('permissions', 'Permissions', $perm, 'trim')
				->fieldset_close();
		}

		$form->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', 'Update Role', array('class' => 'button positive'))
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
					'label' => 'Roles',
					'link' => 'administration/role'
				),
				array(
					'label' => 'Edit '.$role['name'].' Role',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_roles_edit'), $role['name']);
			$this->build('administration/role/edit', $title, $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Update database
			$this->role_model->update_role_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_roles_edit'), $this->input->post('name'));
			$this->message->success($message);

			// Redirect
			redirect('administration/role', $this->registry->get_item('redirect'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete role page
	 *
	 * @return  View
	 */
	public function delete($id = 0)
	{
		// ID not defined
		if ( ! $id)
		{
			$error = sprintf(lang('error_not_acceptable'), 'Role ID not defined');
			show_error($error, 406);
		}

		// Get role info (for the success message)
		$role = $this->role_model->get_role_by_id($id);

		// How many users are using this role?
		$count = $this->role_model->get_user_count_in_role_id($id);

		// If there are no products with this role, delete the role
		if ( ! $count)
		{
			// Delete role
			$this->role_model->delete_role_by_id($id);

			// Add a message
			$message = sprintf(lang('administration_success_roles_delete'), $role['name']);
			$this->message->success($message);

			// Redirect
			referrer_redirect('administration/role', $this->registry->get_item('redirect'));
		}

		// If there are users with this role, notify the user that the role cannot be deleted
		else
		{
			// Pass to view
			$data = array();

			// Error message
			$message = sprintf(lang('administration_body_roles_cannot_delete'), $role['name'], $count);
			$data['error'] = $message;

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'Administration',
					'link' => 'administration'
				),
				array(
					'label' => 'Roles',
					'link' => 'administration/role'
				),
				array(
					'label' => 'Cannot Delete Role',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$title = sprintf(lang('administration_title_roles_delete'), $role['name']);
			$this->build('error/error', $title, $data, $breadcrumbs);
		}
	}

}