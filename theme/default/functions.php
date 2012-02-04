<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Modernizr
$this->assets->js('modernizr', 'assets/includes/modernizr/modernizr-2.0.6.js', 'head', '', 'lt IE 9');

// Theme Javascript
$this->assets->js('theme', theme_url('assets/js/theme.js'), 'head');