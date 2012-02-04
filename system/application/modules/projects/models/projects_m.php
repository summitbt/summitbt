<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_m extends MY_Model {

	/**
	 * Total number of projects in the system
	 *
	 * @return int
	 */
	function get_total()
	{
		// Load the model
		$this->load->model('project_model');

		$projects = $this->project_model->get_all_projects();

		return count($projects);
	}

	// --------------------------------------------------------------------

	/**
	 * Get segmented list for pagination
	 *
	 * @param   int     $page
	 * @param   int     $count
	 * @return  array
	 */
	public function get_segmented($page = 1, $count = 200)
	{
		// Start at record
		$start = $page - 1;

		// Offset
		$offset = $page * $count;

		// All of the records
		$projects = $this->project_model->get_all_projects();

		// Make sure the offset is less than the total number of records
		if ($offset > count($projects))
		{
			$offset = count($projects);
		}

		return array_splice($projects, $start, $offset);
	}

}