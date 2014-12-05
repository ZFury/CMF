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
            $scope.page = 0;
            /**
             * Get searched users function
             * */
            $scope.getUsers = function () {
                if (typeof ($routeParams.params) === 'undefined') {
                    var page = parseInt($scope.page) + 1;
                    $location.path('/params/page=' + page);
                } else {
                    parseUrlParams($routeParams.params);
                }
                $scope.params.page = $scope.page - 1;
                userService.getUsers($scope.params, function(response) {
                    $scope.usersGrid = [];
                    angular.forEach(response.data,function(item) {
                        $scope.usersGrid.push(item);
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
                setUrl();
                $scope.getUsers();
            };

            /**
             * get data fo page
             */
            $scope.getPage = function(flag) {
                if (flag == 'next') {
                    $scope.page++;
                    setUrl();
                }
                if (flag == 'prev') {
                    $scope.page--;
                    setUrl();
                }
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
                $scope.params.limit = limit;
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
                    $scope.params.limit = params.limit;
                }
                if (typeof (params.search) !== 'undefined') {
                    $scope.params.searchString = params.search;
                }
                if (typeof (params.field) !== 'undefined') {
                    $scope.params.field = params.field;
                }
                if (typeof (params.order) !== 'undefined') {
                    $scope.params.reverse = params.order;
                }
                return params;
            };

            setUrl = function () {
                var params;
                if (typeof ($scope.page) !== 'undefined') {
                    params = 'page=' + $scope.page;
                }
                if (typeof ($scope.params.limit) !== 'undefined') {
                    params += '&limit=' + $scope.params.limit;
                }
                if (typeof ($scope.params.searchString) !== 'undefined' && $scope.params.searchString !== '') {
                    params += '&search=' + $scope.params.searchString;
                }
                if (typeof ($scope.params.field) !== 'undefined') {
                    params += '&field=' + $scope.params.field;
                }
                if (typeof ($scope.params.reverse) !== 'undefined') {
                    params += '&order=' + $scope.params.reverse;
                }
                $location.path('/params/' + params);
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
