<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messaging {

	protected $CI;

	protected $_config = array(
		'user' => NULL
	);

	/**
	 * Constructor
	 *
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->CI = &get_instance();

		// Load form validation library
		$this->CI->load->model('messaging_model');

		if ( ! empty($config))
		{
			//$this->initialize($config);
		}

		log_message('debug', 'Messaging Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @param   array
	 * @return  Messaging
	 */
	public function initialize($config = array())
	{
		foreach ($config as $name => $value)
		{
			if (array_key_exists($name, $this->_config[$name]))
			{
				$this->_config[$name] = $value;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_messages()
	{

	}

	// --------------------------------------------------------------------

	/**
	 * Get user
	 *
	 * @return  int
	 */
	public function get_user()
	{
		return $this->_config['user'];
	}

	// --------------------------------------------------------------------

	/**
	 * Set user
	 *
	 * @param   int     $user
	 * @return  Messaging
	 */
	public function set_user($user = NULL)
	{
		if ($user)
		{
			$this->_config['user'] = $user;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_sent_messages()
	{

	}

	// --------------------------------------------------------------------

	public function create_message()
	{

	}

	// --------------------------------------------------------------------

	public function delete_message($id = 0)
	{
		return $this->messaging_model->delete_message($id);
	}


//
//    /*
//        function get_message() - will return a single message, including the status for specified user.
//        @parameters - $msg_id REQUIRED, $user_id REQUIRED
//    */
//
//    function get_message($msg_id, $user_id)
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($msg_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_MSG_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_MSG_ID));return $status;}
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//
//        if ($message = $this->ci->mahana_model->get_message($msg_id, $user_id))
//        {
//            return $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_SUCCESS), 'retval'=>$message);
//        }
//        return $status;
//    }
//
//
//    /*
//        function get_full_thread() - will return a entire thread, including the status for specified user.
//
//        @parameters - $thread_id REQUIRED, $user_id REQUIRED, $order_by OPTIONAL
//                    - $full_thread - if true, user will also see messages from thread posted BEFORE user became participant
//    */
//
//    function get_full_thread($thread_id, $user_id, $full_thread=false, $order_by='asc')
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($thread_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_THREAD_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_THREAD_ID));return $status;}
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//
//        if ($message = $this->ci->mahana_model->get_full_thread($thread_id, $user_id, $full_thread, $order_by))
//        {
//            return $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_SUCCESS), 'retval'=>$message);
//        }
//        return $status;
//    }
//
//    /*
//        function get_all_threads() - will return all threads for user, including the status for specified user.
//
//        @parameters - $user_id REQUIRED, $order_by OPTIONAL
//                    - $full_thread - if true, user will also see messages from thread posted BEFORE user became participant
//    */
//
//    function get_all_threads($user_id,  $full_thread=false, $order_by='asc')
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//
//        if ($message = $this->ci->mahana_model->get_all_threads($user_id,  $full_thread, $order_by))
//        {
//            return $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_SUCCESS), 'retval'=>$message);
//        }
//        return $status;
//    }
//
//
//    /*
//        function update_message_status() - will change status on message for particular user
//
//        @parameters - $msg_id REQUIRED, $user_id REQUIRED, $status_id REQUIRED
//                    - $status_id should come from config/mahana.php list of constants
//    */
//    function update_message_status($msg_id, $user_id, $status_id  )
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($msg_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_MSG_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_MSG_ID));return $status;}
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//        if (empty($status_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_STATUS_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_STATUS_ID));return $status;}
//
//        if ($this->ci->mahana_model->update_message_status($msg_id, $user_id, $status_id  ))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_STATUS_UPDATE));
//        }
//        return $status;
//    }
//
//    /*
//        function add_participant() - adds user to existing thread
//
//        @parameters - $thread_id REQUIRED, $user_id REQUIRED
//    */
//    function add_participant($thread_id, $user_id)
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($thread_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_THREAD_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_THREAD_ID));return $status;}
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//
//        if (!$this->ci->mahana_model->valid_new_participant($thread_id, $user_id))
//        {
//            $status = array('err'=>1, 'code'=>1, 'msg'=>lang('mahana_'.MSG_ERR_PARTICIPANT_EXISTS));
//            return $status;
//        }
//
//        if (!$this->ci->mahana_model->application_user($user_id))
//        {
//            $status = array('err'=>1, 'code'=>1, 'msg'=>lang('mahana_'.MSG_ERR_PARTICIPANT_NONSYSTEM));
//            return $status;
//        }
//
//        if ($this->ci->mahana_model->add_participant($thread_id, $user_id ))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_PARTICIPANT_ADDED));
//        }
//        return $status;
//    }
//
//    /*
//        function remove_participant() - removes user from existing thread
//
//        @parameters - $thread_id REQUIRED, $user_id REQUIRED
//    */
//    function remove_participant($thread_id, $user_id)
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($thread_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_THREAD_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_THREAD_ID));return $status;}
//        if (empty($user_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_USER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_USER_ID));return $status;}
//
//        if ($this->ci->mahana_model->remove_participant($thread_id, $user_id ))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_PARTICIPANT_REMOVED));
//        }
//        return $status;
//    }
//
//    /*
//        function send_new_message() - sends new internal message. This function will create a new thread
//
//        @parameters - $sender_id REQUIRED, $recipients REQUIRED
//                    - $recipients may be either a single integer or an array of integers, representing user_ids
//    */
//    function send_new_message($sender_id, $recipients, $subject='', $body='', $priority=PRIORITY_NORMAL){
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($sender_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_SENDER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_SENDER_ID));return $status;}
//        if (empty($recipients)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_RECIPIENTS, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_RECIPIENTS));return $status;}
//
//        if ($this->ci->mahana_model->send_new_message($sender_id, $recipients, $subject, $body, $priority))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_MESSAGE_SENT));
//        }
//        return $status;
//    }
//
//    /*
//        function reply_to_message() - replies to internal message. This function will NOT create a new thread or participant list
//
//        @parameters - $sender_id REQUIRED, $msg_id REQUIRED
//    */
//    function reply_to_message($msg_id, $sender_id, $subject='', $body='', $priority=PRIORITY_NORMAL)
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($sender_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_SENDER_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_SENDER_ID));return $status;}
//        if (empty($msg_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_MSG_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_MSG_ID));return $status;}
//
//        if ($this->ci->mahana_model->reply_to_message($msg_id, $sender_id,  $body, $priority))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_MESSAGE_SENT));
//        }
//        return $status;
//    }
//
//    /*
//        function get_participant_list() - returns list of participants on given thread. If sender_id set, sender_id will be left off list
//
//        @parameters - $sender_id REQUIRED, $thread_id REQUIRED
//    */
//    function get_participant_list($thread_id, $sender_id=0)
//    {
//        $status = array('err'=>1, 'code'=>MSG_ERR_GENERAL, 'msg'=>lang('mahana_'.MSG_ERR_GENERAL));
//
//        if (empty($thread_id)){$status = array('err'=>1, 'code'=>MSG_ERR_INVALID_THREAD_ID, 'msg'=>lang('mahana_'.MSG_ERR_INVALID_THREAD_ID));return $status;}
//
//        if ($participants = $this->ci->mahana_model-> get_participant_list($thread_id, $sender_id))
//        {
//            $status = array('err'=>0, 'code'=>MSG_SUCCESS, 'msg'=>lang('mahana_'.MSG_SUCCESS)  ,'retval'=>$participants);
//        }
//        return $status;
//    }

}