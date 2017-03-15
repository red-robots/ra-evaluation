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
		$id = wp_insert_post(array(
			'post_title'=>'Random Title',
			'post_type'=>'evaluation'
		));
		if($id){
			$values = array();
			if(isset($_POST['middle-1'])){
				$values['middle-1'] = strcmp(sanitize_text_field($_POST['middle-1']),"1")===0?1:0;
			} else {
				$values['middle-1'] = 0;
			}
			for($i=1;$i<37;$i++){
				if(isset($_POST['left-'.$i])){
					$values['left-'.$i] = strcmp(sanitize_text_field($_POST['left-'.$i]),"1")===0?1:0;
				} else {
					$values['left-'.$i] = 0;
				}
			}
			for($i=1;$i<37;$i++){
				if(isset($_POST['right-'.$i])){
					$values['right-'.$i] = strcmp(sanitize_text_field($_POST['right-'.$i]),"1")===0?1:0;
				} else {
					$values['right-'.$i] = 0;
				}
			}
			add_post_meta($id,'ra_skeleton',json_encode($values),true);
		}
	}
}