(function() {
    define(['jquery'],function($) {
        var testController = function($scope, $rootScope, testService, $location) {
            $scope.newTest = {};
            $scope.test = {};
            $scope.testError = {};
            /**
             * Create test
             * */
            $scope.createTest = function () {
                testService.createTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    if (typeof(response.errors) !== 'undefined') {
                        $scope.testError = response.errors;
                        if ($scope.testError.length < 1) {
                            window.location = testService.apiUrl + "management";
                        }
                    }
                });
            };

            /**
             * Create test
             * */
            $scope.editTest = function () {
                testService.editTest($scope.test.email, $scope.test.name, function(response) {
                    if (typeof(response.errors) !== 'undefined') {
                        $scope.testError = response.errors;
                    }
                });
            };

            /**
             * Get test data
             * */
            $scope.getTest = function () {
                testService.getTest(function(response) {
                    $scope.testId = response.id;
                    console.log(response.id);
                    //if (typeof(response.errors) !== 'undefined') {
                    //    $scope.testError = response.errors;
                    //}
                });
            };

            function init() {
                $scope.getTest();
            }

            init();
        };

        return ['$scope', '$rootScope', 'testService', '$location', testController];
    });
}());
