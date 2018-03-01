module.exports = function(grunt) {

	grunt.initConfig({


		// Pospojuj JS
		concat: {
			js: {
				options: {
					separator: ';'
				},
				src: [


				// jQuery UI
				'bower_components/jquery-ui/jquery-ui.js', // Kvůli konfliktu s Bootstrap Tooltips je umístěno před

				// React
				'bower_components/react/react.js',
				'bower_components/react/react-dom.js',

				// Bootstrap vanilla JS komponenty
				// 'bower-components/bootstrap/js/affix.js',
				// 'bower-components/bootstrap/js/alert.js',
				// 'bower-components/bootstrap/js/button.js',
				// 'bower-components/bootstrap/js/carousel.js',
				// 'bower-components/bootstrap/js/collapse.js',
				// 'bower-components/bootstrap/js/dropdown.js',
				// 'bower-components/bootstrap/js/modal.js',
				// 'bower-components/bootstrap/js/popover.js',
				// 'bower-components/bootstrap/js/scrollspy.js',
				// 'bower-components/bootstrap/js/tab.js',
				// 'bower-components/bootstrap/js/tooltip.js',
				// 'bower-components/bootstrap/js/transition.js',
				'bower_components/bootstrap/dist/js/bootstrap.js',


				// Retina.js
				// 'bower_components/retina.js/dist/retina.js',


				// DataTables
				'bower_components/DataTables/media/js/jquery.dataTables.js',
				'bower_components/DataTables/media/js/dataTables.bootstrap.js',


				// Orb (pivot table)
				'bower_components/orb/dist/orb.js',


				// FastCLick
				'bower_components/fastclick/lib/fastclick.js',


				// Pace
				'bower_components/pace/pace.js',


				// Calx
				'bower_components/jquery-calx/jquery-calx-2.2.7.js',


				// Highchart
				'bower_components/highcharts/highcharts.js',
				'bower_components/highchartTable/jquery.highchartTable.js',


				// DatePicker
				'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
				'bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.cs.min.js',


				// Main.js
				'js/main.js'


				],
				dest: '../web/assets/js/main.min.js'
			}
		},





		// Minifikuj JS
		uglify: {
			options: {
				mangle: false
			},
			js: {
				files: {
					'../web/assets/js/main.min.js': ['../web/assets/js/main.min.js']
				}
			}
		},





		// Kompiluj a minifikuj less
		less: {
			style: {
				files: {
					"../web/assets/css/global.min.css": "less/global.less"
				},
				options: {
					compress: true,
					relativeUrls: true,
					yuicompress: true,

					sourceMap: true, // Nezapomenout přeponout i v Autoprefixeru!
					sourceMapFilename: '../web/assets/css/global.min.css.map', // where file is generated and located
					sourceMapURL: 'global.min.css.map', // the complete url and filename put in the compiled css file
					sourceMapBasepath: '', // Sets sourcemap base path, defaults to current working directory.
					sourceMapRootpath: '../' // adds this path onto the sourcemap filename and less file paths
				}
			}
		},





		// Autoprefixuj
		autoprefixer: {
			options: {
				map: true
			},
			file: {
				src: '../web/assets/css/global.min.css',
				dest: '../web/assets/css/global.min.css'
			}
		},





		// Zkopíruj potřebné dependency komponenty z bower adresáře, aby šly použít mimo tento adresář
		bowercopy: {
			options: {

			},
			jquery: {
				files: {
					'../web/assets/js/jquery.min.js': 'jquery/dist/jquery.min.js'
				}
			},
			html5shiv: {
				files: {
					'../web/assets/js/html5shiv.min.js': 'html5shiv/dist/html5shiv.min.js'
				}
			},
			css3mediaqueries: {
				files: {
					'../web/assets/js/css3-mediaqueries.js': 'css3-mediaqueries-js/css3-mediaqueries.js'
				}
			},
			fontawesome: {
				files: {
					'../web/assets/font/fontawesome/': 'fontawesome/fonts',
				}
			},
			/*fancybox: {
				files: {
					'../web/assets/standalone-components/fancybox/': 'fancybox',
				}
			},*/
		},





		// Copy
		copy: {
			main: {
				files: [
					{
						expand: true,
						filter: 'isFile',
						flatten: true,
						src: ['bower_components/bootstrap/fonts/*'],
						dest: '../web/assets/font/glyphicons/'
					},
				],
			},

		},





		// Notifikace úspěchu a failů pro parádu
		notify: {
			notify_js: {
				options: {
					title: 'Kombinace a minifikace JS se povedla!',  // Volitelný
					message: 'Jsi šikovný borec, jen tak dál!' // Povinný
				}
			},
			notify_less: {
				options: {
					title: 'LESS se zkompilovalo na výbornou!',  // Volitelný
					message: 'Jsi šikovný borec, jen tak dál!' // Povinný
				}
			},
			notify_push: {
				options: {
					title: 'Push se povedl!',  // Volitelný
					message: 'Jsi šikovný borec, jen tak dál!' // Povinný
				}
			},
			notify_pull: {
				options: {
					title: 'Pull se povedl!',  // Volitelný
					message: 'Jsi šikovný borec, jen tak dál!' // Povinný
				}
			}
		},





		// Sleduj a konej
		watch: {
			configFiles: {
				files: ['gruntfile.js'] // Při aktualizace gruntfile.js jej znovu načti (netřeba watch exitovat a znovu spouštět)
			},
			js: {
				files: ['js/*.js'],
				tasks: ['concat:js', 'uglify:js', 'notify:notify_js'],
				options: {
					spawn: false,
					livereload: true
				}
			},
			less: {
				files: ['less/**/*.less'],
				tasks: ['less:style', 'autoprefixer:file', 'notify:notify_less'],
				options: {
					spawn: false,
					livereload: true
				}
			}
		},





		// Dalek tests
		dalek: {
		    options: {
		    	// invoke phantomjs, chrome & chrome canary
		    	//   browser: ['phantomjs', 'chrome', 'chrome:canary'],
		    	// generate an html & an jUnit report
				reporter: ['html', 'junit'],
		    	// don't load config from an Dalekfile
		    	dalekfile: false,
		    	// specify advanced options (that else would be in your Dalekfile)
		    	advanced: {
		    		// specify a port for chrome
		        	browsers: [{
		        		chrome: {
		        			port: 4000
		        		}
		    		}]
		    	}
			},
			login: {
	        	src: ['tests/login.js']
			},
			login2: {
				src: ['tests/login2.js']
			}
		},





		// Hashres
		hashres: {
			options: {
				encoding: 'utf8',
				fileNameFormat: '${name}.${ext}?${hash}',
				renameFiles: false
			},
			prod: {
				options: {
				},
				src: ['../web/assets/css/global.min.css', '../web/assets/js/main.min.js'],
				dest: ['../app/Resources/views/base.html.twig'],
			}
		},





		// Rsync
		rsync: {
			options: {
				args: ["--verbose"], // Buď ukecaný
				exclude: [
					".git*",
					"node_modules",
					".DS_Store",
					"license.txt",
					"readme.html",
					"web-dev",
					"app/cache/*",
					"app/logs/*",
					"web/uploads",
					"LICENSE",
					"composer.json",
					"composer.lock"
				],
				recursive: true
			},
			uploads: {
				options: {
					src: "sebek@sebek.vs.fortion.net:/data/www/domeny/feeio.com/app/web/uploads",
					dest: "../web",
					delete: false // Pozor
				}
			},
			staging: {
				options: {
					src: "../",
					dest: "/data/www/domeny/feeio.com/staging",
					host: "sebek@sebek.vs.fortion.net",
					delete: false // Pozor
				}
			},
			release: {
				options: {
					src: "../",
					dest: "/data/www/domeny/feeio.com/release",
					host: "sebek@sebek.vs.fortion.net",
					delete: false // Pozor
				}
			},
		},


	});





	// Naloaduj tasky
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-bowercopy');
	grunt.loadNpmTasks('grunt-notify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-hashres');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-dalek');
	grunt.loadNpmTasks("grunt-rsync");





	// Registruj spouštějící úlohy pro terminál - pro defaultní stačí volat "grunt"
	grunt.registerTask('default', [ 'watch' ]);


};
