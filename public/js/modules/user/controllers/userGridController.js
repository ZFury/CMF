(function() {
    define(['jquery'],function($) {
        var userGridController = function($scope, $rootScope, userService, $routeParams, $location) {
            $scope.params = {};
            $scope.params.limit = 2;
            $scope.params.page = 0;
            $scope.params.predicate = 'id';
            $scope.params.reverse = true;
            $scope.params.search = '';
            /**
             * Get searched users function
             * */
            $scope.getUsers = function () {
                if (typeof ($routeParams.page) === 'undefined') {
                    $scope.page = 1;
                    $location.path(this.apiUrl + 'management/users-grid/' + $scope.page);
                } else {
                    $scope.page = $routeParams.page;
                }
                $scope.params.page = $scope.page - 1;
                userService.getUsers($scope.params, function(response) {
                    $scope.usersGrid = [];
                    angular.forEach(response.users,function(item) {
                        $scope.usersGrid.push(item);
                    });
                    $scope.gridPages = Math.ceil(response.count/$scope.params.pageSize);
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
                    $scope.page = parseInt($scope.page) + 1;
//                    $location.path('/users/' + $scope.page);
                }
                if (flag == 'prev') {
                    $scope.page = parseInt($scope.page) - 1;
//                    $location.path('/users/' + $scope.page);
                }
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
