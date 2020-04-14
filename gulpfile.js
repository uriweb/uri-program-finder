var gulp = require('gulp');
var pkg = require('./package.json');

// include plug-ins
var autoprefixer = require('autoprefixer');
var concat = require('gulp-concat');
var eslint = require('gulp-eslint');
var postcss = require('gulp-postcss');
var replace = require('gulp-replace-task');
var sass = require('gulp-sass');
var shell = require('gulp-shell');
var sourcemaps = require('gulp-sourcemaps');
var terser = require('gulp-terser');

// options
var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'compressed' //expanded, nested, compact, compressed
};

// CSS concat, auto-prefix and minify
gulp.task('styles', styles);

function styles(done) {

	gulp.src('./src/sass/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(concat('programs.built.css'))
    .pipe(postcss([ autoprefixer() ]))
		.pipe(sourcemaps.write('./map'))
		.pipe(gulp.dest('./css/'));

  done();
  //console.log('styles ran');
}

// JS code checking
gulp.task('scripts', scripts);

function scripts(done) {

  // Run eslint for src js
  gulp.src('./src/js/*.js')
    .pipe(eslint(done))
    .pipe(eslint.format());

  gulp.src('./src/js/*.js')
    .pipe(concat('programs.built.js'))
    //.pipe(stripDebug())
    .pipe(terser())
    .pipe(gulp.dest('./js/'));

	done();
 // console.log('scripts ran');
}

// run codesniffer
gulp.task('sniffs', sniffs);

function sniffs(done) {

    return gulp.src('.', {read:false})
        .pipe(shell(['./.sniff']));

    done();
    //console.log('sniffs ran');
}

// Update plugin version
gulp.task('version', version);

function version(done) {

	gulp.src('./uri-program-finder.php')
		.pipe(replace({
			patterns: [{
				match: /Version:\s([^\n\r]*)/,
				replace: 'Version: ' + pkg.version
			}]
		}))
		.pipe(gulp.dest('./'));

}

// watch
gulp.task('watcher', watcher);

function watcher(done) {

  // watch for CSS changes
  gulp.watch('./src/sass/*.scss', styles);

    // watch for JS changes
	gulp.watch('./src/js/*.js', scripts);

    // watch for PHP change
    gulp.watch('./**/*.php', sniffs);

	done();
}

gulp.task( 'default',
	gulp.parallel('styles', 'scripts', 'sniffs', 'version', 'watcher', function(done){
		done();
	})
);


function done() {
	console.log('done');
}
