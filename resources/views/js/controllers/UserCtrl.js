angular.module('UserCtrl', []).controller('UserController', function($scope, $http, $rootScope, $sessionStorage) {

	var
		nameList = ['Pierre', 'Pol', 'Jacques', 'Robert', 'Elisa'],
		familyName = ['Dupont', 'Germain', 'Delcourt', 'bjip', 'Menez'];

	function createRandomItem() {
		var
			firstName = nameList[Math.floor(Math.random() * 4)],
			lastName = familyName[Math.floor(Math.random() * 4)],
			age = Math.floor(Math.random() * 100),
			email = firstName + lastName + '@whatever.com',
			balance = Math.random() * 3000;

		return {
			firstName: firstName,
			lastName: lastName,
			age: age,
			email: email,
			balance: balance
		};
	}

	$scope.itemsByPage=15;

	$scope.rowCollection = [];
	for (var j = 0; j < 200; j++) {
		$scope.rowCollection.push(createRandomItem());
	}

	$scope.translate = null;
	$scope.currentItem = null;
	$scope.currentValue = null;
	$scope.originalValue = null;
	$scope.originalTarget = null;

	$scope.message = null;

	$scope.autoSave = 0;

	$scope.getPopoveerContent = function (lang, col, id, curentValue) {
		$scope.currentValue = curentValue;

		var data = { lang:lang, col: col, id: id};
		$scope.currentItem = data;
		$http.post('/popover', data).then(function (res) {
			if(res) $scope.translate = JSON.parse(res.data);
		}, function(err){
			console.log(err);
		});
	};

	$scope.dynamicPopover = {
		templateUrl: 'translatePopoverTemplate.html',
		placement: 'auto bottom top',
		trigger: 'outsideClick click'
	};

	$scope.allColors = ['White', 'Yellow'];
	$scope.syncColors = function (colors) {

		var val = $scope.currentValue[$scope.currentItem.col];

		var save = false;
		_.forEach($scope.currentValue, function (value, key) {

			// get all colors from key
			var allColors = [];
			var allKeys = key.split(' ');
			_.forEach(allKeys, function (color) {
				if ($scope.allColors.indexOf(color) >= 0){
					allColors.push(color);
				}
			});

			//
			if (colors.length == allColors.length){
				for (var i=0; i<allColors.length; ++i) {
					if (colors.indexOf(allColors[i]) == -1){
						return;
					}
				}
				save = true;
				$scope.currentValue[key] = val;
			}
		});

		if(save) {
			$http.post('/save-collors', {data: $scope.currentValue})
				.then(function (res) {
					if(res) console.log('data updated');
					else console.log('update failure ');
				}, function (err) {
					console.log('update failure ', err);
				});
		}
	};

	$scope.focus = function (e) {
		$scope.originalValue = e.target.value;
		$scope.originalTarget = e.target;
	};
	$scope.blur = function (e) {
		var targetValue = e.target.value;

		if($scope.originalValue != targetValue) {
			if($scope.autoSave) {
				$scope.currentItem.value = targetValue;
				$http.post('/update-translate', {data: $scope.currentItem})
					.then(function (res) {
						if(res) console.log('field updated');
						else console.log('update field failure');
					}, function (err) {
						console.log('update field failure ', err);
					});
			}

		}
	};

	$scope.saveChanges = function () {
		$scope.currentItem.value = $scope.originalTarget.value;
		$http.post('/update-translate', {data: $scope.currentItem})
			.then(function (res) {
				if(res) console.log('field updated');
				else console.log('update field failure');
			}, function (err) {
				console.log('update field failure ', err);
			});
	};
    //
	$scope.languages = function () {
		$http.get('/language-by-user/'+$rootScope.currentUser.id)
			.then(function(res) {
					var data = JSON.parse(res.data);
				console.log(data);
					if(!data) {
						$scope.message = true;
					} else {
						$scope.data = data;
					}
				}, function(err) {
					// console.log(err);
				});
	};
	$scope.languages();


});