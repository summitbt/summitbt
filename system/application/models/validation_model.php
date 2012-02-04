<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Validation_model extends MY_Model {

	/**
	 * Check if a specific value is in use except when the value is attached to a specific row ID
	 *
	 * @param   string  $table
	 * @param   string  $field
	 * @param   string  $value
	 * @param   string  $key
	 * @param   int|string  $id
	 * @return  bool
	 */
	public function is_unique_except($table, $field, $value, $key, $id)
	{
		$this->db->select('*')
			->from($table)
			->where($field, $value)
			->where($key.' !=', $id);

		$count = $this->db->count_all_results();

		return ($count) ? FALSE : TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Find by value
	 *
	 * @param   string  $table
	 * @param   string  $column
	 * @param   string  $value
	 * @return  bool
	 */
	public function find_by_field_value($table = '', $column = '', $value = '')
	{
		$this->db->select('user_id')
			->from($table)
			->where($column, $value);

		$query = $this->db->get();

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			return $row['user_id'];
		}

		return FALSE;
	}
}