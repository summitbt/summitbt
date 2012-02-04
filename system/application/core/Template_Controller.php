<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_Controller extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Not logged in. Redirect to login page
		if (strtolower($this->uri->rsegment(1) != 'access') AND strtolower($this->uri->rsegment(1) != 'error404') AND ! $this->auth->logged_in())
		{
			redirect('login');
		}

		// Load the cache library
		$cache = array(
			'adapter' => $this->registry->get_item('cache_adapter'),
			'backup' => $this->registry->get_item('cache_backup')
		);

		$this->load->driver('cache', $cache);

		// HTML helper
		$this->load->helper('html');

		// Load model
		$this->load->model('user_model');

		// Misc
		//$this->load->helper('misc');

		// Language helper
		$this->load->helper('language');

		// Language data
		$this->lang->load('system');

		// Formation library
		$this->load->library('formation');

		// Shorttags library
		$this->load->library('shorttags');

		// Template library
		$this->load->library('template');

		// Assets
		$this->load->library('assets');

		// Messages
		$this->load->library('message');

		// Validation errors for forms
		$this->form_validation->set_error_delimiters('<p class="msg msg-validation">', '</p>');

		// Create asset groups
		$this->assets->add_group('head', 0)
			->add_group('includes', 20, 'assets/includes/')
			->add_group('assets', 50, 'assets/');

		// Load jQuery
		$this->assets->js('jquery', 'assets/includes/jquery/jquery-1.7.1.min.js', 'head', 0)
			->js('jquery-noconflict', 'assets/includes/jquery/jquery.noConflict.js', 'head', 1);

		// Enable profiler
		if ($this->config->item('enable_profiler') AND ! $this->input->is_ajax_request())
		{
			$this->output->enable_profiler(TRUE);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Build view
	 *
	 * Generates the view to be displayed to the user
	 *
	 * @param   string  $view
	 * @param   string  $title
	 * @param   array   $vars
	 * @param   array   $breadcrumbs
	 * @param   string  $layout
	 * @param   string  $theme
	 * @return  void
	 */
	public function build($view, $title = 'Untitled', $vars = array(), $breadcrumbs = array(), $layout = NULL, $theme = NULL)
	{
		// Assign the parser variables to 'page'
		$data = array(
			'page' => $vars
		);

		// Mix the data
		$data = $this->_tags($data);

		// Layout
		if ($layout)
		{
			$this->template->set_layout($layout);
		}

		// Theme
		if ($theme)
		{
			$this->template->set_theme($theme);
		}

		// Breadcrumbs
		if ($breadcrumbs)
		{
			$crumbs = anchor(site_url(), 'Dashboard');

			$count = count((array)$breadcrumbs);

			foreach ((array)$breadcrumbs as $index => $crumb )
			{
				if (isset($crumb['label']) AND isset($crumb['link']))
				{
					$crumbs .= '<span class="sep">&raquo;</span>';

					// Last item. So don't make it link
					if ($index == $count - 1)
					{
						$crumbs .= '<span>'.$crumb['label'].'</span>';
					}

					// Not the last item
					else
					{
						$crumbs .= anchor($crumb['link'], $crumb['label']);
					}
				}
			}

			$data['page']['breadcrumbs'] = $crumbs;
		}

		// Finish up
		$this->template->title($title)
			->set($data)
			->build($view);
	}

	// --------------------------------------------------------------------

	/**
	 * Tags
	 *
	 * Custom parser tags used throughout the site
	 *
	 * @param   array   $data
	 * @return  array
	 */
	private function _tags($data = array())
	{
		// Load theme functions
		$this->load->file($this->template->get_theme_path().'functions.php', TRUE);

		// Load modules
		$this->load->model('modules_model');

		foreach ($this->modules_model->get_all_modules() as $module)
		{
			$name = $module['name'];

			$path = FCPATH.'modules/'.$name.'/';

			// Config data
			$config = $this->_object_to_array(json_decode($module['config']));

			if ( ! $config)
			{
				$config = array();
			}

			// Add package
			$this->load->add_package_path($path);

			// Load library
			$this->load->library($name, $config);

			// Set 'is'
			$this->is->query('module-'.$name, TRUE);

			// Remove package
			//$this->load->remove_package_path($path);
		}

		// Logged in user information
		$user = $this->user_model->get_user_by_id($this->auth->user_id());
		$user_meta = $this->user_model->get_user_meta_by_id($this->auth->user_id());

		// Site locations
		$code = '// Common locations
			var BASE_URL = "'.base_url().'";
			var SITE_URL = "'.trim(site_url(), '/').'/";
			var THEME_URL = "'.theme_url().'";
			var UPLOADS_URL = "'.uploads_url().'";
			var CURRENT_URL = "'.current_url().'";
			var URI_STRING = "'.$this->uri->uri_string().'";
			var GET = '.json_encode($this->input->get()).';
		';

		$this->assets->js_block('site-loc', $code, 'head');

		// AJAX requests
		$code = '
			jQuery(function() {
				jQuery.ajaxSetup({
					cache: false,
					timeout: 20000,
					type: "get",
					dataType: "json"
				});
			});
		';

		$this->assets->js_block('ajax-loading', $code);

		// Load library
		$this->load->library('menu');

		// Logo
		if ($logo = $this->registry->get_item('logo'))
		{
			$logo = uploads_url($logo);
		}

		// Quick search
		$form = $this->formation->form('search/results')
			->honeypot('search_user')
			->hidden('referrer', current_url())
			->fieldset_open('Quick Search')
				->text('q', 'Search for:', 'trim|xss_clean|required')
				->submit('submit', 'Search')
			->fieldset_close();

		// Pre-defined tags to use
		$tags = array(
			'site' => array(
				'theme_name' => $this->registry->get_item('theme'),
				'name' => $this->registry->get_item('name'),
				'menu' => array(
					'primary' => $this->menu->generate($this->_menu_primary()),
					'secondary' => $this->menu->generate($this->_menu_secondary()),
					'account' => $this->menu->generate($this->_menu_account($user['first_name'].' '.$user['last_name'])),
				),
				'lang' => array(
					'name' => $this->registry->get_item('lang-name'),
					'code' => $this->registry->get_item('lang-code'),
					'direction' => $this->registry->get_item('lang-direction')  // @TODO: Document language codes from http://www.seoconsultants.com/meta-tags/language
				)
			),
			'page' => array(
				'current' => current_url(),
				'form' => array(
					'quick_search' => $form->generate()
				),
				'messages' => $this->message->display(),
				'rendered' => 'Page loaded in {elapsed_time} seconds using {memory_usage} of memory',
			),
			'user' => array(
				'logged_in' => $this->auth->logged_in(),
				'id' => $this->auth->user_id(),
				'email' => $user['email'],
				'username' => $user['username'],
				'avatar' => $user_meta['avatar'],
				'name' => array(
					'full' => $user['first_name'].' '.$user['last_name'],
					'first' => $user['first_name'],
					'last' => $user['last_name']
				)
			)
		);

		return array_merge_recursive((array)$data, $tags);
	}

	// --------------------------------------------------------------------

	/**
	 * Primary menu
	 *
	 * @return  array
	 */
	private function _menu_primary()
	{
		$items = array();

		// Dashboard
		$items[] = array(
			'uri' => '/',
			'label' => 'Dashboard'
		);

		// Projects
		if ($this->user_model->has_permission('projects_general'))
		{
			// Projects children
			$children = array();

			// List of projects user has access to
			$children[] = array(
				'uri' => 'project/PROJECT',
				'label' => 'Project 1'
			);

			$children[] = array(
				'uri' => 'project/DEMO',
				'label' => 'Demo Project'
			);

			// Create a project
			if ($this->user_model->has_permission('projects_create'))
			{
				$children[] = array(
					'uri' => 'project/new',
					'label' => 'New Project'
				);
			}

			// Projects
			$items[] = array(
				'uri' => 'projects',
				'label' => 'Projects',
				'children' => $children
			);
		}

		// Issues
		if ($this->user_model->has_permission('issues_general'))
		{
			// Issues children
			$children = array(
				array(
					'uri' => 'issues',
					'label' => 'All Issues'
				)
			);

			// List of recently updated issues user has based on projects they have access to
			$children[] = array(
				'uri' => '',
				'label' => 'Updated Recently',
				'class' => 'updated-recently'
			);

			// List of latest updated issues user has based on projects they have access to
			$children[] = array(
				'uri' => '',
				'label' => 'Latest Issues',
				'class' => 'latest-issues'
			);

			// Issues
			$items[] = array(
				'uri' => 'issues',
				'label' => 'Issues',
				'children' => $children
			);
		}

		// Messages
		if ($this->user_model->has_permission('messages_general'))
		{
			// Messages children
			$children = array(
				array(
					'uri' => 'messages',
					'label' => 'Inbox'
				)
			);

			// Create message
			if ($this->user_model->has_permission('messages_create'))
			{
				$children[] = array(
					'uri' => 'messages/new',
					'label' => 'New Message'
				);
			}

			// List of sent messages
			if ($this->user_model->has_permission('messages_sent'))
			{
				$children[] = array(
					'uri' => 'messages/sent',
					'label' => 'Sent Messages'
				);
			}

			// Messages
			$items[] = array(
				'uri' => 'messages',
				'label' => 'Messages',
				'children' => $children
			);
		}

		// Administration
		if ($this->user_model->has_permission('administration_general'))
		{
			// Administration children
			$children = array(
				array(
					'uri' => 'administration/email',
					'label' => 'Email Templates'
				),
				array(
					'uri' => 'administration/resolution',
					'label' => 'Resolutions'
				),
				array(
					'uri' => 'administration/priority',
					'label' => 'Priorities'
				),
				array(
					'uri' => 'administration/type',
					'label' => 'Issues Types'
				),
				array(
					'uri' => 'administration/status',
					'label' => 'Statuses'
				),
				array(
					'uri' => 'administration/modules',
					'label' => 'Modules'
				),
				array(
					'uri' => 'administration/users',
					'label' => 'Users'
				),
				array(
					'uri' => 'administration/link',
					'label' => 'Issue Links'
				),
				array(
					'uri' => 'administration/role',
					'label' => 'Roles'
				)
			);

			// Administration
			$items[] = array(
				'uri' => 'administration',
				'label' => 'Administration',
				'children' => $children
			);
		}

		return $items;
	}

	// --------------------------------------------------------------------

	/**
	 * Footer menu
	 *
	 * @return  array
	 */
	private function _menu_secondary()
	{
		$items = array();

		// Dashboard
		$items[] = array(
			'uri' => '/',
			'label' => 'Dashboard'
		);

		// Projects
		if ($this->user_model->has_permission('projects_general'))
		{
			$items[] = array(
				'uri' => 'projects',
				'label' => 'Projects'
			);
		}

		// Issues
		if ($this->user_model->has_permission('issues_general'))
		{
			$items[] = array(
				'uri' => 'issues',
				'label' => 'Issues'
			);
		}

		// Messages
		if ($this->user_model->has_permission('messages_general'))
		{
			$items[] = array(
				'uri' => 'messages',
				'label' => 'Messages'
			);
		}

		// Administration
		if ($this->user_model->has_permission('administration_general'))
		{
			$items[] = array(
				'uri' => 'administration',
				'label' => 'Administration'
			);
		}

		return $items;
	}

	// --------------------------------------------------------------------

	/**
	 * Account menu
	 *
	 * @return  array
	 */
	private function _menu_account($name = 'Unknown User')
	{
		$items = array(
			array(
				'uri' => 'profile',
				'label' => $name,
				'children' => array(
					array(
						'uri' => 'profile',
						'label' => 'Profile'
					),
					array(
						'uri' => 'profile/preferences',
						'label' => 'Preferences'
					),
					array(
						'uri' => 'logout',
						'label' => 'Log Out'
					)
				)
			)
		);

		return $items;
	}

	// --------------------------------------------------------------------

	private function _object_to_array($object)
	{
		$array = array();

		foreach($object as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

}