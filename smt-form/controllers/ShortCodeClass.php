<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class ShortCodeClass extends MainClass{
	private $formModel;
	public function __construct(){  
     session_start();
	 ob_start();
     add_shortcode("smt-booking-form",array($this,"smt_booking_form"));
	 add_shortcode("monkey-form",array($this,"monkey_form"));
	 add_action('wp_enqueue_scripts', array(&$this, 'load_validate_scripts'));
	 define('ALLOW_UNFILTERED_UPLOADS', true);
	 add_filter('upload_mimes',array(&$this, 'add_myme_types'), 1, 1);
   } 
   public function load_validate_scripts(){
	   wp_enqueue_script('jquery-1.11.1', SMT_CONTACT_URL . '/view/js/jquery-1.11.1.js', array(), '1.11.1');
	   wp_register_script('jquery.validate.min', SMT_CONTACT_URL . '/view/js/jquery.validate.min.js','','1.1', true);
	   wp_enqueue_script('jquery.validate.min');
	   wp_register_script('jquery.additional-method', SMT_CONTACT_URL . '/view/js/additional-methods.min.js','','1.15.0', true);
	   wp_enqueue_script('jquery.additional-method');
	   wp_enqueue_style('frontend-style', SMT_CONTACT_URL . '/view/css/frontend-style.css', array(), '1.0');
   }
    /* Multiple form shortcode*/
   public function monkey_form($attr){
	    
	   	$short_attr=shortcode_atts( array('id'=>'','action' => ''), $attr);
		$action=isset($_REQUEST['action'])?$_REQUEST['action']:$short_attr['action'];
		if(isset($short_attr['id'])){
			$id=$short_attr['id'];			
			if ("smt_contact_form"!==get_post_type($id)||get_post_status ( $id ) != 'publish') {
				echo '[monkey-form 404 "Not Found"]';
			}else{
			 /*Load the Form Model*/	
			 if (!class_exists('Form')) {
			 $this->formModel=$this->loadmodel("Form");
			 }else{
				 $this->formModel=new Form();
			 }
			/*Check the action and process the form*/
			switch($action){ 
				case "submit":
				try{
				$location=get_permalink();	
				/*Validate the require field */	
				$form_fields=$this->formModel->getFormFields($id);
				$google_captha_setting=$this->formModel->getGoogleCapthaSetting($id);	
				$validate_require_fields=$this->ValidateRequiredField($_POST,$form_fields);	
				/*Format the POST data */
				$form_datas=$this->formatPostdata($_POST,$form_fields);
				$postDataExcel=$this->formatPostdataExcel($_POST);
				$form_size=sizeof($form_datas);
				/*Upload the files from form data*/	
					if(isset($_FILES)){
						foreach($_FILES as $key=>$file){
							$text=$this->getFieldTextBykey($key,$form_fields);
							$file_data=$this->uploadFile($file);
							if(isset($file_data['url'])){
								$file_path=explode("uploads",$file_data['url']);
								$form_datas[$form_size]['name']=$text;	
								$form_datas[$form_size]['key']=$key;
								$form_datas[$form_size]['value']=$file['name'];
								$form_datas[$form_size]['url']=$file_data['url'];
								$form_datas[$form_size]['path']=$file_path[1];
								$form_datas[$form_size]['type']='file';
								$postDataExcel[$key]=$file_path[1];
							}else{
								throw new Exception($file_data);
							}
							$form_size++;
						}
					}
					 /*Store Value in Excel File*/
					$this->storeExcel(array($postDataExcel),$id);
					
					/*Store the data in DB*/
					$model=$this->loadmodel("Formdata");
					$model->insertData($form_datas,$id);
					
					/*Start the mail sending */
					$email_options=$this->formModel->getMailOptions($id);
						/* Mail function */
					$mail_template_data=$this->load_form_email_template($form_datas);
					add_filter ("wp_mail_content_type", array($this,"set_mail_content_type"));
					  # Send emails
					$headers = 'From: '.$email_options['sender_name'].'  <'.$email_options['sender_mail'].'>' . "\r\n";
					$headers .= 'BCC:'.$email_options['bcc_name'].' <'.$email_options['bcc_email'].'>;' . "\r\n";
					$email_status=wp_mail($email_options['recevier_email'],get_the_title($id), $mail_template_data,$headers, '' );
					if($email_status){
						$_SESSION['formstatus']=1;
						$_SESSION['formmessage']="Your mail sent successfully.";
						if(isset($email_options['is_redirect_enable'])&&$email_options['is_redirect_enable']){
							if(isset($email_options['redirect_url'])&&$email_options['redirect_url']){
								$location=$email_options['redirect_url'];
								unset($_SESSION['formstatus']);
								unset($_SESSION['formmessage']);
							}
						}
					}else{
						throw new Exception('Error occur sent the mail.');
					}
					/*End the mail sending*/
					
				}catch (Exception $e) {
					 $_SESSION['formstatus']=0;
					 $_SESSION['formmessage']=$e->getMessage();
				}	
				
               /*Redirect the page */				
				wp_redirect($location);
				exit;
				break;
				default:
				$form_fields=$this->formModel->getFormFields($id);
				$google_captha_setting=$this->formModel->getGoogleCapthaSetting($id);
				$this->loadview("form/form",array("form_id"=>$id,"smt_form_fields"=>$form_fields,"smt_google_captha_setting"=>$google_captha_setting));
				break;
			}
		}
	 }else{
		echo '[monkey-form 404 "Not Found"]';
	 }
   }
   /* Single form shortcode*/
   public function smt_booking_form($attr){
	    $short_attr=shortcode_atts( array('action' => ''), $attr);
	   	$action=isset($_REQUEST['action'])?$_REQUEST['action']:$short_attr['action'];
		/*Check the action and process the form*/
		switch($action){ 
		
		    case "submit":
			try{
			$location=get_permalink();	
			/*Validate the require field */	
			$form_fields=get_option('smt_Form_Fields');
			$validate_require_fields=$this->ValidateRequiredField($_POST,$form_fields);	
			/*Format the POST data */
			$form_datas=$this->formatPostdata($_POST,$form_fields);
			//$postDataExcel=$this->formatPostdataExcel($_POST);
			$form_size=sizeof($form_datas);
			/*Upload the files from form data*/	
				if(isset($_FILES)){
					foreach($_FILES as $key=>$file){
						$text=$this->getFieldTextBykey($key,$form_fields);
						$file_data=$this->uploadFile($file);
						if(isset($file_data['url'])){
							$file_path=explode("uploads",$file_data['url']);
							$form_datas[$form_size]['name']=$text;	
							$form_datas[$form_size]['key']=$key;
							$form_datas[$form_size]['value']=$file['name'];
							$form_datas[$form_size]['url']=$file_data['url'];
							$form_datas[$form_size]['path']=$file_path[1];
							$form_datas[$form_size]['type']='file';
							//$postDataExcel[$key]=$file_path[1];
						}else{
							throw new Exception($file_data);
						}
						$form_size++;
					}
				}
		         /*Store Value in Excel File*/
				//$this->storeExcel(array($postDataExcel));
				
				/*Store the data in DB*/
				$model=$this->loadmodel("Formdata");
				$model->insertData($form_datas);
				
				/*Start the mail sending */
				$email_options=get_option( 'SMT_Mail_Options');
					/* Mail function */
				$mail_template_data=$this->load_form_email_template($form_datas);
				add_filter ("wp_mail_content_type", array($this,"set_mail_content_type"));
				  # Send emails
				$headers = 'From: '.$email_options['sender_name'].'  <'.$email_options['sender_mail'].'>' . "\r\n";
				$headers .= 'BCC:'.$email_options['bcc_name'].' <'.$email_options['bcc_email'].'>;' . "\r\n";
				$email_status=wp_mail($email_options['recevier_email'], 'Conatct Form ', $mail_template_data,$headers, '' );
				if($email_status){
					$_SESSION['formstatus']=1;
					$_SESSION['formmessage']="Your mail sent successfully.";
					if(isset($email_options['is_redirect_enable'])&&$email_options['is_redirect_enable']){
						if(isset($email_options['redirect_url'])&&$email_options['redirect_url']){
							$location=$email_options['redirect_url'];
						}
					}
				}else{
					throw new Exception('Error occur sent the mail.');
				}
				/*End the mail sending*/
				
            }catch (Exception $e) {
                 $_SESSION['formstatus']=0;
				 $_SESSION['formmessage']=$e->getMessage();
            }			
			
			wp_redirect($location);
			exit;
			break;
			
			default:
			$form_fields=get_option('SMT_Form_Fields');
			$this->loadview("form/form",array("smt_mail_options"=>get_option('SMT_Mail_Options'),"smt_form_fields"=>$form_fields,"smt_google_captha_setting"=>get_option('SMT_Google_Captha_Options')));
			break;
		}
   }
  public function add_myme_types($mime_types){
	   $mime_types['xla|xls|xlt|xlw']='application/vnd.ms-excel';
	    $mime_types['doc']='application/msword';
		return  $mime_types;
	}
  
  private function ValidateRequiredField($form_datas,$form_fields){
      foreach($form_fields as $key=>$val){
			 if($val['validatefield']&&$val['type']!='file'){
				 $slug=$val['slug'];
				 if($form_datas[$slug]==''){
					 throw new Exception('Please enter all required fields');
				 }
			 }
	  } 	  
  }  
  /*Upload the files to uploads folder*/
  private function uploadFile($uploadedfile){
	   if ( ! function_exists( 'wp_handle_upload' ) ) {
	    	require_once( ABSPATH . 'wp-admin/includes/file.php' );
    	}
		$upload_overrides = array( 'test_form' => false );

		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
          
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			return $movefile;
		} else {
			/**
			 * Error generated by _wp_handle_upload()
			 * @see _wp_handle_upload() in wp-admin/includes/file.php
			 */
			return $movefile['error'];
		}
   }
   
   /*Format the post data according to the form fields*/
   public function formatPostdata($postdata,$form_fields){
	   unset($postdata['action']);
	   unset($postdata['submit']);
	   unset($postdata['g-recaptcha-response']);
	   unset($postdata['hiddenRecaptcha']);
	   $newpostdata=array();
	   $i=0;
	   foreach($postdata as $key=>$value){
		   $text=$this->getFieldTextBykey($key,$form_fields);
		   $newpostdata[$i]['name']=$text;
		   $newpostdata[$i]['key']=$key;
		   if(is_array($value)){
			  $newpostdata[$i]['value']=implode(",",$value);
		   }else{
			  $newpostdata[$i]['value']=$value;
		   }
		   $i++;
	   }
	   return $newpostdata;
   }
   public function formatPostdataExcel($postdata){
	   unset($postdata['action']);
	   unset($postdata['submit']);
	   unset($postdata['g-recaptcha-response']);
	   unset($postdata['hiddenRecaptcha']);
	   $newpostdata=array();
	    foreach($postdata as $key=>$value){
		   if(is_array($value)){
			   $newpostdata[$key]=implode(",",$value);
		   }else{
			   $newpostdata[$key]=$value;
		   }
	   }
	   return $newpostdata;
	 }
   /* Store Data Excel File*/
   public function storeExcel($data,$id){
	   $fp = fopen(SMT_CONTACT_CSV_STORE.'/smt-contact-form-data-'.$id.'.csv', 'a');
			foreach ($data as $fields) {
		      fputcsv($fp, $fields);
			}
	   fclose($fp);		
   }
   /*Get the post field  name using form slug*/
   private function getFieldTextBykey($slug,$form_fields){
	   foreach($form_fields as $key=>$fieldvalue){
		   if($fieldvalue['slug']===$slug){
			   return $fieldvalue['name'];
		   }
	   }
   }
	/*Set the mail content type*/
	public function set_mail_content_type(){
	 return "text/html";
	}
    /*Load the mail content from "view/mail/form_mail" */ 
	  public function load_form_email_template($form_datas){
				ob_start();
				$this->loadview("/mail/form_mail",array("form_datas"=>$form_datas));
				$message = ob_get_contents();
				ob_end_clean();
				return $message;
	  }
   public function redirect_url($url)
   {
	   ob_start();
	   header("Location: ".$url);
	   ob_end_flush();
	
   }
}