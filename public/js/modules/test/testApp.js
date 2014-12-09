(function() {
    var dependencies = [
        'angular',
        'angularSanitize',
        'modules/test/services/testService',
        'modules/test/controllers/testController'
    ];

    define(dependencies, function(
        angular,
        angularSanitize,
        testService,
        testController
        ) {

        var app = angular.module('zfstarter', ['ngSanitize']);
        app
            .factory('testService', testService)
            .controller('testController', testController);

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