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

        var app = angular.module('zfstarter', ['ngRoute']);
        app
            .factory('userService', userService)
            .controller('userGridController', userGridController);
        app.config(['$routeProvider', function($routeProvider, $locationProvider) {
            $routeProvider
                .when('/params/:params?', {
                    templateUrl: 'templates/user/grid.html',
                    controller: userGridController
                })
                .otherwise({
                    redirectTo: '/'
                });
//                $locationProvider.html5Mode(true);
        }]);

        app.controller('Home', ['$scope', function($scope){
            $scope.message = 'Welcome to my project';
        }]);

        angular.element(document).ready(function () {
            var promise = new Promise(function(resolve, reject) {
                var ngObject = angular.bootstrap(document, ['zfstarter']);

                if (ngObject) {
                    resolve("Stuff worked!");
                } else {
                    reject(Error("It broke"));
                }
            });
        });
    });
}());