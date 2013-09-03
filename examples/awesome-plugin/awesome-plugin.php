<?php
/*
Plugin Name: Awesome Plugin
Description:  Demonstrates the amazing Wordpress Simple Settings framework.
Version: 0.1
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

// Include the framework only if another plugin has not already done so
if ( ! class_exists('WordPress_SimpleSettings') )
	require('inc/wordpress-simple-settings.php'); 

class AwesomePlugin extends WordPress_SimpleSettings {
	var $prefix = 'awesome'; // this is super recommended

	function __construct() {
		parent::__construct(); // this is required

		// Actions
		add_action('admin_menu', array($this, 'menu') );

		register_activation_hook(__FILE__, array($this, 'activate') );
	}

	function menu () {
		add_options_page("Awesome", "Awesome", 'manage_options', "awesome-plugin", array($this, 'admin_page') );
	}

	function admin_page () {
		include 'awesome-plugin-admin.php';
	}

	function activate() {
		$this->add_setting('favorite_color', 'red');
		$this->add_setting('favorite_array', 'element 1;element 2;element 3;');
	}
}

$AwesomePlugin = new AwesomePlugin();