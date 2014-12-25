(function() {
    var dependencies = [
        'angular',
        'angularSanitize',
        'angularRoute',
        'modules/test/services/testService',
        'modules/test/controllers/testController',
        'modules/test/controllers/editTestController',
        'modules/test/controllers/createTestController'
    ];

    define(dependencies, function(
        angular,
        angularSanitize,
        angularRoute,
        testService,
        testController,
        editTestController,
        createTestController
        ) {

        var app = angular.module('zfstarter', ['ngSanitize']);
        app
            .factory('testService', testService)
            .controller('testController', testController)
            .controller('createTestController', createTestController)
            .controller('editTestController', editTestController);

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