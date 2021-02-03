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
	return del(['dist/js', 'dist/css', 'dist/assets/img']);
});

//Watch js lint
gulp.task('js-lint', function(){
	return gulp.src('js/*.js')
	.pipe(plumber())
	.pipe(jshint())
	.pipe(jshint.reporter())
});

gulp.task('default', gulp.series('clean', 'js-lint-all', 'css', 'compress-images', function(done){
	gulp.src('*.php')
	.pipe(gulp.dest('dist/'));
	gulp.src(['assets/libs', 'assets/scripts', 'assets/snippets'])
	.pipe(gulp.dest('dist/assets'));

	done();
}));