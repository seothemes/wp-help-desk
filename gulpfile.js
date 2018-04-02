//process.env.DISABLE_NOTIFIER = true; // Disable all notifications.

// Require our dependencies.
var autoprefixer = require( 'autoprefixer' );
var browsersync  = require( 'browser-sync' );
var gulp         = require( 'gulp' );
var cache        = require( 'gulp-cached' );
var filter       = require( 'gulp-filter' );
var notify       = require( 'gulp-notify' );
var plumber      = require( 'gulp-plumber' );
var rename       = require( 'gulp-rename' );
var sass         = require( 'gulp-sass' );
var sort         = require('gulp-sort');
var sourcemaps   = require( 'gulp-sourcemaps' );
var uglify       = require( 'gulp-uglify' );
var wpPot 		 = require( 'gulp-wp-pot' );

// Set assets paths.
var paths = {
	php: [ './*.php', './**/*.php', './**/**/*.php' ],
	js:  [ 'assets/js/*.js', '!assets/js/*.min.js' ],
	css: [ 'assets/css/*.scss', '!assets/css/*.css' ]
};

/**
 * Autoprefixed browser support.
 *
 * https://github.com/ai/browserslist
 */
const AUTOPREFIXER_BROWSERS = [
	'last 2 versions',
	'> 0.25%',
	'ie >= 8',
	'ie_mob >= 9',
	'ff >= 28',
	'chrome >= 40',
	'safari >= 6',
	'opera >= 22',
	'ios >= 6',
	'android >= 4',
	'bb >= 9'
];

/**
 * Compile Sass.
 *
 * https://www.npmjs.com/package/gulp-sass
 */
gulp.task( 'styles', function () {

gulp.src( paths.css )

	// Notify on error
	.pipe( plumber( { errorHandler: notify.onError( "Error: <%= error.message %>" ) } ) )

	// Source maps init
	.pipe( sourcemaps.init() )

	// Process sass
	.pipe( sass( {
		outputStyle: 'compressed'
	} ) )

	// Output non minified css to theme directory.
	.pipe( gulp.dest( './assets/css/' ) )

	// Inject changes via browsersync.
	.pipe( browsersync.reload( { stream: true } ) )

	// Write source map.
	.pipe( sourcemaps.write( './' ) )

	// Filtering stream to only css files.
	.pipe( filter( '**/*.css' ) )

	// Notify on successful compile (uncomment for notifications).
	.pipe( notify( "Compiled: <%= file.relative %>" ) );

} );

/**
 * Minify javascript files.
 *
 * https://www.npmjs.com/package/gulp-uglify
 */
gulp.task( 'scripts', function () {

	gulp.src( paths.js )

	// Notify on error.
	.pipe( plumber( { errorHandler: notify.onError( "Error: <%= error.message %>" ) } ) )

	// Cache files to avoid processing files that haven't changed.
	.pipe( cache( 'scripts' ) )

	// Add .min suffix.
	.pipe( rename( { suffix: '.min' } ) )

	// Minify.
	.pipe( uglify() )

	// Output the processed js to this directory.
	.pipe( gulp.dest( './assets/js/' ) )

	// Inject changes via browsersync.
	.pipe( browsersync.reload( { stream: true } ) )

	// Notify on successful compile.
	.pipe( notify( "Minified: <%= file.relative %>" ) );

} );

/**
 * Create a POT file.
 *
 * https://www.npmjs.com/package/gulp-wp-pot
 */
gulp.task( 'i18n', function() {

	return gulp.src( paths.php )

	.pipe( plumber( { errorHandler: notify.onError( "Error: <%= error.message %>" ) } ) )

	.pipe( sort() )

	.pipe( wpPot( {
		domain: 'wp-help-desk',
		destFile:'wp-help-desk.pot',
		package: 'WP_Help_Desk',
		bugReport: 'https://seothemes.com/support',
		lastTranslator: 'Lee Anthony <seothemeswp@gmail.com>',
		team: 'Seo Themes <seothemeswp@gmail.com>'
	} ) )

	.pipe( gulp.dest( './languages/' ) );

} );

/**
 * Process tasks and reload browsers on file changes.
 *
 * https://www.npmjs.com/package/browser-sync
 */
gulp.task( 'watch', function() {

	// HTTPS.
	browsersync( {
		proxy: 'https://wphelpdesk.dev',
		port: 8000,
		notify: false,
		open: false,
		https: {
			"key": "/Users/seothemes/.valet/Certificates/wphelpdesk.dev.key",
			"cert": "/Users/seothemes/.valet/Certificates/wphelpdesk.dev.crt"
		}
	} );

	/**
	 * Non-HTTPS browsersync.
	 *
	 * Use this instead if you are not using a self signed
	 * certificate on your local development environment.
	 *
	 * browsersync( {
	 *     proxy: 'wphelpdesk.dev'
	 * } );
	 */

	// Run tasks when files change.
	gulp.watch( paths.css, [ 'styles' ] );
	gulp.watch( paths.js, [ 'scripts' ] );
	gulp.watch( paths.php ).on( 'change', browsersync.reload );

} );

/**
 * Create default task.
 */
gulp.task( 'default', [ 'watch' ], function() {
	gulp.start( 'styles', 'scripts' );
} );
