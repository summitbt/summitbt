<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_RESOLUTIONS', 'resolutions');
define('CACHE_RESOLUTION', 'resolution_%d');

class Resolution_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_resolutions());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all links
	 *
	 * @return  array
	 */
	public function get_all_resolutions()
	{
		// Cache file name
		$cache_name = CACHE_RESOLUTIONS;

		// Load in the cached values
		if ( ! $resolutions = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					resolution_id as id,
					resolution_name as name,
					resolution_description as description,
					resolution_icon as icon,
					resolution_order as sort,
					resolution_date_created as date_created,
					resolution_date_modified as date_modified
				')
				->from('resolutions')
				->order_by('resolution_order', 'ASC')
				->order_by('resolution_name', 'ASC')
				->get();

			$resolutions = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $resolutions, $this->registry->get_item('cache_lifetime'));
		}

		return $resolutions;
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
				resolution_id as id,
				resolution_name as name,
				resolution_description as description,
				resolution_icon as icon,
				resolution_date_created as date_created,
				resolution_date_modified as date_modified
			')
			->from('resolutions')
			->order_by('resolution_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Create resolution
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_resolution($post = array())
	{
		$data = array(
			'resolution_name' => $post['name'],
			'resolution_description' => $post['description'],
			//'resolution_icon' => $post['icon'],
			'resolution_date_created' => date('Y-m-d H:i:s'),
			'resolution_date_modified' => date('Y-m-d H:i:s')
		);

		$this->db->insert('resolutions', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_RESOLUTIONS);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get resolution by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_resolution_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_RESOLUTION, $id);

		// Load in the cached values
		if ( ! $resolution = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					resolution_id as id,
					resolution_name as name,
					resolution_description as description,
					resolution_icon as icon,
					resolution_date_created as date_created,
					resolution_date_modified as date_modified
				')
				->from('resolutions')
				->where('resolution_id', $id)
				->get();

			$resolution = FALSE;

			if ($query->num_rows() > 0)
			{
				$resolution = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $resolution, $this->registry->get_item('cache_lifetime'));
		}

		return $resolution;
	}

	// --------------------------------------------------------------------

	/**
	 * Update resolution by ID
	 *
	 * @param   int     $id
	 * @param   array   $post
	 * @return  bool
	 */
	public function update_resolution_by_id($id = 0, $post = array())
	{
		// Data to to updated in database
		$data = array(
			'resolution_name' => $post['name'],
			'resolution_description' => $post['description'],
			//'resolution_icon' => $post['icon'],
			'resolution_date_modified' => date('Y-m-d H:i:s')
		);

		// Update resolution
		$this->db->where('resolution_id', $id)
			->update('resolutions', $data);

		// Clear the cache
		$this->cache->delete(CACHE_RESOLUTIONS);

		$cache = sprintf(CACHE_RESOLUTION, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Issue count
	 *
	 * Number of issues using this resolution
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_issue_count_in_resolution_id($id = 0)
	{
		$this->db->select('resolution_id')
			->from('issues')
			->where('resolution_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete resolution by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_resolution_by_id($id = 0)
	{
		// Delete the resolution
		$this->db->where('resolution_id', $id)
			->delete('resolutions');

		// Clear the cache
		$this->cache->delete(CACHE_RESOLUTIONS);

		$cache = sprintf(CACHE_RESOLUTION, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

}