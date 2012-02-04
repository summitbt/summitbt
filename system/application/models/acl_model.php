<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter ACL Class
 *
 * This class enables apply permissions to controllers, controller and models, as well as more fine tuned permissions '
 * at code level.
 *
 * @package     CodeIgniter
 * @subpackage  Models
 * @category    Models
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-acl
 */
class Acl_model extends MY_Model {

	/**
	 * Get permissions from database
	 *
	 * @param   int $role
	 * @return  array
	 */
	public function has_permission($role = 0)
	{
		$this->db->select("p.{$this->acl->acl_permissions_fields['key']} as k")
			->from($this->acl->acl_table_permissions.' p')
			->join($this->acl->acl_table_role_permissions.' rp', "rp.{$this->acl->acl_role_permissions_fields['permission_id']} = p.{$this->acl->acl_permissions_fields['id']}")
			->where("rp.{$this->acl->acl_role_permissions_fields['role_id']}", $role);

		$query = $this->db->get();

		return $query->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Get current user by session info
	 *
	 * @return  array
	 */
	public function user_role($user = 0)
	{
		$this->db->select("u.{$this->acl->acl_users_fields['role_id']} as role_id")
			->from($this->acl->acl_table_users.' u')
			->where("u.{$this->acl->acl_users_fields['id']}", $user);

		$query = $this->db->get();

		// User was found
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			return $row['role_id'];
		}

		// No role
		return 0;
	}

}
// END Acl_model class

/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */