<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class Formdata{
	private $form;
	private $id;
	private $data;
	private $date;
	public function insertData($data,$id='Contact Form'){
		 global $wpdb;
		 $smt_form_data= $wpdb->prefix . 'smt_form_data';
		 return $wpdb->insert($smt_form_data,array('form'  => $id,'data' => serialize( $data ),'date' => date('Y-m-d H:i:s')));
	}
	public function getFormData($id){
		global $wpdb;
		$where='';
		if(!empty($id)){
			$where=" and form=".$id;
		}
		$sql="SELECT * FROM `".$wpdb->prefix."smt_form_data` where 1=1 $where order by date desc";
		return $wpdb->get_results($sql);
	}
	public function getFormTitle(){
		global $wpdb;
		$args = array('post_type'=>'smt_contact_form', 'posts_per_page' => -1,'order'=> 'DESC','orderby' => 'post_date' );
		return get_posts( $args );
	}
	public function deleteFormdata($id){
		 global $wpdb;
		 $smt_form_data= $wpdb->prefix . 'smt_form_data';
		 return $wpdb->query('DELETE  FROM '.$smt_form_data.' WHERE id = "'.$id.'"');
	}
	public function deleteDataByForm($form_id){
		 global $wpdb;
		 $smt_form_data= $wpdb->prefix . 'smt_form_data';
			if ( $wpdb->get_var( $wpdb->prepare( 'SELECT form FROM '.$smt_form_data.' WHERE form = %d', $form_id ) ) ) {
				$wpdb->query( $wpdb->prepare( 'DELETE FROM '.$smt_form_data.' WHERE form = %d', $form_id ) );
			}
	}
	
}