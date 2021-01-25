angular.module('LoginCtrl', []).controller('LoginController', function($scope, $rootScope, $http, $state, $sessionStorage) {

	$scope.message = false;

	$scope.init = function () {
		if($rootScope.currentUser) $rootScope.currentUser.role_id == 2 ? $state.go('user') : $state.go('admin');
	};
	$scope.init();

	$scope.login = function () {
		$scope.message = false;
		$http.post('/login', $scope.user).then(function (res) {
			var data = JSON.parse(res.data);
			if(data) {
				$sessionStorage.currentUser = data;
				$rootScope.currentUser = $sessionStorage.currentUser;

				$rootScope.currentUser.role_id == 2 ? $state.go('user') : $state.go('admin');
			} else {
				$scope.message = true;
			}

		}, $scope.errorCallback);
	};

	$scope.clearLoginForm= function () {
		$scope.loginForm.$setUntouched();
		$scope.loginForm.$setPristine();

		document.getElementById('loginForm').reset();
	};

	$scope.errorCallback = function(err) { console.log(err); }
});