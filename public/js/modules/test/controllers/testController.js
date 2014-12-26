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
            $scope.page = 1;
            $scope.currentPage = 1;
            $scope.reverse = true;
            $scope.defaultOrder = 'email';
            $scope.defaultFilter = 'email';

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
            $scope.getTests = function (num) {
                $scope.currentPage = $scope.page;
                if (typeof(num) !== 'undefined') {
                    $scope.page = num + 1;
                }
                if (typeof($scope.orderField) === 'undefined') {
                    $scope.orderField = $scope.defaultOrder;
                }
                if (typeof($scope.filterField) === 'undefined') {
                    $scope.filterField = $scope.defaultFilter;
                }
                testService.getTests($scope.page, $scope.orderField, $scope.order, $scope.filterField, $scope.searchString, function(response) {
                    $scope.testGrid = [];
                    angular.forEach(response.data,function(item) {
                        $scope.testGrid.push(item);
                    });
                    $scope.allowedFilters = response.allowedFilters;
                    $scope.totalPages = response.totalPages;
                    $scope.allowedOrders = response.allowedOrders;
                    $scope.defaultLimit = response.defaultLimit;
                    $scope.urlPrev = response.urlPrev;
                    $scope.urlNext = response.urlNext;
                    $scope.gridPages = Math.ceil(response.count/$scope.params.limit);
                });
            };

            $scope.getTimes=function(n){
                return new Array(n);
            };

            /**
             * Set orders params
             * */
            $scope.setOrder = function (order) {
                $scope.orderField = order;
                $scope.reverse = !$scope.reverse;
                if ($scope.reverse) {
                    $scope.order = ORDER_ASC;
                } else {
                    $scope.order = ORDER_DESC;
                }
                $scope.page = 1;
                $scope.getTests();
            };

            /**
             * get prev page
             */
            $scope.prev = function() {
                $scope.page--;
                $scope.getTests();
            };

            /**
             * get next page
             */
            $scope.next = function() {
                $scope.page++;
                $scope.getTests();
            };

            /**
             * Search test
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
