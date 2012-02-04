<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Message Class
 *
 * This class allows you to create valid HTML forms, with validation and required field indication.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Library
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-message
 */
class Message {

	protected $CI;

	protected $_config = array(
		'session_var' => 'messages',
		'type_wrapper' => 'ul',
		'type_wrapper_css' => 'messages',
		'type_wrapper_css_prefix' => 'type-',
		'wrapper' => 'li',
		'wrapper_css' => 'msg',
		'wrapper_css_prefix' => 'msg-',
		'validation_errors' => FALSE
	);

	protected $_session = array();

	protected $_now = array();

	/**
	 * Constructor
	 *
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();

		// Load session library
		$this->CI->load->library('session');

		// Update the values from the config file
		if ( ! empty($config))
		{
			$this->initialize($config);
		}

		log_message('debug', 'Message Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @param   array
	 * @return  Message
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->_config[$key]))
			{
				$this->{$key} = $val;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * get magic method
	 *
	 * @param   string
	 * @return  mixed
	 */
	public function __get($name)
	{
		return isset($this->_config[$name]) ? $this->_config[$name] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * set magic method
	 *
	 * @param   string
	 * @return  null
	 */
	public function __set($name, $value)
	{
		if (isset($this->_config[$name]))
		{
			$this->_config[$name] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add information message
	 *
	 * @param   string  $msg
	 * @param   bool    $now
	 * @return  Message
	 */
	public function info($msg = '', $now = FALSE)
	{
		$type = 'info';

		if ($msg)
		{
			// Displayed message now
			if ($now)
			{
				$this->now($msg, $type);
			}

			// Set message in the session
			else
			{
				$this->message($msg, $type);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * @param   string  $msg
	 * @param   bool    $now
	 * @return  Message
	 */
	public function error($msg = '', $now = FALSE)
	{
		$type = 'error';

		if ($msg)
		{
			// Displayed message now
			if ($now)
			{
				$this->now($msg, $type);
			}

			// Set message in the session
			else
			{
				$this->message($msg, $type);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add success message
	 *
	 * @param   string  $msg
	 * @param   bool    $now
	 * @return  Message
	 */
	public function success($msg = '', $now = FALSE)
	{
		$type = 'success';

		if ($msg)
		{
			// Displayed message now
			if ($now)
			{
				$this->now($msg, $type);
			}

			// Set message in the session
			else
			{
				$this->message($msg, $type);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add warning message
	 *
	 * @param   string  $msg
	 * @param   bool    $now
	 * @return  Message
	 */
	public function warning($msg = '', $now = FALSE)
	{
		$type = 'warning';

		if ($msg)
		{
			// Displayed message now
			if ($now)
			{
				$this->now($msg, $type);
			}

			// Set message in the session
			else
			{
				$this->message($msg, $type);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * General/custom message
	 *
	 * @param   string  $msg
	 * @param   string  $type
	 * @return  Message
	 */
    public function message($msg = '', $type = 'info')
	{
		if ($msg)
		{
			// Type not defined
			if ( ! $type)
			{
				log_message('debug', 'Message Class: Cannot create message "{$msg}". Message type not defined.');

				return $this;
			}

			// Set message
			$this->_session[$type][] = $msg;

			// Save to session
			$this->CI->session->set_flashdata($this->_config['session_var'], $this->_session);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a message to be displayed now
	 *
	 * @param   string  $msg
	 * @param   string  $type
	 * @return  Message
	 */
    public function now($msg = '', $type = 'info')
	{
		if ($msg)
		{
			// Type not defined
			if ( ! $type)
			{
				log_message('debug', 'Message Class: Cannot create message "{$msg}" to be displayed now. Message type not defined.');

				return $this;
			}

			// Set message
			$this->_now[$type][] = $msg;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Display messages
	 *
	 * @param   string  $type
	 * @param   bool    $validation
	 * @return  string
	 */
	public function display($type = '', $validation = FALSE)
	{
		// Session messages
		$session = $this->CI->session->flashdata($this->_config['session_var']);

		// Now messages
		$now = $this->_now;

		// Messages
		$messages = array_merge_recursive((array)$session, (array)$now);

		// Only a specific type
		if ($type AND isset($messages[$type]))
		{
			$m = $messages[$type];
			$messages = array();
			$messages[$type] = $m;
		}

		// Include form validation errors
		if ($this->_config['validation_errors'] OR $validation == TRUE)
		{
			// Load form validation library
			$this->CI->load->library('form_validation');

			// Validation errors
			$validation = $this->CI->form_validation->error_array();

			// Loop over validation errors
			foreach ($validation as $error_field => $error_msg)
			{
				$messages['validation'][] = $error_msg;
			}
		}

		// Start output
		$output = '';

		// Loop over messages
		foreach ($messages as $type => $msgs)
		{
			if ( ! empty($msgs))
			{
				// Begin type wrapper
				if ($this->_config['type_wrapper'])
				{
					// Add the class to the wrapper
					$class = $class = explode(' ', $this->_config['type_wrapper_css']);

					// Add the type class
					$class[] = $this->_config['type_wrapper_css_prefix'].$type;

					// Add the item class
					$class = ($class) ? ' class="'.implode(' ', $class).'"' : '';

					$output .= '<'.$this->_config['type_wrapper'].$class.'>';
				}

				// Loop over items
				foreach ($msgs as $msg)
				{
					// Begin item wrapper
					if ($this->_config['wrapper'])
					{
						// Add the common class to the item wrapper
						$class = explode(' ', $this->_config['wrapper_css']);

						// Add the type class
						$class[] = $this->_config['wrapper_css_prefix'].$type;

						// Add the item class
						$class = ($class) ? ' class="'.implode(' ', $class).'"' : '';

						$output .= '<'.$this->_config['wrapper'].$class.'>';
					}

					// The actual message
					$output .= $msg;

					// End item wrapper
					if ($this->_config['wrapper'])
					{
						$output .= '</'.$this->_config['wrapper'].'>';
					}
				}

				// End type wrapper
				if ($this->_config['type_wrapper'])
				{
					$output .= '</'.$this->_config['type_wrapper'].'>';
				}
			}
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Clear messages
	 * 
	 * @param   bool    $now
	 * @return  void
	 */
	public function clear()
	{
		// Clear messages to display now
		$this->_now = array();

		// Clear messages to display in session
		$this->_session = array();

		$this->CI->session->set_userdata($this->_config['session_var'], $this->_session);
	}

}
// END Message class

/* End of file Message.php */
/* Location: ./application/libraries/Message.php */