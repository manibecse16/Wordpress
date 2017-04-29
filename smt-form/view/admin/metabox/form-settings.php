<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
if(is_admin()):
//print_r($smt_form_fields);
$admin_email = get_option( 'admin_email' );
?>
<div ng-app="ManageContactForm" ng-controller="ContactFormFieldController">
<h2 class="nav-tab-wrapper">
<a href="javascript:void(0);" class="nav-tab nav-tab-active" id="contact-general" ng-click="tab=1">Manage Fields</a>
<a href="javascript:void(0);" class="nav-tab" id="contact-mail-setting" ng-click="tab=2">Mail Settings</a>
<a href="javascript:void(0);" class="nav-tab" id="contact-google-captha" ng-click="tab=3">Google Captcha</a>
</h2>
<div class="metabox-holder">         
			<!-- // TODO Move style in css --> 
<div class="menu-group contact-general tb-nav-tab-active">
 <div   id="ManageContactForm"> 
 <div id="contact-form-field-option" class="postbox">
	<div id="page-option" class="postbox">
		<div title="" class="handlediv"><br></div><h2 class="hndle"><span>Manage form fields</span></h2> 
			<div class="inside">
			 <p>You can reorder field using the drag option.</p>
			 <table class="form-table" as-sortable="sortableCloneOptions1" data-ng-model="formfields">
				<tr><th>Field Name</th> <th>Field Type</th><th>Is Required</th><th>Action</th></tr>
			 
			 <?php /*Start the form fields listing*/ ?>
				 <tr ng-repeat="formfield in formfields" as-sortable-item> 
						<td  ng-hide="isEditform" as-sortable-item-handle>{{formfield.name}}</td> 
						<td ng-hide="isEditform" as-sortable-item-handle>{{formfield.type}}</td>
						<td ng-hide="isEditform" as-sortable-item-handle>{{formfield.validatefield ? 'YES' : 'NO'}}</td>
						<td ng-hide="isEditform"><a ng-click="isEditform=true;"  class="btn" title="Edit Field"><span class="dashicons dashicons-edit"></span></a> | <a class="btn" ng-click="remove(formfield)" title="Remove Field"><span class="dashicons dashicons-trash"></span></a></td>
						
						<td ng-show="isEditform" colspan="3">
						 <table class="form-table">
							<tr valign="top">
							   <th scope="row">Field Name*</th>
							   <td><input type="text" ng-model="formfield.name" name="smt_form_field[{{$index}}][name]" /></td>       
						   </tr>
						   <tr valign="top">
							   <th scope="row">Field Slug*</th>
							   <td><input type="text" ng-model="formfield.slug" name="smt_form_field[{{$index}}][slug]" ng-change="validateSlug(formfield.slug,$index)" readonly /><em style="font-size:12px;" >It must be unquie.</em>
							   <p ng-show="slugvalid" class="smterror">You can't use <strong>"{{formfield.slug}}"</strong>.It's wordpress reserved terms.</p>
							   <p ng-show="slugduplicate" class="smterror">It must be unquie.<strong>"{{formfield.slug}}"</strong> already exist.</p>
							   
							   </td>       
						   </tr>
						   <tr valign="top">
							   <th scope="row">Type*</th>
							   <td>
								<select ng-model="formfield.type" name="smt_form_field[{{$index}}][type]">
											<option value="{{type.value}}" ng-repeat="type in types">{{type.name}}</option>
								</select>
							   </td>       
						   </tr>
						   <tr ng-show="formfield.type=='file'">
							 <th>File Size</th>
							 <td><input type="text" ng-model="formfield.filesize" name="smt_form_field[{{$index}}][filesize]" value="5" /><em style="font-size:12px;" >Normal size in MB.Default size 5 MB.</em></td>  
						 
						   </tr>
						   <tr ng-show="formfield.type=='file'">
							 <th>File Allowed Extension</th>
							 <td><input type="text" ng-model="formfield.fileextension" name="smt_form_field[{{$index}}][fileextension]" value="xls|csv|docx|doc|png|jpg|gif|pdf" /><em style="font-size:12px;">Add the file format like "xls|csv|docx|doc|png|jpg|gif|pdf"</em></td>     
						   </tr>
						   <tr ng-show="formfield.type=='checkbox'|| formfield.type=='radio'|| formfield.type=='select'|| formfield.type=='mulipleselect'">
							   <th>Options</th>
							   <td>
							   <table class="form-table">
								 <tr><th>Display Text</th><th>Custom field content</th><th>Default</th></tr>
								 <tr ng-repeat="option in formfield.options"> 
									<td><input type="text" ng-model="option.title" name="smt_form_field[{{$parent.$index}}][options][{{$index}}][title]" /></td>
									<td><input type="text" ng-model="option.value" name="smt_form_field[{{$parent.$index}}][options][{{$index}}][value]" /></td>
									<td><a class="btn" ng-click="removeOption(option,formfield.options)" title="Remove Option" ><span class="dashicons dashicons-trash"></span></a></td>					
								</tr>
								<tr ng-show="isOption"> 
									<td><input type="text" ng-model="newOption.title" /></td>
									<td><input type="text" ng-model="newOption.value" /></td>
									<td><a class="btn" ng-click="addOption(formfield.options)" title="Save New Option"><span class="dashicons dashicons-plus-alt"></span>Save</a> | <a class="btn" ng-click="isOption=false;" title="Cancel New Option"><span class="dashicons dashicons-dismiss"></span></a></td>					
								</tr>
								</table>
								<div ng-hide="isOption">
									<a ng-click="isOption=true;" ><span class="dashicons dashicons-plus"></span> Add Option</a>
								</div>
							   </td>	
						   </tr>
						  <tr ng-show="formfield.type!='checkbox'|| formfield.type!='radio'|| formfield.type!='select'|| formfield.type!='mulipleselect'">
							   <th scope="row">Placeholder</th>
							   <td>
								  <input type="text" ng-model="formfield.placeholder" name="smt_form_field[{{$index}}][placeholder]" />
							   </td>       
						   </tr>
							<tr valign="top">
							   <th scope="row">Default Value</th>
							   <td><input type="text" ng-model="formfield.defaultvalue" name="smt_form_field[{{$index}}][defaultvalue]" /> 
							   <em style="font-size:12px;" ng-show="formfield.type=='checkbox'|| formfield.type=='mulipleselect'">Add the 	Custom field content with comma separated</em>
							   </td>       
						   </tr>
							<tr valign="top">
							   <th scope="row">Class Name</th>
							   <td><textarea  ng-model="formfield.fieldclass" name="smt_form_field[{{$index}}][fieldclass]" row="5" ></textarea>
							   </td>       
						   </tr>
						   <tr valign="top">
							   <th scope="row">Validate</th>
							   <td>
								  <input type="checkbox" ng-model="formfield.validatefield" value="1" ng-checked="formfield.validatefield==1" ng-click="unchecke(formfield.validatefield,formfield)"   name="smt_form_field[{{$index}}][validatefield]" ng-true-value="1" ng-false-value="0" />Validate
							   </td>       
						   </tr>
						   <tr valign="top">
							   <th scope="row">Validate Message</th>
							   <td>
								 <input type="text" ng-model="formfield.validate_message" name="smt_form_field[{{$index}}][validate_message]" /> 
							   </td>       
						   </tr>
						   <tr valign="top">
							   <th scope="row">Min Length</th>
							   <td>
								  <input type="text" ng-model="formfield.minlegnth" value="1"  name="smt_form_field[{{$index}}][minlegnth]" ng-disabled="formfield.type=='email'||formfield.type=='radio'|| formfield.type=='select'"  />
							   </td>       
						   </tr>
						   <tr valign="top">
							 <td colspan="2"><a class="btn" ng-click="isEditform=false;" ><span class="dashicons dashicons-welcome-write-blog"></span> Update Field</a>
							   </td>       
						   </tr>
						</table>
					  </td>	 	  
					</tr>
					 <?php /*End the form fields listing*/ ?>
			</table>	
			
        <?php /*Start Add the new form fields */ ?>
			<div ng-hide="isAddForm">
				<a ng-click="isAddForm=true;" ><span class="dashicons dashicons-plus"></span> Add Field</a>
			</div>
			
			<ng-form ng-show="isAddForm" name="newform" >
			<table class="form-table">
				<tr valign="top">
				   <th scope="row">Field Name*</th>
				   <td><input type="text" ng-model="newFormfield.name" name="smt_form_new_field[{{$index}}][name]"  /></td>       
			   </tr>
			   <tr valign="top">
				   <th scope="row">Field Slug*</th>
				   <td><input type="text" ng-model="newFormfield.slug"  name="smt_form_new_field[{{$index}}][slug]" ng-change="validateSlug(newFormfield.slug,-1)"  /><em style="font-size:12px;" >It must be unquie.</em>
				   <p ng-show="slugvalid" class="smterror">You can't use <strong>"{{newFormfield.slug}}"</strong>.It's wordpress reserved terms.</p>
				    <p ng-show="slugduplicate" class="smterror">It must be unquie.<strong>"{{newFormfield.slug}}"</strong> already exist.</p>
				   </td>       
			   </tr>
			   <tr valign="top">
				   <th scope="row">Type*</th>
				   <td>
					<select ng-model="newFormfield.type" name="smt_form_new_field[{{$index}}][type]" >
								<option value="{{type.value}}" ng-repeat="type in types">{{type.name}}</option>
					</select>
				   </td>       
			   </tr>
			   <tr ng-show="newFormfield.type=='file'">
				 <th>File Size</th>
				 <td><input type="text" ng-model="newFormfield.filesize" name="smt_form_new_field[{{$index}}][filesize]" value="5" /><em style="font-size:12px;" >Normal size in MB.Default size 5 MB.</em></td>     
				 
			   </tr>
			   <tr ng-show="newFormfield.type=='file'">
				 <th>File Allowed Extension</th>
				 <td><input type="text" ng-model="newFormfield.fileextension" name="smt_form_new_field[{{$index}}][fileextension]" value="xls|csv|docx|doc|png|jpg|gif|pdf" />
				 <em style="font-size:12px;">Add the file format like "xls|csv|docx|doc|png|jpg|gif|pdf"</em>
				 </td>     
			   </tr>
			   <tr ng-show="newFormfield.type=='checkbox'|| newFormfield.type=='radio'|| newFormfield.type=='select'|| newFormfield.type=='mulipleselect'" class="border">
				   <th>Options</th>
				   <td>
				   <table class="form-table">
					 <tr><th>Display Text</th><th>Custom field content</th></tr>
					 <tr ng-repeat="option in newFormfield.options"> 
						<td><input type="text" ng-model="option.title" name="smt_form_new_field[{{$parent.$index}}][options][title]]"  /></td>
						<td><input type="text" ng-model="option.value" name="smt_form_new_field[{{$parent.$index}}][options][value]]" /></td>
						<td><a class="btn" ng-click="removeOption(option,newFormfield.options)" >Remove Option</a></td>					
					</tr>
					<tr ng-show="isOption"> 
						<td><input type="text" ng-model="newOption.title" /></td>
						<td><input type="text" ng-model="newOption.value" /></td>
						<td><a class="btn" ng-click="addOption(newFormfield.options)" title="Save New Option"><span class="dashicons dashicons-plus-alt"></span> Save</a> | <a class="btn" ng-click="isOption=false;" title="Cancel New Option"><span class="dashicons dashicons-dismiss"></span></a></td>					
					</tr>
					</table>
					<div ng-hide="isOption">
						<a ng-click="isOption=true;" ><span class="dashicons dashicons-plus"></span> Add Option</a>
					</div>
				   </td>	
			   </tr>
			  <tr valign="top" ng-show="newFormfield.type!='checkbox'|| newFormfield.type!='radio'|| newFormfield.type!='select'|| newFormfield.type!='mulipleselect'">
				   <th scope="row">Placeholder</th>
				   <td>
					  <input type="text" ng-model="newFormfield.placeholder" name="smt_form_new_field[{{$index}}][placeholder]" />
				   </td>       
			   </tr>
				<tr valign="top">
				   <th scope="row">Default Value</th>
				   <td><input type="text" ng-model="newFormfield.defaultvalue" name="smt_form_new_field[{{$index}}][defaultvalue]" /> 
				   </td>       
			   </tr>
			   <tr valign="top">
				   <th scope="row">Class Name</th>
				   <td><textarea ng-model="newFormfield.fieldclass" name="smt_form_new_field[{{$index}}][fieldclass]"  row="5" ></textarea>
				   </td>       
			   </tr>
			   <tr valign="top">
				   <th scope="row">Validate</th>
				   <td>
					  <input type="checkbox" ng-model="newFormfield.validatefield" value="1"  name="smt_form_new_field[{{$index}}][validatefield]" ng-true-value="1" ng-false-value="0" />Validate
				   </td>       
			   </tr>
			   <tr valign="top">
				   <th scope="row">Validate Message</th>
				   <td>
					 <input type="text" ng-model="newFormfield.validate_message" name="smt_form_new_field[{{$index}}][validate_message]" /> 
				   </td>       
			   </tr>
				 <tr valign="top">
				   <th scope="row">Min Length</th>
				   <td>
					  <input type="text" ng-model="newFormfield.minlegnth" value="1"  name="smt_form_new_field[{{$index}}][minlegnth]" ng-disabled=" formfield.type=='email'||formfield.type=='radio'|| formfield.type=='select'" />
				   </td>       
			   </tr>
			</table>
			<a class="btn" ng-click="addField()"><span class="dashicons dashicons-welcome-add-page"></span> Save Field</a> | <a class="btn" ng-click="isAddForm=false;validRequired=false;slugduplicate=false;"><span class="dashicons dashicons-dismiss"></span> Cancel Field</a>
			 <p ng-show="validRequired" class="smterror">Please filled out all required field "*".</p>
			</ng-form>
	 <?php /*End Add the new form fields */ ?>			
	</div>
	</div>
   </div>
 </div>
</div>
<div class="menu-group contact-mail-setting">
<div id="page-option" class="postbox">
<?php $mail_options=(object)$smt_mail_options;?>
	<div title="" class="handlediv"><br></div><h2 class="hndle"><span>Mail Settings</span></h2> 
    <div class="inside">
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Sender Email*</th>
        <td><input type="text" name="smt_mail_setting[sender_mail]" value="<?php echo isset($mail_options->sender_mail)?$mail_options->sender_mail:$admin_email;?>" required /></td>       
        </tr>
		 <tr valign="top">
        <th scope="row">Sender Name*</th>
        <td><input type="text" name="smt_mail_setting[sender_name]" value="<?php echo isset($mail_options->sender_name)?$mail_options->sender_name:'';?>" required/></td>
        </tr>
		<tr valign="top">
        <th scope="row">Recevier Email*</th>
        <td><input type="text" name="smt_mail_setting[recevier_email]" value="<?php echo isset($mail_options->recevier_email)?$mail_options->recevier_email:$admin_email;?>" required/></td>
        </tr>
		<tr valign="top">
        <th scope="row">Recevier Name*</th>
        <td><input type="text" name="smt_mail_setting[recevier_name]" value="<?php echo isset($mail_options->recevier_name)?$mail_options->recevier_name:'';?>" required/></td>
        </tr>
		<tr valign="top">
        <th scope="row">BCC Email*</th>
        <td><input type="text" name="smt_mail_setting[bcc_email]" value="<?php echo isset($mail_options->bcc_email)?$mail_options->bcc_email:$admin_email;?>" required/></td>
        </tr>
		<tr valign="top">
        <th scope="row">BCC Name*</th>
        <td><input type="text" name="smt_mail_setting[bcc_name]" value="<?php echo isset($mail_options->bcc_name)?$mail_options->bcc_name:'';?>" required/></td>
        </tr>
        <tr valign="top">
			<th scope="row">Redirect On Success</th>
			<td>
			<?php if(isset($mail_options->is_redirect_enable)&&$mail_options->is_redirect_enable):?>
			    <input type="radio" name="smt_mail_setting[is_redirect_enable]" value="1" checked /> Enable 
			    <input type="radio" name="smt_mail_setting[is_redirect_enable]" value="0"/> Disable
			<?php else:?>
			    <input type="radio" name="smt_mail_setting[is_redirect_enable]" value="1" /> Enable 
			    <input type="radio" name="smt_mail_setting[is_redirect_enable]" value="0" checked /> Disable
            <?php endif;?>			
			</td>
        </tr>
		<tr valign="top">
			<th scope="row">Thank You Page URL</th>
			<td><input type="text" name="smt_mail_setting[redirect_url]" value="<?php echo isset($mail_options->redirect_url)?$mail_options->redirect_url:'';?>" /></td>
        </tr>
		</table>
        </div>
        </div>
</div>  
<div class="menu-group contact-google-captha">
<div id="page-option" class="postbox">
<?php $google_option=(object)$smt_google_captha_setting;?>
	<div title="" class="handlediv"><br></div><h2 class="hndle">Google Captcha Settings</span></h2> 
    <div class="inside">
	  <table class="form-table">
	    <tr valign="top">
			<th scope="row">Is Enable?</th>
			<td>
			<?php if(isset($google_option->is_enable)&&$google_option->is_enable):?>
			    <input type="radio" name="smt_google_captha_setting[is_enable]" value="1" checked /> Enable 
			    <input type="radio" name="smt_google_captha_setting[is_enable]" value="0"/> Disable
			<?php else:?>
			    <input type="radio" name="smt_google_captha_setting[is_enable]" value="1" /> Enable 
			    <input type="radio" name="smt_google_captha_setting[is_enable]" value="0" checked /> Disable
            <?php endif;?>			
			</td>
        </tr>
        <tr valign="top">
			<th scope="row">Data Site Key</th>
			<td><input type="text" name="smt_google_captha_setting[data_sitekey]" value="<?php echo isset($google_option->data_sitekey)?$google_option->data_sitekey:'';?>"/> </td>       
        </tr>
	</table>	
    </div>
 </div>	
 </div>
</div>
</div>
<style type="text/css">
.menu-group {
    display: none;
}
.tb-nav-tab-active {
    display: block;
}
</style>
<script type="text/javascript">
var app = angular.module('ManageContactForm', ['ngRoute',
    'as.sortable']);
//angular.element('#publish').triggerHandler('click');	
app.controller('ContactFormFieldController', ['$scope',
  function($scope) { 
    //$scope.slugvalid=false;
	//$scope.slugduplicate=false;
	//$scope.isEditform=false;
	//$scope.validRequired=false;
	<?php if(is_array($smt_form_fields)&&sizeof($smt_form_fields)>0){?>
    $scope.formfields =<?php echo json_encode($smt_form_fields);?>;
	<?php }else{?>
	  $scope.formfields=[];
	<?php }?>
	$scope.types=[{name:"Text",value:'text'},{name:"Email",value:'email'},{name:"Phone Number",value:'tel'},{name:"Number",value:'number'},{name:"URL",value:'url'},{name:"Textarea",value:'textarea'},{name:"File",value:'file'},{name:"Checkbox",value:'checkbox'},{name:"Radio",value:'radio'},{name:"Select Box",value:'select'},{name:"Muliple Select Box",value:'mulipleselect'}];
    /*Start the field add/remove process*/
	$scope.newFormfield = {};
	$scope.addField = function () {
			if ($scope.newFormfield.name && $scope.newFormfield.slug&&$scope.newFormfield.type&&!$scope.slugvalid&&!$scope.slugduplicate) {
				$scope.formfields.push($scope.newFormfield);
				$scope.newFormfield={};
				$scope.isAddForm = false;
				$scope.validRequired=false;
			}else{
				$scope.validRequired=true;
			}
    };
	$scope.updateField = function (formfield) {
		
			if (formfield.name &&formfield.slug&&formfield.type&&!$scope.slugvalid&&!$scope.slugduplicate) {
				$scope.isEditform=false;
				$scope.validRequired=false;
			}else{
				$scope.validRequired=true;
			}
    };
	$scope.remove = function(item) { 
	  var index = $scope.formfields.indexOf(item);
	  $scope.formfields.splice(index, 1);     
	}
	/*End the field add/remove process*/
	
	$scope.validateSlug=function(value,cindex){
		var wrevervedterms = ['attachment','attachment_id','author','author_name','calendar','cat','category','category__and','category__in','category__not_in','category_name','comments_per_page','comments_popup','customize_messenger_channel','customized','cpage','day','debug','error','exact','feed','fields','hour','link_category','m','minute','monthnum','more','name','nav_menu','nonce','nopaging','offset','order','orderby','p','page','page_id','paged','pagename','pb','perm','post','post__in','post__not_in','post_format','post_mime_type','post_status','post_tag','post_type','posts','posts_per_archive_page','posts_per_page','preview','robots','s','search','second','sentence','showposts','static','subpost','subpost_id','tag','tag__and','tag__in','tag__not_in','tag_id','tag_slug__and','tag_slug__in','taxonomy','tb','term','theme','type','w','withcomments','withoutcomments','year'];
		 if (wrevervedterms.indexOf(value) !== -1) {
		    $scope.slugvalid=true;  	
		  }else{
			  $scope.slugvalid=false;  	
		  }
	$scope.slugduplicate=false;
	var check=$scope.slugduplicateCheck(value,cindex);
		if(check){
		  $scope.slugduplicate=true;
		}
	}
	$scope.slugduplicateCheck=function(value,cindex){
		 var i=0;	
		angular.forEach($scope.formfields, function(item,index){
			  if(item.slug==value&&cindex!=index){
				  i=1;
			  }
		});
		return i;
	}
	$scope.unchecke=function(value,formfield){
		if(!angular.isNumber(value)&&value=="1"){
			formfield.validatefield=0;
		}
	}
	/*Start the option add/remove process*/
	$scope.newOption={};
	$scope.addOption= function(item) { 
		if(angular.isUndefined($scope.newFormfield.options)){
			$scope.newFormfield.options=[];
		}
	   if ($scope.newOption.title && $scope.newOption.value) {
		  if(item){
			 item.push($scope.newOption); 
			 $scope.newOption={};		
			 $scope.isOption = false; 
			 setTimeout(function () {
		     $scope.$apply(function(){
				 $scope.isOption = false;});
			   }, 1000);
		  }		 
        }
	}
	
	$scope.removeOption= function(item,formfield) { 
	   console.log(formfield);
	   var index = formfield.indexOf(item);
	   formfield.splice(index, 1);
	}
	/*End the option add/remove process*/
	$scope.tab=1; 
	$scope.jumpToInvalidTab=function(){
        $scope.tab=1;
        if($scope.form1.$valid){
            $scope.tab=2;
        };
    };
}]);
jQuery('.nav-tab-wrapper .nav-tab').click(function(){
		jQuery('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		el = jQuery(this);
		elid = el.attr('id');
		jQuery('.menu-group').hide(); 
		jQuery('.'+elid).show();
		el.addClass('nav-tab-active');
	});
</script>
<?php endif;?>