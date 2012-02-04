<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_STATUSES', 'statuses');
define('CACHE_STATUS', 'status_%d');

class Status_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_statuses());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all statuses
	 *
	 * @return  array
	 */
	public function get_all_statuses()
	{
		// Cache file name
		$cache_name = CACHE_STATUSES;

		// Load in the cached values
		if ( ! $statuses = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					status_id as id,
					status_name as name,
					status_description as description,
					status_icon as icon,
					status_order as sort,
					status_date_created as date_created,
					status_date_modified as date_modified
				')
				->from('statuses')
				->order_by('status_order', 'ASC')
				->order_by('status_name', 'ASC')
				->get();

			$statuses = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $statuses, $this->registry->get_item('cache_lifetime'));
		}

		return $statuses;
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
				status_id as id,
				status_name as name,
				status_description as description,
				status_icon as icon,
				status_order as sort,
				status_date_created as date_created,
				status_date_modified as date_modified
			')
			->from('statuses')
			->order_by('status_order', 'ASC')
			->order_by('status_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Create status
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_status($post = array())
	{
		$data = array(
			'status_name' => $post['name'],
			'status_description' => $post['description'],
			//'status_icon' => $post['icon'],
			'status_date_created' => date('Y-m-d H:i:s'),
			'status_date_modified' => date('Y-m-d H:i:s')
		);

		$this->db->insert('statuses', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_STATUSES);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get status by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_status_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_STATUS, $id);

		// Load in the cached values
		if ( ! $status = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					status_id as id,
					status_name as name,
					status_description as description,
					status_icon as icon,
					status_order as sort,
					status_date_created as date_created,
					status_date_modified as date_modified
				')
				->from('statuses')
				->where('status_id', $id)
				->get();

			$status = FALSE;

			if ($query->num_rows() > 0)
			{
				$status = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $status, $this->registry->get_item('cache_lifetime'));
		}

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Update status by ID
	 *
	 * @param   int     $id
	 * @param   array   $post
	 * @param   array   $file
	 * @return  bool
	 */
	public function update_status_by_id($id = 0, $post = array(), $file = array())
	{
		// Data to to updated in database
		$data = array(
			'status_name' => $post['name'],
			'status_description' => $post['description'],
			//'status_icon' => $post['icon'],
			'status_date_modified' => date('Y-m-d H:i:s')
		);

		// Only if a file was uploaded
		if ( ! empty($file))
		{
			$data['status_icon'] = $file['file_name'];
		}

		// Update status
		$this->db->where('status_id', $id)
			->update('statuses', $data);

		// Clear the cache
		$this->cache->delete(CACHE_STATUSES);

		$cache = sprintf(CACHE_STATUS, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Issue count
	 *
	 * Number of issues using this status
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_issue_count_in_status_id($id = 0)
	{
		$this->db->select('status_id')
			->from('issues')
			->where('status_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete status by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_status_by_id($id = 0)
	{
		// Delete the status
		$this->db->where('status_id', $id)
			->delete('statuses');

		// Clear the cache
		$this->cache->delete(CACHE_STATUSES);

		$cache = sprintf(CACHE_STATUS, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

}