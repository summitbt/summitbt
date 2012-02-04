<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Assets Config
 *
 * Set the default configuration for Assets.
 *
 * @package     CodeIgniter
 * @subpackage  Config
 * @category    Config
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-assets
 */

/*
|--------------------------------------------------------------------------
| Combine Javascript
|--------------------------------------------------------------------------
|
| Combine all external Javascript files into a single file.
|
*/
$config['combine_js'] = FALSE;

/*
|--------------------------------------------------------------------------
| Minify Javascript
|--------------------------------------------------------------------------
|
| Minify external Javascript contents at the same time of combining. Only
| applies if 'combine_js' is set to TRUE.
|
| WARNING: When minifying Javascript, the lack or semicolons (;) become an
| issue
|
*/
$config['minify_js'] = FALSE;

/*
|--------------------------------------------------------------------------
| Combine stylesheets
|--------------------------------------------------------------------------
|
| Combine all external CSS files into a single file.
|
*/
$config['combine_css'] = FALSE;

/*
|--------------------------------------------------------------------------
| Minify stylesheets
|--------------------------------------------------------------------------
|
| Minify external CSS contents at the same time of combining. Only
| applies if 'combine_css' is set to TRUE.
|
*/
$config['minify_css'] = FALSE;

/*
|--------------------------------------------------------------------------
| Gzip of Javascript and CSS assets
|--------------------------------------------------------------------------
|
| Type of gzip to use for the assets. Available values are 'gz',
| 'phpgzip', 'none'. If this value is not set, it will not gzip the files.
| Only applies if 'combine_css' or 'combine_js' is set to TRUE.
|
| gz - Adds .gz after the file extension. Decompression of the gzipped
|      file must be done through the .htaccess file.
| phpgzip - Saves cached files with a .php extension and PHP does the
|           gzip using headers.
| none - Simply saves the files. Gzip can be applied through the .htaccess
|        file if needed
|
*/
$config['asset_gzip'] = '';

/*
|--------------------------------------------------------------------------
| Default group
|--------------------------------------------------------------------------
|
| Name of the default group to add assets into. This is the group that, if
| a group is not specified, the asset will go into this group.
|
*/
$config['group_default'] = 'general';

/*
|--------------------------------------------------------------------------
| Default group priority
|--------------------------------------------------------------------------
|
| Default priority that a new group will be set to. When creating a new
| group, if a priority is not set, the group will have this priority level.
|
*/
$config['priority_group'] = 20;

/*
|--------------------------------------------------------------------------
| Default asset priority
|--------------------------------------------------------------------------
|
| Default priority that a new asset will be set to. When adding a new
| asset, if a priority is not set, the asset will have this priority level.
|
*/
$config['priority_asset'] = 20;

/*
|--------------------------------------------------------------------------
| Asset base
|--------------------------------------------------------------------------
|
| From the root of the site, the folder path that will be followed when
| loading the assets for a group. If a new group does not set a base path,
| this path will be followed.
|
*/
$config['asset_base_path'] = NULL;

/*
|--------------------------------------------------------------------------
| Javascript/CSS cache directory
|--------------------------------------------------------------------------
|
| From the root of the site, the path where combined/minified Javascript
| and CSS files will be saved.
|
*/
$config['asset_cache_dir'] = '';

/*
|--------------------------------------------------------------------------
| Combine external
|--------------------------------------------------------------------------
|
| Combine external assets (assets from another domain or CDN).
|
| * NOT CURRENTLY BEING USED
|
*/
$config['combine_external'] = FALSE;

/* End of file assets.php */
/* Location: ./application/config/assets.php */