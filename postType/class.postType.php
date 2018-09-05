<?php
/**
 * Created by PhpStorm.
 * User: fritz
 * Date: 3/3/17
 * Time: 10:36 AM
 */

class RAPostType {
	private static $custom_meta_fields, $custom_image, $prefix, $custom_meta_fields_static;
	public static function init() {
		self::$prefix = 'raeval_';
		self::$custom_meta_fields_static = array(
			array(
				'label' => 'Primary Care Physician',
				'desc' => 'Please fill in the name of the PCP.',
				'id' => self::$prefix.'pcp',
				'class' => 'physician',
				'type' => 'text'
			),
			array(
				'label' => 'Primary Care Physician Email',
				'desc' => 'Please fill in the email field',
				'id' => self::$prefix.'email',
				'class' => 'email',
				'type' => 'text'
			),
			array(
				'label' => 'Primary Care Physician Phone',
				'desc' => 'Please fill in the phon of the pcp.',
				'id' => self::$prefix.'phone',
				'class' => 'phone',
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
			array(
				'label' => 'DOB',
				'desc' => '',
				'id' => self::$prefix.'dob',
				'class' => 'dob',
				'type' => 'text'
			),
			array(
				'label' => 'Joint Score',
				'desc' => '',
				'id' => self::$prefix.'joint_score',
				'class' => 'joint-score',
				'type' => 'text'
			),
			array(
				'label' => 'Serology',
				'desc' => '',
				'id' => self::$prefix.'serology',
				'class' => 'serology',
				'type' => 'text'
			),
			array(
				'label' => 'Duration of Symptoms',
				'desc' => '',
				'id' => self::$prefix.'duration',
				'class' => 'duration',
				'type' => 'text'
			),
			array(
				'label' => 'Acute Phase Reactants',
				'desc' => '',
				'id' => self::$prefix.'apr',
				'class' => 'apr',
				'type' => 'text'
			),
			array(
				'label' => 'Unique Id',
				'desc' => '',
				'id' => self::$prefix.'uniqid',
				'class' => 'uniqid',
				'type' => 'text'
			),
			array(
				'label' => 'Patient Overall Score',
				'desc' => '',
				'id' => self::$prefix.'score',
				'class' => 'score',
				'type' => 'text'
			),
		);
		self::$custom_meta_fields = array(
		);
		self::$custom_image = 'image';
		add_action( 'save_post', array( 'RAPostType', 'save_custom_meta' ) );
		add_action( 'add_meta_boxes', array( 'RAPostType', 'add_custom_meta_box' ) );
		add_action( 'edit_form_after_title', array( 'RAPostType', 'move_deck' ) );
		add_action('wp_enqueue_scripts', array('RAPostType','front_end_styles'));
		add_shortcode( 'raeval_skeleton', array('RAPostType','add_shortcode') );
		add_filter( 'login_redirect', array('RAPostType','login_redirect'), 10, 3 );
		add_filter('authenticate', array('RAPostType','verify_user_pass'), 1, 3);
		add_action('wp_logout',array('RAPostType','logout_redirect'),0);
		add_action('wp_login_failed', array('RAPostType','login_failed'),0);

	}
	public static function plugin_activation() {
		add_role( 'doctor', 'Doctor', array( 'read' => false) );
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
		foreach (self::$custom_meta_fields_static as $field) {
			// get value of this field if it exists for this post
			$meta = get_post_meta($post->ID, $field['id'], true);
			// begin a table row with
			echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>'.$meta.'</td></tr>';
		} // end foreach
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
		$image_id = get_post_meta($post->ID, self::$custom_image, true);
		if($image_id){
			echo '<img class="patient" src="'.wp_get_attachment_url($image_id).'">';
		}
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

		if(!isset($_POST['raeval_nonce'])) {
			echo '<form action="" method="POST" enctype="multipart/form-data">';
			echo '<div class="ra-eval">';
			echo '<ul class="tabs"><li><a href="#tab1">Step 1</a></li><li><a href="#tab2">Step 2</a></li></ul>';
			echo '<div id="tab1" class="tab-content">';
			require_once( plugin_dir_path( __FILE__ ) . 'questionare.php' );
			echo '</div><div id="tab2" class="tab-content">';
			require_once( RAEVAL__PLUGIN_DIR . 'inc/skeleton/skeleton-form.php' );
			echo '</div><!--.tab-2--></div><!--.ra-eval--></form><!--end ra form-->';
		} else {
			echo '<div class="ra-eval">';
			require_once( plugin_dir_path(__FILE__).'completed.php');
			echo '</div><!--.ra-eval.completed-->';
		}

		return ob_get_clean();
	}
	public static function front_end_styles(){
		wp_enqueue_style( 'custom-styles' , plugin_dir_url(RAEVAL__PLUGIN_DIR). 'ra-evaluation/inc/css/style.css' );
		wp_enqueue_style( 'jquery-ui-datepicker-style' , 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'custom-scripts', plugin_dir_url(RAEVAL__PLUGIN_DIR).'ra-evaluation/inc/assets/js/custom.js', array( 'jquery' ), '20170315',true );
		wp_localize_script( 'custom-scripts', 'bella', array(
			'admin' => false
		));
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
		);
		register_post_type('evaluation',$args); // name used in query
		remove_post_type_support( 'evaluation', 'editor' );
	}
	/*
	 * The following section restricts logins
	 */
	public static function redirect_login_page() {
		if(!is_user_logged_in()) {
			$login_page  = home_url( '/login/' );
			$parsed_wp_login_page = parse_url(home_url('/wp-login.php'));
			$parsed_login_page = parse_url($login_page);
			$parsed_page_viewed = parse_url( $_SERVER['REQUEST_URI'] );
			if($parsed_login_page&&$parsed_page_viewed&&$parsed_wp_login_page
			   &&!empty($parsed_login_page['path'])&&!empty($parsed_page_viewed['path']&&!empty($parsed_wp_login_page['path']))){
				if(strcmp($parsed_page_viewed['path'],$parsed_login_page['path'])!==0
				   &&!(strcmp($parsed_wp_login_page['path'],$parsed_page_viewed['path'])===0 && strcmp($_SERVER['REQUEST_METHOD'],'POST')===0)) {
					wp_redirect( $login_page );
					exit;
				}
			} else {
				wp_redirect( $login_page );
				exit;
			}
		}
	}

	public static function login_failed() {
		$login_page  = home_url('/login/');
		wp_redirect($login_page . '?login=failed');
		exit;
	}

	public static function verify_user_pass($user, $username, $password) {
		$login_page  = home_url('/login/');
		if($username == "" || $password == "") {
			wp_redirect($login_page . "?login=empty");
			exit;
		}
	}

	public static function logout_redirect() {
		$login_page  = home_url('/login/');
		wp_redirect($login_page . "?login=false");
		exit;
	}

	/**
	 * Redirect user after successful login.
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged user's data.
	 * @return string
	 */
	public static function login_redirect( $redirect_to, $request, $user ) {
		//is there a user to check?
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			$home = home_url();
			$wp_admin = home_url('/wp-admin/');
			if ( in_array( 'administrator', $user->roles ) ) {
				return $wp_admin;
			}
			elseif ( in_array( 'doctor', $user->roles ) ) {
				return $home;
			} else {
				return $redirect_to;
			}
		} else {
			return $redirect_to;
		}
	}
	public static function wp_login_form( $args = array() ) {
		$defaults = array(
			'echo'           => true,
			// Default 'redirect' value takes the user back to the request URI.
			'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'form_id'        => 'loginform',
			'label_username' => __( 'Username or Email' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'remember'       => true,
			'value_username' => '',
			// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
			'value_remember' => false,
		);

		/**
		 * Filter the default login form output arguments.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_login_form()
		 *
		 * @param array $defaults An array of default login form arguments.
		 */
		$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

		/**
		 * Filter content to display at the top of the login form.
		 *
		 * The filter evaluates just following the opening form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_top = apply_filters( 'login_form_top', '', $args );

		/**
		 * Filter content to display in the middle of the login form.
		 *
		 * The filter evaluates just following the location where the 'login-password'
		 * field is displayed.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_middle = apply_filters( 'login_form_middle', '', $args );

		/**
		 * Filter content to display at the bottom of the login form.
		 *
		 * The filter evaluates just preceding the closing form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array $args Array of login form arguments.
		 */
		$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

		$form     = '
               <form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
	                        ' . $login_form_top . '
	                        <p class="login-username">
	                                <label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
	                                <input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
	                        </p>
	                        <p class="login-password">
	                                <label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
	                                <input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input" value="" size="20" />
	                        </p>
	                        ' . $login_form_middle;
		$form_end = '' . ( $args['remember'] ? '<p class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></p>' : '' ) . '
	                        <p class="login-submit">
	                                <input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
	                                <input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
	                        </p>
	                        ' . $login_form_bottom.'
							</form>';

		if ( $args['echo'] ) {
			$wp_error = new WP_Error();
			global $error;
			/**
			 * Filters the message to display above the login form.
			 *
			 * @since 2.1.0
			 *
			 * @param string $message Login message text.
			 */
			$message = apply_filters( 'login_message', '' );
			if ( !empty( $message ) )
				echo $message . "\n";

			// In case a plugin uses $error rather than the $wp_errors object
			if ( !empty( $error ) ) {
				$wp_error->add('error', $error);
				unset($error);
			}

			if ( $wp_error->get_error_code() ) {
				$errors = '';
				$messages = '';
				foreach ( $wp_error->get_error_codes() as $code ) {
					$severity = $wp_error->get_error_data( $code );
					foreach ( $wp_error->get_error_messages( $code ) as $error_message ) {
						if ( 'message' == $severity )
							$messages .= '	' . $error_message . "<br />\n";
						else
							$errors .= '	' . $error_message . "<br />\n";
					}
				}
				if ( ! empty( $errors ) ) {
					/**
					 * Filters the error messages displayed above the login form.
					 *
					 * @since 2.1.0
					 *
					 * @param string $errors Login error message.
					 */
					echo '<div id="login_error">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
				}
				if ( ! empty( $messages ) ) {
					/**
					 * Filters instructional messages displayed above the login form.
					 *
					 * @since 2.5.0
					 *
					 * @param string $messages Login messages.
					 */
					echo '<p class="message">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
				}
			}
			echo $form;
			do_action( 'login_form' );
			echo $form_end;
		} else
			return $form.$form_end;
	}
}
