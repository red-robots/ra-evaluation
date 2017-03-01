<?php 

// Add the Meta Box
function raeval_add_custom_meta_box() {
    add_meta_box(
        'custom_meta_box', // $id
        'Questionaire', // $title 
        'raeval_show_custom_meta_box', // $callback
        'evaluation', // $page / posttype
        'evaluhigh', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'raeval_add_custom_meta_box');


// Field Array
$prefix = 'raeval_';
$custom_meta_fields = array(
    array(
        'label'=> 'Primary Care Physician',
        'desc'  => 'Please fill in the name of the PCP.',
        'id'    => $prefix.'pcp',
        'class' => 'physician',
        'type'  => 'text'
    ),
    array(
        'label'=> 'Date of Evaluation',
        'desc'  => 'Click in the field to choose a date.',
        'id'    => $prefix.'date',
        'class' => 'js-datepicker',
        'type'  => 'text'
    ),
    array(
        'label'=> 'Patient Initials',
        'desc'  => 'Please fill in the initials for the patient.',
        'id'    => $prefix.'initials',
        'class' => 'initials',
        'type'  => 'text'
    ),
   
    // array(
    //     'label'=> 'Checkbox Input',
    //     'desc'  => 'A description for the field.',
    //     'id'    => $prefix.'checkbox',
    //     'type'  => 'checkbox'
    // ),
    // array(
    //     'label'=> 'Select Box',
    //     'desc'  => 'A description for the field.',
    //     'id'    => $prefix.'select',
    //     'type'  => 'select',
    //     'options' => array (
    //         'one' => array (
    //             'label' => 'Option One',
    //             'value' => 'one'
    //         ),
    //         'two' => array (
    //             'label' => 'Option Two',
    //             'value' => 'two'
    //         ),
    //         'three' => array (
    //             'label' => 'Option Three',
    //             'value' => 'three'
    //         )
    //     )
    // )
);


/*
*
*   Move the metaboxes above the WP Editor
*
*/
function raeval_move_deck() {
	# Get the globals:
	global $post, $wp_meta_boxes;

	# Output the "advanced" meta boxes:
	do_meta_boxes( get_current_screen(), 'evaluhigh', $post );

	# Remove the initial "advanced" meta boxes:
	unset($wp_meta_boxes['post']['evaluhigh']);
}

add_action('edit_form_after_title', 'raeval_move_deck');


// The Callback
function raeval_show_custom_meta_box() {
global $custom_meta_fields, $post;
// Use nonce for verification
wp_nonce_field( basename( __FILE__ ), 'raeval_nonce' );
     
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($custom_meta_fields as $field) {
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

    require_once('skeleton.php');
}


// Save the Data
function raeval_save_custom_meta($post_id) {
    global $custom_meta_fields;
     
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
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'raeval_save_custom_meta');