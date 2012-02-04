<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Formation Class
 *
 * This class allows you to create valid HTML forms, with validation and required field indication.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Library
 * @author      David Freerksen
 * @link        https://github.com/dfreerksen/ci-formation
 */
class Formation {

	protected $CI;

	protected $_config = array(
		'id_prefix' => '',
		'id_suffix' => '',
		'field_wrapper_tag' => 'div',
		'field_wrapper_class' => 'field-wrapper',
		'radio_checkbox_label_position' => 'right', // left or right
		'radios_checkboxes_wrapper' => '',
		'radios_checkboxes_item_wrapper' => 'div',
		'fill_password_value' => FALSE,
		'collective_errors' => TRUE,
		'collective_error_prefix' => '',
		'collective_error_suffix' => '',
		'field_error_location' => 'before', // before, after, between
		'field_required' => '<span class="required">*</span>',
		'wrapper_validation_class' => 'wrapper-validation-error',
		'field_validation_class' => 'field-validation-error',
		'description_location' => 'after', // before, after
		'description_wrapper' => 'div',
		'description_class' => 'field-description',
		'default_rules' => array(
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
		)
	);

	protected $_structure = array(
		'action' => '',
		'attributes' => array(),
		'hidden' => array(),
		'multipart' => FALSE,
		'fields' => array(),
		'descriptions' => array()
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
		$this->CI->load->library('form_validation');

		// Load form helper
		$this->CI->load->helper('form');

		if ( ! empty($config))
		{
			$this->initialize($config);
		}

		log_message('debug', 'Formation Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize config values
	 *
	 * @param   array
	 * @return  Formation
	 */
	public function initialize($config = array())
	{
		foreach ($config as $name => $value)
		{
			// Special consideration for default_rules configuration
			if ($name == 'default_rules')
			{
				foreach ($value as $type => $rule)
				{
					if (isset($this->_config[$name][$type]))
					{
						$this->_config[$name][$type] = $rule;
					}
				}
			}

			// All of the other confog values
			else
			{
				if (isset($this->_config[$name]))
				{
					$this->_config[$name] = $value;
				}
			}
		}

		return $this;
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
		return isset($this->_config[$name]) ? $this->_config[$name] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * set magic method
	 *
	 * @param   string
	 * @return  null
	 */
	public function __set($name, $value)
	{
		if (isset($this->_config[$name]))
		{
			$this->_config[$name] = $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the form
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->generate();
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for action()
	 *
	 * @param   string  $action
	 * @param   array   $attributes
	 * @param   array   $hidden
	 * @return  Formation
	 */
	public function form($action = '', $attributes = '', $hidden = array())
	{
		return $this->action($action, $attributes, $hidden);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the action for the form
	 *
	 * @param   string  $action
	 * @param   array   $attributes
	 * @param   array   $hidden
	 * @return  Formation
	 */
	public function action($action = '', $attributes = '', $hidden = array())
	{
		$this->_structure['action'] = $action;
		$this->_structure['attributes'] = $attributes;

		if ( ! empty($hidden))
		{
			$this->hidden($hidden);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set form as multipart
	 *
	 * @param   bool    $multi
	 * @return  Formation
	 */
	public function multipart($multi = TRUE)
	{
		$this->_structure['multipart'] = $multi;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add hidden fields
	 *
	 * @param   string|array    $key
	 * @param   string          $value
	 * @return  Formation
	 */
	public function hidden($key = '', $value = '')
	{
		// If a key/value was passed instead of a string, turn it into an array
		if (is_string($key))
		{
			$key = array(
				$key => $value
			);
		}

		$this->_structure['hidden'] = array_merge($this->_structure['hidden'], $key);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Open fieldset
	 *
	 * @param   string  $legend
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function fieldset_open($legend = '', $attributes = array())
	{
		$this->_structure['fields'][] = array(
			'type' => 'fieldset_open',
			'legend' => $legend,
			'attributes' => $attributes,
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Close fieldset
	 *
	 * @return  Formation
	 */
	public function fieldset_close()
	{
		$this->_structure['fields'][] = array(
			'type' => 'fieldset_close',
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Code block
	 *
	 * @param   string  $code
	 * @param   bool    $wrap
	 * @return  Formation
	 */
	public function block($code = '', $wrap = FALSE)
	{
		$this->_structure['fields'][] = array(
			'type' => 'block',
			'code' => $code,
			'wrap' => $wrap
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Field description
	 *
	 * @param   string  $field
	 * @param   string  $text
	 * @param   string  $position
	 * @return  Formation
	 */
	public function description($field = '', $text = '', $position = '')
	{
		$this->_structure['descriptions'][$field] = array(
			'text' => $text,
			'position' => $position
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a honeypot field
	 *
	 * @param   string  $name
	 * @param   string  $rules
	 * @return  Formation
	 */
	public function honeypot($name = 'honeypot', $rules = '')
	{
		return $this->_input('honeypot', $name, '', $rules);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a text input field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function text($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		return $this->_input('text', $name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for text()
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function input($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		return $this->text($name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a file input field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function file($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		$this->multipart(TRUE);

		return $this->_input('file', $name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for file()
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function upload($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		return $this->file($name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a password input field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function password($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		return $this->_input('password', $name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a textarea input field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function textarea($name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		return $this->_input('textarea', $name, $label, $rules, $value, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a select field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   string  $selected
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function select($name = '', $label = '', $options = array(), $rules = '', $selected = '', $attributes = array())
	{
		return $this->_select('select', FALSE, $name, $label, $options, $rules, $selected, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a multi select field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   string  $selected
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function multiselect($name = '', $label = '', $options = array(), $rules = '', $selected = array(), $attributes = array())
	{
		return $this->_select('select', TRUE, $name, $label, $options, $rules, $selected, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for select()
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   string  $selected
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function dropdown($name = '', $label = '', $options = array(), $rules = '', $selected = '', $attributes = array())
	{
		return $this->select($name, $label, $options, $rules, $selected, $attributes);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a checkbox field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $value
	 * @param   string  $rules
	 * @param   bool    $checked
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	public function checkbox($name = '', $label = '', $value = '', $rules = '', $checked = FALSE, $attributes = array(), $label_position = NULL)
	{
		return $this->_radio_checkbox('checkbox', $name, $label, $value, $rules, $checked, $attributes, $label_position);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a radio field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $value
	 * @param   string  $rules
	 * @param   bool    $checked
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	public function radio($name = '', $label = '', $value = '', $rules = '', $checked = FALSE, $attributes = array(), $label_position = NULL)
	{
		return $this->_radio_checkbox('radio', $name, $label, $value, $rules, $checked, $attributes, $label_position);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a group of checkbox fields
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	public function checkboxes($name = '', $label = '', $options = array(), $rules = '', $attributes = array(), $label_position = NULL)
	{
		return $this->_radios_checkboxes('checkboxes', $name, $label, $options, $rules, $attributes, $label_position);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a group of radio fields
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	public function radios($name = '', $label = '', $options = array(), $rules = '', $attributes = array(), $label_position = NULL)
	{
		return $this->_radios_checkboxes('radios', $name, $label, $options, $rules, $attributes, $label_position);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a submit button
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function submit($name = '', $value = 'Submit', $attributes = array(), $wrap = FALSE)
	{
		return $this->_button('submit', $name, $value, $attributes, $wrap);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a reset button
	 *
	 * @param   string  $name
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function reset($name = '', $value = 'Reset', $attributes = array(), $wrap = FALSE)
	{
		return $this->_button('reset', $name, $value, $attributes, $wrap);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a generic button
	 *
	 * @param   string  $name
	 * @param   string  $content
	 * @param   array   $attributes
	 * @return  Formation
	 */
	public function button($name = '', $content = 'Button', $attributes = array(), $wrap = FALSE)
	{
		return $this->_button('button', $name, $content, $attributes, $wrap);
	}

	// --------------------------------------------------------------------

	/**
	 * Validate form
	 *
	 * @return  bool
	 */
	public function validate()
	{
		return $this->CI->form_validation->run();
	}

	// --------------------------------------------------------------------

	/**
	 * Generate finalized form
	 *
	 * @return  string
	 */
	public function generate()
	{
		$generated = '';

		// Open form
		$generated .= $this->_form_open();

		// If this is a multipart form, always display for upload errors above the form
		if ($this->_structure['multipart'])
		{
			// Load library just in case it hasn't been loaded
			$this->CI->load->library('upload');

			$generated .= $this->CI->upload->display_errors($this->_config['collective_error_prefix'], $this->_config['collective_error_suffix']);
		}

		// Validation errors
		if ($this->_config['collective_errors'])
		{
			if ($this->_config['collective_error_prefix'] AND $this->_config['collective_error_suffix'])
			{
				$this->CI->form_validation->set_error_delimiters($this->_config['collective_error_prefix'], $this->_config['collective_error_suffix']);
			}

			$generated .= validation_errors();
		}

		// Generate the form
		$generated .= $this->_generate();

		// Close form
		$generated .= $this->_form_close();

		// Done! Return the form
		return $generated;
	}

	// --------------------------------------------------------------------

	/**
	 * Alias for generate()
	 *
	 * @return  string
	 */
	public function render()
	{
		return $this->generate();
	}

	// --------------------------------------------------------------------

	/**
	 * Add input field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	private function _input($type = 'text', $name = '', $label = '', $rules = '', $value = '', $attributes = array())
	{
		$this->_structure['fields'][] = array(
			'type' => $type,
			'name' => $name,
			'label' => $label,
			'rules' => $rules,
			'value' => $value,
			'attributes' => $attributes
		);

		// Set the validation
		$this->_set_validation($type, $name, $label, $rules);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add select field
	 *
	 * @param   string  $type
	 * @param   bool    $multiselect
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $selected
	 * @param   array   $attributes
	 * @return  Formation
	 */
	private function _select($type = 'select', $multiselect = FALSE, $name = '', $label = '', $options = array(), $rules = '', $selected = '', $attributes = array())
	{
		if ($multiselect)
		{
			$name .= '[]';
		}

		$this->_structure['fields'][] = array(
			'type' => $type,
			'multiselect' => $multiselect,
			'name' => $name,
			'label' => $label,
			'options' => $options,
			'rules' => $rules,
			'selected' => $selected,
			'attributes' => $attributes
		);

		// Set the validation
		$this->_set_validation($type, $name, $label, $rules);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a single radio or checkbox field
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $value
	 * @param   string  $rules
	 * @param   bool    $checked
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	private function _radio_checkbox($type = 'checkbox', $name = '', $label = '', $value = '', $rules = '', $checked = FALSE, $attributes = array(), $label_position = NULL)
	{
		$this->_structure['fields'][] = array(
			'type' => $type,
			'checked' => $checked,
			'name' => $name,
			'label' => $label,
			'label_position' => $label_position,
			'rules' => $rules,
			'value' => $value,
			'attributes' => $attributes
		);

		// Set the validation
		$this->_set_validation($type, $name, $label, $rules);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a group of radio or checkbox fields
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $label
	 * @param   array   $options
	 * @param   string  $rules
	 * @param   array   $attributes
	 * @param   null    $label_position
	 * @return  Formation
	 */
	private function _radios_checkboxes($type = 'checkboxes', $name = '', $label = '', $options = array(), $rules = '', $attributes = array(), $label_position = NULL)
	{
		$this->_structure['fields'][] = array(
			'type' => $type,
			'name' => $name,
			'label' => $label,
			'label_position' => $label_position,
			'options' => $options,
			'rules' => $rules,
			'attributes' => $attributes
		);

		// Set the validation
		$this->_set_validation($type, $name.'[]', $label, $rules);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add button field
	 *
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @param   string  $value
	 * @param   array   $attributes
	 * @return  Formation
	 */
	private function _button($type = 'button', $name = '', $value = '', $attributes = array(), $wrap = FALSE)
	{
		$this->_structure['fields'][] = array(
			'type' => $type,
			'name' => $name,
			'value' => $value,
			'attributes' => $attributes,
			'wrap' => $wrap
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate form
	 *
	 * @return  string
	 */
	private function _generate()
	{
		$fields = '';

		foreach ($this->_structure['fields'] as $field)
		{
			// Field type isn't available. Skip this iteration
			if ( ! array_key_exists('type', $field))
			{
				continue;
			}

			$type = $field['type'];

			$function = '_render_'.$type;

			if (method_exists($this, $function))
			{
				$fields .= $this->{$function}($field);
			}

			else
			{
				// Field is an unknown type. Just log a message
				log_message('error', 'Formation: Cannot generate form field for field of unknown type \''.$type.'\'.');
			}
		}

		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Open form
	 *
	 * @return  string
	 */
	private function _form_open()
	{
		$action = $this->_structure['action'];
		$attributes = $this->_structure['attributes'];
		$hidden = $this->_structure['hidden'];

		// Multipart form
		if ($this->_structure['multipart'])
		{
			return form_open_multipart($action, $attributes, $hidden)."\n";
		}

		// Not multipart form
		return form_open($action, $attributes, $hidden)."\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Close form
	 *
	 * @return  string
	 */
	private function _form_close()
	{
		return form_close();
	}

	// --------------------------------------------------------------------

	/**
	 * Render a fieldset open
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_fieldset_open($field = array())
	{
		// Name of the fieldset
		if ( ! array_key_exists('id', $field['attributes']))
		{
			$field['attributes']['id'] = $this->_field_id($field['legend']);
		}

		return form_fieldset($field['legend'], $field['attributes']);
	}

	// --------------------------------------------------------------------

	/**
	 * Render a fieldset close
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_fieldset_close($field = array())
	{
		return form_fieldset_close()."\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Render a block
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_block($field = array())
	{
		$output = '';

		// Open wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_open('block');
		}

		// Insert the block
		$output .= $field['code']."\n";

		// Close wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_close('');
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a honeypot field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_honeypot($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Build field settings
		$attr = (array)$field['attributes'];

		$attr['style'] = 'display:none;';

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => '',
		);

		$settings = array_merge($attr, $fld);

		// Build the field
		$output .= form_input($settings);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a text field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_text($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'text');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => set_value($name, $field['value'])
		);

		$settings = array_merge($attr, $fld);

		// Build the label
		$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the field
		$output .= form_input($settings);

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a textarea field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_textarea($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'textarea');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => set_value($name, $field['value'])
		);

		$settings = array_merge($attr, $fld);

		// Build the label
		$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the field
		$output .= form_textarea($settings);

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a password field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_password($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'password');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		// Value of password field
		$value = '';

		if ($this->_config['fill_password_value'])
		{
			$value = set_value($name, $field['value']);
		}

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => $value
		);

		$settings = array_merge($attr, $fld);

		// Build the label
		$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the field
		$output .= form_password($settings);

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a file upload field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_file($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'file');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => set_value($name, $field['value'])
		);

		$settings = array_merge($attr, $fld);

		// Build the label
		$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the field
		$output .= form_upload($settings);

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a select field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_select($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'select');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		// Unset settings we don't want to override
		unset($attr['name']);

		// Give it the correct ID attribute
		$attr['id'] = $id;

		// Now built the attribute string
		$attr = $this->_attributes($attr);

		// Build the label
		$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the field (multiselect)
		if ($field['multiselect'])
		{
			// Selected
			$selected = array();

			if ($this->CI->input->get_post($name) !== FALSE)
			{
				$selected = $this->CI->input->get_post($name);
			}

			else
			{
				foreach ($field['options'] as $key => $value)
				{
					if (array_key_exists('selected', $field) AND in_array($key, $field['selected']))
					{
						$selected[] = $key;
					}
				}
			}

			$output .= form_multiselect($name, $field['options'], $selected, $attr);
		}

		// Build the field (single select)
		else
		{
			// Selected
			$selected = '';

			if ($this->CI->input->get_post($name) !== FALSE)
			{
				$selected = $this->CI->input->get_post($name);
			}

			else
			{
				foreach ($field['options'] as $key => $value)
				{
					if (array_key_exists('selected', $field) AND $field['selected'] != '' AND $field['selected'] == $key)
					{
						$selected = $key;
						break;
					}
				}
			}

			$output .= form_dropdown($name, $field['options'], $selected, $attr);
		}

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a single checkbox field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_checkbox($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'checkbox');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => $field['value']
		);

		// Checked
		if (set_checkbox($name, $field['value'], $field['checked']))
		{
			$fld['checked'] = 'checked';
		}

		$settings = array_merge($attr, $fld);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the label (left)
		if (strtolower($field['label_position']) == 'left')
		{
			$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);
		}

		// Build the field
		$output .= form_checkbox($settings);

		// Build the label (right)
		if (strtolower($field['label_position']) != 'left')
		{
			$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);
		}

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a single radio field
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_radio($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'radio');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => $field['value']
		);

		// Checked
		if (set_radio($name, $field['value'], $field['checked']))
		{
			$fld['checked'] = 'checked';
		}

		$settings = array_merge($attr, $fld);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Build the label (left)
		if (strtolower($field['label_position']) == 'left')
		{
			$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);
		}

		// Build the field
		$output .= form_radio($settings);

		// Build the label (right)
		if (strtolower($field['label_position']) != 'left')
		{
			$output .= $this->_field_label($field['label'], $id, $field['rules'], $field['type']);
		}

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a multiple checkbox fields
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_checkboxes($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'checkboxes');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Open radios/checkboxes wrapper
		if ($this->_config['radios_checkboxes_wrapper'])
		{
			$output .= '<'.$this->_config['radios_checkboxes_wrapper'].'>';
		}

		foreach ($field['options'] as $item)
		{
			// Open radios/checkboxes item wrapper
			if ($this->_config['radios_checkboxes_item_wrapper'])
			{
				$output .= '<'.$this->_config['radios_checkboxes_item_wrapper'].'>';
			}

			$name = $this->_field_name($field['name']).'[]';
			$id = $this->_field_id($this->_field_name($field['name'].'_'.$item['value']));

			$fld = array(
				'name' => $name,
				'id' => $id,
				'value' => $item['value']
			);

			// Checked
			if (array_key_exists('checked', $item) AND set_checkbox($name, $item['value'], $item['checked']))
			{
				$fld['checked'] = 'checked';
			}

			$settings = array_merge($attr, $fld);

			// Build the label (left)
			if (strtolower($field['label_position']) == 'left')
			{
				$output .= form_label($item['label'], $id)."\n";
			}

			// Build the field
			$output .= form_checkbox($settings);

			// Build the label (right)
			if (strtolower($field['label_position']) != 'left')
			{
				$output .= form_label($item['label'], $id)."\n";
			}

			// Close radios/checkboxes item wrapper
			if ($this->_config['radios_checkboxes_item_wrapper'])
			{
				$output .= '</'.$this->_config['radios_checkboxes_item_wrapper'].'>';
			}
		}

		// Close radios/checkboxes wrapper
		if ($this->_config['radios_checkboxes_wrapper'])
		{
			$output .= '</'.$this->_config['radios_checkboxes_wrapper'].'>';
		}

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a multiple radio fields
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_radios($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);

		// Validation
		$validation = form_error($name);

		// Open wrapper
		$output .= $this->_wrapper_open($name, 'radios');

		// Add validation error to the field
		$attr = $this->_field_validation_class($validation, (array)$field['attributes']);

		// Validation error between
		if ( ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'between')
		{
			$output .= $validation;
		}

		// Open radios/checkboxes wrapper
		if ($this->_config['radios_checkboxes_wrapper'])
		{
			$output .= '<'.$this->_config['radios_checkboxes_wrapper'].'>';
		}

		foreach ($field['options'] as $item)
		{
			// Open radios/checkboxes item wrapper
			if ($this->_config['radios_checkboxes_item_wrapper'])
			{
				$output .= '<'.$this->_config['radios_checkboxes_item_wrapper'].'>';
			}

			$name = $this->_field_name($field['name']).'[]';
			$id = $this->_field_id($this->_field_name($field['name'].'_'.$item['value']));

			$fld = array(
				'name' => $name,
				'id' => $id,
				'value' => $item['value']
			);

			// Checked
			if (array_key_exists('checked', $item) AND set_radio($name, $item['value'], $item['checked']))
			{
				$fld['checked'] = 'checked';
			}

			$settings = array_merge($attr, $fld);

			// Build the label (left)
			if (strtolower($field['label_position']) == 'left')
			{
				$output .= form_label($item['label'], $id)."\n";
			}

			// Build the field
			$output .= form_radio($settings);

			// Build the label (right)
			if (strtolower($field['label_position']) != 'left')
			{
				$output .= form_label($item['label'], $id)."\n";
			}

			// Open radios/checkboxes item wrapper
			if ($this->_config['radios_checkboxes_item_wrapper'])
			{
				$output .= '</'.$this->_config['radios_checkboxes_item_wrapper'].'>';
			}
		}

		// Close radios/checkboxes wrapper
		if ($this->_config['radios_checkboxes_wrapper'])
		{
			$output .= '</'.$this->_config['radios_checkboxes_wrapper'].'>';
		}

		// Close wrapper
		$output .= $this->_wrapper_close($name);

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a submit button
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_submit($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Open wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_open($name, 'submit');
		}

		// Build field settings
		$attr = (array)$field['attributes'];

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => $field['value']
		);

		$settings = array_merge($attr, $fld);

		// Build the field
		$output .= form_submit($settings);

		// Close wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_close($name);
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a reset button
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_reset($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Open wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_open($name, 'reset');
		}

		// Build field settings
		$attr = (array)$field['attributes'];

		$fld = array(
			'name' => $name,
			'id' => $id,
			'value' => $field['value']
		);

		$settings = array_merge($attr, $fld);

		// Build the field
		$output .= form_reset($settings);

		// Close wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_close($name);
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Render a general button
	 *
	 * @param   array   $field
	 * @return  string
	 */
	private function _render_button($field = array())
	{
		$output = '';

		$name = $this->_field_name($field['name']);
		$id = $this->_field_id($name);

		// Open wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_open($name, 'button');
		}

		// Build field settings
		$attr = (array)$field['attributes'];

		$fld = array(
			'name' => $name,
			'id' => $id,
			'content' => $field['value']
		);

		$settings = array_merge($attr, $fld);

		// Build the field
		$output .= form_button($settings);

		// Close wrapper
		if ($field['wrap'])
		{
			$output .= $this->_wrapper_close($name);
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Set validation
	 *
	 * @param   string  $type
	 * @param   string  $name
	 * @param   string  $label
	 * @param   string  $rules
	 * @return  void
	 */
	private function _set_validation($type = 'text', $name = '', $label = '', $rules = '')
	{
		$name = $this->_field_name($name);

		if ( ! $rules)
		{
			$rules = $this->_config['default_rules'][$type];
		}

		$this->CI->form_validation->set_rules($name, $label, $rules);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate name for the field
	 *
	 * @param   string  $name
	 * @return  string
	 */
	private function _field_name($name = '')
	{
		// Load helper
		$this->CI->load->helper('text');

		// Replace accented characters
		$name = convert_accented_characters($name);

		// Replace spaces with underscore
		$name = preg_replace('/\s/', '_', $name);

		return $name;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate id for the field
	 *
	 * @param   string  $name
	 * @return  string
	 */
	private function _field_id($name = '')
	{
		// Replace brackets and spaces with underscore
		$name = preg_replace('/[\s\[\]]/', '_', $name);

		// Remove trailing underscore
		$name = preg_replace('/_$/', '', $name);

		return strtolower($this->_config['id_prefix'].$name.$this->_config['id_suffix']);
	}

	// --------------------------------------------------------------------

	/**
	 * Field label
	 *
	 * @param   string  $label
	 * @param   string  $id
	 * @return  string
	 */
	private function _field_label($label = '', $id = '', $rules = '', $type = 'text')
	{
		$output = '';

		// Default rules
		if ( ! $rules)
		{
			$rules = $this->_config['default_rules'][$type];
		}

		// Description (left)
		$output .= $this->_description('left', $id);

		// Label
		$output .= $label;

		// Add required indicator
		if (preg_match("/required/i", $rules))
		{
			$output .= $this->_config['field_required'];
		}

		// Description (right)
		$output .= $this->_description('right', $id);

		return form_label($output, $id)."\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Field description
	 *
	 * @param   string  $position
	 * @param   string  $name
	 * @return  string
	 */
	private function _description($position = '', $name = '')
	{
		$description = '';

		// If there is a description
		if (array_key_exists($name, $this->_structure['descriptions']))
		{
			// If position not defined, use the default
			if ( ! $loc = $this->_structure['descriptions'][$name]['position'])
			{
				$loc = $this->_config['description_location'];
			}

			// Set the description if this is the correction position
			if (strtolower($loc) == strtolower($position))
			{
				// Wrapper
				$wrapper = $this->_config['description_wrapper'];

				// Opening description wrapper
				if ($wrapper)
				{
					$class = '';

					if ($this->_config['description_class'])
					{
						$class = ' class="'.$this->_config['description_class'].'"';
					}

					$description .= '<'.$wrapper.$class.'>';
				}

				$description .= $this->_structure['descriptions'][$name]['text'];

				// Closing description wrapper
				if ($wrapper)
				{
					$description .= '</'.$wrapper.'>';
				}
			}
		}

		return $description;
	}

	// --------------------------------------------------------------------

	/**
	 * Add validation error class for fields
	 *
	 * @param   string  $validation
	 * @param   array   $attr
	 * @return  string
	 */
	private function _field_validation_class($validation = '', $attr = array())
	{
		if ($validation)
		{
			if (array_key_exists('class', $attr))
			{
				// String with length
				if(is_string($attr['class']) AND trim($attr['class']) != '')
				{
					$attr['class'] .= ' '.$this->_config['field_validation_class'];
				}

				// String without length
				else
				{
					$attr['class'] = $this->_config['field_validation_class'];
				}
			}

			else
			{
				$attr['class'] = $this->_config['field_validation_class'];
			}
		}

		return $attr;
	}

	// --------------------------------------------------------------------

	/**
	 * Field wrapper open
	 *
	 * @return  string
	 */
	private function _wrapper_open($name = '', $type = '')
	{
		// Validation
		$validation = form_error($name);

		$attr = array($this->_config['field_wrapper_class']);
			$attr[] = 'field-'.$name;
			$attr[] = 'field-type-'.$type;

		// Add validation error to the wrapper
		if ($validation)
		{
			$attr[] = $this->_config['wrapper_validation_class'];
		}

		$output = '<'.$this->_config['field_wrapper_tag'].' class="'.implode(' ', $attr).'">'."\n";

		// Description (before)
		$output .= $this->_description('before', $name);

		if ($name AND ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'before')
		{
			$output .= $validation;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Field wrapper close
	 *
	 * @return  string
	 */
	private function _wrapper_close($name = '')
	{
		$output = '';

		// Validation
		$validation = form_error($name);

		if ($name AND ! $this->_config['collective_errors'] AND $this->_config['field_error_location'] == 'after')
		{
			$output .= $validation;
		}

		// Description (after)
		$output .= $this->_description('after', $name);

		$output .= '</'.$this->_config['field_wrapper_tag'].'>'."\n";

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate a string of HTML attributes from an array
	 *
	 * @param   array   $attr
	 * @return  string
	 */
	private function _attributes($attr = array())
	{
		$attributes = '';

		foreach ($attr as $attribute => $value)
		{
			// If the value is an array, assume it is for class groups
			if (is_array($value))
			{
				$value = implode(' ', $value);
			}

			$attributes .= ' '. $attribute.'="'.$value.'"';
		}

		return $attributes;
	}

}
// END Formation class

/* End of file Formation.php */
/* Location: ./application/libraries/Formation.php */