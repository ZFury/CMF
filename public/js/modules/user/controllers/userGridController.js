(function() {
    define(['jquery'],function($) {
        var userGridController = function($scope, $rootScope, userService, $routeParams, $location) {
            var ORDER_ASC = "ASC";
            var ORDER_DESC = "DESC";
            $scope.params = {};
            $scope.params.limit = 2;
            $scope.params.page = 0;
            $scope.reverse = true;
            $scope.params.search = '';
            $scope.page = 0;
            /**
             * Get searched users function
             * */
            $scope.getUsers = function () {
//                if (typeof ($routeParams.page) === 'undefined') {
//                  $scope.page = 1;
//                    $location.path($scope.page);
//                } else {
//                    $scope.page = $routeParams.page;
//                }
                $scope.params.page = $scope.page;
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
                $scope.page = 0;
                $scope.getUsers();
            };

            /**
             * get data fo page
             */
            $scope.getPage = function(flag) {
                if (flag == 'next') {
                    $scope.page++;
//                    $location.path('/users/' + $scope.page);
                }
                if (flag == 'prev') {
                    $scope.page--;
//                    $location.path('/users/' + $scope.page);
                }
                $scope.getUsers();
            };

            /**
             * First search
             * */
            $scope.search = function () {
                $scope.page = 0;
                $scope.getUsers();
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
