<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class MainClass{
	
	/* At the time activation add plug-in option */
	 public function activate(){
		 global $wpdb;
		 $smt_form_data= $wpdb->prefix . 'smt_form_data';
		 /*
		 * We'll set the default character set and collation for this table.
		 * If we don't do this, some characters could end up being converted 
		 * to just ?'s when saved in our table.
		 */
		$charset_collate = '';  
	
		if ( ! empty( $wpdb->charset ) ) {
		  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
	
		if ( ! empty( $wpdb->collate ) ) {
		  $charset_collate .= " COLLATE {$wpdb->collate}";
		}
		$sql = "CREATE TABLE IF NOT EXISTS `$smt_form_data` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `form` varchar(100) NOT NULL DEFAULT '',
		  `data` text NOT NULL,
		  `date` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		)$charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	 }
	/** 
	* Load the view file or render files
	*/
	public function loadview($view_file_name,$varibles=array()){
		 if(file_exists(SMT_CONTACT_VIEW."/".$view_file_name.".php")){
			  extract($varibles);
		      include(SMT_CONTACT_VIEW."/".$view_file_name.".php");
		 }else{
			  die("File Not Found in the location.".SMT_CONTACT_VIEW."/");
		 }
	}
	/** 
	* Load the DB file or model
	* 
	*/
	public function loadmodel($model_file_name){
		 if(file_exists(SMT_CONTACT_MODEL."/".$model_file_name.".php")){
		     include(SMT_CONTACT_MODEL."/".$model_file_name.".php");
		     return new $model_file_name();
		 }else{
			 die("File Not Found in the location.".SMT_CONTACT_MODEL."/".$model_file_name);
		 }
	}
}