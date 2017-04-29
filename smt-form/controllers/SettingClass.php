<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class SettingClass extends MainClass{
	public function __construct(){
		//add_action("admin_menu",array($this,"add_menu"));
		add_action( 'admin_enqueue_scripts',array($this, 'load_custom_wp_admin_script' ));
	}
	public function load_custom_wp_admin_script(){
		wp_enqueue_script( 'angular.min', SMT_CONTACT_URL . '/view/js/angular.min.js', array(), '5.0' );
		wp_enqueue_script( 'angular-route.min', SMT_CONTACT_URL . '/view/js/angular-route.min.js', array(), '5.0' );
		wp_enqueue_script( 'dirPagination', SMT_CONTACT_URL . '/view/js/dirPagination.js', array(), '5.0' );
		wp_enqueue_script( 'ng-sortable', SMT_CONTACT_URL . '/view/js/ng-sortable.js', array(), '5.0' );
		wp_enqueue_style('admin-style', SMT_CONTACT_URL . '/view/css/admin-style.css', array(), '1.0'); 
	}
	public function index(){
		$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
		switch($action){
			default:
			if(isset($_POST['submit'])){
				unset($_POST['submit']);
				update_option( 'SMT_Form_Fields',$_POST['SMT_form_field']);
				update_option( 'SMT_Mail_Options',$_POST['SMT_mail_setting']);
				update_option( 'SMT_Google_Captha_Options',$_POST['SMT_google_captha_setting']);
			}
			$this->loadview("admin/setting",array("smt_mail_options"=>get_option('smt_Mail_Options'),"smt_form_fields"=>get_option('smt_Form_Fields'),"smt_google_captha_setting"=>get_option('smt_Google_Captha_Options'))); 
			break;
		}
	}
	public function add_menu(){
		add_menu_page("SMT Contact Form","SMT Contact Form",'manage_options', "smt-contactform-setting", array($this, "index"), 'dashicons-admin-page',28);
	}
}