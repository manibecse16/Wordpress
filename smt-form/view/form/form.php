<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
$form_fields=$smt_form_fields;
$google_captha=(object)$smt_google_captha_setting;
$validate_data_rule=array();
$validate_data_message=array();
?>
<?php if(sizeof($form_fields)>0&&is_array($form_fields)){?>
<div class="form-container">
<?php if(isset($_SESSION['formstatus'])){?>
    <?php if($_SESSION['formstatus']){?>
       <p class="success-msg"><?php echo $_SESSION['formmessage'];?></p> 
	<?php }else{?>
	   <p class="error-msg"><?php echo $_SESSION['formmessage'];?></p> 
	<?php }?>
<?php unset($_SESSION['formstatus']); unset($_SESSION['formmessage']); }?>

<form name="<?php echo $form_id;?>-contact_form" method="post" action="<?php echo get_permalink(); ?>" id="<?php echo $form_id;?>-contact_form" enctype="multipart/form-data" >

<?php  /*Start display the added fields from admin*/?>
<?php foreach($form_fields as $fields){?>
<?php $required=(isset($fields['validatefield'])&&($fields['validatefield']))?true:false;?>
<div class="form-field <?php echo $fields['fieldclass'];?>" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>-wrap">
  <label><?php echo $fields['name'];?><?php echo ($required)?'*':'';?> </label> 
  <span class="form-input-field">
   
  <?php if($fields['type']=="text"||$fields['type']=="email"||$fields['type']=="tel"||$fields['type']=="url"||$fields['type']=="number"):?>
   <input type="<?php echo $fields['type'];?>" name="<?php echo $fields['slug'];?>" placeholder="<?php echo $fields['placeholder'];?>" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>" value="<?php echo $fields['defaultvalue'];?>" <?php echo ($required)?'required="required"':'';?> <?php echo isset($fields['minlegnth'])?"minlength='".$fields['minlegnth']."'":''; ?> />
  <?php endif;?>
  
  <?php if($fields['type']=="textarea"):?> 
    <textarea name="<?php echo $fields['slug'];?>" placeholder="<?php echo $fields['placeholder'];?>" rows="5" cols="30" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>" <?php echo ($required)?'required="required"':'';?> <?php echo "minlength='".$fields['minlegnth']."'"; ?>><?php echo $fields['defaultvalue'];?></textarea> 
   <?php endif;?>  
   
   <?php if($fields['type']=="select"||$fields['type']=="mulipleselect"):?> 
   <?php $default_values=explode(",",$fields['defaultvalue']);?>
	<select name="<?php echo $fields['slug']; echo ($fields['type']=='mulipleselect')?"[]":'';?>" <?php echo ($fields['type']=='mulipleselect')?"multiple "."minlength=".$fields['minlegnth']." title='".$fields['validate_message']."'":'';?> <?php echo ($required)?'required="required"':'';?>>
    	<option value="">Select Option</option>
	 <?php foreach($fields['options'] as $val):?>
		  <option value="<?php echo $val['value'];?>" <?php echo ($fields['type']=='mulipleselect'&&in_array($val['value'], $default_values))? "selected":'';?>><?php echo $val['title'];?></option>
	 <?php endforeach;?>
	</select>
   <?php endif;?>
   
   <?php if($fields['type']=="file"):?> 
      <input type="file" name="<?php echo $fields['slug'];?>" placeholder="<?php echo $fields['placeholder'];?>" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>" value="<?php echo $fields['defaultvalue'];?>" <?php echo ($required)?'required="required"':'';?>   />
   <?php endif;?> 
   
   <?php if($fields['type']=="radio"):?> 
    <?php foreach($fields['options'] as $val):?>
		  <input type="radio" name="<?php echo $fields['slug'];?>" placeholder="<?php echo $fields['placeholder'];?>" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>" value="<?php echo $val['value'];?>" <?php echo ($required)?'required="required"':'';?> <?php echo ($val['value']==$fields['defaultvalue'])? "checked":'';?> /><?php echo $val['title'];?>
	 <?php endforeach;?>
   <?php endif;?> 
   
   <?php if($fields['type']=="checkbox"):?> 
   <?php $cdefault_values=explode(",",$fields['defaultvalue']);?>
   <?php foreach($fields['options'] as $val):?>
		  <input type="checkbox" name="<?php echo $fields['slug']."[]";?>" placeholder="<?php echo $fields['placeholder'];?>" id="<?php echo $form_id;?>-<?php echo $fields['slug'];?>" value="<?php echo $val['value'];?>" <?php echo ($required)?'required="required"':'';?> <?php echo (in_array($val['value'], $cdefault_values))? "checked":'';?>  /><?php echo $val['title'];?>
	 <?php endforeach;?>
   <?php endif;?> 
   <?php 
   
   /* Added field to validation script*/
   if($required){
	   if($fields['type']=='email'){
		   $validate_data_rule[$fields['slug']]=array('required'=>$required,'email'=>true);
	   }elseif($fields['type']=='tel'){
		   $validate_data_rule[$fields['slug']]=array('required'=>$required,'phoneUS'=>true);
	   }elseif($fields['type']=='url'){
		   $validate_data_rule[$fields['slug']]=array('required'=>$required,'url'=>true);
	   }elseif($fields['type']=='number'){
		    $validate_data_rule[$fields['slug']]=array('required'=>$required,'number'=>true);   
	   }elseif($fields['type']=="mulipleselect"){
		     $validate_data_rule[$fields['slug']."[]"]=array('required'=>$required);
	   }elseif($fields['type']=='file'){
	         $validate_data_rule[$fields['slug']]=array('required'=>$required,"filesize"=>!empty($fields['filesize'])?$fields['filesize']:5,"extension"=>!empty($fields['fileextension'])?$fields['fileextension']:'xls|csv|docx|doc|png|jpg|gif|pdf');
	   }else{
		   $validate_data_rule[$fields['slug']]=array('required'=>$required);
	   }
	   if(!empty($fields['validate_message'])){
		  if($fields['type']=="mulipleselect"){
	         $validate_data_message[$fields['slug']."[]"]=array('required'=>$fields['validate_message']);
		  }else{
           $validate_data_message[$fields['slug']]=array('required'=>$fields['validate_message']);
		  } 		  
	   }
	   
   }else{
	 if($fields['type']=='email'){
			$validate_data_rule[$fields['slug']]=array('email'=>true);
	   }elseif($fields['type']=='tel'){
		   $validate_data_rule[$fields['slug']]=array('phoneUS'=>true);
	   }elseif($fields['type']=='number'){
		   $validate_data_rule[$fields['slug']]=array('number'=>true);
	   }elseif($fields['type']=='url'){
		   $validate_data_rule[$fields['slug']]=array('url'=>true);
	   }
   }
   
   if(isset($fields['minlegnth'])&&!empty($fields['minlegnth'])){
	   
		   if(isset( $validate_data_rule[$fields['slug']])){
			   $validate_data_rule[$fields['slug']]=array_merge($validate_data_rule[$fields['slug']],array("minlength"=>$fields['minlegnth']));
		   }else{
				 $validate_data_rule[$fields['slug']]=array("minlength"=>$fields['minlegnth']);
		   }
   }
   ?>
  </span>
 </div>
<?php }?>
<?php  /*End display the added fields from admin*/?>

<?php  /*Check googleRecaptcha enable and enable the captcha*/?>
<?php if($google_captha->is_enable){?>
<div class="form-field google-recaptcha" id="<?php echo $form_id;?>-google-recaptcha-wrap">
<?php  
$validate_data_rule['hiddenRecaptcha']=array('required'=>'function() {if(grecaptcha.getResponse()==""){return true;}else{ return false;}}');
//$validate_data_rule['hiddenRecaptcha']=array('required'=>true,"googleRecaptcha"=>true);
$validate_data_message['hiddenRecaptcha']=array('required'=>'This is required field.');
 ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
 <div class="g-recaptcha" data-sitekey="<?php echo $google_captha->data_sitekey;?>"></div>
 <input type="hidden" class="hiddenRecaptcha" name="hiddenRecaptcha" id="<?php echo $form_id;?>hiddenRecaptcha">
 </div>
<?php }?> 
<?php  /*End googleRecaptcha*/?>
<div class="form-field">
<input type="hidden" name="action" id="action" value="submit">
<input value="Submit" class="btn btn-default" id="submit" name="submit" type="submit">
</div>
</div> 
</form>
<script>
jQuery(document).ready(function() {
	jQuery.validator.addMethod('filesize', function (value, element, param) {
	 /*Convert byte to MB*/
	var param1=((param*1024)*1024);
    return this.optional(element) || (element.files[0].size <= param1)
}, 'File size must be less than {0} MB');
	var rules=<?php echo json_encode($validate_data_rule);?>;
	if(rules.hiddenRecaptcha!=undefined){
	   rules.hiddenRecaptcha.required=eval('('+rules.hiddenRecaptcha.required+')');
	}
		jQuery("#<?php echo $form_id;?>-contact_form").validate({
			 ignore: "",
			 rules:
				rules
			,messages: 
				  <?php echo json_encode($validate_data_message);?>
        })
});
</script>
<?php }?>