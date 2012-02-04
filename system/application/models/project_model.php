<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_PROJECTS', 'projects');
define('CACHE_PROJECT', 'project_%s');
define('CACHE_PROJECT_VERSIONS', 'project_%d_versions');
define('CACHE_PROJECT_COMPONENTS', 'project_%d_components');

class Project_model extends MY_Model {

	/**
	 * Get all projects
	 *
	 * @return  array
	 */
	public function get_all_projects()
	{
		// Cache file name
		$cache_name = CACHE_PROJECTS;

		// Load in the cached values
		if ( ! $projects = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					p.project_id as id,
					p.project_key as code,
					p.project_name as name,
					p.project_description as description,
					p.project_url as url,
					p.project_icon as icon,
					p.project_lead_id as lead_id,
					p.project_default_assignee_id as assignee,
					p.project_date_created as date_created,
					p.project_date_modified as date_modified
				')
				->from('projects p')
				->join('users u', 'u.user_id = p.project_lead_id')
				->select('
					u.user_first_name as lead_first_name,
					u.user_last_name as lead_last_name,
					u.user_username as lead_username
				')
				->order_by('p.project_name', 'ASC')
				->order_by('p.project_key', 'ASC')
				->get();

			$projects = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $projects, $this->registry->get_item('cache_lifetime'));
		}

		return $projects;
	}

	// --------------------------------------------------------------------

	/**
	 * Get project components
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_project_components($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_PROJECT_COMPONENTS, $id);

		// Load in the cached values
		if ( ! $components = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					component_id as id,
					component_name as name,
					component_description as description,
					component_date_created as date_created,
					component_date_modified as date_modified
				')
				->from('components')
				->where('project_id', $id)
				->order_by('component_name', 'ASC')
				->get();

			$components = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $components, $this->registry->get_item('cache_lifetime'));
		}

		return $components;
	}

	// --------------------------------------------------------------------

	/**
	 * Get project versions
	 *
	 * @param   int     $id
	 * @return  array
	 */
	public function get_project_versions($id = 0)
	{
		// Cache file name
		$cache_name = sprintf(CACHE_PROJECT_VERSIONS, $id);

		// Load in the cached values
		if ( ! $versions = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					version_id as id,
					version_name as name,
					version_description as description,
					version_date_due as date_due,
					version_date_created as date_created,
					version_date_modified as date_modified
				')
				->from('versions')
				->where('project_id', $id)
				->order_by('version_name', 'ASC')
				->get();

			$versions = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $versions, $this->registry->get_item('cache_lifetime'));
		}

		return $versions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get single project by code
	 *
	 * @param   string  $code
	 * @return  array
	 */
	public function get_project_by_code($code = '')
	{
		// Cache file name
		$cache_name = sprintf(CACHE_PROJECT, $code);

		// Load in the cached values
		if ( ! $project = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					p.project_id as id,
					p.project_key as code,
					p.project_name as name,
					p.project_description as description,
					p.project_url as url,
					p.project_icon as icon,
					p.project_lead_id as lead_id,
					p.project_default_assignee_id as assignee,
					p.project_date_created as date_created,
					p.project_date_modified as date_modified
				')
				->from('projects p')
				->join('users u', 'u.user_id = p.project_lead_id')
				->select('
					u.user_first_name as lead_first_name,
					u.user_last_name as lead_last_name,
					u.user_username as lead_username
				')
				->where('p.project_key', $code)
				->get();

			$project = $query->row_array();

			// Save into the cache
			$this->cache->save($cache_name, $project, $this->registry->get_item('cache_lifetime'));
		}

		return $project;
	}

}