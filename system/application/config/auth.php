<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['auth_cookie_expire'] = 432000; // Seconds to keep the cookie. 60 = 1 minute. Set to 0 to keep cookie only for browser session. Currently set to 5 days

$config['auth_table_users'] = 'users';
$config['auth_users_fields'] = array(
	'id' => 'user_id',
	'role_id' => 'role_id',
	'email' => 'user_email',
	'username' => 'user_username',
	'password' => 'user_password',
	'first_name' => 'user_first_name',
	'last_name' => 'user_last_name',
	'date_last_login' => 'user_last_login_date',
	'date_created' => 'user_date_created',
	'active' => 'user_active'
);

$config['auth_table_user_meta'] = 'preferences';
$config['auth_user_meta_fields'] = array(
	'id' => 'preference_id',
	'user_id' => 'user_id'
);

$config['auth_user_session_key'] = 'user_id';

$config['auth_encryption'] = 'md5';