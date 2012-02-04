<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_LINKS', 'links');
define('CACHE_LINK', 'link_%d');

class Link_model extends MY_Model {

	/**
	 * Get total count
	 *
	 * @return  int
	 */
	public function get_total()
	{
		return count($this->get_all_links());
	}

	// --------------------------------------------------------------------

	/**
	 * Get all links
	 *
	 * @return  array
	 */
	public function get_all_links()
	{
		// Cache file name
		$cache_name = CACHE_LINKS;

		// Load in the cached values
		if ( ! $links = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					link_id as id,
					link_name as name,
					link_outward as outward,
					link_inward as inward,
					link_date_created as date_created,
					link_date_modified as date_modified
				')
				->from('links')
				->order_by('link_name', 'ASC')
				->get();

			$links = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $links, $this->registry->get_item('cache_lifetime'));
		}

		return $links;
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
				link_id as id,
				link_name as name,
				link_outward as outward,
				link_inward as inward,
				link_date_created as date_created,
				link_date_modified as date_modified
			')
			->from('links')
			->order_by('link_name', 'ASC')
			->limit($count, $offset)
			->get();

		$results = $query->result_array();

		return $results;
	}

	// --------------------------------------------------------------------

	/**
	 * Create link
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_link($post = array())
	{
		$data = array(
			'link_name' => $post['name'],
			'link_inward' => $post['inward'],
			'link_outward' => $post['outward']
		);

		$this->db->insert('links', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_LINKS);

		return $id;
	}

	// --------------------------------------------------------------------

	/**
	 * Get link by ID
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_link_by_id($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_LINK, $id);

		// Load in the cached values
		if ( ! $link = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					link_id as id,
					link_name as name,
					link_inward as inward,
					link_outward as outward
				')
				->from('links')
				->where('link_id', $id)
				->get();

			$link = FALSE;

			if ($query->num_rows() > 0)
			{
				$link = $query->row_array();
			}

			// Save into the cache
			$this->cache->save($cache_name, $link, $this->registry->get_item('cache_lifetime'));
		}

		return $link;
	}

	// --------------------------------------------------------------------

	/**
	 * Update link by ID
	 *
	 * @param   int     $id
	 * @param   array   $post
	 * @return  bool
	 */
	public function update_link_by_id($id = 0, $post = array())
	{
		// Data to to updated in database
		$data = array(
			'link_name' => $post['name'],
			'link_inward' => $post['inward'],
			'link_outward' => $post['outward']
		);

		// Update link
		$this->db->where('link_id', $id)
			->update('links', $data);

		// Clear the cache
		$this->cache->delete(CACHE_LINKS);

		$cache = sprintf(CACHE_LINK, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Issue count
	 *
	 * Number of issues using this link
	 *
	 * @param   int     $id
	 * @return  int
	 */
	public function get_issue_count_in_link_id($id = 0)
	{
		$this->db->select('issue_link_id')
			->from('issue_links')
			->where('link_to_id', $id)
			->or_where('link_from_id', $id);

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete link by ID
	 *
	 * @param   int     $id
	 * @return  bool
	 */
	public function delete_link_by_id($id = 0)
	{
		// Delete the link
		$this->db->where('link_id', $id)
			->delete('links');

		// Clear the cache
		$this->cache->delete(CACHE_LINKS);

		$cache = sprintf(CACHE_LINK, $id);
		$this->cache->delete($cache);

		return TRUE;
	}

}