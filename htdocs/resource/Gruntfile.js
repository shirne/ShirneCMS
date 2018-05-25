module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        commons:['js/model/common.js', 'js/model/template.js', 'js/model/dialog.js', 'js/model/jquery.tag.js', 'js/model/datetime.init.js'],
        jshint: {
            files: ['js/*.js', 'js/model/*.js'],
            options: {
                globals: {
                    jQuery: true
                }
            }
        },
        concat:{
            options: {
                separator: ';'
            },
            front:{src: ['<%=commons%>', 'js/front.js'], dest: 'dest/js/init.js'},
            backend:{src: ['<%=commons%>', 'js/backend.js'], dest: 'dest/admin/js/app.js'}
        },
        uglify: {
            front: {
                options: {
                    banner: '/*! front.js <%= grunt.template.today("yyyy-mm-dd") %> */\n'
                },
                src: '<%= concat.front.dest%>',
                dest: 'dest/js/init.min.js'
            },
            backend: {
                options: {
                    banner: '/*! backend.js <%= grunt.template.today("yyyy-mm-dd") %> */\n'
                },
                src: '<%= concat.backend.dest%>',
                dest: 'dest/admin/js/app.min.js'
            }
        },
        sass: {
            options: {
                style: 'compressed'
            },
            all: {
                files: [{
                    src: 'scss/style.scss',
                    dest: 'dest/css/style.css'
                },{
                    src: 'scss/admin.scss',
                    dest: 'dest/admin/css/common.css'
                }]
            }
        },
        copy:{
            all:{
                files:[{
                    expand:true,
                    cwd:'dest',
                    src:['**'],
                    dest:'../public/static/'
                }]
            }
        },
        watch: {
            styles: {
                files: ['scss/*.scss'],
                tasks: ['sass:all','copy:all'],
                options: {
                    spawn: false
                }
            },
            scripts:{
                files: ['js/*.js','js/model/*.js'],
                tasks: ['concat:front','concat:backend','uglify:front','uglify:backend','copy:all'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['sass:all','concat:front','concat:backend','uglify:front','uglify:backend','copy:all']);

};