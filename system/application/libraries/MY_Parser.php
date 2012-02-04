<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Parser extends CI_Parser {

	protected $CI;

	protected $_glue = ':';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		// Lex
		if ( ! class_exists('Lex_Autoloader'))
		{
			include APPPATH.'/libraries/Lex/Autoloader.php';
		}
	}

	// --------------------------------------------------------------------

	/**
	*  Parse a template
	*
	 * @param   string  $template
	 * @param   array   $data
	 * @param   bool    $return
	 * @return  string
	 */
	public function parse($template, $data, $return = FALSE)
	{
		$this->CI =& get_instance();
		$template = $this->CI->load->view($template, $data, TRUE);

		return $this->_parse($template, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	*  Parse a String
	*
	 * @param   string  $template
	 * @param   array   $data
	 * @param   bool    $return
	 * @return  string
	 */
	public function parse_string($template, $data, $return = FALSE)
	{
		return $this->_parse($template, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Apply tags
	 *
	 * @param   string  $string
	 * @param   array   $data
	 * @param   bool    $return
	 * @return  bool
	 */
	public function _parse($string = '', $data = array(), $return = FALSE)
	{
		// Nothing to do
		if ($string == '')
		{
			return FALSE;
		}

		// Convert from object to array
		if ( ! is_array($data))
		{
			$data = (array)$data;
		}

		$data = array_merge($data, $this->CI->load->_ci_cached_vars);

		// Register Lex
		Lex_Autoloader::register();

		// Tag support
		$parser = new Lex_Parser();
		$parser->scope_glue($this->_glue);
		$parser->cumulative_noparse(TRUE);
		$parsed = $parser->parse($string, $data, array($this, 'parser_callback'));

		// Inject noparse values back in
		Lex_Parser::inject_noparse($parsed);

		unset($parser);

		// Return results or not ?
		if ( ! $return)
		{
			$this->CI->output->append_output($parsed);

			return;
		}

		return $parsed;
	}

	// --------------------------------------------------------------------

	/**
	 * Tags callback
	 *
	 * @param   string  $tag
	 * @param   array   $attributes
	 * @param   string  $content
	 */
	public function parser_callback($tag = '', $attributes = array(), $content = '')
	{
		$tag_callbacks = $this->CI->shorttags->get_shorttags();

		$parsed_return = '';

		if (array_key_exists($tag, $tag_callbacks))
		{
			$parsed_return = call_user_func($tag_callbacks[$tag], $attributes, $content);
		}

		return $parsed_return;
	}

}