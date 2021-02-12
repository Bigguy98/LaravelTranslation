angular.module('appRoutes', ['ui.router', 'ngStorage'])
	.run(function ($rootScope, $sessionStorage, $http) {
		$rootScope.currentUser = $sessionStorage.currentUser;
		if($rootScope.currentUser) {
			$http.post('/currentUser', { name: $rootScope.currentUser.name }).then(
				function (result) {
					var currentUser = JSON.parse(result.data);
					$rootScope.currentUser = currentUser;

					var obj = {};
					_.forEach(currentUser.permission, function (value) {
						var newObj = {};
						newObj[value.lang_title] = { view: value.view, edit: value.edit, id: value.language_id };
						_.assign(obj, newObj);
					});
					$rootScope.currentUser.newPermission = obj;
					$sessionStorage.currentUser = $rootScope.currentUser;
				}, function (bad) {
					console.log('bad ', bad);
				});
		}

	})
	.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
		$stateProvider
			.state('home', {
				url: '/',
				templateUrl: 'views/login.html',
				controller: 'LoginController',
				resolve: {

				}
			})
			.state('admin', {
				url: '/admin',
				templateUrl: 'views/admin.html',
				controller: 'MainController',
				resolve: {
					goto: function($q, $rootScope){
						var deferred = $q.defer();
						if($rootScope.currentUser.role_id == 1) deferred.resolve();
						else deferred.reject();

						return deferred.promise;
					}
				}
			})
			.state('user', {
				url: '/user',
				templateUrl: 'views/user.html',
				controller: 'UserController',
				resolve: {
					goto: function($q, $rootScope){
						var deferred = $q.defer();
						if($rootScope.currentUser) deferred.resolve();
						else deferred.reject();

						return deferred.promise;
					}
				}
			})
			.state('login', {
				url: '/login',
				templateUrl: 'views/login.html',
				controller: 'LoginController',
				resolve:  {
					goto: function($q, $rootScope){
						var deferred = $q.defer();

						if($rootScope.currentUser) deferred.reject();
						else deferred.resolve();

						return deferred.promise;
					}
				}
			})
			.state('logout', {
				url: '/logout',
				template: '<div>Logout</div>',
				controller: 'LogoutController',
				resolve: {
					goto: function($q, $rootScope){
						var deferred = $q.defer();
						if($rootScope.currentUser) deferred.resolve();
						else deferred.reject();

						return deferred.promise;
					}
				}

			});

		$urlRouterProvider.otherwise("/");
}]);