/* global module:false */
module.exports = function (grunt) {
    var fs = require('fs');
    var mozjpeg = require('imagemin-mozjpeg');
    var sassRename = function (dest, src) {
        var folder = src.substring(0, src.lastIndexOf('/'));
        var filename = src.substring(src.lastIndexOf('/'), src.length);
        filename = filename.substring(0, filename.lastIndexOf('.'));
        return dest + '/' + folder + filename + '.css';
    };
    var path = require('path');
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        sass: {
            options: {
                sourceMap: true
            },
            below: {
                files: [{
                    expand: true,
                    cwd: 'resources/sass/below',
                    src: ['**/*.scss'],
                    dest: 'resources/css/below',
                    rename: sassRename
                }],
                options: {
                    sourcemap: true,
                    style: 'nested'
                }
            }
        },


        concat_sourcemap: {
            options: {
                sourceRoot: '/'
            },
            below: {
                src: ['resources/css/below/*.css'],
                dest: 'public/css/tollwerkstatt-below.css'
            }
        },

        autoprefixer: {
            options: {
                browsers: ['last 3 versions', 'ie 8'],
                map: true
            },
            below: {
                src: ['public/css/tollwerkstatt-below.css']
            },
        },


        cssmin: {

            below: {
                files: {
                    'public/css/tollwerkstatt-below.min.css': ['public/css/tollwerkstatt-below.css']
                }
            },
        },


        clean: {
            below: ['public/css/tollwerkstatt-below.css', 'public/css/tollwerkstatt-below.min.css'],
        },


        watch: {
            // Watch Sass resource changes
            sassBelow: {
                files: ['resources/sass/below/**/*.scss', 'resources/sass/common/**/*.scss'],
                tasks: ['sass:below']
            },

            // Watch changing CSS resources
            cssBelow: {
                files: ['resources/css/below/*.css'],
                tasks: ['clean:below', 'concat_sourcemap:below', 'autoprefixer:below', 'cssmin:below'],
                options: {
                    spawn: true
                }
            },

            grunt: {
                files: ['Gruntfile.js'],
                options: {
                    reload: true
                }
            }
        }
    });

    // Default task.
    grunt.registerTask('default', ['sass', 'css']);
    grunt.registerTask('css', ['clean:below',
        'concat_sourcemap:below','autoprefixer','cssmin']);
};