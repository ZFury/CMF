(function() {
    define(function() {
        var testService = function($http) {
            var testFactory = {};
            testFactory.apiUrl = '/test/';
            testFactory.templatesUrl = '/templates/';
            $http.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

            /**
             * Create
             * @param email
             * @param name
             * @param callback
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            testFactory.createTest = function(email, name, /*function*/ callback) {
                var params = {
                    url: this.apiUrl + 'management/create',
                    method: "POST",
                    data: $.param({email: email, name: name}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };
                ajaxRequest(params, callback);
            };

            /**
             * Edit
             * @param email
             * @param name
             * @param callback
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            testFactory.editTest = function(email, name, /*function*/ callback) {
                var params = {
                    url: this.apiUrl + 'management/edit',
                    method: "POST",
                    data: $.param({email: email, name: name}),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                };
                ajaxRequest(params, callback);
            };

            /**
             * Get test
             * @param callback
             *
             * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
             */
            testFactory.getTest = function(/*function*/ callback) {
                var params = {
                    url: this.apiUrl + 'management/edit-with-angular',
                    method: "GET",
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
                    alert('Reload page.');
                }
            }

            return testFactory;
        };
        return ['$http', testService];
    });
}());