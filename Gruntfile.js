module.exports = function(grunt) {
    'use strict';
    
    // Force use of Unix newlines
    grunt.util.linefeed = '\n';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            options: {
                preserveComments: 'some'
            },
            admin: {
                files: {
                    'var/grunt/plugins.min.js':   ['public/js/src/plugins.js'],
                    'var/grunt/mdeditor.min.js':  ['public/js/src/admin/mdeditor.js'],
                    'var/grunt/admin.min.js':     ['public/js/src/admin/app.js'],
                    'var/grunt/bootbox.min.js':   ['bower_components/bootbox/bootbox.js'],
                    'var/grunt/bootstrap-datepicker.min.js': ['bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js'],
                    'var/grunt/bootstrap-datepicker.ru.min.js': ['bower_components/bootstrap-datepicker/js/locales/bootstrap-datepicker.ru.js']
                }
            }
        },

        concat: {
            options: {
                stripBanners: false
            },
            admin: {
                files: {
                    'public/js/admin.min.js': [
                        'bower_components/jquery/dist/jquery.min.js',
                        'var/grunt/plugins.min.js',
                        'bower_components/bootstrap/dist/js/bootstrap.min.js',
                        'var/grunt/bootbox.min.js',
                        'var/grunt/bootstrap-datepicker.min.js',
                        'var/grunt/bootstrap-datepicker.ru.min.js',
                        'var/grunt/admin.min.js',
                        'var/grunt/mdeditor.min.js'
                    ]
                }
            },
            front: {
                files: {
                    'public/js/app.min.js': [
                        'bower_components/jquery/dist/jquery.min.js',
                        'bower_components/bootstrap/dist/js/bootstrap.min.js'
                    ]
                }
            }
        },

        cssmin: {
            options: {
                keepSpecialComments: '*',
                noAdvanced: true, // turn advanced optimizations off until the issue is fixed in clean-css
                report: 'min',
                selectorsMergeMode: 'ie8'
            },
            admin: {
                files: {
                    'public/css/admin.min.css': [
                        'bower_components/bootstrap-datepicker/css/datepicker3.css',
                        'public/css/src/admin/app.css'
                    ],
                    'public/css/admin.login.min.css': ['public/css/src/admin/login.css']
                }
            },
            front: {
                files: {
                    'public/css/app.min.css': [
                        'public/css/src/app.css'
                    ]                    
                }
            }
        },

        copy: {
            bootstrap: {
                files: [
                    {
                        // jquery
                        src:  'bower_components/jquery/dist/jquery.min.js',
                        dest: 'public/js/jquery.min.js'
                    },
                    {
                        // bootstrap css
                        src:  'bower_components/bootstrap/dist/css/bootstrap.min.css', 
                        dest: 'public/css/bootstrap.min.css'
                    },
                    {
                        // bootstrap js
                        src:  'bower_components/bootstrap/dist/js/bootstrap.min.js',
                        dest: 'public/js/bootstrap.min.js'
                    },
                    {
                        // bootstrap fonts
                        expand: true, 
                        flatten: true, 
                        src: 'bower_components/bootstrap/fonts/*', 
                        dest: 'public/fonts/', 
                        filter: 'isFile'
                    }
                ]
            }
        },

        // remove temporary created files
        clean: {
            admin: ['var/grunt']
        }

    });

    // load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    grunt.registerTask('admin', [
        'copy:bootstrap',
        'uglify:admin', 
        'concat:admin', 
        'cssmin:admin', 
        'clean:admin'
    ]);
    grunt.registerTask('front', ['copy:bootstrap', 'concat:front', 'cssmin:front']);
};