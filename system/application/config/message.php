<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Message Config
 *
 * This class allows you to create valid HTML forms, with validation and required field indication.
 *
 * @package     CodeIgniter
 * @subpackage  Config
 * @category    Config
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-message
 */

/*
|--------------------------------------------------------------------------
| Session Variable
|--------------------------------------------------------------------------
|
| Session variable that holds the messages to be displays on the new page
| visit.
|
*/
$config['session_var'] = 'messages';

/*
|--------------------------------------------------------------------------
| Type Wrapper
|--------------------------------------------------------------------------
|
| Wrapper element that separates error types. This value can also be empty
| to only use a message wrapper.
|
*/
$config['type_wrapper'] = 'ul';

/*
|--------------------------------------------------------------------------
| Type Wrapper CSS Class
|--------------------------------------------------------------------------
|
| CSS class(es) to be used for the type wrapper.
|
*/
$config['type_wrapper_css'] = 'messages';

/*
|--------------------------------------------------------------------------
| Type Prefix CSS Class
|--------------------------------------------------------------------------
|
| Prefix for the message type to be added to the type wrapper.
|
*/
$config['type_wrapper_css_prefix'] = 'type-';

/*
|--------------------------------------------------------------------------
| Message wrapper
|--------------------------------------------------------------------------
|
| Wrapper element for each message.
|
*/
$config['wrapper'] = 'li';

/*
|--------------------------------------------------------------------------
| Message Wrapper CSS Class
|--------------------------------------------------------------------------
|
| CSS class(es) to be used for the message wrapper.
|
*/
$config['wrapper_css'] = 'msg';

/*
|--------------------------------------------------------------------------
| Message Prefix CSS Class
|--------------------------------------------------------------------------
|
| Prefix to be added to the message wrapper.
|
*/
$config['wrapper_css_prefix'] = 'msg-';

/*
|--------------------------------------------------------------------------
| Validation Errors Included
|--------------------------------------------------------------------------
|
| Include form validation errors. This requires the error_array() method
| found in MY_Form_validation.php.
|
*/
$config['validation_errors'] = FALSE;

/* End of file message.php */
/* Location: ./application/config/message.php */