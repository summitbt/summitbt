<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('profile', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * User profile
	 *
	 * @return  View
	 */
	public function index()
	{
		// Set 'is'
		$this->is->query('profile-user', $this->auth->user_id());

		// Get user data
		$data = $this->user_model->get_user_by_id($this->auth->user_id());

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open(lang('profile_fieldset_profile'))
				->text('first_name', lang('profile_field_first_name'), 'trim|required|xss_clean', $data['first_name'])
				->text('last_name', lang('profile_field_last_name'), 'trim|required|xss_clean', $data['last_name'])
				->text('email', lang('profile_field_email'), 'trim|required|valid_email|is_unique_except[users.user_email.user_id.'.$this->auth->user_id().']', $data['email'])
				->text('username', lang('profile_field_username'), 'trim', $data['username'], array('readonly' => 'readonly'))
			->fieldset_close()

			->fieldset_open(lang('profile_fieldset_password'))
				->block(lang('profile_password_change_description'))
				->password('password', lang('profile_field_password'), 'trim|min_length[6]|matches[password2]')
				->password('password2', lang('profile_field_password2'), 'trim|matches[password]')
			->fieldset_close()

			->block('<div class="actions">')
				->submit('submit', lang('profile_button_profile'), array('class' => 'button positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'profile' => $form->generate()
				)
			);

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'My Profile',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('profile/profile', lang('profile_title_profile'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Update the user information
			$this->profile_m->update_profile();

			// Add a message
			$this->message->success(lang('profile_profile_success'));

			// Redirect
			redirect('profile');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * User preferences
	 *
	 * @return  View
	 */
	public function preferences()
	{
		// Set 'is'
		$this->is->query('profile-preferences', TRUE);

		// Get user data
		$user = $this->user_model->get_user_by_id($this->auth->user_id());

		// Fields for the form
		$form = $this->formation->form()
			->fieldset_open(lang('profile_fieldset_preferences'))
//				->text('first_name', 'First Name', 'trim|required|xss_clean', $data['first_name'])
//				->text('last_name', 'Last Name', 'trim|required|xss_clean', $data['last_name'])
//				->text('email', 'Email', 'trim|required|valid_email|is_unique_except[Users,Email,UserID,'.$this->auth->user_id().']', $data['email'])
//				->text('username', 'Username', 'trim', $data['username'], array('readonly' => 'readonly'))
//				->fieldset_open('Change Password')
//					->block('<p>You only need to fill out this section if you want to change your current password. If you do not want to change your password, you do not need to fill out these fields.</p>')
//					->password('password', 'Password', 'trim|min_length[6]|matches[password2]')
//					->password('password2', 'Confirm Password', 'trim')
//				->fieldset_close()
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', lang('profile_button_preferences'), array('class' => 'button positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'preferences' => $form->generate()
				)
			);

			// Breadcrumbs
			$breadcrumbs = array(
				array(
					'label' => 'My Profile',
					'link' => 'profile'
				),
				array(
					'label' => 'My Preferences',
					'link' => $this->uri->uri_string()
				)
			);

			// Build the page
			$this->build('profile/preferences', lang('profile_title_preferences'), $data, $breadcrumbs);
		}

		// Form validated
		else
		{
			// Update the user information
			$this->profile_m->update_profile();

			// Add a message
			$this->message->success(lang('profile_preferences_success'));

			// Redirect
			redirect('profile/preferences', $this->registry->get_item('redirect'));
		}
	}

}