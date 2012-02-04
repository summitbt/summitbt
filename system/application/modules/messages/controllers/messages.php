<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('messages', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Messages
	 *
	 * @return  View
	 */
	public function index($page = 1)
	{
		// Set 'is'
		$this->is->query('messages-page', $page);

		// Pass to view
		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Messages',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('messages/list', lang('messages_title_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	public function sent($page = 1)
	{
		// Set 'is'
		$this->is->query('messages-sent', TRUE);

		// Pass to view
		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Messages',
				'link' => 'messages'
			),
			array(
				'label' => 'Sent Messages',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('messages/list', lang('messages_title_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	public function view($id = 1)
	{
		// Set 'is'
		$this->is->query('messages-view', $id);

		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Messages',
				'link' => 'messages'
			),
			array(
				'label' => 'MESSAGE TITLE',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('messages/message', lang('messages_title_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	public function create()
	{
		// Set 'is'
		$this->is->query('messages-create', TRUE);

		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Messages',
				'link' => 'messages'
			),
			array(
				'label' => 'New Message',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('messages/create', lang('messages_title_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	public function delete($id = 0)
	{
		echo 'DELETE MESSAGE '.$id;
	}

}