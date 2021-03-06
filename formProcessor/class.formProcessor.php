<?php
/**
 * Created by PhpStorm.
 * User: fritz
 * Date: 3/3/17
 * Time: 10:36 AM
 */
class RAFormProcessor {
	public static function process(){
		if ( !isset( $_POST['raeval_nonce'] ) || !wp_verify_nonce( $_POST['raeval_nonce'], basename( RAEVAL__PLUGIN_DIR) ) ){
			return;
		}
		//get option from option table for uniqid
		$uniqid = get_option("raeval_uniqid");
		if(!$uniqid){
			add_option("raeval_uniqid",0,'','no');
			$uniqid=0;
		}
		//set uniquid to 1 on start or next id
		++$uniqid;
		update_option('raeval_uniqid',$uniqid);
		//create post for this entry into evaluations
		$id = wp_insert_post(array(
			'post_title'=> $uniqid,
			'post_type'=>'evaluation',
			'post_status'=>'publish'
		));
		//if post created successfully
		if($id) {
			$values = array();
			$score  = 0;
			$large_joint_affected = 0;
			$small_joint_affected = 0;
			$large_joints = array(4,5,22,23,24);
			$non_click_joins = array(7,18,19,20,21,31);
			$joint_score = 0;
			//save values for skeleton
			if ( isset( $_POST['middle-1'] ) ) {
				$val                = preg_match( '/[123]/', sanitize_text_field( $_POST['middle-1'] ) ) === 1 ? sanitize_text_field( $_POST['middle-1'] ) : 0;
				$values['middle-1'] = $val;
				if($val>0) {
					$small_joint_affected ++;
				}
			} else {
				$values['middle-1'] = 0;
			}
			for ( $i = 1; $i < 37; $i ++ ) {
				if ( isset( $_POST[ 'left-' . $i ] ) ) {
					$val                    = preg_match( '/[123]/', sanitize_text_field( $_POST[ 'left-' . $i ] ) ) === 1 ? sanitize_text_field( $_POST[ 'left-' . $i ] ) : 0;
					$values[ 'left-' . $i ] = $val;
					if($val>0) {
						if(!in_array($i,$non_click_joins)) {
							if ( in_array( $i, $large_joints ) ) {
								$large_joint_affected ++;
							} else {
								$small_joint_affected ++;
							}
						}
					}
				} else {
					$values[ 'left-' . $i ] = 0;
				}
			}
			for ( $i = 1; $i < 37; $i ++ ) {
				if ( isset( $_POST[ 'right-' . $i ] ) ) {
					$val                     = preg_match( '/[123]/', sanitize_text_field( $_POST[ 'right-' . $i ] ) ) === 1 ? sanitize_text_field( $_POST[ 'right-' . $i ] ) : 0;
					$values[ 'right-' . $i ] = $val;
					if($val>0){
						if(!in_array($i,$non_click_joins)) {
							if ( in_array( $i, $large_joints ) ) {
								$large_joint_affected ++;
							} else {
								$small_joint_affected ++;
							}
						}
					}
				} else {
					$values[ 'right-' . $i ] = 0;
				}
			}
			//calculate joint score for skeleton
			if($small_joint_affected>0 && ($small_joint_affected + $large_joint_affected) >10){
				$score+=5;
				$joint_score+=5;
			} elseif($small_joint_affected > 3){
				$score+=3;
				$joint_score+=3;
			} elseif($small_joint_affected > 0) {
				$score += 2;
				$joint_score += 2;
			} elseif($large_joint_affected>1 && $large_joint_affected<11){
				$score += 1;
				$joint_score += 1;
			}
			//save joint score
			add_post_meta($id,'raeval_joint_score',$joint_score,true);
			//save value for serology and add to score
			if ( isset( $_POST[ 'serology' ] ) ) {
				$serology = sanitize_text_field($_POST['serology']);
				$val = preg_match( '/^2|3$/', $serology ) === 1 ? $serology : 0;
				$score += $val;
				add_post_meta($id,'raeval_serology',$val,true);
			}
			//save value for duration and add to score
			if ( isset( $_POST[ 'duration' ] ) ) {
				$duration = sanitize_text_field($_POST['duration']);
				$val = preg_match( '/^1$/', $duration ) === 1 ? $duration : 0;
				$score += $val;
				add_post_meta($id,'raeval_duration',$val,true);
			}
			//save value for apr and add to score
			if ( isset( $_POST[ 'apr' ] ) ) {
				$apr = sanitize_text_field($_POST['apr']);
				$val = preg_match( '/^1$/', $apr ) === 1 ? $apr : 0;
				$score += $val;
				add_post_meta($id,'raeval_apr',$val,true);
			}
			//pcp save
			if ( isset( $_POST['pcp'] ) ) {
				add_post_meta( $id, 'raeval_pcp', sanitize_text_field( $_POST['pcp'] ), true );
			}
			//phone save
			if ( isset( $_POST['phone'] ) ) {
				add_post_meta( $id, 'raeval_phone', sanitize_text_field( $_POST['phone'] ), true );
			}
			//email save
			if ( isset( $_POST['email'] ) ) {
				add_post_meta( $id, 'raeval_email', sanitize_text_field( $_POST['email'] ), true );
			}
			//initials save
			if ( isset( $_POST['initials'] ) ) {
				add_post_meta( $id, 'raeval_initials', sanitize_text_field( $_POST['initials'] ), true );
			}
			//dob save
			if(isset($_POST['dob'])){
				try{
					$dob = (new DateTime(sanitize_text_field( $_POST['dob'] )))->format('m/d/Y');
				} catch(Exception $e) {
					$dob = "unknown";
				}
				add_post_meta( $id, 'raeval_dob', $dob, true );
			}
			//date save now
			try{
				$now = (new DateTime())->format('m/d/Y');
			} catch(Exception $e) {
				$now = "unknown";
			}
			add_post_meta($id,'raeval_date',$now,true);
			//skeleton save
			add_post_meta($id,'raeval_skeleton',json_encode($values),true);
			//score save
			add_post_meta($id,'raeval_score',$score,true);
			//unique id save
			add_post_meta($id,'raeval_uniqid',$uniqid,true);
			
			//save uploaded image
			$uploads = wp_upload_dir();
			$upload_file_url = array();
			$mime = array('image/jpeg','image/png');
			if(isset($_FILES['image'])){
				$errors_founds = false;
				$err = '';

				if ($_FILES['image']['error'] != 0){
					$errors_founds = true;
					$err .= 'error';
				}

				if (!in_array($_FILES['image']['type'], $mime)){
					$errors_founds = true;
					$err .= 'mime';
				}

				if ($_FILES['image']['size'] == 0){
					$errors_founds = true;
					$err .= 'size 0';
				}

				if ($_FILES['image']['size'] > 1048576*2){
					$errors_founds = true;
					$err .= 'size 2000';
				}

				if(!is_uploaded_file($_FILES['image']['tmp_name'])){
					$errors_founds = true;
					$err .= '!is uploaded file';
				}

				if ($errors_founds === false){
					//Sanitize the filename (See note below)
					$remove_these = array(' ','`','"','\'','\\','/');
					$newname = str_replace($remove_these,'', $_FILES['image']['name']);
					//Make the filename unique
					$newname = time().'-'.$newname;
					//Save the uploaded the file to another location

					$upload_path = $uploads['path'] . "/$newname";
					if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
						$wp_filetype = wp_check_filetype(basename($upload_path), null );
						$attachment = array(
											'post_mime_type' => $wp_filetype['type'],
											'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload_path)),
											'post_content' => '',
											'post_status' => 'inherit'
											);
						$attach_id = wp_insert_attachment( $attachment, $upload_path);
						// you must first include the image.php file
						// for the function wp_generate_attachment_metadata() to work
						if ( ! function_exists( 'wp_crop_image' ) ) {
							include( ABSPATH . 'wp-admin/includes/image.php' );
						}
						$attach_data = wp_generate_attachment_metadata( $attach_id, $upload_path );
						wp_update_attachment_metadata( $attach_id, $attach_data );
						add_post_meta($id,'image',$attach_id,true);
					}
				}
			} 


			//send results to registered emails
			$emails = get_option('raeval_email');
			if(strcmp($emails,"None")!==0) {
				function raeval_filter_wp_mail_from( $from_email ) {
					return 'results@raeval.com';
				}

				;
				add_filter( 'wp_mail_from', 'raeval_filter_wp_mail_from', 10, 1 );
				function raeval_filter_wp_mail_from_name( $from_email_name ) {
					return "RAEval App";
				}

				;
				add_filter( 'wp_mail_from_name', 'raeval_filter_wp_mail_from_name', 10, 1 );

				$message = "Hello,\r\n\r\n";
				$message .= "The following information has been received for patient: " . $uniqid . "\r\n";
				$message .= "Primary Care Physician: " . get_post_meta( $id, 'raeval_pcp', true ) . "\r\n";
				$message .= "Primary Care Physician Phone: " . get_post_meta( $id, 'raeval_phone', true ) . "\r\n";
				$message .= "Date of evaluation: " . get_post_meta( $id, 'raeval_date', true ) . "\r\n";
				$message .= "Initials: " . get_post_meta( $id, 'raeval_initials', true ) . "\r\n";
				$message .= "DOB: " . get_post_meta( $id, 'raeval_dob', true ) . "\r\n";
				$message .= "Joint Score: " . get_post_meta( $id, 'raeval_joint_score', true ) . "\r\n";
				$message .= "Serology: " . get_post_meta( $id, 'raeval_serology', true ) . "\r\n";
				$message .= "Duration of Symptoms: " . get_post_meta( $id, 'raeval_duration', true ) . "\r\n";
				$message .= "Acute Phase Reactants: " . get_post_meta( $id, 'raeval_apr', true ) . "\r\n";
				$message .= "Patient overall score: " . get_post_meta( $id, 'raeval_score', true ) . "\r\n\r\n";
				$message .= "Please login to see patient skeleton.";

				wp_mail( $emails, sprintf( __( 'Results [%s]' ), $uniqid ), $message );
				remove_filter( 'wp_mail_from', 'raeval_filter_wp_mail_from', 10 );
				remove_filter( 'wp_mail_from_name', 'raeval_filter_wp_mail_from_name', 10 );
			}
		}
	}
}
