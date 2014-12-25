(function() {
    define(['jquery'],function($) {
        var testController = function($scope, $rootScope, testService, $location) {
            var ORDER_ASC = "asc";
            var ORDER_DESC = "desc";
            $scope.newTest = {};
            $scope.test = {};
            $scope.testError = {};
            $scope.params = {};
            $scope.params.limit = 5;
            $scope.params.page = 0;
            $scope.params.searchString = '';
            $scope.params.searchField = 'email';
            $scope.page = 1;
            $scope.reverse = true;

            /**
             * Create test
             * */
            $scope.createTest = function () {
                testService.createTest($scope.newTest.email, $scope.newTest.name, function(response) {
                    if (typeof(response.errors) !== 'undefined') {
                        $scope.testError = response.errors;
                        if ($scope.testError.length < 1) {
                            window.location = testService.apiUrl + "management/angular";
                        }
                    }
                });
            };

            /**
             * Get test data
             * */
            $scope.getTests = function () {
                $scope.params.page = $scope.page - 1;
                testService.getTests($scope.params, function(response) {
                    $scope.testGrid = [];
                    angular.forEach(response.data,function(item) {
                        $scope.testGrid.push(item);
                    });
                    $scope.gridPages = Math.ceil(response.count/$scope.params.limit);
                });
            };

            /**
             * Set orders params
             * */
            $scope.setOrder = function (field) {
                $scope.params.field = field;
                $scope.reverse = !$scope.reverse;
                if ($scope.reverse) {
                    $scope.params.reverse = ORDER_ASC;
                } else {
                    $scope.params.reverse = ORDER_DESC;
                }
                $scope.page = 1;
                $scope.getTests();
            };

            /**
             * get data fo page
             */
            $scope.getPage = function(flag) {
                if (flag == 'next') {
                    $scope.page++;
                }
                if (flag == 'prev') {
                    $scope.page--;
                }
                $scope.getTests();
            };

            /**
             * Search users
             * */
            $scope.search = function () {
                $scope.page = 1;
                $scope.getTests();
            };

            /**
             * Change limit
             * */
            $scope.changeLimit = function (limit) {
                $scope.page = 1;
                $scope.params.limit = limit;
                $scope.getTests();
            };

            function init() {
                $scope.getTests();
            }

            init();
        };

        return ['$scope', '$rootScope', 'testService', '$location', testController];
    });
}());
