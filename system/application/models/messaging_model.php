<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messaging_model extends MY_Model {

	public function delete_message($id = 0)
	{
		$result = $this->db->where('id', $id)
			->delete('mytable');

		return $result;
	}

}
