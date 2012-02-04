<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_m extends MY_Model {

	/**
	 * Create project
	 *
	 * @param   array   $post
	 * @return  array
	 */
	public function create_project($post = array())
	{
		$data = array(
			'project_key' => $post['key'],
			'project_name' => $post['name'],
			'project_description' => $post['description'],
			'project_url' => $post['url'],
			'project_icon' => $post['icon'],
			'project_lead_id' => $post['lead_id'],
			'project_default_assignee_id' => $post['default_assignee_id'],
			'project_date_created' => date('Y-m-d H:i:s'),
			'project_date_modified' => date('Y-m-d H:i:s')
		);

		$this->db->insert('projects', $data);

		$id = $this->db->insert_id();

		// Clear the cache
		$this->cache->delete(CACHE_PROJECTS);

		return $id;
	}

	// --------------------------------------------------------------------

}