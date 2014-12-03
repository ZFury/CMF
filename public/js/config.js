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
        "jquery-ui": './jquery-ui',
        "jquery-nestedSortable": './jquery.mjs.nestedSortable',
        categories: '../module/categories/js/management',
        dashboard: './../module/dashboard/js/dashboard'
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
        }
    },
    enforceDefine: true
});

require(['bootstrap']);
