angular.module('BackupCtrl', []).controller('BackupController', function($scope, $http, $sessionStorage) {

	$scope.backups = null;
	$scope.status = 'awaiting';

	$scope.getBackUpsList = function () {
		$http.get('/backups').then(function (res) {
			$scope.backups = res.data;
		}, $scope.errorCallback);
	};

	$scope.init = function () {
		$scope.getBackUpsList();
		$scope.status = 'awaiting';
	};
	$scope.init();

	$scope.errorCallback = function(err) { console.log(err); $scope.status = 'fail'; }

	$scope.commit = function () {
		$scope.status = 'processing';
		$http.post('/commit').then(function (res) {
			if(res.data == 'ok'){
				$scope.status = 'success';
			}else{
				$scope.status = 'fail';
			}
		}, $scope.errorCallback);	
	};

});