<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Is{

	protected $_is = array();

	public function __construct()
	{
		// Nothing
	}

	public function query($is = '', $value = '')
	{
		// Setting a value
		if($value)
		{
			$this->_is[$is] = $value;

			return TRUE;
		}

		// Getting a value
		else
		{
			// Value hasn't been set. Return null
			if( ! isset($this->_is[$is]))
			{
				return NULL;
			}

			// Return the value
			else
			{
				return $this->_is[$is];
			}
		}
	}

	public function all()
	{
		$items = array();

		foreach ($this->_is as $key => $value)
		{
			$items[] = $key;

			if ( ! is_bool($value))
			{
				$items[] = $key.'-'.$value;
			}
		}

		return $items;
	}

}