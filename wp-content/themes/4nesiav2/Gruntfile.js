module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		sass: {                              // Task
			dist: {                            // Target
				options: {                       // Target options
					style: 'compact', //nested, compact, compressed, expanded.
					// loadPath: 'sass',
					debugInfo: false
				},
				files: {                         // Dictionary of files
					// expand: true, // Allowing to specify a directory
					// cwd: 'styles',
					// src: ['*.scss'],
					// dest: '../public',
					// ext: '.css'

					// All our SCSS get compiled into style.scss
					'style.css': './sass/style.scss'       // 'destination': 'source'
					// 'widgets.css': 'widgets.scss'
				}
			}
		}
	});

	// Load the plugins that provides the tasks above.
	// grunt.loadNpmTasks('grunt-contrib-uglify');
	// grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');

	// Default task(s).
	grunt.registerTask('default', ['sass']);
	// grunt.registerTask('default', ['uglify', 'cssmin', 'sass']);

};