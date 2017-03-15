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
class RAAdmin {
	function init(){
		add_action('admin_menu', array('RAAdmin','admin_menu' ));
		add_action('admin_enqueue_scripts', array('RAAdmin','admin_styles'));
		add_action('admin_enqueue_scripts', array('RAAdmin','admin_scripts'));
	}
	public static function admin_menu() {
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
			plugin_dir_url( RAEVAL__PLUGIN_DIR) . 'ra-evaluation/inc/images/dashicon.png',
			'21'
		);
		add_submenu_page(
			$slug_evaluation,
			__('Subpage, options','raeval'),
			__('Subpage, options',
				'raeval'),
			$cap,
			'evalutation-options',
			array('RAAdmin','options_page')
		);
	}
	public static function options_page() {
		require_once( plugin_dir_url(__FILE__). 'options.php' );
	}
	public static function admin_styles() {
		wp_enqueue_style( 'jquery-ui-datepicker-style' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( 'custom-styles' , plugin_dir_url(RAEVAL__PLUGIN_DIR). 'ra-evaluation/inc/css/style.css' );
	}
	public static function admin_scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'custom-scripts', plugin_dir_url(RAEVAL__PLUGIN_DIR).'ra-evaulation/inc/assets/js/custom.js' );
	}

}
