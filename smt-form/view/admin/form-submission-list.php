<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php $upload_dir = wp_upload_dir(); ?>
<h2>Monkey Form Submission List</h2>
<?php if(isset($_SESSION['status'])){?>
    <?php if($_SESSION['status']){?>
        <div class="notice notice-success"><p><?php echo $_SESSION['message'];?></p></div> 
	<?php }else{?>
	    <div class="notice-error notice"><p><?php echo $_SESSION['message'];?></p></div> 
	<?php }?>
<?php unset($_SESSION['status']); unset($_SESSION['message']); }?>
<div class="metabox-holder">  
<div style="width: 99.5%" class="postbox-container">
 <div ng-app="FormSubmissionList" ng-controller="FormSubmissionController" id="FormSubmissionList"> 
  <div id="contact-form-field-option" class="postbox">
	<div id="page-option" class="postbox">
		<div title="" class="handlediv"><br></div><h2 class="hndle"><span>Form Submission List</span></h2> 
			<div class="inside">
				<div class="row">
					<div class="form-group1" style="margin-bottom: 10px;float: left;">
					   <label >Select Form </label>
						<select ng-model="formtitleselect" ng-options="formTitle.post_title for formTitle in formTitles  track by formTitle.ID" ng-change="fetchFormData(formtitleselect)" >
						</select>
						<a href="javascript:void(0);" ng-click="downLoadCSV();" class="button">Download To CSV</a>			  
					</div>
					<form class="form-inline" style="float: right; margin-right: 30px;">
								<div class="form-group" style="margin-bottom: 10px;">
									<label >Search</label>
									<input type="text" ng-model="search" class="form-control" placeholder="Search">
								</div>
					</form>
				</div>
				<table class="wp-list-table widefat fixed striped">
					<tr ng-if="formDataHeader.length>0"> 
						<th class="check-column"  ></th>
						<th ng-repeat="header in formDataHeader" ng-click="sort(header.key)"><span>{{header.value}}</span><span class="glyphicon sort-icon" ng-show="sortKey=='{{header.key}}'" ng-class="{'dashicons dashicons-arrow-up':reverse,'dashicons dashicons-arrow-down':!reverse}"></span></th>
					</tr>
					<tr ng-if="formDataHeader.length==0"> 
						<td class="">Empty Form Fields</td>  
					</tr>
					<?php /*Start the form fields listing*/ ?>
					<tr dir-paginate="formdata in formDataLists|orderBy:sortKey:reverse|filter:search|itemsPerPage:20"> 		   
						<th class="check-column" ><a class="btn" href="<?php echo admin_url( 'edit.php?post_type=smt_contact_form&page=smt-contact-form-submission', '' );?>&action=delete&id={{formdata.id}}" title="Remove Field"><span class="dashicons dashicons-trash"></span></a></th>
						<td ng-repeat="data in formdata.data">
							<div ng-if="!data.type">  
								{{data.value}}
							</div> 
							<div ng-if="data.type">  
								<a ng-href="<?php echo $upload_dir['baseurl']; ?>{{data.path}}" target="_blank">{{data.value}}</a> 
							</div> 
						</td>
					</tr>
					<tr ng-if="formDataLists.length==0">
						<th class="check-column"></th>
						<td colspan="{{formDataHeader.length}}">Not found the form submission list</td>
					</tr>			 
				</table>
				<dir-pagination-controls max-size="20"	direction-links="true"	boundary-links="false" ></dir-pagination-controls>
		</div>
	</div>	
  </div>
</div>  
</div>
</div>
<script type="text/javascript">
var app = angular.module('FormSubmissionList', ['angularUtils.directives.dirPagination']);
app.controller('FormSubmissionController', function ($scope, $http) {
	<?php if(is_array($header)&&sizeof($header)>0){?>
		$scope.formDataHeader =<?php echo json_encode($header);?>;
	<?php }else{?>
		$scope.formDataHeader=[];
	<?php }?> 
	<?php if(is_array($form_title)&&sizeof($form_title)>0){?>
		$scope.formTitles =<?php echo json_encode($form_title);?>;
		$scope.formtitleselect = $scope.formTitles[0];
	<?php }else{?>
		$scope.formTitles=[];
	<?php }?> 

	<?php if(is_array($form_list)&&sizeof($form_list)>0){?>
		$scope.formDataLists =<?php echo json_encode($form_list);?>;
	<?php }else{?>
		$scope.formDataLists=[];
	<?php }?>  
		$scope.sort = function(keyname){
		$scope.sortKey = keyname;   //set the sortKey to the param passed
		$scope.reverse = !$scope.reverse; //if true make it false and vice versa
	}
	$scope.selected  = {};
	$scope.toggleAll =  function(){
		var toggleStatus = $scope.isAllSelected;
		for (var i = 0; i < $scope.formDataLists.length; i++) {
			var item = $scope.formDataLists[i];
			$scope.selected[item.id] = toggleStatus;
		}
	}
	$scope.optionToggled = function(){
	//$scope.isAllSelected = $scope.formDataLists.every(function(itm){ console.log(itm); })
	}
	$scope.fetchFormData=function(selectedData){
		var durl='<?php echo admin_url( 'admin-ajax.php' );?>';
		$http({
			url: durl,
			method: "POST",
			params: {
				action: "get_ajax_form_details",
				form_id:selectedData.ID
			}
		}).success(function(data){
			$scope.formDataHeader=data.header;
			$scope.formDataLists=data.form_list;
		});
	}
	$scope.downLoadCSV=function(){
		var durl='<?php echo admin_url( 'edit.php?post_type=smt_contact_form&page=smt-contact-form-submission', '' );?>&action=export&form_id='+$scope.formtitleselect.ID;
		window.location=durl;
	}
});
</script>