<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Issues_m extends MY_Model {

	/**
	 * Total number of issues in the system
	 *
	 * @return int
	 */
	function get_total()
	{
		$this->db->select('issue_id')
			->from('issues');

		return $this->db->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * Get segmented issue list for pagination
	 *
	 * @param   int     $page
	 * @param   int     $count
	 * @return  array
	 */
	public function get_issues($page = 1, $count = 200)
	{
		// Offset
		$offset = ($page - 1) * $count;

		// Get records
		$this->db->select('
				i.issue_id as id,
				i.issue_parent_id as parent_id,
				i.issue_key as code,
				i.issue_summary as summary,
				i.issue_url as url,
				i.issue_description as description,
				i.issue_environment as environment,
				i.issue_assignee_id as assignee_id,
				i.issue_reporter_id as reporter_id,
				i.component_id as component_id,
				i.type_id as type_id,
				i.status_id as status_id,
				i.priority_id as priority_id,
				i.resolution_id as resolution_id,
				i.version_id as version_id,
				i.project_id as project_id,
				i.issue_date_due as date_due,
				i.issue_date_created as date_created,
				i.issue_date_modified as date_modified
			')
			->from('issues i')

			->join('projects p', 'p.project_id = i.project_id', 'left')
				->select('p.project_name as project_name, p.project_key as project_code')

			->join('users ua', 'ua.user_id = i.issue_assignee_id', 'left')
				->select('ua.user_first_name as assignee_first, ua.user_last_name as assignee_last, ua.user_username as assignee_username')

			->join('users ur', 'ur.user_id = i.issue_reporter_id', 'left')
				->select('ur.user_first_name as reporter_first, ur.user_last_name as reporter_last, ur.user_username as reporter_username')

			->join('types t', 't.type_id = i.type_id', 'left')
				->select('t.type_name as type')

			->join('statuses s', 's.status_id = i.status_id', 'left')
				->select('s.status_name as status')

			->join('resolutions r', 'r.resolution_id = i.resolution_id', 'left')
				->select('r.resolution_name as resolution')

			->join('priorities pr', 'pr.priority_id = i.priority_id', 'left')
				->select('pr.priority_name as priority')

			->join('components c', 'c.component_id = i.component_id AND c.project_id = p.project_id', 'left')
				->select('c.component_name as component')

			->join('versions v', 'v.version_id = i.component_id AND v.project_id = p.project_id', 'left')
				->select('v.version_name as version')

			->order_by('i.issue_date_modified', 'ASC')

			->limit($count, $offset);

		$query = $this->db->get();

		return $query->result_array();
	}

}