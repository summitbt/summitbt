<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['upload_path'] = FCPATH.'uploads/';
$config['overwrite'] = FALSE;
$config['encrypt_name'] = TRUE;
$config['remove_spaces'] = TRUE;
$config['allowed_types'] = 'jpg|gif|png';
$config['max_size'] = 102400;   // 100Mb
