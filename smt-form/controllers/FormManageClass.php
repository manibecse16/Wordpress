<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class FormManageClass extends MainClass{
    private $formModel;
 	public function __construct(){  
	    $this->formModel=$this->loadmodel("Form");
		add_action( 'init',array($this, 'smt_contact_form'), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_forms_details_meta_box' ) );
	    add_action( 'save_post', array( $this, 'smtforms_save_meta_box_data' ), 1, 2 );
		add_action( 'smtform_mangement_meta', array($this,"save_forms_details"), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions'), 10, 2 );
        add_filter('manage_smt_contact_form_posts_columns', array( $this, 'shortcode_columns_head'));
        add_action('manage_smt_contact_form_posts_custom_column', array( $this, 'shortcode_columns_content'), 10, 2);
	    add_filter( 'bulk_actions-edit-smt_contact_form',  array( $this,'remove_bulk_actions_links') );
		add_action( 'delete_post', array( $this,'removeFormData'), 10 );
		//add_action( 'post_submitbox_misc_actions',array( $this, 'custom_button'));
 	}
	public function add_forms_details_meta_box(){
		add_meta_box( 'form-option-setting', __( 'Manage Form Details', 'smt Form' ),array( $this, 'form_option_setting_meta_box' ), 'smt_contact_form','advanced','high' );
	}
    /**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function smtforms_save_meta_box_data( $post_id,$post ) {
		do_action( 'smtform_mangement_meta', $post_id, $post );
	}
	public function save_forms_details($post_id,$post){
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$slug = 'smt_contact_form';
		// If this isn't a 'book' post, don't update it.
		if ( $slug != $post->post_type ) {
			return;
		}
		/* Add the meta gallery */
	   if((isset($_POST['save'])||isset($_POST['publish']))&&$_REQUEST['post_type']=="smt_contact_form"){
		   if(isset($_POST['smt_form_field'])){
               $this->formModel->saveFormFields($post_id,$_POST['smt_form_field']);
		   }
           if(isset($_POST['smt_google_captha_setting'])){
               $this->formModel->saveGoogleCapthaSetting($post_id,$_POST['smt_google_captha_setting']);
		   }
           if(isset($_POST['smt_mail_setting'])){
             $this->formModel->saveMailOptions($post_id,$_POST['smt_mail_setting']);
		   }	   
	   }

	}
	public function form_option_setting_meta_box($post){
	  /*Load the form details*/	
      $smt_form_fields=$this->formModel->getFormFields($post->ID);
	  $smt_mail_options=$this->formModel->getMailOptions($post->ID);
	  $smt_google_captha_setting=$this->formModel->getGoogleCapthaSetting($post->ID);
	  
	  $this->loadview("admin/metabox/form-settings",array("smt_mail_options"=>$smt_mail_options,"smt_form_fields"=>$smt_form_fields,"smt_google_captha_setting"=>$smt_google_captha_setting));
    }
	// Register Custom Post Type
	public function smt_contact_form() {

		$labels = array(
			'name'                  => _x( 'My Monkey Forms', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Monkey Form', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Monkey Forms', 'text_domain' ),
			'name_admin_bar'        => __( 'Monkey Form', 'text_domain' ),
			'archives'              => __( 'FormArchives', 'text_domain' ),
			'attributes'            => __( 'FormAttributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Form:', 'text_domain' ),
			'all_items'             => __( 'Forms', 'text_domain' ),
			'add_new_item'          => __( 'Add New Form', 'text_domain' ),
			'add_new'               => __( 'Add New Form', 'text_domain' ),
			'new_item'              => __( 'New Form', 'text_domain' ),
			'edit_item'             => __( 'Edit Form', 'text_domain' ),
			'update_item'           => __( 'Update Form', 'text_domain' ),
			'view_item'             => __( 'View Form', 'text_domain' ),
			'view_items'            => __( 'View Forms', 'text_domain' ),
			'search_items'          => __( 'Search Form', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into Form', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Form', 'text_domain' ),
			'items_list'            => __( 'Forms list', 'text_domain' ),
			'items_list_navigation' => __( 'Forms list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter form list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Monkey Form', 'text_domain' ),
			'description'           => __( 'Manage your Monkey Forms', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title'),
			'taxonomies'            => array( ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 10,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => false,
			'has_archive'           => false,		
			'exclude_from_search'   => false,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
		);
		register_post_type( 'smt_contact_form', $args );
    }
   public function wps_translation_mangler($translation, $text, $domain) {
        global $post;
		if ($post->post_type == 'smt_contact_form') {
			$translations = &get_translations_for_domain( $domain);
			if ( $text == 'Scheduled for: <b>%1$s</b>') {
				return $translations->translate( 'Event Date: <b>%1$s</b>' );
			}
			if ( $text == 'Published on: <b>%1$s</b>') {
				return $translations->translate( 'Event Date: <b>%1$s</b>' );
			}
			if ( $text == 'Publish <b>immediately</b>') {
				return $translations->translate( 'Event Date: <b>%1$s</b>' );
			}
		}
		return $translation;
   }
   public function remove_row_actions( $actions, $post )
	{
	  global $current_screen;
		if( $current_screen->post_type != 'smt_contact_form' ) return $actions;
		//unset( $actions['edit'] );
		unset( $actions['view'] );
		/* if(!isset($actions[ 'delete' ])){
		 $actions[ 'delete' ]= str_replace("Trash",'Delete',str_replace("trash","delete",$actions['trash']));
		} */
		//unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		

		return $actions;
	}
	// ADD NEW COLUMN
	public function shortcode_columns_head($defaults) {
		return array(
			'cb' => '<input type="checkbox"/>',
			'title' => __('Title'),
			'shortcode' => __('Short Code'),
			'author'=>__('Author'),
			'date' => __('Date')
			);
	}
 
	// SHOW THE Short Code
	public function shortcode_columns_content($column_name, $post_ID) {
		if ($column_name == 'shortcode') {
			echo '[monkey-form id="'.$post_ID.'"]';
		}
    }
    /* Removed the link in bulk action*/
    public function remove_bulk_actions_links( $actions ){
        unset( $actions[ 'edit' ] );
		unset( $actions[ 'trash' ] );
		$actions[ 'delete' ]="Delete";
        return $actions;
    }
	public function removeFormData($post_id){
		if ( 'smt_contact_form' != get_post_type( $post_id ))
          return;
  	/*Load the Form Model*/	
		if (!class_exists('Formdata')) { 
		      $formData=$this->loadmodel("Formdata");
		}else{
			  $formData=new Formdata();
		}
         $formData->deleteDataByForm($post_id);
	}
	public function custom_button($post){
		if($post->post_type=='smt_contact_form'){
		
		$html = '<div id="major-publishing-actions" style="overflow:hidden">';
	    $html.='<div id="delete-action">
<a class="submitdelete deletion" href="'.get_delete_post_link($post->ID).'">Move Trash</a></div>';
        $html .= '<div id="publishing-action">';

        $html .= '<span class="spinner"></span><input name="publish" id="custom_publish" class="button button-primary button-large" value="Save" type="submit" ng-click="jumpToInvalidTab()">';
        $html .= '</div>';
        $html .= '</div><style>.submitbox > #major-publishing-actions{display:none;}</style>';
        echo $html;
		}
	}
}