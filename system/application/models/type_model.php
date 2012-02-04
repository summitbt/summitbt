<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_TYPES', 'types');
define('CACHE_TYPE', 'type_%d');

class Type_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_types());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all types
	 *
	 * @return  array
	 */
	public function get_all_types()
	{
		// Cache file name
		$cache_name = CACHE_TYPES;

		// Load in the cached values
		if ( ! $types = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					type_id as id,
					type_name as name,
					type_description as description,
					type_icon as icon,
					type_date_created as date_created,
					type_date_modified as date_modified
				')
				->from('types')
				->order_by('type_name', 'ASC')
				->get();

			$types = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $types, $this->registry->get_item('cache_lifetime'));
		}

		return $types;
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
				type_id as id,
				type_name as name,
				type_description as description,
				type_icon as icon,
				type_date_created as date_created,
				type_date_modified as date_modified
			')
			->from('types')
			->order_by('type_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Create type
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_type($post = array())
	{
		$data = array(
			'type_name' => $post['name'],
			'type_description' => $post['description'],
			//'type_icon' => $post['icon'],
			'type_date_created' => date('Y-m-d H:i:s'),
			'type_date_modified' => date('Y-m-d H:i:s')
		);

		$this->db->insert('types', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_TYPES);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get type by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_type_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_TYPE, $id);

		// Load in the cached values
		if ( ! $type = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					type_id as id,
					type_name as name,
					type_description as description,
					type_icon as icon,
					type_date_created as date_created,
					type_date_modified as date_modified
				')
				->from('types')
				->where('type_id', $id)
				->get();

			$type = FALSE;

			if ($query->num_rows() > 0)
			{
				$type = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $type, $this->registry->get_item('cache_lifetime'));
		}

		return $type;
	}

	// --------------------------------------------------------------------

	/**
	 * Update type by ID
	 *
	 * @param   int     $id
	 * @param   array   $post
	 * @return  bool
	 */
	public function update_type_by_id($id = 0, $post = array())
	{
		// Data to to updated in database
		$data = array(
			'type_name' => $post['name'],
			'type_description' => $post['description'],
			//'type_icon' => $post['icon'],
			'type_date_modified' => date('Y-m-d H:i:s')
		);

		// Update type
		$this->db->where('type_id', $id)
			->update('types', $data);

		// Clear the cache
		$this->cache->delete(CACHE_TYPES);

		$cache = sprintf(CACHE_TYPE, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Issue count
	 *
	 * Number of issues using this type
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_issue_count_in_type_id($id = 0)
	{
		$this->db->select('type_id')
			->from('issues')
			->where('type_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete type by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_type_by_id($id = 0)
	{
		// Delete the type
		$this->db->where('type_id', $id)
			->delete('types');

		// Clear the cache
		$this->cache->delete(CACHE_TYPES);

		$cache = sprintf(CACHE_TYPE, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

}