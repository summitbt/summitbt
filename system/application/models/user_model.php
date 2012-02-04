<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_USERS', 'users');
define('CACHE_USER', 'user_%d');
define('CACHE_USER_META', 'user_%d_meta');

class User_model extends MY_Model {

	/**
	 * Get all users
	 *
	 * @return  array
	 */
	public function get_all_users()
	{
		// Cache file name
		$cache_name = CACHE_USERS;

		// Load in the cached values
		if ( ! $users = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					user_id as id,
					user_first_name as first_name,
					user_last_name as last_name
				')
				->from('users')
				->order_by('first_name', 'ASC')
				->order_by('last_name', 'ASC')
				->get();

			$users = array();

			foreach ($query->result_array() as $user)
			{
				$users[$user['id']] = $user['first_name'].' '.$user['last_name'];
			}

			// Save into the cache
			$this->cache->save($cache_name, $users, $this->registry->get_item('cache_lifetime'));
		}

		return $users;
	}

	// --------------------------------------------------------------------

	/**
	 * Get user by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_user_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_USER, $id);

		// Load in the cached values
		if ( ! $user = $this->cache->get($cache_name))
		{
			$user = $this->auth->get_user($id);

			// Save into the cache
			$this->cache->save($cache_name, $user, $this->registry->get_item('cache_lifetime'));
		}

		return $user;
	}

	// --------------------------------------------------------------------

	/**
	 * Get user meta by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_user_meta_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_USER_META, $id);

		// Load in the cached values
		if ( ! $user = $this->cache->get($cache_name))
		{
			$user = $this->auth->get_user_meta($id);

			// Save into the cache
			$this->cache->save($cache_name, $user, $this->registry->get_item('cache_lifetime'));
		}

		return $user;
	}

	// --------------------------------------------------------------------

	/**
	 * Check if user has permission
	 *
	 * @param   string  $key
	 * @return  array
	 */
	public function has_permission($key = '')
	{
		// Load model
		$this->load->model('role_model');

		$user_id = $this->auth->user_id();

		$permissions = $this->role_model->get_role_permissions($user_id);

		return array_key_exists(strtolower($key), $permissions);
	}

}