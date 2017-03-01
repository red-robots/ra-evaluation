<?php 
add_action('init', 'evaluation_posttype');

function evaluation_posttype() 
{

	// Register the Homepage Evaluations

	$labels = array(
		'name' => _x('Evaluations', 'post type general name'),
		'singular_name' => _x('Evaluation', 'post type singular name'),
		'add_new' => _x('Add New', 'Evaluation'),
		'add_new_item' => __('Add New Evaluation'),
		'edit_item' => __('Edit Evaluations'),
		'new_item' => __('New Evaluation'),
		'view_item' => __('View Evaluations'),
		'search_items' => __('Search Evaluations'),
		'not_found' =>  __('No Evaluations found'),
		'not_found_in_trash' => __('No Evaluations found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Evaluations'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => false, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => false, 
		'hierarchical' => false, // 'false' acts like posts 'true' acts like pages
		'menu_position' => 20,
		'supports' => array('title','editor','custom-fields','thumbnail'),
	); 

	register_post_type('evaluation',$args); // name used in query




} // close custom post type