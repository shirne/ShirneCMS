'use strict';
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var sourcemaps=require('gulp-sourcemaps');
var del = require('del');
var copy = require('copy');

var is_watching=false;

gulp.task('sass', function () {
    return gulp.src('./scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/css')).on('end',function () {
            if(is_watching)copyDest();
        });
});


gulp.task('sassadmin', function () {
    return gulp.src('./scss/admin/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/css')).on('end',function () {
            if(is_watching)copyDest();
        });
});


var basejs=['js/model/common.js', 'js/model/template.js', 'js/model/dialog.js', 'js/model/jquery.tag.js', 'js/model/datetime.init.js'];
var backsrces=basejs.concat(['js/model/map.js','js/backend.js']);
gulp.task('backend', function () {
    return gulp.src(backsrces)
        .pipe(sourcemaps.init())
        .pipe(concat('backend.js'))
        .pipe(rename({ basename: 'app' }))
        .pipe(gulp.dest('./dest/admin/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/js/')).on('end',function () {
            if(is_watching)copyDest();
        });
});


var frontsrces=basejs.concat(['js/front.js']);
gulp.task('front', function () {
    return gulp.src(frontsrces)
        .pipe(sourcemaps.init())
        .pipe(concat('front.js'))
        .pipe(rename({ basename: 'init' }))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end',function () {
            if(is_watching)copyDest();
        });
});


gulp.task('mobile', function () {
    return gulp.src(['js/mobile.js'])
        .pipe(gulp.dest('./dest/js/'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end',function () {
            if(is_watching)copyDest();
        });
});


gulp.task('location', function () {
    return gulp.src(['js/model/areas.js','js/model/location.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('location.js'))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end',function () {
            if(is_watching)copyDest();
        });
});


gulp.task('clean',function (cb) {
    del('dest/**/*').then(function(paths) {
        if(paths && paths.length) {
            console.log('Deleted files and folders:\n', paths.join('\n'));
        }else{
            console.log('No files were deleted.');
        }
        cb()
    });
});

function copyDest() {
    console.log('Copy dest to public...');
    copy(['dest/**/*.css','dest/**/*.min.js','dest/**/*.min.js.map'],'../public/static/',function () {
        
    });
}
function watchAll() {
    is_watching=true;
    console.log('Starting watch all files...');
    gulp.watch(['./scss/*.scss','./scss/model/*.scss'],['sass'],function () {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['./scss/admin/*.scss'],['sassadmin'],function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(backsrces,['backend'],function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(frontsrces,['front'],function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['js/mobile.js'],['mobile'],function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['js/model/areas.js','js/model/location.js'],['location'],function (event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
}

gulp.task('default', ['sass','sassadmin','backend','front','mobile','location'],function () {
    watchAll();
    return copyDest();
});

gulp.task('watch', watchAll);

