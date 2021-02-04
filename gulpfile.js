const { dest } = require('gulp');
var gulp = require('gulp'),
	jshint = require('gulp-jshint'),
	plumber = require('gulp-plumber'),
	imagemin = require('gulp-imagemin'),
	cssnano = require('gulp-cssnano'),
	uglify = require('gulp-uglify'),
	concat = require('gulp-concat'),
	autoprefixer = require('gulp-autoprefixer'),
	rename = require('gulp-rename'),
	//htmlhint_inline = require('gulp-htmlhint-inline');
	del = require('del');

gulp.copy = function (src, dest) {
  return gulp.src(src, { base: "." })
    .pipe(gulp.dest(dest));
};

//Scripts
gulp.task('js-lint-all', function(){
	return gulp.src('js/*.js')
	.pipe(plumber())
	.pipe(jshint())
	.pipe(jshint.reporter())
	.pipe(concat('main.js'))
	.pipe(gulp.dest('dist/js'))
	.pipe(rename({suffix:'.min'}))
	.pipe(uglify())
	.pipe(gulp.dest('dist/js'));
});

//Styles
gulp.task('css', function(){
	return gulp.src('css/*.css')
	.pipe(plumber())
	.pipe(autoprefixer('last 2 versions'))
	.pipe(concat('style.css'))
	.pipe(gulp.dest('dist/css'))
	.pipe(rename({suffix:'.min'}))
	.pipe(cssnano())
	.pipe(gulp.dest('dist/css'));
});

//Images
gulp.task('compress-images', function(){
	return gulp.src('assets/img/*')
	.pipe(plumber())
	.pipe(imagemin({progressive: true}))
	.pipe(gulp.dest('dist/assets/img'))
});

//Clean
gulp.task('clean', function(){
	return del(['dist']);
});

//Watch js lint
gulp.task('js-lint', function(){
	return gulp.src('js/*.js')
	.pipe(plumber())
	.pipe(jshint())
	.pipe(jshint.reporter())
});

gulp.task(
	'dist',
	gulp.series(
		'clean',
		'js-lint-all',
		'css',
		'compress-images',
		function(done){
			// Copy php files
      gulp.copy(['*.php', 'vendor/**/*'], 'dist/');

      // Copy .env file if exists
      gulp.src('.env', { allowEmpty: true }, gulp.dest('dist/'));

			// Copy assets files
			gulp.copy(['assets/libs/**/*', 'assets/scripts/**/*', 'assets/snippets/**/*'], 'dist/');

      // Join all database definition files
      gulp.src('assets/schema/*.sql')
			.pipe(concat('schema.sql'))
			.pipe(gulp.dest('dist'));

			done();
		}
	)
);
