'use strict';
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var sourcemaps=require('gulp-sourcemaps');

gulp.task('sass', function () {
    return gulp.src('./scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/css'));
});
gulp.task('sassadmin', function () {
    return gulp.src('./scss/admin/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/css'));
});

gulp.task('backend', function() {
    return gulp.src(['js/model/common.js', 'js/model/template.js', 'js/model/dialog.js', 'js/model/jquery.tag.js', 'js/model/datetime.init.js','js/model/map.js','js/backend.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('backend.js'))
        .pipe(rename({ basename: 'app' }))
        .pipe(gulp.dest('./dest/admin/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/js/'));
});

gulp.task('front', function() {
    return gulp.src(['js/model/common.js', 'js/model/template.js', 'js/model/dialog.js', 'js/model/jquery.tag.js', 'js/model/datetime.init.js','js/front.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('front.js'))
        .pipe(rename({ basename: 'init' }))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/'));
});

gulp.task('mobile', function() {
    return gulp.src(['js/mobile.js'])
        .pipe(gulp.dest('./dest/js/'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/'));
});

gulp.task('location', function() {
    return gulp.src(['js/model/areas.js','js/model/location.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('location.js'))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/'));
});

gulp.task('default', ['sass','sassadmin','backend','front','mobile','location'],function () {
    return gulp.src('dest/**/*')
        .pipe(gulp.dest('../public/static'));
});


var sasswatcher = gulp.watch('./scss/**/*.scss', ['sass','sassadmin','default']);
sasswatcher.on('change', function(event) {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
});

var watcher = gulp.watch('js/**/*.js', ['backend','front','mobile','location','default']);
watcher.on('change', function(event) {
    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
});