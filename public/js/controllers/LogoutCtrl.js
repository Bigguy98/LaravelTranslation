angular.module('LogoutCtrl', []).controller('LogoutController', function($scope, $rootScope, $state, $sessionStorage, $http) {

	delete $sessionStorage.currentUser;
	$rootScope.currentUser = $sessionStorage.currentUser;
	$state.go('login');
});