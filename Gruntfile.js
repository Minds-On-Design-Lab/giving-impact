module.exports = function(grunt) {

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

      /**
       * Sass
       *
       * Used to compile SASS into CSS.
       * For consistency MODL will use a master scss file under styles/scss/master.scss
       */

      sass: {
        options: {
          includePaths: ['styles/bower_components/foundation/scss']
        },
        dist: {
          options: {
            outputStyle: 'compressed'
          },
          files: {
            'www/assets/stylesheets/master.css': 'styles/scss/master.scss',
            'www/assets/stylesheets/hosted-donation.css': 'styles/scss/hosted-donation.scss'
          }
        }
      },

      /**
       * JS Hint
       *
       * Used to review MODL JS files for issues.
       * For consistency MODL will store custom JS in the styles/js/modl/ folder.
       */

      jshint: {
        beforeconcat: ['styles/js/modl/*.js']
      },

      /**
       * Uglify
       *
       * Uglify is used to compress JS files by removing whitespace, shortening variable names,
       * and various other opt  izations.  It can be used on a single concatenated file or multiple files
       *
       * https://github.com/gruntjs/grunt-contrib-uglify
       */

      uglify: {
        build: {
          files: {
            'styles/js/modl/app_min.js': 'www/assets/javascripts/app.js',
            'styles/js/vendor/modernizr_min.js': 'styles/bower_components/modernizr/modernizr.js',
            'styles/js/vendor/fastclick_min.js': 'styles/bower_components/fastclick/lib/fastclick.js'
          }
        }
      },

      /**
       *  Concat
       *
       *  MODL using for JS contactenation. We specify two destination files, one for scripts to
       *  be loaded in the <head> and another to be loaded just before </body> in footer. Set SRC
       *  files as needed.
       */

      concat: {
        topjs: {
          src: [
            'styles/js/vendor/modernizr_min.js',
          ],
          dest: 'www/assets/javascripts/gi_top.js'
        },
        bottomjs: {
          src: [
            'styles/bower_components/jquery/dist/jquery.min.js',
            'styles/js/vendor/fastclick_min.js',
            'styles/bower_components/foundation/js/foundation.min.js',
            'styles/js/modl/app_min.js'
          ],
          dest: 'www/assets/javascripts/gi_bottom.js'
        }
      },

      /**
       * Imagemin
       *
       * The ImageMin task searches out any images it finds (png or jpg format) and compresses them
       * so they are smaller in file size.
       * For consistency MODL will store design-related image files in the styles/i/ folder.
       *
       * https://github.com/gruntjs/grunt-contrib-imagemin
       */

      imagemin: {
        dynamic: {
          files: [{
            expand: true,
            cwd: 'styles/i/',
            src: ['*.{png,jpg,gif}'],
            dest: 'www/assets/images/min/'
          }]
        }
      },

      /**
       * Watch
       *
       * This is the task for grunt to watch a project. Similar to how compass watch works.
       * This task "calls" other the other tasks above as part of it's process when it identifies
       * changes.
       *
       * https://github.com/gruntjs/grunt-contrib-watch
       */

      watch: {
        options: {
          livereload: false,
        },
        sass: {
          files: 'styles/scss/**/*.scss',
          tasks: ['sass']
        },
        scripts: {
          files: ['styles/js/modl/*.js'],
          tasks: ['concat', 'uglify', 'jshint'],
          options: {
            spawn: false,
          }
        },
        images: {
          files: ['styles/i/*.{png,jpg,gif}'],
          tasks: ['imagemin'],
          options: {
            spawn: false,
          }
        }
      },

      /**
       * Connect
       *
       * Connect spawns a web server that runs while Grunt is running.  Once the tasks are complete
       * The server stops.
       *
       * https://github.com/gruntjs/grunt-contrib-connect
       */

      connect: {
        server: {
          options: {
            port: 8000,
            base: './'
          }
        }
      },

    });

    require('load-grunt-tasks')(grunt);

    // Default Task
    grunt.registerTask('default', ['sass', 'uglify', 'concat', 'imagemin']);

    // Dev Task
    grunt.registerTask('dev', ['connect', 'watch']);

    // Minify Only
    grunt.registerTask('minify', ['uglify']);

    // Concatenate Only
    grunt.registerTask('conc', ['concat']);

};
