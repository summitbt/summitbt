<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administration extends Admin_Controller {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * General administration
	 *
	 * @return  View
	 */
	public function index()
	{
		// Pass to view
		$data = array();

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Administration',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/administration', lang('administration_title_administration_general'), $data, $breadcrumbs);
	}

	// --------------------------------------------------------------------

	/**
	 * Modules
	 *
	 * @return  View
	 */
	public function modules()
	{
		// Load model
		$this->load->model('modules_model');

		// Modules
		$modules = array();

		// Modules directory
		$modules_dir = FCPATH.'modules/';

		// Items in the directory
		$listing = scandir($modules_dir);

		// Run through each one. Directories are considered modules
		foreach ($listing as $item)
		{
			// Not worried about parent directory links
			if ($item === '.' or $item === '..'){
				continue;
			}

			$object = $modules_dir.$item;

			// Directory found
			if (is_dir($object))
			{
				$file = $object.'/module.php';

				if (file_exists($file))
				{
					include $file;

					// If it's not set, set an empty array()
					if ( ! isset($module))
					{
						$module = array();
					}

					if ( ! empty($module))
					{
						$modules[] = $module;
					}
				}
			}
		}

		// Pass to view
		$data = array(
			'modules' => $modules,
			'active' => $this->modules_model->get_all_modules()
		);

		// Breadcrumbs
		$breadcrumbs = array(
			array(
				'label' => 'Administration',
				'link' => 'administration'
			),
			array(
				'label' => 'Modules',
				'link' => $this->uri->uri_string()
			)
		);

		// Build the page
		$this->build('administration/modules/list', lang('administration_title_modules_general'), $data, $breadcrumbs);
	}

}