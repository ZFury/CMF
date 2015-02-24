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
        "jquery-nestedSortable": './libs/jquery.mjs.nestedSortable',
        redactor: './../redactor/redactor',
        'redactorContent': '../redactor/redactorContent',
        dashboard: './../module/dashboard/js/dashboard',
        comment: './../module/comment/js/comment',
        categories: '../module/categories/js/management',
        //BLUEIMP BEGIN
        "load-image": './libs/jQuery-File-Upload-master/load-image',
        "load-image-meta": './libs/jQuery-File-Upload-master/load-image-meta',
        "load-image-exif": './libs/jQuery-File-Upload-master/load-image-exif',
        "load-image-ios": './libs/jQuery-File-Upload-master/load-image-ios',
        "jquery.ui.widget": './libs/jQuery-File-Upload-master/vendor/jquery.ui.widget',
        "canvas-to-blob": './libs/jQuery-File-Upload-master/canvas-to-blob.min',
        "fileupload": './libs/jQuery-File-Upload-master/jquery.fileupload',
        "fileupload-process": './libs/jQuery-File-Upload-master/jquery.fileupload-process',
        "fileupload-image": './libs/jQuery-File-Upload-master/jquery.fileupload-image',
        "fileupload-audio": './libs/jQuery-File-Upload-master/jquery.fileupload-audio',
        "fileupload-video": './libs/jQuery-File-Upload-master/jquery.fileupload-video',
        "fileupload-validate": './libs/jQuery-File-Upload-master/jquery.fileupload-validate',
        "tmpl": './libs/jQuery-File-Upload-master/tmpl.min',
        "iframe-transport": './libs/jQuery-File-Upload-master/jquery.iframe-transport',
        "fileupload-ui": './libs/jQuery-File-Upload-master/jquery.fileupload-ui',
        //BLUEIMP END
        "image-categories": '../module/categories/js/image',
        "image": '../module/test/js/image',
        "audio": '../module/test/js/audio',
        "video": '../module/test/js/video',
        'conversion': '../module/media/js/conversion',
        'fury.form': './fury.form',
        'fury.notify': './fury.notify',
        'fury.ajax': './fury.ajax',
        'mail-inputs': '../module/install/js/mail-inputs',
        goog: './libs/requirejs-plugins/src/goog',
        async: './libs/requirejs-plugins/src/async',
        propertyParser: './libs/requirejs-plugins/src/propertyParser',
        chart: '../module/dashboard/js/chart',
        "statistic-table": '../module/dashboard/js/statistic-table'
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
    },
    enforceDefine: true
});
