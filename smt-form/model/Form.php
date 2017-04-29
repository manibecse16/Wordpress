<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class Form{
	private $id;
	
	public function getFormFields($post_id){
		return get_post_meta( $post_id, '_smt_form_fields',true);
	}
	public function getMailOptions($post_id){
		return get_post_meta( $post_id, '_smt_mail_options',true);
	}
	public function getGoogleCapthaSetting($post_id){
		return get_post_meta( $post_id, '_smt_google_captha_setting',true);
	}
	public function saveFormFields($post_id,$data){
		update_post_meta( $post_id, '_smt_form_fields', $data);
	}
	public function saveGoogleCapthaSetting($post_id,$data){
		update_post_meta( $post_id, '_smt_google_captha_setting', $data);
	}
	public function saveMailOptions($post_id,$data){
		update_post_meta( $post_id, '_smt_mail_options', $data);
	}
}