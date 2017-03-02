<?php 

/*
*  admin_menu
*
*  Create Admin menu
*
*  @type	action (admin_menu)
* 
*  @since	0.1.0
*
*  @param	n/a
*  @return	n/a
*/

function raeval_admin_menu() {
	
	// bail early if no show_admin
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	
	// vars
	$slug_evaluation = 'edit.php?post_type=evaluation';
	$cap = 'manage_options';
	
	
	// add parent
	add_menu_page(
		__("RA Evaluations",'raeval'), 
		__("Evaluations",'raeval'), 
		$cap, 
		$slug_evaluation, 
		false, 
		plugin_dir_url( __FILE__ ) . 'images/dashicon.png', 
		'21'
	);
	// 
	
	
	// add children for Orders
	// add_submenu_page(
	// 	$slug_order, 
	// 	__('Orders','raeval'), 
	// 	__('Orders','raeval'), 
	// 	$cap, 
	// 	$slug_order );
	add_submenu_page(
		$slug_evaluation, 
		__('Subpage, options','raeval'), 
		__('Subpage, options',
		'raeval'), 
		$cap, 
		'evalutation-options',
		'raeval_options_page_html' // Not sure why this callback isn't working??? see below
	);


	
	
}
add_action( 'admin_menu', 'raeval_admin_menu' );

/*
*
* 	Possible options page
*
*/

function raeval_options_page_html() {
	require_once( plugin_dir_path(__FILE__).'options.php' );
}