(function() {
    define(function() {
        var userService = function($http) {
            var usersFactory = {};
            usersFactory.apiUrl = '/user/';
            usersFactory.templatesUrl = '/templates/';
            $http.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

            /**
             * Get searched users
             * @param allParams
             * @param callback
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            usersFactory.getUsers = function(allParams, /*function*/ callback) {
                var params = {
                    url: this.apiUrl + 'management/grid',
                    method: "POST",
                    data: $.param({data: allParams}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };
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
                    alert('Произошла ошибка! Перезагрузите страницу и повторите.');
                }
            }

            return usersFactory;
        };
        return ['$http', userService];
    });
}());