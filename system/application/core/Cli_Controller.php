<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		// Only accessible through CLI requests
		if ( ! $this->input->is_cli_request() AND ! $this->config->item('http_cli_requests'))
		{
			show_error('This page is only accessible from the CLI');
		}
	}

}