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
		$uniqid = uniqid();
		$id = wp_insert_post(array(
			'post_title'=> $uniqid,
			'post_type'=>'evaluation',
			'post_status'=>'publish'
		));
		if($id){
			$values = array();
			$score =0;
			if(isset($_POST['middle-1'])){
				$val = strcmp(sanitize_text_field($_POST['middle-1']),"1")===0?1:0;
				$values['middle-1'] = $val;
				$score += $val;
			} else {
				$values['middle-1'] = 0;
			}
			for($i=1;$i<37;$i++){
				if(isset($_POST['left-'.$i])){
					$val = strcmp(sanitize_text_field($_POST['left-'.$i]),"1")===0?1:0;
					$values['left-'.$i] = $val;
					$score += $val;
				} else {
					$values['left-'.$i] = 0;
				}
			}
			for($i=1;$i<37;$i++){
				if(isset($_POST['right-'.$i])){
					$val = strcmp(sanitize_text_field($_POST['right-'.$i]),"1")===0?1:0;
					$values['right-'.$i] = $val;
					$score += $val;
				} else {
					$values['right-'.$i] = 0;
				}
			}
			if(isset($_POST['pcp'])){
				add_post_meta($id,'raeval_pcp',sanitize_text_field($_POST['pcp']),true);
			}
			if(isset($_POST['initials'])){
				add_post_meta($id,'raeval_initials',sanitize_text_field($_POST['initials']),true);
			}
			add_post_meta($id,'raeval_date',(new DateTime())->format('m/d/Y'),true);
			add_post_meta($id,'raeval_skeleton',json_encode($values),true);
			add_post_meta($id,'raeval_score',$score,true);
			add_post_meta($id,'raeval_uniqid',$uniqid,true);
		}
	}
}