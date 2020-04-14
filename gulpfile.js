var gulp = require('gulp');
var pkg = require('./package.json');

// include plug-ins
var eslint = require('gulp-eslint');
var shell = require('gulp-shell');

// JS code checking
gulp.task('scripts', scripts);

function scripts(done) {

  // Run eslint for src js
  gulp.src('./js/*.js')
    .pipe(eslint(done))
    .pipe(eslint.format());

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
	gulp.watch('./js/*.js', scripts);

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
