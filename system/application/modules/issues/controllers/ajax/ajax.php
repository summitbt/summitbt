<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends REST_Controller {

	/*
	 * Issue watchers
	 */
	public function filter_get()
	{
		// Watch of issue
		$issue = $this->get('filter');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

}