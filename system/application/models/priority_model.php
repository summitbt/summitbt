<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_PRIORITIES', 'priorities');
define('CACHE_PRIORITY', 'priority_%d');

class Priority_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_priorities());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all priorities
	 *
	 * @return  array
	 */
	public function get_all_priorities()
	{
		// Cache file name
		$cache_name = CACHE_PRIORITIES;

		// Load in the cached values
		if ( ! $priorities = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					priority_id as id,
					priority_name as name,
					priority_description as description,
					priority_color as color,
					priority_icon as icon,
					priority_order as sort,
					priority_date_created as date_created,
					priority_date_modified as date_modified
				')
				->from('priorities')
				->order_by('priority_order', 'ASC')
				->order_by('priority_name', 'ASC')
				->get();

			$priorities = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $priorities, $this->registry->get_item('cache_lifetime'));
		}

		return $priorities;
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
				priority_id as id,
					priority_name as name,
					priority_description as description,
					priority_color as color,
					priority_icon as icon,
					priority_order as sort,
					priority_date_created as date_created,
					priority_date_modified as date_modified
			')
			->from('priorities')
			->order_by('priority_order', 'ASC')
			->order_by('priority_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Create priority
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_priority($post = array())
	{
		$data = array(
			'priority_name' => $post['name'],
			'priority_description' => $post['description'],
			'priority_color'=> $post['color'],
			'priority_icon' => $post['icon'],
			'priority_date_created' => date('Y-m-d H:i:s'),
			'priority_date_modified' => date('Y-m-d H:i:s')
		);

		$this->db->insert('priorities', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_PRIORITIES);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get priority by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_priority_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_PRIORITY, $id);

		// Load in the cached values
		if ( ! $priority = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					priority_name as name,
					priority_description as description,
					priority_color as color,
					priority_icon as icon,
					priority_order as sort,
					priority_date_created as date_created,
					priority_date_modified as date_modified
				')
				->from('priorities')
				->where('priority_id', $id)
				->get();

			$priority = FALSE;

			if ($query->num_rows() > 0)
			{
				$priority = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $priority, $this->registry->get_item('cache_lifetime'));
		}

		return $priority;
	}

	// --------------------------------------------------------------------

	/**
	 * Update priority by ID
	 *
	 * @param   int     $id
	 * @param   array   $post
	 * @return  bool
	 */
	public function update_priority_by_id($id = 0, $post = array())
	{
		// Data to to updated in database
		$data = array(
			'priority_name' => $post['name'],
			'priority_description' => $post['description'],
			'priority_color'=> $post['color'],
			'priority_icon' => $post['icon'],
			'priority_date_modified' => date('Y-m-d H:i:s')
		);

		// Update priority
		$this->db->where('priority_id', $id)
			->update('priorities', $data);

		// Clear the cache
		$this->cache->delete(CACHE_PRIORITIES);

		$cache = sprintf(CACHE_PRIORITY, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Issue count
	 *
	 * Number of issues using this priority
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_issue_count_in_priority_id($id = 0)
	{
		$this->db->select('priority_id')
			->from('issues')
			->where('priority_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete priority by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_priority_by_id($id = 0)
	{
		// Delete the priority
		$this->db->where('priority_id', $id)
			->delete('priorities');

		// Clear the cache
		$this->cache->delete(CACHE_PRIORITIES);

		$cache = sprintf(CACHE_PRIORITY, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

}