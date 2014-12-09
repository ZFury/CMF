/**
 * Created by alexander on 11/25/14.
 */
/*global define,require*/
require.config({
    // why not simple "js"? Because IE eating our minds!
    baseUrl: '/js',
    // if you need disable JS cache
    urlArgs: "bust=" + (new Date()).getTime(),
    paths: {
        angular: './libs/angular',
        'jquery': './libs/jquery.min',
        'bootstrap': './libs/bootstrap.min',
        'angularSanitize': './libs/angular-sanitize',
        'angularRoute': './libs/angular-route',
        respond: './libs/respond.min',
        html5shiv: './libs/html5shiv',
        "jquery-ui": './libs/jquery-ui',
        "jquery-nestedSortable": './libs/jquery.mjs.nestedSortable.js',
        redactor: './../redactor/redactor',
        //"redactor-fullscreen": './../redactor/plugins/fullscreen'
        dashboard:'./../module/dashboard/js/dashboard',
        categories: '../module/categories/js/management'
    },
    shim: {
        angular: {
            deps: [],
            exports: 'angular'
        },
        'angularSanitize': {
            deps: ['angular'],
            exports: 'angular'
        },
        'angularRoute': {
            deps: ['angular'],
            exports: 'angular'
        },
        'jquery': {
            exports: '$'
        },
        bootstrap: {
            deps: ['jquery'],
            exports: '$.fn.popover'
        },
        "jquery-ui": {
            deps: ['jquery'],
            exports: '$.ui'
        },
        "jquery-nestedSortable": {
            deps: ['jquery', 'jquery-ui'],
            exports: '$.fn.nestedSortable'
        },
        redactor: {
            deps: ['jquery'],
            exports: '$.fn.redactor'
        }
        //'redactor-fullscreen': {
        //    deps: ['jquery', 'redactor'],
        //    exports: '$.fn.redactor'
        //}
    },
    enforceDefine: true
});
