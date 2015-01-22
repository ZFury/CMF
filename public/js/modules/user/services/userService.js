(function() {
    define(function() {
        var userService = function($http) {
            var usersFactory = {};
            usersFactory.apiUrl = '/user/';
            usersFactory.templatesUrl = '/templates/';
            $http.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

            /**
             * Get searched users
             * @param page
             * @param orderField
             * @param order
             * @param filterField
             * @param searchString
             * @param limit
             * @param callback
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            usersFactory.getUsers = function(page, orderField, order, filterField, searchString, limit, /*function*/ callback) {
                var ord = 'order-' + orderField;
                var filt = 'filter-' + filterField;
                var params = {
                    url: this.apiUrl + 'management/grid',
                    method: "GET",
                    params: {
                        page: page,
                        limit: limit
                    },
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };
                params.params[ord] = order;
                params.params[filt] = searchString;
                ajaxRequest(params, callback);
            };


            /**
             * Base function to send ajax request
             * @param params
             * @param callback
             * @param overlay - is overlay show
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             * */
            function ajaxRequest (/*object*/ params, /*function*/ callback, /*boolean*/ overlay) {
                overlay = ('undefined' == typeof(overlay)) ? false : overlay;
                if (!overlay) {
                    $('.spinner').show();
                }
                return $http(params).error(function(e, status) {
                    if (overlay) {
                        $('.spinner').hide();
                    }
                    ajaxError(status);
                }).then(function(result) {
                    if (!overlay) {
                        $('.spinner').hide();
                    }
                    return callback(result.data);
                });
            }

            function ajaxError (status) {
                if (status !== 0) {
                    alert('Reload page.');
                }
            }

            return usersFactory;
        };
        return ['$http', userService];
    });
}());