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
        //"redactor-fullscreen": './../redactor/plugins/fullscreen'
        dashboard: './../module/dashboard/js/dashboard',
        comment: '../module/comment/js/management',
        categories: '../module/categories/js/management',
        test: '../module/test/js/comment',
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
        "image-categories": './modules/categories/image',
        "image": './modules/test/image',
        "audio": './modules/test/audio',
        "video": './modules/test/video',
        'redactorContent': '../redactor/redactorContent'
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
        },
        frontend: {
            deps: ['bootstrap']
        },
        backend: {
            deps: ['angularRoute', 'angularSanitize']
        }
        //'redactor-fullscreen': {
        //    deps: ['jquery', 'redactor'],
        //    exports: '$.fn.redactor'
        //}
    },
    enforceDefine: true
});
