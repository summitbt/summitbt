<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Constants
define('CACHE_MODULES', 'modules');

class Modules_model extends MY_Model {

	/**
	 * Get all links
	 *
	 * @return  array
	 */
	public function get_all_modules()
	{
		// Cache file name
		$cache_name = CACHE_MODULES;

		// Load in the cached values
		if ( ! $modules = $this->cache->get($cache_name))
		{
			$query = $this->db->select('
					module_id as id,
					module_name as name,
					module_config as config,
					module_date_activated as date_activated
				')
				->from('modules')
				->order_by('module_name', 'ASC')
				->get();

			$modules = $query->result_array();

			// Save into the cache
			$this->cache->save($cache_name, $modules, $this->registry->get_item('cache_lifetime'));
		}

		return $modules;
	}

}