(function() {
    define(['jquery'],function($) {
        var userGridController = function($scope, $rootScope, userService, $routeParams, $location) {
            var ORDER_ASC = "asc";
            var ORDER_DESC = "desc";
            $scope.params = {};
            $scope.params.limit = 3;
            $scope.params.page = 0;
            $scope.reverse = true;
            $scope.params.searchString = '';
            $scope.params.searchField = 'email';
            $scope.page = 1;

            $scope.usersGrid = [];
            $scope.limit = 5;
            $scope.page = 1;
            $scope.currentPage = 1;
            $scope.reverse = true;
            $scope.defaultOrder = 'user.email';
            $scope.defaultFilter = 'user.email';
            /**
             * Get searched users function
             * */
            $scope.getUsers = function (num) {
                if (typeof ($routeParams.params) === 'undefined') {
                    $location.path('/params/page=1');
                } else {
                    parseUrlParams($routeParams.params);
                }

                if (typeof($scope.orderField) === 'undefined') {
                    $scope.orderField = $scope.defaultOrder;
                    $scope.order = 'asc';
                }
                if (typeof($scope.filterField) === 'undefined') {
                    $scope.filterField = $scope.defaultFilter;
                }
                userService.getUsers($scope.page, $scope.orderField, $scope.order, $scope.filterField, $scope.searchString, $scope.limit, function(response) {
                    $scope.testGrid = [];
                    angular.forEach(response.data,function(item) {
                        $scope.usersGrid.push(item);
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
                setUrl();
                $scope.getUsers();
            };

            /**
             * change page
             */
            $scope.changePage = function(num) {
                $scope.page = num + 1;
                setUrl();
                $scope.getUsers();
            };

            /**
             * get prev page
             */
            $scope.prev = function() {
                $scope.page--;
                setUrl();
                $scope.getUsers();
            };

            /**
             * get next page
             */
            $scope.next = function() {
                $scope.page++;
                setUrl();
                $scope.getUsers();
            };

            /**
             * Search users
             * */
            $scope.search = function () {
                $scope.page = 1;
                setUrl();
                $scope.getUsers();
            };

            /**
             * Change limit
             * */
            $scope.changeLimit = function (limit) {
                $scope.page = 1;
                $scope.limit = limit;
                setUrl();
                $scope.getUsers();
            };

            parseUrlParams = function (urlParams) {
                var params = [];
                tmp = (urlParams).split('&');
                for(var i=0; i < tmp.length; i++) {
                    tmp2 = tmp[i].split('=');
                    params[tmp2[0]] = tmp2[1];
                }
                if (typeof (params.page) !== 'undefined') {
                    $scope.page = params.page;
                }
                if (typeof (params.limit) !== 'undefined') {
                    $scope.limit = params.limit;
                }
                if (typeof (params.filter_string) !== 'undefined') {
                    $scope.searchString = params.filter_string;
                }
                if (typeof (params.filter_field) !== 'undefined') {
                    $scope.filterField = params.filter_field;
                }
                if (typeof (params.order_field) !== 'undefined') {
                    $scope.orderField = params.order_field;
                }
                if (typeof (params.order) !== 'undefined') {
                    $scope.order = params.order;
                }
                return params;
            };

            setUrl = function () {
                var params;
                if (typeof ($scope.page) !== 'undefined') {
                    params = 'page=' + $scope.page;
                }
                if (typeof ($scope.limit) !== 'undefined') {
                    params += '&limit=' + $scope.limit;
                }
                if (typeof ($scope.searchString) !== 'undefined' && $scope.searchString !== '') {
                    params += '&filter_string=' + $scope.searchString;
                }
                if (typeof ($scope.filterField) !== 'undefined' && $scope.filterField !== '') {
                    params += '&filter_field=' + $scope.filterField;
                }
                if (typeof ($scope.order) !== 'undefined') {
                    params += '&order=' + $scope.order;
                }
                if (typeof ($scope.orderField) !== 'undefined') {
                    params += '&order_field=' + $scope.orderField;
                }
                $location.path('/params/' + params);
            };

            $scope.getTimes=function(n){
                return new Array(n);
            };

            init();

            /**
             * Initial function to load users and show grid
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            function init() {
                $scope.getUsers();
            }

        };

        return ['$scope', '$rootScope', 'userService', '$routeParams', '$location', userGridController];
    });
}());
