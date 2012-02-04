<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Formation Config
 *
 * Set the default configuration for all Formation forms.
 *
 * @package     CodeIgniter
 * @subpackage  Config
 * @category    Config
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-formation
 */

/*
|--------------------------------------------------------------------------
| ID Prefix
|--------------------------------------------------------------------------
|
| You may prefix all form fields with a string so to not cause name
| collisions with other elements on the page.
|
*/
$config['id_prefix'] = '';

/*
|--------------------------------------------------------------------------
| ID Suffix
|--------------------------------------------------------------------------
|
| You may add a suffix to all form fields with a string so to not cause
| name collisions with other elements on the page.
|
*/
$config['id_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Wrapper Element
|--------------------------------------------------------------------------
|
| All fields are wrapped with an HTML element to separate them from other
| fields.
| Example:
|
|	div
|
*/
$config['field_wrapper_tag'] = 'div';

/*
|--------------------------------------------------------------------------
| Class Assigned To Wrapper
|--------------------------------------------------------------------------
|
| Base class(es) to be added to the field wrapper
| Example:
|
|	field-wrapper
|
| When added to the page, this would result in:
|	<div class="field-wrapper">...</div>
|
*/
$config['field_wrapper_class'] = 'field-wrapper';

/*
|--------------------------------------------------------------------------
| Label Position Of Radio And Checkbox
|--------------------------------------------------------------------------
|
| Location of label for radio and checkbox form elements. Valid options
| are 'left' and 'right'
|
*/
$config['radio_checkbox_label_position'] = 'right';

/*
|--------------------------------------------------------------------------
| Radios/Checkboxes Group Wrapper
|--------------------------------------------------------------------------
|
| HTML element for grouped radio and checkbox items
|
*/
$config['radios_checkboxes_wrapper'] = 'ul';

/*
|--------------------------------------------------------------------------
| Radios/Checkboxes Item Wrapper
|--------------------------------------------------------------------------
|
| HTML element for grouped radio and checkbox items
|
*/
$config['radios_checkboxes_item_wrapper'] = 'li';

/*
|--------------------------------------------------------------------------
| Fill Password Values
|--------------------------------------------------------------------------
|
| Fill the password field with value both before and after validation.
|
*/
$config['fill_password_value'] = FALSE;

/*
|--------------------------------------------------------------------------
| Collective Errors
|--------------------------------------------------------------------------
|
| Collect all validation errors together and place them above the form. If
| set to FALSE, form validation will be displayed next to each field.
|
*/
$config['collective_errors'] = TRUE;

/*
|--------------------------------------------------------------------------
| Collective Error Prefix
|--------------------------------------------------------------------------
|
| Opening field validation for collected validation errors. This can be
| applied through this configuration or through
| $this->form_validation->set_error_delimiters('<div class="error">', '...');
|
*/
$config['collective_error_prefix'] = '';

/*
|--------------------------------------------------------------------------
| Collective Error Suffix
|--------------------------------------------------------------------------
|
| Closing field validation for collected validation errors. This can be
| applied through this configuration or through
| $this->form_validation->set_error_delimiters('...', '</div>');
|
*/
$config['collective_error_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Field Validation Error Location
|--------------------------------------------------------------------------
|
| When the 'collective_errors' configuration value is set to 'FALSE',
| validation will be placed next to form fields. Valid options
| are 'before', 'after' and 'between'
|
| before - Places validation before both label and form field
| after - Places validation after both label and form field
| between - Places validation between label and form field
|
*/
$config['field_error_location'] = 'before'; // before, after, between

/*
|--------------------------------------------------------------------------
| Required Field Identifier
|--------------------------------------------------------------------------
|
| Sets the visual notification that a form field is required
|
*/
$config['field_required'] = '<span class="required">*</span>';

/*
|--------------------------------------------------------------------------
| Validation Class For Wrapper
|--------------------------------------------------------------------------
|
| Validation CSS class to be use for field wrappers.
|
*/
$config['wrapper_validation_class'] = 'wrapper-validation-error';

/*
|--------------------------------------------------------------------------
| Description Location
|--------------------------------------------------------------------------
|
| Location of the description field. Valid options
| are 'before', 'after', 'between', 'left' and 'right'
|
| before - Places description before both label and form field
| after - Places description after both label and form field
| between - Places description between label and form field
| left - Places description inside label, before the label text
| right - Places description inside label, after the label text and required indicator
|
*/
$config['description_location'] = 'right';

/*
|--------------------------------------------------------------------------
| Description Wrapper
|--------------------------------------------------------------------------
|
| All descriptions are wrapped with an HTML element to separate them from other
| content.
| Example:
|
|	div
|
*/
$config['description_wrapper'] = 'span';

/*
|--------------------------------------------------------------------------
| Description Class For Wrapper
|--------------------------------------------------------------------------
|
| Description CSS class to be use for description containers.
|
*/
$config['description_class'] = 'field-description';

/*
|--------------------------------------------------------------------------
| Default Validation Rules
|--------------------------------------------------------------------------
|
| If validation is not defined on the field, these are the validation
| rules that will be applied.
|
*/
$config['default_rules'] = array(
	'honeypot' => 'trim|xss_clean',
	'text' => 'trim|xss_clean',
	'file' => 'trim|xss_clean',
	'password' => 'trim|xss_clean',
	'textarea' => 'trim|xss_clean',
	'select' => 'trim',
	'checkbox' => 'trim',
	'checkboxes' => 'trim',
	'radio' => 'trim',
	'radios' => 'trim'
);