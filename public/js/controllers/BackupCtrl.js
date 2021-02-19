angular.module('BackupCtrl', []).controller('BackupController', function($scope, $http, $sessionStorage) {

	$scope.backups = null;
	$scope.message = null;

	$scope.getBackUpsList = function () {
		$http.get('/backups').then(function (res) {
			$scope.backups = res.data;
		}, $scope.errorCallback);
	};

	$scope.init = function () {
		$scope.getBackUpsList();
	};
	$scope.init();

	$scope.errorCallback = function(err) { console.log(err); }

});