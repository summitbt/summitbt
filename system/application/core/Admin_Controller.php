<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends Template_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Set 'is'
		$this->is->query('administration', TRUE);
	}

}