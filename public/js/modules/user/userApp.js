(function() {

    var dependencies = [
        'angular',
        'angularSanitize',
        'angularRoute',
        'modules/user/services/userService',
        'modules/user/controllers/userGridController'
    ];

    define(dependencies, function(
        angular,
        angularSanitize,
        angularRoute,
        userService,
        userGridController
        ) {

        var app = angular.module('myzf', ['ngRoute']);
        app
            .factory('userService', userService)
            .controller('userGridController', userGridController);
        app.filter('startFrom', function () {
            return function (input, start) {
                if (input === undefined || input === null || input.length === 0) {
                    return [];
                }
                start = +start;
                return input.slice(start);
            };
        });
//        app.config(['$routeProvider', function($routeProvider, $locationProvider) {
//                $routeProvider
//                    .when('/users/:page', {
//                        templateUrl: 'users.html',
//                        controller: userGridController
//                    })
//                    .when('/home', {
//                        templateUrl: 'home.html',
//                        controller: 'Home'
//                    })
//                    .otherwise({ redirectTo: '/home' });
////                $locationProvider.html5Mode(true);
//            }]);
//
//        app.controller('Home', ['$scope', function($scope){
//            $scope.message = 'Welcome to my project';
//        }]);

        angular.element(document).ready(function () {
            var promise = new Promise(function(resolve, reject) {
                var ngObject = angular.bootstrap(document, ['myzf']);

                if (ngObject) {
                    resolve("Stuff worked!");
                } else {
                    reject(Error("It broke"));
                }
            });
        });
    });
}());