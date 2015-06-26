
module.exports = function( grunt ) {

	grunt.initConfig( {
		cssmin: {
			combine: {
				files: {
					'deepwiki-themes/default/build/web.min.css': [
						'deepwiki-themes/default/vendor/bootstrap/css/bootstrap.css',
						'deepwiki-themes/default/vendor/bootstrap/css/bootstrap-theme.css',
						'deepwiki-themes/default/vendor/prism/themes/prism.css',
						'deepwiki-themes/default/css/gfm.css',
						'deepwiki-themes/default/css/global.css',
					],
				},
			},
		},
		concat: {
			options: {
				sourceMap: true,
				separator: ';',
			},
			all: {
				src: [
					'deepwiki-themes/default/vendor/jquery/jquery.js',
					'deepwiki-themes/default/vendor/bootstrap/js/bootstrap.js',
					'deepwiki-themes/default/vendor/prism/components/prism-core.js',
					'deepwiki-themes/default/vendor/prism/components/prism-markup.js',
					'deepwiki-themes/default/vendor/prism/components/prism-css.js',
					'deepwiki-themes/default/vendor/prism/components/prism-clike.js',
					'deepwiki-themes/default/vendor/prism/components/prism-javascript.js',
					'deepwiki-themes/default/vendor/prism/components/prism-bash.js',
					'deepwiki-themes/default/vendor/prism/components/prism-php.js',
					'deepwiki-themes/default/vendor/prism/components/prism-php-extras.js',
					'deepwiki-themes/default/vendor/prism/components/prism-python.js',
					'deepwiki-themes/default/vendor/prism/components/prism-ini.js',
					'deepwiki-themes/default/js/global.js',
				],
				dest: 'deepwiki-themes/default/build/web.js',
			},
		},
		uglify: {
			options: {
				sourceMap: true,
				preserveComments: 'some',
			},
			all: {
				files: {
					'deepwiki-themes/default/build/web.min.js': [
						'deepwiki-themes/default/build/web.js',
					],
				},
			},
		},
	} );

	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-concat') ;
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );

	grunt.registerTask( 'default', [ 'cssmin' , 'concat', 'uglify' ] );

	readme = [
		'# DeepWiki',
		grunt.file.read( 'deepwiki-docs-example/01. About DeepWiki [about].markdown' ),
		'## Installation',
		grunt.file.read( 'deepwiki-docs-example/02. Installation.markdown' ),
		'## Quick Start',
		grunt.file.read( 'deepwiki-docs-example/03. Quick Start.markdown' ),
		'## Configuration',
		grunt.file.read( 'deepwiki-docs-example/04. Configuration.markdown' ),
		'## Writing Documents',
		grunt.file.read( 'deepwiki-docs-example/05. Writing Documents.markdown' ),
		'## Auto Deployment for Documents',
		grunt.file.read( 'deepwiki-docs-example/06. Auto Deployment for Documents.markdown' ),
		'## Theme Development',
		grunt.file.read( 'deepwiki-docs-example/07. Theme Development.markdown' ),
		'## URL Rewrite',
		grunt.file.read( 'deepwiki-docs-example/08. URL Rewrite.markdown' ),
		'## Change Log',
		grunt.file.read( 'deepwiki-docs-example/09. Change Log.markdown' ),
		'## Credits',
		grunt.file.read( 'deepwiki-docs-example/10. Credits.markdown' ),
	];
	grunt.file.write( 'README.md', readme.join( '\n' ) );

};
