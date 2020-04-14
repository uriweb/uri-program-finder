var gulp = require('gulp');
var pkg = require('./package.json');

// include plug-ins
var concat = require('gulp-concat');
var eslint = require('gulp-eslint');
var shell = require('gulp-shell');
var terser = require('gulp-terser');

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

// watch
gulp.task('watcher', watcher);

function watcher(done) {

    // watch for Theme JS changes
	gulp.watch('./src/js/*.js', scripts);

    // watch for PHP change
    gulp.watch('./**/*.php', sniffs);

	done();
}

gulp.task( 'default',
	gulp.parallel('scripts', 'sniffs', 'watcher', function(done){
		done();
	})
);


function done() {
	console.log('done');
}
