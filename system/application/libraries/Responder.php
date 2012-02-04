<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Responder {

	private static $ci;

	protected static $_config = array(
		'responder_table' => 'responder',
		'responder_bcc' => array(),
	);

	protected static $_settings = array(
		'from_name' => '',
		'from_email' => '',
		'reply_name' => '',
		'reply_email' => '',
		'subject' => '',
		'to' => array(),
		'cc' => array(),
		'bcc' => array(),
		'priority' => '',
		'attachment' => array(),
		'vars' => array()
	);

	public function __construct($config = array())
	{
		self::$ci = &get_instance();

		// Set config values
		if ( ! empty($config))
		{
			self::initialize($config);
		}

		log_message('debug', 'Responder Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @access	public
	 * @param	array
	 * @return	null
	 */
	public function initialize($config = array())
	{
		self::$_config = array_merge(self::$_config, $config);
	}

	// --------------------------------------------------------------------

	/**
	 * Get magic method
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function __get($key = '')
	{
		return isset(self::$_config[$key]) ? self::$_config[$key] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Set magic method
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	null
	 */
	public function __set($key = '', $value = '')
	{
		if (isset(self::$_config[$key]))
		{
			self::$_config[$key] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Email recipient(s)
	 *
	 * @access	public
	 * @param   string, array
	 * @return	void
	 */
	public function to($emails = '')
	{
		self::$_settings['to'] = $emails;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email parser variables
	 *
	 * @access	public
	 * @param   string, array
	 * @param   string
	 * @return	void
	 */
	public function vars($vars = array(), $value = '')
	{
		// $vars is not an array. Data being passed one-by-one
		if ( ! is_array($vars))
		{
			self::$_settings['vars'][$vars] = $value;
		}

		// $vars is an array
		else
		{
			foreach ($vars as $k => $v)
			{
				self::$_settings['vars'][$k] = $v;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email author
	 *
	 * @access	public
	 * @param   string
	 * @param   string
	 * @return	void
	 */
	public function from($email = '', $name = '')
	{
		self::$_settings['from_name'] = $name;

		self::$_settings['from_email'] = $email;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Reply to
	 *
	 * @access	public
	 * @param   string
	 * @param   string
	 * @return	void
	 */
	public function reply_to($email = '', $name = '')
	{
		self::$_settings['reply_name'] = $name;

		self::$_settings['reply_email'] = $email;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email CC
	 *
	 * @access	public
	 * @param   string, array
	 * @return	void
	 */
	public function cc($emails = '')
	{
		self::$_settings['cc'] = $emails;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email BCC
	 *
	 * @access	public
	 * @param   string, array
	 * @return	void
	 */
	public function bcc($emails = '')
	{
		self::$_settings['bcc'] = $emails;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email subject
	 *
	 * @access	public
	 * @param   string
	 * @return	void
	 */
	public function subject($subject = '')
	{
		self::$_settings['subject'] = $subject;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email priority
	 *
	 * @access	public
	 * @param   integer
	 * @return	void
	 */
	public function priority($priority = 3)
	{
		self::$_settings['priority'] = $priority;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Email attachments
	 *
	 * @access	public
	 * @param   string, array
	 * @return	void
	 */
	public function attach($attachments = '')
	{
		self::$_settings['attachment'] = $attachments;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Send email
	 *
	 * @access	public
	 * @param   string
	 * @return	string, bool
	 */
	public function send($key = '')
	{
		// Get the responder data from the database
		$data = self::_find_responder($key);

		// Responder not found
		if ($data === FALSE)
		{
			log_message('error', 'Unable to send email. Responder with key "'.$key.'" does not exist');

			return FALSE;
		}

		// Load the library
		self::$ci->load->library('email');

		// Clear email queue
		self::$ci->email->clear();

		// Priority
		$priority = self::$_settings['priority'];
			$priority OR $priority = $data['email_priority'];
		self::$ci->email->set_priority($priority);

		// Mail type
		self::$ci->email->set_mailtype($data['email_type']);

		// From
		$from_email = self::$_settings['from_email'];
			$from_email OR $from_email = $data['email_from_email'];
		$from_name = self::$_settings['from_name'];
			$from_name OR $from_name = $data['email_from_name'];
		self::$ci->email->from($from_email, $from_name);

		// Reply to
		if (self::$_settings['reply_email'])
		{
			$reply_email = self::$_settings['reply_email'];
				$reply_email OR $reply_email = $from_email;
			$reply_name = self::$_settings['reply_name'];
				$reply_name OR $reply_name = $from_name;
			self::$ci->email->reply_to($reply_email, $reply_name);
		}

		// To
		$to = self::$_settings['to'];
		self::$ci->email->to($to);

		// Subject
		$subject = self::$_settings['subject'];
			$subject OR $subject = $data['email_subject'];
			$subject = self::_parse($subject);
		self::$ci->email->subject($subject);

		// Message
		$message = $data['email_message'];
			$message = self::_parse($message);
		self::$ci->email->message($message);

		// Alt message
		if ($data['email_alt_message'])
		{
			$alt_message = $data['email_alt_message'];
				$alt_message = self::_parse($alt_message);
			self::$ci->email->set_alt_message($alt_message);
		}

		// Attachment(s)
		if ( ! empty(self::$_settings['attachment']))
		{
			$files = self::$_settings['attachment'];

			if ( ! is_array($files))
			{
				$files = explode(',', $files);
			}

			foreach ($files as $file)
			{
				self::$ci->email->attach(trim($file));
			}
		}

		// Send the email
		if ( ! self::$ci->email->send())
		{
			log_message('error', 'Unable to send email. Message from server:'."\n".self::$ci->email->print_debugger());

			return self::$ci->email->print_debugger();
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Check if responder exists
	 *
	 * @access	public
	 * @param   string
	 * @return	bool
	 */
	public function exists($key = '')
	{
		return self::_responder_exists($key);
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing responder
	 *
	 * @access	public
	 * @param	string, array
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function update($key, $type = 'text', $name = '', $description = '', $sender_email = '', $sender_name = '', $subject = '', $message = '', $alt_message = '', $priority = '')
	{
		// Default values
		$defaults = array();

		// The first item is a string. Build an array if the values
		if ( ! is_array($key))
		{
			$key = array(
				'email_key' => $key,
				'email_type' => $type,
				'email_name' => $name,
				'email_description' => $description,
				'email_from_email' => $sender_email,
				'email_from_name' => $sender_name,
				'email_subject' => $subject,
				'email_message' => $message,
				'email_alt_message' => $alt_message,
				'email_priority' => $priority
			);
		}

		// Clean out the empty values
		$key = array_filter($key);

		// Merge the defined data with the defaults
		$data = array_merge($defaults, $key);

		// Pull the key out of the array
		$key = $data['key'];
		unset($data['key']);

		// Update database
		$success = self::$ci->db->where('email_key', $key)
			->update(self::$_config['responder_table'], $data);

		return ($success) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new responder
	 *
	 * @access	public
	 * @param	string, array
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function create($key, $type = 'text', $name = '', $description = '', $sender_email = '', $sender_name = '', $subject = '', $message = '', $alt_message = '', $priority = '')
	{
		// Default values
		$defaults = array(
			'email_key' => '',
			'email_type' => 'text',
			'email_name' => 'Responder name not defined',
			'email_description' => '',
			'email_from_email' => 'no-reply@email.com',
			'email_from_name' => '',
			'email_subject' => 'Subject',
			'email_message' => '',
			'email_alt_message' => '',
			'email_priority' => '',
			'email_date_created' => date('Y-m-d h:i:s'),
			'email_date_modified' => date('Y-m-d h:i:s')
		);

		// The first item is a string. Build an array if the values
		if ( ! is_array($key))
		{
			$key = array(
				'email_key' => $key,
				'email_type' => $type,
				'email_name' => $name,
				'email_description' => $description,
				'email_from_email' => $sender_email,
				'email_from_name' => $sender_name,
				'email_subject' => $subject,
				'email_message' => $message,
				'email_alt_message' => $alt_message,
				'email_priority' => $priority
			);
		}

		// Clean out the empty values
		$key = array_filter($key);

		// Merge the defined data with the defaults
		$data = array_merge($defaults, $key);

		// Update database
		$success = self::$ci->db->insert(self::$_config['responder_table'], $data);

		return ($success) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete responder
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function delete($key = '')
	{
		// Update database
		$success = self::$ci->db->where('email_key', $key)
			->delete(self::$_config['responder_table']);

		return ($success) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Pull the email template from the database
	 *
	 * @access	private
	 * @param   string
	 * @return	array, bool
	 */
	private function _find_responder($key = '')
	{
		self::$ci->db->select('*')
			->from(self::$_config['responder_table'])
			->where('email_key', $key);

		$query = self::$ci->db->get();

		// Found. Return the data
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}

		// Responder not found
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Pull the responder id from the database
	 *
	 * @access	private
	 * @param   string
	 * @return	bool
	 */
	private function _responder_exists($key = '')
	{
		self::$ci->db->select('id')
			->from(self::$_config['responder_table'])
			->where('email_key', $key);

		$query = self::$ci->db->get();

		// Found
		if ($query->num_rows() > 0)
		{
			return TRUE;
		}

		// Not found
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Replace variables with value
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	private function _parse($str = '')
	{
		// Load parser
		self::$ci->load->library('parser');

		// Merge the ones passed with the defaults
		$vars = self::$_settings['vars'];

		return self::$ci->parser->parse_string($str, $vars, TRUE);
	}

}