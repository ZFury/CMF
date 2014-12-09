(function() {
    define(['jquery'],function($) {
        var testController = function($scope, $rootScope, testService) {
            $scope.newTest = {};
            $scope.testError = {};
            /**
             * Create test
             * */
            $scope.createTest = function () {
                testService.createTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    if (!response.result.success) {
                        $scope.testError = response.validationMessages;
                    } else {
                        alert('Good job');
                    }
                });
            };
        };

        return ['$scope', '$rootScope', 'testService', testController];
    });
}());
