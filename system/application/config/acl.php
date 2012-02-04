<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['acl_table_users'] = 'users';

$config['acl_users_fields'] = array(
	'id' => 'user_id',
	'role_id' => 'role_id'
);

$config['acl_table_permissions'] = 'permissions';

$config['acl_permissions_fields'] = array(
	'id' => 'permission_id',
	'key' => 'permission_name'
);

$config['acl_table_role_permissions'] = 'role_permissions';

$config['acl_role_permissions_fields'] = array(
	'id' => 'id',
	'role_id' => 'role_id',
	'permission_id' => 'permission_id'
);

$config['acl_user_session_key'] = 'user_id';

$config['acl_restricted'] = array(

	'controller/method' => array(
		'allow_roles' => array(1),
		'allow_users' => array(1),
		'error_msg' => 'You do not have permission to visit this page!'
	),

	'welcome/*' => array(
		'allow_roles' => array(1),
		'allow_users' => array(1),
		'error_msg' => 'You do not have permission to visit this page!'
	)

);