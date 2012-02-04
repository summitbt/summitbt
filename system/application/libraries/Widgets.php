<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Widgets {

	private $CI;

	private $_widgets = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		$this->register('activity', 'Activity Log', array($this, 'widget__activity_log'));

		$this->register('quickstats', 'Quick Stats', array($this, 'widget__quick_stats'));
	}

	// --------------------------------------------------------------------

	/**
	 * All widgets
	 *
	 * @return  array
	 */
	public function get_widgets($widget = '')
	{
		return $this->_widgets;
	}

	// --------------------------------------------------------------------

	/**
	 * Register a widget
	 *
	 * @param   string  $key
	 * @param   string  $name
	 * @param   array   $callback
	 * @return  Widgets
	 */
	public function register($key = '', $name = '', $callback = array())
	{
		// Make sure required values are passed
		if ( ! $key OR ! $name)
		{
			log_message('debug', 'Widgets: Key reference and name are required.');
		}

		$this->_widgets[$key] = array(
			'name' => $name,
			'callback' => $callback
		);

		// Return
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Unregister a widget
	 *
	 * @param   array   $callback
	 * @return  Widgets
	 */
	public function unregister($trigger = '')
	{
		unset($this->_widgets[$trigger]);

		// Return
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Trigger a widget
	 *
	 * @param   string  $key
	 * @param   array   $options
	 * @return  mixed
	 */
	public function trigger($key = '', $options = array())
	{
		// Does the widget exist
		if (array_key_exists($key, $this->_widgets))
		{
			if (array_key_exists('callback', $this->_widgets[$key]) AND is_callable($this->_widgets[$key]['callback']))
			{
				return call_user_func($this->_widgets[$key]['callback'], $options);
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Activity log
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function widget__activity_log($attributes = array(), $content = '')
	{
		// Load user info
		$user = $this->CI->auth->get_user($this->CI->auth->user_id());

		// Load helper
		$this->CI->load->helper('misc');

		// Load library
		$this->CI->load->library('activity', array(
			'keep_for' => $this->CI->registry->get_item('changelog_keep'),
			'user' => $user['first_name'].' '.$user['last_name'],
			'pre_formatter' => 'activity_pre_formatter',
			'post_formatter' => 'activity_post_formatter'
		));

		// Name
		$name = 'activity';

		// Minimized
		$minimized = ($attributes[$name]['minimized']) ? 'minimized' : '';

		$history = array();

		foreach ($this->CI->activity->history() as $item)
		{
			$history[] = $item;
		}

		// Correct the dates on the history log
		foreach ($history as $index => $item)
		{
			$history[$index]['entry_date'] = date('M jS, Y g:i:s A', strtotime($item['entry_date']));
		}

		// Pass to view
		$data = array(
			'widget' => array(
				'module' => $name,
				'minimized' => $minimized,
				'data' => array(
					'history' => $history
				)
			)
		);

		return $this->CI->parser->parse('dashboard/widgets/'.$name, $data, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Quick stats
	 *
	 * @param   array   $attributes
	 * @param   string  $content
	 * @return  string
	 */
	public function widget__quick_stats($attributes = array(), $content = '')
	{
		// Name
		$name = 'quickstats';

		// Minimized
		$minimized = ($attributes[$name]['minimized']) ? 'minimized' : '';

		// Pass to view
		$data = array(
			'widget' => array(
				'module' => $name,
				'minimized' => $minimized,
				'data' => $this->CI->dashboard_model->get_quickstats()
			)
		);

		return $this->CI->parser->parse('dashboard/widgets/'.$name, $data, TRUE);
	}

}