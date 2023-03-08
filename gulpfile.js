var gulp = require('gulp');
var pkg = require('./package.json');

// include plug-ins
var autoprefixer = require('autoprefixer');
var concat = require('gulp-concat');
var eslint = require('gulp-eslint');
var postcss = require('gulp-postcss');
var replace = require('gulp-replace-task');
var sass = require('gulp-sass')(require('sass'));
var shell = require('gulp-shell');
var sourcemaps = require('gulp-sourcemaps');
var terser = require('gulp-terser');

// options
var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'compressed' //expanded, nested, compact, compressed
};

// watch
const watchCSS = () => gulp.watch('./src/sass/*.scss', styles);
const watchJS = () => gulp.watch('./src/js/*.js', scripts);
const watchPHP = () => gulp.watch('./**/*.php', sniffs);

// CSS concat, auto-prefix and minify
function styles(done) {

	gulp.src('./src/sass/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass.sync(sassOptions).on('error', sass.logError))
		.pipe(concat('programs.built.css'))
    .pipe(postcss([ autoprefixer() ]))
		.pipe(sourcemaps.write('./map'))
		.pipe(gulp.dest('./css/'));

  done();
  //console.log('styles ran');
}

// JS code checking
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
function sniffs(done) {

    return gulp.src('.', {read:false})
        .pipe(shell(['./.sniff']));

    done();
    //console.log('sniffs ran');
}

// Update plugin version
function version(done) {

	gulp.src('./uri-program-finder.php')
		.pipe(replace({
			patterns: [{
				match: /Version:\s([^\n\r]*)/,
				replace: 'Version: ' + pkg.version
			}]
		}))
		.pipe(gulp.dest('./'));

    done();
    // console.log('version ran');
}

// Default
const dev = gulp.series(
    gulp.parallel(styles, scripts, sniffs, version),
    gulp.parallel(watchCSS, watchJS, watchPHP)
);
exports.default = dev;

function done() {
	console.log('done');
}
