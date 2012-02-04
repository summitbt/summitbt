<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('access', TRUE);

		// Load Javascript
		$this->assets->js('general', 'js/access.js?v='.$this->config->item('app_version'), 'assets');
	}

	// --------------------------------------------------------------------

	/**
	 * User login
	 *
	 * @return  View
	 */
	public function index()
	{
		// Already logged in
		if ($this->auth->logged_in())
		{
			redirect('');
		}

		// Set 'is'
		$this->is->query('access-login', TRUE);

		// Login form
		$form = $this->formation->action()
			->honeypot('user', 'is_empty')
			->fieldset_open(lang('access_fieldset_login'))
				->text('username', lang('access_field_username'), 'trim|login|required|xss_clean')
				->password('password', lang('access_field_password'), 'trim|required|xss_clean')
				->checkbox('remember', lang('access_field_remember'), '1', 'trim')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', lang('access_button_login'), array('class' => 'button pill positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'access' => array(
						'login' => $form->generate()
					)
				)
			);

			// Build the page
			$this->build('access/login', lang('access_title_login'), $data);
		}

		// Form validated
		else
		{
			// Send the user information to auth to create session
			// Custom form validation "login" has already done this for us. Happy day!

			// Redirect
			redirect('');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * User forgot username or password
	 *
	 * @return  View
	 */
	public function forgot()
	{
		// Already logged in
		if ($this->auth->logged_in())
		{
			redirect('');
		}

		// Set 'is'
		$this->is->query('access-forgot', TRUE);

		// Forgot password form
		$form = $this->formation->action()
			->honeypot('user', 'is_empty')
			->fieldset_open(lang('access_fieldset_forgot'))
				->text('username', lang('access_field_username'), 'trim|forgot|required|xss_clean')
			->fieldset_close()
			->block('<div class="actions">')
				->submit('submit', lang('access_button_forgot'), array('class' => 'button pill positive'))
			->block('</div>');

		// Form not validated
		if ( ! $form->validate())
		{
			// Pass to view
			$data = array(
				'form' => array(
					'access' => array(
						'forgot' => $form->generate()
					)
				)
			);

			// Build the page
			$this->build('access/forgot', lang('access-forgot-title'), $data);
		}

		// Form validated
		else
		{
			// Find the user by the username/email
			$id = $this->auth->get_by_username($this->input->post('username'));

			if ($id === FALSE)
			{
				// Add a message to say it has been sent
				$this->message->info(lang('access_forgot_no_user'));

				// Redirect
				redirect('forgot');
			}

			else
			{
				// Load the library
				$this->load->library('responder');

				// Variables for the email
				$vars = array (
					'site_url' => site_url(),
					'base_url' => base_url(),
					'password' => site_url($id.'/'.md5($id))
				);

				// Get user information
				$user = $this->user_model->get_user_by_id($id);

				// Sent the email
				$email_success = $this->responder->to($user['email'])
					->vars($vars)
					->send('forgot-password');

				// Email failed to send
				if ($email_success !== TRUE)
				{
					// Message to display to the user
					$message = sprintf(lang('access_forgot_error'), $email_success);

					// Add a message to say it has been sent
					$this->message->warning($message);

					// Redirect
					redirect('forgot');
				}

				// Email sent successfully
				else
				{
					// Add a message to say it has been sent
					$this->message->success(lang('access_forgot_sent'));

					// Redirect
					redirect('login');
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Reset password
	 *
	 * @return  View
	 */
	public function reset($username = '', $key = '')
	{
		// Already logged in
		if ($this->auth->logged_in())
		{
			redirect('');
		}

		// Set 'is'
		$this->is->query('access-reset', TRUE);

		// Load library
		$this->load->library('encrypt');

		// Key not specified or doesn't match the user
		if ( ! $username OR ! $key OR $this->auth->hash_password($username) != $key)
		{
			// Pass to view
			$data = array(
				'error' => array(
					'headline' => lang('error_headline_general'),
					'message' => lang('error_body_reset_key')
				)
			);

			// Build the page
			$this->build('error/error', lang('error_title_general'), $data);
		}

		// Known
		else
		{
			// Login form
			$form = $this->formation->action()
				->honeypot('user', 'is_empty')
				->fieldset_open(lang('access_fieldset_reset'))
					->password('password', lang('access_field_password'), 'trim|required|xss_clean')
					->password('password2', lang('access_field_password2'), 'trim|required|matches[password]|xss_clean')
				->fieldset_close()
				->block('<div class="actions">')
					->submit('submit', lang('access_button_reset'), array('class' => 'button pill positive'))
				->block('</div>');

			// Form not validated
			if ( ! $form->validate())
			{
				// Pass to view
				$data = array(
					'form' => array(
						'access' => array(
							'reset' => $form->generate()
						)
					)
				);

				// Build the page
				$this->build('access/reset', lang('access_title_reset'), $data);
			}

			// Form validated
			else
			{
				// Find the user ID
				$id = $this->auth->get_by_username($username);

				// Change password
				$this->auth->change_password($id, $this->input->post('password'));

				// Get user information
				$user = $this->user_model->get_user_by_id($id);

				// Log the user in
				$this->auth->login($user['username'], $this->input->post('password'));

				// Redirect
				redirect('login');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Logout
	 *
	 * @return  void
	 */
	public function logout()
	{
		// Kill session and cookie
		$this->auth->logout();

		// Add a message
		$this->message->now(lang('access_logout_success'), 'success');

		// Redirect
		redirect('login');
	}

}