<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// Defaults
$route['default_controller'] = 'dashboard';
$route['404_override'] = 'error404';

// Access
$route['login'] = 'access';
$route['logout'] = 'access/logout';
$route['forgot'] = 'access/forgot';

// Project
$route['project/([a-zA-Z0-9-_]+)'] = 'project/index/$1';
$route['project/new'] = 'project/create';

// Issue
$route['project/([a-zA-Z0-9-_]+)/issue/([a-zA-Z0-9-_]+)'] = 'issue/index/$2/$1';
$route['issue/([a-zA-Z0-9-_]+)'] = 'issue/index/$1';

// Create issue
$route['project/([a-zA-Z0-9-_]+)/issue/new'] = 'issue/create/$1';
$route['issue/new'] = 'issue/create';

// User
$route['user/([a-zA-Z0-9-_]+)'] = 'user/index/$1';

// Search
$route['search/results/([a-zA-Z0-9]+)/page/(:num)'] = 'search/results/$1/$2';

// Messages
$route['messages/page/(:num)'] = 'messages/index/$1';
$route['messages/sent/page/(:num)'] = 'messages/sent/$1';
$route['messages/new'] = 'messages/create';

// Administration
$route['administration/role/new'] = 'administration/role/create';
$route['administration/link/new'] = 'administration/link/create';
$route['administration/status/new'] = 'administration/status/create';
$route['administration/type/new'] = 'administration/type/create';
$route['administration/priority/new'] = 'administration/priority/create';
$route['administration/resolution/new'] = 'administration/resolution/create';

/* End of file routes.php */
/* Location: ./application/config/routes.php */