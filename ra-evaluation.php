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


/*
*
* 	Register Styles
*
*/
function raeval_admin_styles() {

	wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	wp_enqueue_style( 'egg-style' , plugin_dir_url( __FILE__ ) . 'css/style.css' );

}
add_action('admin_print_styles', 'raeval_admin_styles');

/*
*
* 	Register Scripts
*
*/
function raeval_admin_scripts() {

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'custom-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js' );

}
add_action('admin_enqueue_scripts', 'raeval_admin_scripts');

/*
*
* 	Create the Post Type of 'Evaluation'
*
*/
require_once ('inc/posttypes.php');
/*
*
* 	Require Metaboxes
*
*/
require_once ('inc/metaboxes.php');
/*
*
* 	Require Admin View
*
*/
require_once ('admin/admin.php');