module.exports = function (grunt) {

    // 1. All configuration goes here 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        sass: {
            dist: {     
            files: {
                'public/assets/css/frontend.scss.css':'scss/theme.scss'
            }
          }
        },

        cssmin: {
            combine: {
                files: {
                    'public/assets/css/built/theme.min.css': 'public/assets/css/frontend.scss.css'
                }
            }
        },
        
        watch: {
            sass: {
              files: ['scss/*scss'],
              tasks: ['sass']
            },
            stylesheets: {
                files: ['public/assets/css/*.css'],
                tasks: 'cssmin'
            }
        }
    });

    // 3. Where we tell Grunt we plan to use this plug-in.
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask(
      'default',
      [
        'sass',
        'cssmin'
      ]
    );
};
