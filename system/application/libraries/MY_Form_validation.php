<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	protected $CI;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->CI =& get_instance();

		// Load model
		$this->CI->load->model('validation_model');

		// Load language
		$this->CI->lang->load('system_form_validation');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns array of validation errors
	 *
	 * @return  array
	 */
	public function error_array()
	{
		return $this->_error_array;
	}

	// ------------------------------------------------------------------------

	/**
	 * Inject an additional validation rule on to validation rule that has already been set
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function inject_rule($field, $rules = '', $label = '')
	{
		// No reason to set rules if we have no POST data
		if (count($_POST) == 0)
		{
			return $this;
		}

		// No fields? Nothing to do...
		if ( ! is_string($field) OR  ! is_string($rules) OR $field == '')
		{
			return $this;
		}

		// Make sure the field has been set up already. If If not, create it instead of inject new rules
		if ( ! isset($this->_field_data[$field]))
		{
			$this->set_rules($field, $label, $rules);
		}
		// Rule exists, append the new rule
		else
		{
			$rules = $this->_field_data[$field]['rules'];

			foreach ((array)$rules as $rule)
			{
				if (strlen ($rules))
				{
					$rules .= '|';
				}
				$rules .= $rule;
			}

			$this->_field_data[$field]['rules'] = $rules;
		}

		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if a specific value is in use except when the value is attached to a specific row ID
	 *
	 * @param   string  $str
	 * @param   string  $field
	 * @return  bool
	 */
	public function is_unique_except($str, $field)
	{
		list($table, $column, $fld, $id) = explode('.', $field, 4);

		$this->CI->form_validation->set_message('is_unique_except', 'The %s that you requested is already in use.');

		return $this->CI->validation_model->is_unique_except($table, $column, $str, $fld, $id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Required if another field has a value (related fields)
	 *
	 * @access  public
	 * @param   string  $str
	 * @param   string  $field
	 * @return  bool
	 */
	public function required_if($str, $field)
	{
		list($fld, $val) = explode('.', $field, 2);

		$this->CI->form_validation->set_message('required_if', 'The %s field is required.');

		// $fld is filled out
		if ($this->CI->input->get_post($fld))
		{
			// Must have specific value
			if ($val)
			{
				// Not the specific value we are looking for
				if ($this->CI->input->get_post($fld) == $val AND ! $str)
				{
					return FALSE;
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is empty
	 *
	 * @param   string  $str
	 * @return  bool
	 */
	public function is_empty($str)
	{
		return ( ! empty($str));
	}

	// ------------------------------------------------------------------------

	/**
	* Validate URL
	*
	* @param    string  $str
	* @return   bool
	*/
	public function valid_url($str)
	{
		$this->CI->form_validation->set_message('valid_url', 'The %s field is not a valid URL.');

		$pattern = "/^((ht|f)tp(s?)\:\/\/|~/|/)?([w]{2}([\w\-]+\.)+([\w]{2,5}))(:[\d]{1,5})?/";

		return preg_match($pattern, $str);
	}

	// ------------------------------------------------------------------------

	/**
	 * Login
	 *
	 * @param   string  $str
	 * @return  bool
	 */
	public function login($str)
	{
		$this->CI->form_validation->set_message('login', 'Unable to log you in with the provided credentials.');

		$username = $this->CI->input->get_post('username');
		$password = $this->CI->input->get_post('password');
		$remember = $this->CI->input->get_post('remember');

		$user = $this->CI->auth->login($username, $password, $remember);

		return ($user) ? TRUE : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Forgot password
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function forgot($str)
	{
		$this->CI->form_validation->set_message('forgot', 'The %s was not found in the system.');

		$this->CI->load->helper('email');

		if (valid_email($str))
		{
			return ($this->CI->validation_model->find_by_field_value('users', 'user_email', $str)) ? TRUE : FALSE;
		}

		else
		{
			return ($this->CI->validation_model->find_by_field_value('users', 'user_username', $str)) ? TRUE : FALSE;
		}
	}

}
// END MY_Form_validation class

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */