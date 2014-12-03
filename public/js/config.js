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
        underscore: '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.1/underscore-min',
        backbone: '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.0.0/backbone-min',
        "jquery-ui": './jquery-ui',
        "jquery-nestedSortable": './jquery.mjs.nestedSortable',
        categories: '../module/categories/js/management',
        "delete-confirmation": '../module/categories/js/delete_confirmation',
        dashboard:'./dashboard'
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
        backbone: {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        underscore: {
            exports: '_'
        },
        "jquery-ui": {
            deps: ['jquery'],
            exports: '$.ui'
        },
        "jquery-nestedSortable": {
            deps: ['jquery', 'jquery-ui'],
            exports: '$.fn.nestedSortable'
        }
    },
    enforceDefine: true
});
