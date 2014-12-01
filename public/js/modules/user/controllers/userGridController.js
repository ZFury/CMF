(function() {
    define(['jquery'],function($) {
        var userGridController = function($scope, $rootScope, userService, $routeParams, $location) {
            $scope.params = {};
            $scope.params.limit = 2;
            $scope.params.page = 0;
            $scope.params.predicate = 'id';
            $scope.params.reverse = true;
            $scope.params.search = '';
            $scope.page = 0;
            /**
             * Get searched users function
             * */
            $scope.getUsers = function () {
//                console.log($routeParams);
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
                    console.log(response.count/$scope.params.limit);
                });
            };

            /**
             * Set orders params
             * */
            $scope.setOrder = function (field) {
                $scope.params.predicate = field;
                $scope.params.reverse = !$scope.params.reverse;
                $scope.getSearchedUsers();
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
