(function() {
    define(['jquery'],function($) {
        var editTestController = function($scope, $rootScope, testService, $location) {
            $scope.test = {};
            $scope.testError = {};
            $scope.page = 1;
            $scope.reverse = true;

            /**
             * Edit test
             * */
            $scope.editTest = function () {
                testService.editTest($scope.test.email, $scope.test.name, $scope.test.id, function(response) {
                    $scope.testError = response.errors;
                    if ($scope.testError.length < 1) {
                        window.location = testService.apiUrl + "management/angular";
                    }
                });
            };

            /**
             * Get test
             * */
            $scope.getTest = function () {
                var urlParams = (window.location.pathname).split('/');
                var num = urlParams.length - 1;
                $scope.test.id = urlParams[num];
                testService.getTest($scope.test.id, function(response) {
                    $scope.test.email = response.data.email;
                    $scope.test.name = response.data.name;
                });
            };

            function init() {
                $scope.getTest();
            }

            init();
        };

        return ['$scope', '$rootScope', 'testService', '$location', editTestController];
    });
}());
