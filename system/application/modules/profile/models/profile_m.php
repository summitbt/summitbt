<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_m extends MY_Model {

	/**
	 * Update user profile
	 *
	 * @return  bool
	 */
	public function update_profile()
	{
		// User ID
		$id = $this->auth->user_id();

		// Posted data
		$post = $this->input->post();

		// Is there a value for the password? If so, update their password
		if ($this->input->post('password'))
		{
			$this->auth->change_password($id, $this->input->post('password'));
		}

		// New data
		$data = array(
			'user_name' => $post['name'],
			'user_email' => $post['email']
		);

		// Set the new data
		$this->auth->update_user($id, $data);

		// Clear the cache
		$cache_name = sprintf(CACHE_USER, $id);
		$this->cache->delete($cache_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Update user preferences
	 *
	 * @return  bool
	 */
	public function update_preferences()
	{
		// User ID
		$id = $this->auth->user_id();

		// Posted data
		$post = $this->input->post();

		// New data
		//$data = array(
			//'user_name' => $post['name'],
			//'user_email' => $post['email']
		//);

		// Set the new data
		//$this->auth->update_user($id, $data);

		// Clear the cache
		$cache_name = sprintf(CACHE_USER, $id);
		$this->cache->delete($cache_name);
	}

}