(function() {
    define(['jquery'],function($) {
        var testController = function($scope, $rootScope, testService, $location) {
            $scope.newTest = {};
            $scope.testError = {};
            /**
             * Create test
             * */
            $scope.createTest = function () {
                testService.createTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    if (typeof(response.errors) !== 'undefined') {
                        $scope.testError = response.errors;
                        if ($scope.testError.length < 1) {
                            window.location = "/test/management";
                            //$location.path('/test/management');
                        }
                    }
                });
            };

            /**
             * Create test
             * */
            $scope.editTest = function () {
                testService.editTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    if (typeof(response.errors) !== 'undefined') {
                        $scope.testError = response.errors;
                    }
                });
            };
        };

        return ['$scope', '$rootScope', 'testService', '$location', testController];
    });
}());
