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
			$non_click_joins = array(18,19,20,21,31);
			$joint_score = 0;
			$billateral = false;
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
					if ( isset( $_POST[ 'left-' . $i ] ) ) {
						$billateral = true;
					}
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
			} elseif($small_joint_affected>0 && ($small_joint_affected + $large_joint_affected) >3){
				$score+=3;
				$joint_score+=3;
			} elseif($small_joint_affected>0 && ($small_joint_affected + $large_joint_affected) >0) {
				$score += 2;
				$joint_score += 2;
			} elseif($large_joint_affected>1){
				if($billateral) {
					$score += 1.5;
					$joint_score += 1.5;
				} else  {
					$score += 1;
					$joint_score += 1;
				}
			}
			//save joint score
			add_post_meta($id,'raeval_joint_score',$joint_score,true);
			//save value for serology and add to score
			if ( isset( $_POST[ 'serology' ] ) ) {
				$serology = sanitize_text_field($_POST['serology']);
				$val = preg_match( '/^2|3.5$/', $serology ) === 1 ? $serology : 0;
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
				$val = preg_match( '/^0.5$/', $apr ) === 1 ? $apr : 0;
				$score += $val;
				add_post_meta($id,'raeval_apr',$val,true);
			}
			//pcp save
			if ( isset( $_POST['pcp'] ) ) {
				add_post_meta( $id, 'raeval_pcp', sanitize_text_field( $_POST['pcp'] ), true );
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



			//send results to registered emails
			$emails = get_option('raeval_email');
			function raeval_filter_wp_mail_from( $from_email ) {
				return 'results@raeval.com';
			};
			add_filter( 'wp_mail_from', 'raeval_filter_wp_mail_from', 10, 1 );
			function raeval_filter_wp_mail_from_name( $from_email_name ) {
				return "RAEval App";
			};
			add_filter( 'wp_mail_from_name', 'raeval_filter_wp_mail_from_name', 10, 1 );

			$message = "Hello,\r\n\r\n";
			$message .= "The following information has been received for patient ".$uniqid."\r\n";
			$message .= "Primary Care Physician " . get_post_meta($id,'raeval_pcp',true)."\r\n";
			$message .= "Date of evaluation " . get_post_meta($id,'raeval_date',true)."\r\n";
			$message .= "Initials " . get_post_meta($id,'raeval_initials',true)."\r\n";
			$message .= "DOB " . get_post_meta($id,'raeval_dob',true)."\r\n";
			$message .= "Joint Score " . get_post_meta($id,'raeval_joint_score',true)."\r\n";
			$message .= "Serology " . get_post_meta($id,'raeval_serology',true)."\r\n";
			$message .= "Duration of Symptoms " . get_post_meta($id,'raeval_duration',true)."\r\n";
			$message .= "Acute Phase Reactants " . get_post_meta($id,'raeval_apr',true)."\r\n";
			$message .= "Patient overall score " . get_post_meta($id,'raeval_score',true)."\r\n\r\n";
			$message .= "Please login to see patient skeleton.";

			wp_mail( $emails, sprintf( __( 'Results [%s]'), $uniqid), $message );
			remove_filter( 'wp_mail_from', 'raeval_filter_wp_mail_from', 10);
			remove_filter( 'wp_mail_from_name', 'raeval_filter_wp_mail_from_name', 10 );
		}
	}
}