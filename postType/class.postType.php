<?php
/**
 * Created by PhpStorm.
 * User: fritz
 * Date: 3/3/17
 * Time: 10:36 AM
 */

class RAPostType {
	private static $custom_meta_fields;
	private static $prefix;
	public static function init() {
		self::$prefix = 'raeval_';
		self::$custom_meta_fields = array(
			array(
				'label' => 'Primary Care Physician',
				'desc' => 'Please fill in the name of the PCP.',
				'id' => self::$prefix.'pcp',
				'class' => 'physician',
				'type' => 'text'
			),
			array(
				'label' => 'Date of Evaluation',
				'desc' => 'Click in the field to choose a date.',
				'id' => self::$prefix.'date',
				'class' => 'js-datepicker',
				'type' => 'text'
			),
			array(
				'label' => 'Patient Initials',
				'desc' => 'Please fill in the initials for the patient.',
				'id' => self::$prefix.'initials',
				'class' => 'initials',
				'type' => 'text'
			),
		);
		add_action( 'save_post', array( 'RAPostType', 'save_custom_meta' ) );
		add_action( 'add_meta_boxes', array( 'RAPostType', 'add_custom_meta_box' ) );
		add_action( 'edit_form_after_title', array( 'RAPostType', 'move_deck' ) );
		add_action('wp_enqueue_scripts', array('RAPostType','front_end_styles'));
		add_shortcode( 'raeval_skeleton', array('RAPostType','add_shortcode') );
	}

	public static function add_custom_meta_box() {
		add_meta_box(
			'custom_meta_box', // $id
			'Questionaire', // $title
			array( 'RAPostType', 'show_custom_meta_box' ), // $callback
			'evaluation', // $page / posttype
			'evaluhigh', // $context
			'high' ); // $priority
	}

	public static function move_deck() {
		# Get the globals:
		global $post, $wp_meta_boxes;

		# Output the "advanced" meta boxes:
		do_meta_boxes( get_current_screen(), 'evaluhigh', $post );

		# Remove the initial "advanced" meta boxes:
		unset($wp_meta_boxes['post']['evaluhigh']);
	}
	public static function show_custom_meta_box() {
		global  $post;
		wp_nonce_field( basename( __FILE__ ), 'raeval_nonce' );

		echo '<table class="form-table">';
		foreach (self::$custom_meta_fields as $field) {
			// get value of this field if it exists for this post
			$meta = get_post_meta($post->ID, $field['id'], true);
			// begin a table row with
			echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
			switch($field['type']) {
				// case items will go here
				// text
				case 'text':
					echo '<input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
					        <br /><span class="description">'.$field['desc'].'</span>';
					break;
				// text
				case 'number':
					echo '<input type="number" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
					        <br /><span class="description">'.$field['desc'].'</span>';
					break;
				// textarea
				case 'textarea':
					echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
					        <br /><span class="description">'.$field['desc'].'</span>';
					break;
			} //end switch
			echo '</td></tr>';
		} // end foreach
		echo '</table>'; // end table
		ob_start();
		require_once(RAEVAL__PLUGIN_DIR.'inc/skeleton/skeleton.php');
		echo ob_get_clean();
	}

	public static function save_custom_meta($post_id) {
		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['raeval_nonce'] ) || !wp_verify_nonce( $_POST['raeval_nonce'], basename( __FILE__ ) ) )
			return $post_id;
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
		// check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id))
				return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		// loop through fields and save the data
		foreach (self::$custom_meta_fields as $field) {
			$old = get_post_meta($post_id, $field['id'], true);
			if(isset($_POST[$field['id']])) {
				switch ( $field['type'] ) {
					case 'text':
						$new = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
					case 'number':
						$new = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
					case 'textarea':
						$new = sanitize_textarea_field( $_POST[ $field['id'] ] );
						break;
				} //end switch
			} else {
				$new = '';
			}
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		} // end foreach
	}
	public static function add_shortcode(){
		ob_start();
		require_once(RAEVAL__PLUGIN_DIR.'inc/skeleton/skeleton.php');
		return ob_get_clean();
	}
	public static function front_end_styles(){
		wp_enqueue_style( 'custom-styles' , plugin_dir_url(RAEVAL__PLUGIN_DIR). 'ra-evaluation/inc/css/style.css' );
	}
	public static function register_posttype() {
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
	}
}