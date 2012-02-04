<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Assets Class
 *
 * This class allows you to load external assets (CSS, Javascript, meta, favicon, etc) to the page.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Library
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-assets
 */
class Assets {

	protected $CI;

	protected $_config = array(
		'minify_js' => FALSE,
		'minify_css' => FALSE,
		'combine_js' => FALSE,
		'combine_css' => FALSE,
		'group_default' => 'general',
		'priority_group' => 20,
		'priority_asset' => 20,
		'asset_base_path' => NULL,
		'asset_cache_dir' => 'assets/cache/',
		'asset_gzip' => '',
        'combine_external' => FALSE
	);

	protected $_assets = array();

	/**
	 * Constructor
	 *
	 * @param   array   $config
	 */
	public function __construct($config = array())
	{
		$this->CI = &get_instance();

		// Load helper
		$this->CI->load->helper('html');

		// Set the config values
		if ( ! empty($config))
		{
			$this->_initialize($config);
		}

		// Create the default group
		$this->add_group($this->_config['group_default']);

		log_message('debug', 'Assets Class Initialized');
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
		return (array_key_exists($name, $this->_config)) ? $this->_config[$name] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * set magic method
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 * @return  void
	 */
	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->_config))
		{
			$this->_config[$name] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add external Javascript asset
	 *
	 * @param   string  $name
	 * @param   string  $file
	 * @param   string  $group
	 * @param   int     $priority
	 * @param   string  $conditional
	 * @param   bool    $minify
	 * @return  Assets
	 */
	public function js($name = '', $file = '', $group = NULL, $priority = NULL, $conditional = NULL, $minify = TRUE)
	{
		$data = array(
			'name' => $name,
			'file' => $file,
			'active' => TRUE,
			'conditional' => $conditional,
			'minify' => $minify
		);

		$this->_add_asset($group, 'js', $priority, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add external CSS asset
	 *
	 * @param   string  $name
	 * @param   string  $file
	 * @param   string  $group
	 * @param   int     $priority
	 * @param   string  $conditional
	 * @param   bool    $minify
	 * @return  Assets
	 */
	public function css($name = '', $file = '', $group = NULL, $priority = NULL, $conditional = NULL, $minify = TRUE)
	{
		$data = array(
			'name' => $name,
			'file' => $file,
			'active' => TRUE,
			'conditional' => $conditional,
			'minify' => $minify
		);

		$this->_add_asset($group, 'css', $priority, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for css()
	 *
	 * @param   string  $name
	 * @param   string  $file
	 * @param   string  $group
	 * @param   int     $priority
	 * @param   string  $conditional
	 * @param   bool    $minify
	 * @return  Assets
	 */
	public function link($name = '', $file = '', $group = NULL, $priority = NULL, $conditional = NULL, $minify = TRUE)
	{
		return $this->css($name, $file, $group, $priority, $conditional, $minify);
	}

	// --------------------------------------------------------------------

	/**
	 * Add META asset (favicon, keywords, description, etc)
	 *
	 * @param   string  $name
	 * @param   array   $meta
	 * @param   string  $group
	 * @param   int     $priority
	 * @return  Assets
	 */
	public function meta($name = '', $meta = array(), $group = NULL, $priority = NULL)
	{
		$data = array(
			'name' => $name,
			'meta' => $meta,
			'active' => TRUE
		);

		$this->_add_asset($group, 'meta', $priority, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Javascript code block
	 *
	 * @param   string  $name
	 * @param   string  $content
	 * @param   string  $group
	 * @param   int     $priority
	 * @return  Assets
	 */
	public function js_block($name = '', $content = '', $group = NULL, $priority = NULL)
	{
		$data = array(
			'name' => $name,
			'content' => $this->_regex_get_tag_content('script', $content),
			'active' => TRUE
		);

		$this->_add_asset($group, 'js_block', $priority, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for js_block()
	 *
	 * @param   string  $name
	 * @param   string  $content
	 * @param   string  $group
	 * @param   int     $priority
	 * @return  Assets
	 */
	public function js_code($name = '', $content = '', $group = NULL, $priority = NULL)
	{
		return $this->js_block($name, $content, $group, $priority);
	}

	// --------------------------------------------------------------------

	/**
	 * Add CSS code block
	 *
	 * @param   string  $name
	 * @param   string  $content
	 * @param   string  $group
	 * @param   int     $priority
	 * @return  Assets
	 */
	public function css_block($name = '', $content = '', $group = NULL, $priority = NULL)
	{
		$data = array(
			'name' => $name,
			'content' => $this->_regex_get_tag_content('style', $content),
			'active' => TRUE
		);

		$this->_add_asset($group, 'css_block', $priority, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for css_block()
	 *
	 * @param   string  $name
	 * @param   string  $content
	 * @param   string  $group
	 * @param   int     $priority
	 * @return  Assets
	 */
	public function css_code($name = '', $content = '', $group = NULL, $priority = NULL)
	{
		return $this->css_block($name, $content, $group, $priority);
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new asset group
	 *
	 * @param   string  $name
	 * @param   string  $priority
	 * @param   string  $path
	 * @return  Assets
	 */
	public function add_group($name = '', $priority = NULL, $path = NULL)
	{
		// Only create the group if it doesn't already exist
		if ($this->_find_group_index($name) === FALSE)
		{
			// Priority
			if ( ! is_numeric($priority))
			{
				$priority = $this->_config['priority_group'];
			}

			// Path
			if ($path === NULL)
			{
				$path = $this->_config['asset_base_path'];
			}

			$this->_assets[$priority][$name] = array(
				'active' => TRUE,
				'path' => $path,
				'js' => array(),
				'css' => array(),
				'meta' => array(),
				'js_block' => array(),
				'css_block' => array()
			);

			// Sort groups by the priority
			ksort($this->_assets, SORT_NUMERIC);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate external Javascript asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_js($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('js', $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate external Javascript asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_js($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('js', $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate external CSS asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_css($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('css', $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate external CSS asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_css($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('css', $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate META asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_meta($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('meta', $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate META asset
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_meta($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('meta', $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Javascript code block
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_js_block($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('js_block', $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate Javascript code block
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_js_block($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('js_block', $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate CSS code block
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_css_block($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('css_block', $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate CSS code block
	 *
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_css_block($name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset('css_block', $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate asset type
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate($type = '', $name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset($type, $name, $group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate asset type
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate($type = '', $name = '', $group = NULL)
	{
		$this->_activate_deactivate_asset($type, $name, $group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate entire group of assets
	 *
	 * @param   string  $group
	 * @return  Assets
	 */
	public function activate_group($group = '')
	{
		$this->_activate_deactivate_group($group, TRUE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deactivate entire group of assets
	 *
	 * @param   string  $group
	 * @return  Assets
	 */
	public function deactivate_group($group = '')
	{
		$this->_activate_deactivate_group($group, FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Render META assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_meta($group = NULL)
	{
		$result = '';

		// Content to render
		$contents = $this->_get_contents('meta', $group);

		foreach ($contents as $item)
		{
			$result .= meta($item['meta']);
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Render external Javascript assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_js($group = NULL)
	{
		$result = '';

		// Content to render
		$contents = $this->_get_contents('js', $group);

		// Combine and minify
		if ($this->_config['combine_js'] AND $this->_check_cache_directory())
		{
			$combined = $this->_combine('js', $contents, $this->_config['minify_js']);

			foreach ((array)$combined as $item)
			{
				if (is_array($item))
				{
					// Uri for asset
					$file = $this->_file_uri($item['path'], $item['file']);

					$result .= $this->_conditional(script_tag($file), $item['conditional']);
				}

				else
				{
					$result .= script_tag($item);
				}
			}
		}

		// No combine
		else
		{
			foreach ($contents as $item)
			{
				// Uri for asset
				$file = $this->_file_uri($item['path'], $item['file']);

				$result .= $this->_conditional(script_tag($file), $item['conditional']);
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Render Javascript code blocks
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_js_block($group = NULL)
	{
		$result = '';

		// Content to render
		$contents = $this->_get_contents('js_block', $group);

		if ( ! empty($contents))
		{
			// Open the tag
			$result .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";

			foreach ($contents as $item)
			{
				$result .= $item['content']."\n";
			}

			// Close the tag
			$result .= '//]]>'."\n".'</script>'."\n";
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders all external Javascript and Javascript code block assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_all_js($group = NULL)
	{
		$result = '';

		// JS
		$result .= $this->render_js($group);

		// JS Block
		$result .= $this->render_js_block($group);

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Render external CSS assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_css($group = NULL)
	{
		$result = '';

		// Content to render
		$contents = $this->_get_contents('css', $group);

		// Combine and minify
		if ($this->_config['combine_css'] AND $this->_check_cache_directory())
		{
			$combined = $this->_combine('css', $contents, $this->_config['minify_css']);

			foreach ((array)$combined as $item)
			{
				if (is_array($item))
				{
					// File is actually an array
					if (is_array($item['file']))
					{
						// Uri for asset
						$file = $this->_file_uri($item['path'], $item['file']['href']);
					}

					// File is a string
					else
					{
						// Uri for asset
						$file = $this->_file_uri($item['path'], $item['file']);
					}

					$result .= $this->_conditional(link_tag($file), $item['conditional']);
				}

				else
				{
					$result .= link_tag($item);
				}
			}
		}

		// No combine
		else
		{
			foreach ($contents as $item)
			{
				// File is actually an array
				if (is_array($item['file']))
				{
					$file = $item['file'];

					// Uri for asset
					$file['href'] = $this->_file_uri($item['path'], $file['href']);
				}

				// File is a string
				else
				{
					// Uri for asset
					$file = $this->_file_uri($item['path'], $item['file']);
				}

				$result .= $this->_conditional(link_tag($file), $item['conditional']);
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Render CSS code blocks
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_css_block($group = NULL)
	{
		$result = '';

		// Content to render
		$contents = $this->_get_contents('css_block', $group);

		if ( ! empty($contents))
		{
			// Open the tag
			$result .= '<style type="text/css">'."\n";

			foreach ($contents as $item)
			{
				$result .= $item['content']."\n";
			}

			// Close the tag
			$result .= '</style>'."\n";
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders all external CSS and CSS code block assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render_all_css($group = NULL)
	{
		$result = '';

		// CSS
		$result .= $this->render_css($group);

		// CSS Block
		$result .= $this->render_css_block($group);

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders all external Javascript, external CSS, META, Javascript
	 * code block, and CSS code block assets
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function render($group = NULL)
	{
		$result = '';

		// Meta
		$result .= $this->render_meta($group);

		// CSS
		$result .= $this->render_css($group);

		// CSS Block
		$result .= $this->render_css_block($group);

		// JS
		$result .= $this->render_js($group);

		// JS Block
		$result .= $this->render_js_block($group);

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for render()
	 *
	 * @param   string  $group
	 * @return  string
	 */
	public function generate($group = NULL)
	{
		return $this->render($group);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @param   array   $config
	 * @return  void
	 */
	private function _initialize($config = array())
	{
		foreach ($config as $name => $value)
		{
			if (array_key_exists($name, $this->_config))
			{
				$this->_config[$name] = $value;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get the data by type and group
	 *
	 * @param   string  $type
	 * @param   string  $group
	 * @return  array
	 */
	private function _get_contents($type = '', $group = NULL)
	{
		$contents = array();

		// Only a particular group
		if ($group)
		{
			// Group index
			$index = $this->_find_group_index($group);

			if ($index !== FALSE)
			{
				// Only if the group is active
				if ($this->_assets[$index][$group]['active'])
				{
					foreach ($this->_assets[$index][$group][$type] as $priority => $assets)
					{
						foreach ($assets as $index => $asset)
						{
							// Only if the asset is active
							if ($asset['active'])
							{
								$contents[] = $asset;

								// Now that we've used the asset, deactivate it so we don't use it again
								$this->_assets[$index][$group][$type][$priority][$index]['active'] = FALSE;
							}
						}
					}
				}
			}
		}

		// All groups
		else
		{
			foreach ($this->_assets as $group_priority => $groups)
			{
				foreach ($groups as $name => $group)
				{
					// Only if the group is active
					if (array_key_exists('active', $group) AND $group['active'])
					{
						foreach ($group[$type] as $priority => $assets)
						{
							foreach ($assets as $index => $asset)
							{
								// Only if the asset is active
								if ($asset['active'])
								{
									$contents[] = $asset;

									// Now that we've used the asset, deactivate it so we don't use it again
									$this->_assets[$group_priority][$name][$type][$priority][$index]['active'] = FALSE;
								}
							}
						}
					}
				}
			}
		}

		return $contents;
	}

	// --------------------------------------------------------------------

	/**
	 * Add asset
	 *
	 * @param   string  $group
	 * @param   string  $type
	 * @param   int     $priority
	 * @param   array   $data
	 * @return  bool
	 */
	private function _add_asset($group = NULL, $type = '', $priority = NULL, $data = array())
	{
		// Priority
		if ( ! is_numeric($priority))
		{
			$priority = $this->_config['priority_group'];
		}

		// Group
		$group OR $group = $this->_config['group_default'];

		// Group index
		$index = $this->_find_group_index($group);

		if ($index !== FALSE)
		{
			// Add the path to the data
			$data['path'] = $this->_assets[$index][$group]['path'];

			// Add new asset
			$this->_assets[$index][$group][$type][$priority][] = $data;

			// Sort assets in group by the priority
			ksort($this->_assets[$index][$group][$type], SORT_NUMERIC);

			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate or deactivate an asset
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $group
	 * @param   bool    $active
	 * @return  bool
	 */
	private function _activate_deactivate_asset($type = '', $name = '', $group = NULL, $active = TRUE)
	{
		// Group
		$group OR $group = $this->_config['group_default'];

		// Find the index of the group
		$group_index = $this->_find_group_index($group);

		if ($group_index !== FALSE)
		{
			// Run through each of the assets
			foreach ($this->_assets[$group_index][$group][$type] as $index => $assets)
			{
				foreach ($assets as $indx => $asset)
				{
					if ($asset['name'] == $name)
					{
						$this->_assets[$group_index][$group][$type][$index][$indx]['active'] = $active;

						return TRUE;
					}
				}
			}
		}

		// Fail... yet continue
		log_message('debug', "Assets: Unable to find asset {$name} of type {$type}");

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Activate or deactivate an entire group of assets
	 *
	 * @param   string  $group
	 * @return  bool
	 */
	private function _activate_deactivate_group($group = '', $active = TRUE)
	{
		if ($group)
		{
			// Find the index of the group
			$group_index = $this->_find_group_index($group);

			if ($group_index !== FALSE)
			{
				$this->_assets[$group_index][$group]['active'] = $active;

				return TRUE;
			}
		}

		// Fail... yet continue
		log_message('debug', "Assets: Unable to find group '{$group}'");

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Find the group priority index
	 *
	 * @param   string  $group
	 * @return  bool|int
	 */
	private function _find_group_index($group = '')
	{
		foreach ($this->_assets as $priority => $grp)
		{
			foreach ($grp as $name => $data)
			{
				if ($name == $group)
				{
					return $priority;
				}
			}
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Build the file uri
	 *
	 * @return  string
	 */
	private function _file_uri($path = '', $file = '')
	{
		if ($path = trim($path, '/'))
		{
			$path .= '/';
		}

		return $path.$file;
	}

	// ------------------------------------------------------------------------

	/**
	 * Find the file path
	 *
	 * @return  string
	 */
	private function _file_url($path = '', $file = '')
	{
		// URL from current domain. Get path
		if (is_string($file) AND $this->_is_url($file) AND ! $this->_is_external_url($file))
		{
			return str_replace($this->CI->config->base_url(), '', $file);
		}

		// It's an external URL
		elseif (is_string($file) AND $this->_is_url($file) AND $this->_is_external_url($file))
		{
			return $file;
		}

		return $this->_file_uri($path, $file);
	}

	// ------------------------------------------------------------------------

	/**
	 * Regex to get the content between a tag
	 *
	 * @param   string  $tag
	 * @param   string  $content
	 * @return  string
	 */
	private function _regex_get_tag_content($tag = 'script', $content = '')
	{
		$regex = '/(<'.$tag.'(?:[^"\'>]*|"[^"]*"|\'[^\']*\')*>)?(.*?)(<\/'.$tag.'>)?$/si';

		preg_match($regex, $content, $matches);

		return $matches[2];
	}

	// ------------------------------------------------------------------------

	/**
	 * Add IE conditional tags
	 *
	 * @param   string  $content
	 * @param   string  $condition
	 * @return  string
	 */
	private function _conditional($content = '', $condition = '')
	{
		if ($content)
		{
			$new_content = '';

			// Begin IE conditional
			if ($condition)
			{
				$new_content .= '<!--[if '.$condition.']>'."\n";
			}

			$new_content .= $content;

			// End IE conditional
			if ($condition)
			{
				$new_content .= '<![endif]-->'."\n";
			}

			return $new_content;
		}

		return $content;
	}

	// ------------------------------------------------------------------------

	/**
	 * Test if string is a valid URL
	 *
	 * @param   string  $str
	 * @return  bool
	 */
	private function _is_url($str = '')
	{
		return ( ! preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $str)) ? FALSE : TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Test if string is an external URL
	 *
	 * @param   string  $str
	 * @return  bool
	 */
	private function _is_external_url($str = '')
	{
		// Is a URL but not from this domain
		if ($this->_is_url($str) AND preg_match('/^'.str_replace('/', '\/', $this->CI->config->base_url()).'/i', $str))
		{
			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check is cache directory is present and writable
	 *
	 * @return  bool
	 */
	private function _check_cache_directory()
	{
		if ( ! is_dir($this->_config['asset_cache_dir']))
		{
			log_message('debug', 'Assets: Cache directory is not present. Create directory and set it with 777 permissions.');

			return FALSE;
		}

		elseif ( ! is_writable($this->_config['asset_cache_dir']))
		{
			log_message('debug', 'Assets: Cannot write files to cache directory. Set cache directory with 777 permissions.');

			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Combine assets
	 *
	 * @param   string  $type
	 * @param   array   $assets
	 * @param   bool    $minify
	 * @return  string
	 */
	private function _combine($type = '', $assets = array(), $minify = FALSE)
	{
		$names = array();
		$files = array();

		$not_combined = array();

		$result = array();

		// Gets all of the files to be combined
		foreach($assets as $key => $item)
		{
			// Internal name
			$names[] = $item['name'];

			// Asset file
			$file = $this->_file_url($item['path'], $item['file']);

			// Does not have a conditional and the file exists at the path
			if ( ! $item['conditional'] AND $path = realpath($file))
			{
				$files[] = $path;
			}

			// File cannot be combined
			else
			{
				$not_combined[] = $item;
			}
		}

		// Combine
		if ( ! empty($files))
		{
			// General file name
			$filename = md5(implode(':', $names)).'.'.$type;

			// .gz
			if ($this->_config['asset_gzip'] == 'gz')
			{
				$filename .= '.gz';
			}

			// .php
			elseif ($this->_config['asset_gzip'] == 'phpgzip')
			{
				$filename .= '.php';
			}

			$filename_path = $this->_config['asset_cache_dir'].$filename;

			if (file_exists($filename_path))
			{
				$result[] = $filename_path;
			}

			else
			{
				// php gzip
				if ($this->_config['asset_gzip'] == 'phpgzip')
				{
					$content = $this->_php_gzip_header($type);

					file_put_contents($filename_path, $content, FILE_APPEND);
				}

				foreach($files as $index => $row)
				{
					//$min = ($minify AND $row['minify']) ? TRUE : FALSE;

					$content = $this->_minify($type, $row, $minify);

					file_put_contents($filename_path, $content, FILE_APPEND);
				}

				$result[] = $filename_path;
			}
		}

		// Add files that could not be found to the result array
		foreach ($not_combined as $item)
		{
			$result[] = $item;
		}

		return $result;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param   string  $type
	 * @return  string
	 */
	private function _php_gzip_header($type = '')
	{
		if ($type == 'css')
		{
			$type = 'text/css';
		}

		else
		{
			$type = 'text/javascript';
		}

		$header = '<?php
header("Content-type: '.$type.'; charset: UTF-8");

function gzip_compress($output) {
	$compressed_out = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
	$compressed_out .= substr(gzcompress($output, 2), 0, -4);

	if (strlen($output) >= 1000)
	{
		header("Content-Encoding: gzip");
		return $compressed_out;
	}
	else
	{
		return $output;
	}
}

if (strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip"))
{
	ob_start("gzip_compress");
}

header("Cache-Control: must-revalidate");
header("Expires: '.date('D, d M Y H:i:s T', mktime(0, 0, 0, date('m'), date('d'), date('Y')+1)).'"); // One year in the future
?>
';
		return $header;
	}

	// ------------------------------------------------------------------------

	/**
	 * Return minified contents
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @param   bool    $minify
	 * @return  string
	 */
	private function _minify($type = '', $file = '', $minify = FALSE)
	{
		$contents = file_get_contents($file);

		if ($minify)
		{
			if ($type == 'js')
			{
				// Load the library
				$this->CI->load->library('minify/jsmin');

				return $this->CI->jsmin->minify($contents);
			}

			elseif ($type == 'css')
			{
				// Load the library
				$this->CI->load->library('minify/cssmin');

				return $this->CI->cssmin->minify($contents);
			}
		}

		return $contents;
	}

}
// END Assets class

/* End of file Assets.php */
/* Location: ./application/libraries/Assets.php */