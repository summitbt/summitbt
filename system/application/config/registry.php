<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| REGISTRY CONFIG
| -------------------------------------------------------------------
| This file will contain the settings needed to load the registry.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['reg_use_database']	TRUE/FALSE - Enables/disables use of the database
|	['reg_table_name'] 		The table to use if database is enabled
|
*/

$config['reg_use_database'] = TRUE;
$config['reg_table_name']   = 'configurations';
$config['reg_key_field_name']   = 'configuration_key';
$config['reg_value_field_name']   = 'configuration_value';

/* End of file registry.php */
/* Location: ./application/config/registry.php */