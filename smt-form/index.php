<?php
/*
Plugin Name: SMT Multiple Form
Plugin URI: 
Description:Create Multiple form with google capatcha. 
Version: 2.0
Author: Small Eye Tech
Author URI:
License: GPLv2 or later
Text Domain: smalleyetech
*/
/* Define the directory and slug*/ 
define('SMT_CONTACT_SLUG', 'smt-form');
define('SMT_CONTACT_DIR', WP_PLUGIN_DIR. '/' . SMT_CONTACT_SLUG);
define('SMT_CONTACT_CONTROLLER', SMT_CONTACT_DIR. '/controllers');
define('SMT_CONTACT_MODEL', SMT_CONTACT_DIR.'/model');
define('SMT_CONTACT_VIEW', SMT_CONTACT_DIR. '/view');
define('SMT_CONTACT_CSV_STORE', SMT_CONTACT_DIR. '/csvdata');
define('SMT_CONTACT_URL', WP_PLUGIN_URL . '/'.SMT_CONTACT_SLUG);
require_once(SMT_CONTACT_CONTROLLER."/MainClass.php");
require_once(SMT_CONTACT_CONTROLLER."/SettingClass.php");
require_once(SMT_CONTACT_CONTROLLER."/FormManageClass.php");
require_once(SMT_CONTACT_CONTROLLER."/FormDataClass.php");
require_once(SMT_CONTACT_CONTROLLER."/ShortCodeClass.php");
new FormManageClass();
$setting=new SettingClass();
//add_filter('gettext', array($setting,'wps_translation_mangler'), 10, 4);
register_activation_hook( __FILE__,array($setting,'activate' ));
$shortcode=new ShortCodeClass();
$form_data=new FormDataClass();		
