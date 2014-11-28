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
        bootstrap: './bootstrap',
        jquery: './jquery.min',
        respond: './respond.min',
        html5shiv: './html5shiv',
        underscore: '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.1/underscore-min',
        backbone: '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.0.0/backbone-min',
        "jquery-ui": './jquery-ui',
        "jquery-nestedSortable": './jquery.mjs.nestedSortable',
        categories: './categories',
        "delete-confirmation": './delete_confirmation'
    },
    shim: {
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

require(['bootstrap']);