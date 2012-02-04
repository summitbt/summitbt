<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends REST_Controller {

	/*
	 * Issue watchers
	 */
	public function watchers_get()
	{
		// Watchers of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(NULL, 400);
		}

		$watchers = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);

		if( ! empty($watchers))
		{
			$this->response($watchers, 200);
		}

		else
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Watch issue
	 */
	public function watch_get()
	{
		// Watch of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue voters
	 */
	public function voters_get()
	{
		// Voters of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(NULL, 400);
		}

		$voters = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);

		if( ! empty($voters))
		{
			$this->response($voters, 200);
		}

		else
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Vote for issue
	 */
	public function vote_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue comments
	 */
	public function comments_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue worklog
	 */
	public function worklog_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue history
	 */
	public function history_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue activity
	 */
	public function activity_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue all (activity, history, worklog, comments)
	 */
	public function all_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

		if( ! $issue)
		{
			$this->response(array('error' => 'Unable to find issue'), 404);
		}

		else
		{
			$this->response(TRUE, 200);
		}
	}

	// --------------------------------------------------------------------

	/*
	 * Issue attachment
	 */
	public function attach_get()
	{
		// Vote for of issue
		$issue = $this->get('issue');

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