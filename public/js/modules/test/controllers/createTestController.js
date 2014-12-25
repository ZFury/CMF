(function() {
    define(['jquery'],function($) {
        var createTestController = function($scope, $rootScope, testService, $location) {
            $scope.newTest = {};

            /**
             * Create test
             * */
            $scope.createTest = function () {
                testService.createTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    $scope.testError = response.errors;
                    if ($scope.testError.length < 1) {
                        window.location = testService.apiUrl + "management/angular";
                    }
                });
            };
        };

        return ['$scope', '$rootScope', 'testService', '$location', createTestController];
    });
}());
