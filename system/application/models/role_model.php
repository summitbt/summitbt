<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_ROLES', 'roles');
define('CACHE_ROLE', 'role_%d');
define('CACHE_PERMISSIONS', 'permissions');
define('CACHE_ROLE_PERMISSIONS', 'role_%d_permissions');

class Role_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_roles());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all
	 *
	 * @return  array
	 */
	public function get_all_roles()
	{
		// Cache file name
		$cache_name = CACHE_ROLES;

		// Load in the cached values
		if ( ! $roles = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					role_id as id,
					role_name as name,
					role_description as description
				')
				->from('roles')
				->order_by('role_name', 'ASC')
				->get();

			$roles = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $roles, $this->registry->get_item('cache_lifetime'));
		}

		return $roles;
	}

	// --------------------------------------------------------------------

	/**
	 * Get segmented results
	 *
	 * @param   int     $page
	 * @param   int     $count
	 * @return  array
	 */
	public function get_segmented($page = 1, $count = 200)
	{
		$offset = ($page - 1) * $count;

		$query = $this->db->select('
				role_id as id,
				role_name as name,
				role_description as description
			')
			->from('roles')
			->order_by('role_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Get all permissions
	 *
	 * @return  array
	 */
	public function get_all_permissions()
	{
		// Cache file name
		$cache_name = CACHE_PERMISSIONS;

		// Load in the cached values
		if ( ! $permissions = $this->cache->get($cache_name))
		{
			$query = $this->db->select('p.permission_id as id, p.permission_name as name, p.permission_description as description')
				->from('permissions p')
				->join('permissiongroups pg', 'pg.permissiongroup_id = p.permissiongroup_id')
				->select('pg.permissiongroup_name as group_name, pg.permissiongroup_description as group_description')
				->order_by('pg.permissiongroup_order', 'ASC')
				->order_by('p.permission_order', 'ASC')
				->get();

			$permissions = array();

			foreach ($query->result_array() as $permission)
			{
				$permissions[$permission['group_name']][] = array(
					'id' => $permission['id'],
					'name' => $permission['name'],
					'description' => $permission['description'],
					'group_name' => $permission['group_name'],
					'group_description' => $permission['group_description']
				);
			}

			// Save into the cache
			$this->cache->save($cache_name, $permissions, $this->registry->get_item('cache_lifetime'));
		}

		return $permissions;
	}

	// --------------------------------------------------------------------

	/**
	 * Create role
	 *
	 * @return  array
	 */
	public function create_role()
	{
		$data = array(
			'role_name' => $this->input->post('name'),
			'role_description' => $this->input->post('description')
		);

		$this->db->insert('roles', $data);

		$id = $this->db->insert_id();

		// Add the permissions
		$data = array();

		foreach ($this->input->post('permissions', array()) as $permission)
		{
			$data[] = array(
				'role_id' => $id,
				'permission_id' => $permission
			);
		}

		if ( ! empty($data))
		{
			$this->db->insert_batch('role_permissions', $data);
		}

		// Clear the cache
		$this->cache->delete(CACHE_ROLES);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get role by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_role_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_ROLE, $id);

		// Load in the cached values
		if ( ! $role = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					role_id as id,
					role_name as name,
					role_description as description
				')
				->from('roles')
				->where('role_id', $id)
				->get();

			$role = FALSE;

			if ($query->num_rows() > 0)
			{
				$role = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $role, $this->registry->get_item('cache_lifetime'));
		}

		return $role;
	}

	// --------------------------------------------------------------------

	/**
	 * Update role by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function update_role_by_id($id = 0)
	{
		//
		// Data to to updated in database
		$data = array(
			'role_name' => $this->input->post('name'),
			'role_description' => $this->input->post('description')
		);

		// Update role
		$this->db->where('role_id', $id)
			->update('roles', $data);

		// Delete the role permissions
		$this->db->where('role_id', $id)
			->delete('role_permissions');

		// Add the permissions
		$data = array();

		foreach ($this->input->post('permissions', array()) as $permission)
		{
			$data[] = array(
				'role_id' => $id,
				'permission_id' => $permission
			);
		}

		if ( ! empty($data))
		{
			$this->db->insert_batch('role_permissions', $data);
		}

		// Clear the cache
		$this->cache->delete(CACHE_ROLES);

		$cache = sprintf(CACHE_ROLE, $id);
		$this->cache->delete($cache);

		$cache = sprintf(CACHE_ROLE_PERMISSIONS, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Number of users in this role
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_user_count_in_role_id($id = 0)
	{
		$this->db->select('user_id')
			->from('users')
			->where('role_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete role by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_role_by_id($id = 0)
	{
		// Delete the role
		$this->db->where('role_id', $id)
			->delete('roles');

		// Delete the role permissions
		$this->db->where('role_id', $id)
			->delete('role_permissions');

		// Clear the cache
		$this->cache->delete(CACHE_ROLES);

		$cache = sprintf(CACHE_ROLE, $id);
		$this->cache->delete($cache);

		$cache = sprintf(CACHE_ROLE_PERMISSIONS, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get role permissions
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_role_permissions($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_ROLE_PERMISSIONS, $id);


		// Load in the cached values
		if ( ! $permissions = $this->cache->get($cache_name))
		{
			$query = $this->db->select('rp.permission_id as id')
				->from('role_permissions rp')
				->join('permissions p', 'p.permission_id = rp.permission_id')
				->select('p.permission_name as name')
				->where('rp.role_id', $id)
				->get();

			$results = $query->result_array();

			$permissions = array();

			foreach ($results as $permission)
			{
				$permissions[$permission['name']] = $permission['id'];
			}

			// Save into the cache
			$this->cache->save($cache_name, $permissions, $this->registry->get_item('cache_lifetime'));
		}

		return $permissions;
	}

}