angular.module('app.controllers', [])
.controller('MainController', function($scope, $http){
    
})

.controller('BathymetryController', function($scope, $http){
	$scope.lat1 = "";
	$scope.lat2 = "";
	$scope.lon1 = "";
	$scope.lon2 = "";
	$scope.angle = "";

	$scope.search = function(){
		alert($scope.lat1);
	}
});
