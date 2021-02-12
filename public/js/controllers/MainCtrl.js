angular.module('MainCtrl', []).controller('MainController', function($scope, $http, $sessionStorage) {

	$scope.languages = null;
	$scope.message = null;
	$scope.users = null;

	$scope.refresh = function () {
		$http.post('/refresh-db', {role: $sessionStorage.currentUser.role_id})
			.then(function (res) {
				var result = res.data;
				if(result) {
					setTimeout(function () {
						$scope.getlanguageList();
						$scope.getUser();
					}, 2000);
				}
			}, $scope.errorCallback);
	};

	$scope.getUser = function () {
		$http.get('/user').then(function (res) {
			$scope.users = JSON.parse(res.data);

		}, $scope.errorCallback);
	};

	$scope.getlanguageList = function () {
		$http.get('/language/list').then(function (res) {
			$scope.languages = JSON.parse(res.data);
		}, $scope.errorCallback);
	};

	$scope.init = function () {
		$scope.getlanguageList();
		$scope.getUser();
	};
	$scope.init();

	$scope.addLanguage = function () {

		$http.post('/language', $scope.language)
			.then(function (res) {

				var result = JSON.parse(res.data);
				if(result) {
					$scope.init();
					$scope.clearLanguageForm();
				}

		}, $scope.errorCallback);
	};

	$scope.addUser = function () {
		$http.post('/user', $scope.user)
			.then(function (res) {
				var result = res.data;
				if(result) {
					$scope.getlanguageList();
					$scope.getUser();

					$scope.clearUserForm();
				} else {
					$scope.message = true;
				}
		}, $scope.errorCallback);
	};

	$scope.deleteUser = function (id) {

		$http.post('/user/del', { userId: id }).then(function (res) {
				if(res) $scope.getUser();
			}, $scope.errorCallback);
	};

	$scope.updateUser = function (id) {

		var obj = {};
		_.forEach($scope.users, function (user) {
			if (user.id == id) _.assign(obj, user);
		});

		$http.put('/user', obj)
			.then(function (res) {

			}, $scope.errorCallback);
	};

	$scope.clearUserForm = function () {
		$scope.addUserForm.$setUntouched();
		$scope.addUserForm.$setPristine();

		document.getElementById('addUserForm').reset();
	};
	$scope.clearLanguageForm= function () {
		$scope.addLanguageForm.$setUntouched();
		$scope.addLanguageForm.$setPristine();

		document.getElementById('addLanguageForm').reset();
	};

	$scope.errorCallback = function(err) { console.log(err); }

});