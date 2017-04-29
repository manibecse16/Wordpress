var app = angular.module('ManageContactForm', []);
app.controller('ContactFormFieldController', function ($scope) {
    $scope.formfields = {};
	 $scope.types=[{name:"Text",value:'text'},{name:"Textarea",value:'textarea'},{name:"File",value:'file'},{name:"Checkbox",value:'checkbox'},{name:"Radio",value:'radio'},{name:"Select Box",value:'select'},{name:"Muliple Select Box",value:'mulipleselect'}];
    $scope.newFormfield = {};
    $scope.addField = function () {
        if ($scope.newFormfield.name && $scope.newFormfield.slug) {
            $scope.formfields.push({"name": $scope.newFormfield.name,"slug": $scope.newFormfield.slug});
			$scope.newFormfield.slug='';
			$scope.newFormfield.name='';
            $scope.isAddForm = false;
        }
    };
});