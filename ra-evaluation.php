<?php
/*
Plugin Name: RA Evaluation
Plugin URI:  https://redrobots.io
Description: Evaluation tool for RA patients
Version:     0.1
Author:      Bellaworks
Author URI:  https://redrobots.io/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: raeval
Domain Path: /languages
*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'RAEVAL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once RAEVAL__PLUGIN_DIR."admin/class.admin.php";
require_once RAEVAL__PLUGIN_DIR."formProcessor/class.formProcessor.php";
require_once RAEVAL__PLUGIN_DIR."postType/class.postType.php";

class RAEval {
	private static $RAAdmin = null;
	private static $RAFormProcessor = null;
	private static $RAPostType = null;

	public static function init(){
		self::$RAAdmin = new RAAdmin();
		self::$RAFormProcessor = new RAFormProcessor();
		self::$RAPostType = new RAPostType();
		self::$RAPostType->init();
		self::$RAAdmin->init();
		self::$RAFormProcessor->process();
	}
}

add_action('init', array('RAPostType','register_posttype'));
add_action("init",array('RAEval','init'));