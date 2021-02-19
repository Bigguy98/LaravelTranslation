angular.module('appRoutes', ['ui.router', 'ngStorage'])
	.run(function ($rootScope, $sessionStorage, $http) {
		$rootScope.currentUser = $sessionStorage.currentUser;
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
			.state('backup', {
				url: '/backup',
				templateUrl: 'views/backup.html',
				controller: 'BackupController',
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