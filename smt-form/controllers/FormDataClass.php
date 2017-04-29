<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class FormDataClass extends MainClass{
	private $formModel;
	public function __construct(){
		ob_start();
		 /*Load the Form Model*/	
	 if (!class_exists('Form')) { 
		      $this->formModel=$this->loadmodel("Form");
		}else{
			  $this->formModel=new Form();
		}
		add_action("admin_menu",array($this,"add_sub_menu"));
		add_action( 'wp_ajax_get_ajax_form_details',array($this,'get_ajax_form_details'));
		add_action( 'wp_ajax_nopriv_get_ajax_form_details',array($this, 'get_ajax_form_details')); // need this to serve non logged in users
	}
	public function add_sub_menu(){
		//add_submenu_page("smt-contactform-setting","Form Submissions",'Form Submissions', 'manage_options', 'smt-form-submission', array( $this, 'index' ));
	    add_submenu_page('edit.php?post_type=smt_contact_form', __('Form Submissions','menu-test'), __('Form Submissions','menu-test'), 'manage_options', 'smt-contact-form-submission',array($this,'index'));
	}
	public function index(){
        $action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

		switch($action){
			case "export":
			 /*Export the form data as CSV format*/
			  $form_id=isset($_REQUEST['form_id'])?$_REQUEST['form_id']:'';
			  try{
			  if($form_id){
				  $form_model=$this->loadmodel("Formdata");
				  $form_list=$form_model->getFormData($form_id);
				  $form_fileds=$this->formModel->getFormFields($form_id);	
				  $header=$this->formHeader($form_fileds);
				  $csvheader = array_map(function ($ar) {return $ar['value'];}, $header);
				  $filename="monkey-".get_the_title($form_id)."-data-".date("Y-m-d").".csv";
				  $filename_dir=SMT_CONTACT_CSV_STORE."/".$filename;
					ob_clean ();	
				$fp = fopen($filename_dir, 'w');
				  if(!$fp){
					   throw new Exception('Unable to open file!');	
				  }				   
				  fputcsv($fp,$csvheader);
				  foreach($form_list as $val=>$formdata){
						   $newdata=$this->removeFieldValue(unserialize($formdata->data),$header);
						   $values = array_map(function ($ar) {return isset($ar['value'])?$ar['value']:'';}, $newdata);
						   fputcsv($fp,$values);
				  }
				fclose($fp);
				$this->readheader($filename_dir);
			  }else{
				  throw new Exception('Not found the form data');
			  }
			}catch (Exception $e) {
                 $_SESSION['status']=0;
				 $_SESSION['message']=$e->getMessage();
				 wp_redirect(admin_url( 'edit.php?post_type=smt_contact_form&page=smt-contact-form-submission', '' ));
				 exit;  
             }	
			break;
			case "delete":
			  $id=isset($_GET['id'])?$_GET['id']:'';
			  try{
				  if($id){
					 $form_model=$this->loadmodel("Formdata");
					 if($form_model->deleteFormdata($id)){
						$_SESSION['status']=1;
						$_SESSION['message']="Data deleted successfully.";
						wp_redirect(admin_url( 'edit.php?post_type=smt_contact_form&page=smt-contact-form-submission', '' ));
						exit;
					 }else{
					  throw new Exception('Error deleting the data.');
					 }					 
				  }else{
					   throw new Exception('Error deleting the data.');
				  }
			  }catch (Exception $e) {
                 $_SESSION['status']=0;
				 $_SESSION['message']=$e->getMessage();
				 wp_redirect(admin_url( 'edit.php?post_type=smt_contact_form&page=smt-contact-form-submission', '' ));
				 exit;  
             }	
			break; 
			
			default: 
			$form_model=$this->loadmodel("Formdata");
			$form_title=$form_model->getFormTitle();
			$format_data=array();
			$header=array();
			if(sizeof($form_title)>0){
				$form_list=$form_model->getFormData($form_title[0]->ID);
			    $form_fileds=$this->formModel->getFormFields($form_title[0]->ID);	
			    $header=$this->formHeader($form_fileds);
		        $format_data=$this->formatData($form_list,$header);
			}
		    $this->loadview("admin/form-submission-list",array("form_list"=>$format_data,"header"=>$header,"form_title"=>$form_title));
			break;
		}
	}
	/*Format the DB value based on the form field*/
    private function formatData($form_list,$header){
		$data=array();
		$i=0;
		foreach($form_list as $val=>$formdata){
			   $newdata=$this->removeFieldValue(unserialize($formdata->data),$header);
			  $data[$i]['id']=$formdata->id;
			  $data[$i]['data']=$newdata;
			  $i++;
		}
		return $data;
	}
	/*Removed the DB value removed form field*/
	private function removeFieldValue($data,$header){
				$newdata=array();
				foreach($header as $k=>$value){
					$newdata[$k]=array();
					foreach($data as $key=>$dval){
						if($value['key']==$dval['key']){
							$newdata[$k]=$dval;
						}
					}						
				}
    return $newdata;		
	} 
  /*Generate the form header*/
   private function formHeader($form_fileds){
     $data=array();
	 //$form_fileds=get_option('smt_Form_Fields');
	 if(is_array($form_fileds)&&sizeof($form_fileds)>0){
		foreach($form_fileds as $key=>$dataval){
			$data[$key]['key']=$dataval['slug'];
			$data[$key]['value']=$dataval['name'];
		}
	 }
		return $data;  
   }
  public function get_ajax_form_details(){
    $form_id=$_REQUEST['form_id'];
	if($form_id){
      $form_model=$this->loadmodel("Formdata");
	  $form_list=$form_model->getFormData($form_id);
	  $form_fileds=$this->formModel->getFormFields($form_id);	
	  $header=$this->formHeader($form_fileds);
	  $format_data=$this->formatData($form_list,$header);
	  echo json_encode(array("form_list"=>$format_data,"header"=>$header));
	}
	 wp_die();
  }  
  /*Download the file as CSV*/
  public function readheader($filename_dir){
		header( "Pragma: public" ); // required
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Cache-Control: private", false ); // required for certain browsers 
		header( "Content-Type: application/force-download" );
		// change, added quotes to allow spaces in filenames, by Rajkumar Singh
		header( "Content-Disposition: attachment; filename=\"" . basename($filename_dir) . "\";" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Length: ". filesize($filename_dir) );
		readfile( "$filename_dir" );
		unlink($filename_dir);
		exit();
	}  
  
}	