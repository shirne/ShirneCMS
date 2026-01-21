'use strict';

import gulp from 'gulp';
import uglify from 'gulp-uglify';
import babel from 'gulp-babel';
import concat from 'gulp-concat';
import rename from 'gulp-rename';
import less from 'gulp-less';
import LessAutoprefix from 'less-plugin-autoprefix';
import LessPluginCleanCSS from 'less-plugin-clean-css';
import sourcemaps from 'gulp-sourcemaps';
import { deleteAsync } from 'del';
import copy from 'copy';
import packageData from "./package.json" assert { type: 'json' };

const cleanCSS = new LessPluginCleanCSS({ advanced: true });
const autoprefix = new LessAutoprefix({ browsers: packageData.browserslist });

let is_watching = false;

function cssTask() {
    return gulp.src('./less/*.less')
        .pipe(sourcemaps.init())
        .pipe(less({ plugins: [autoprefix, cleanCSS] }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/css')).on('end', function () {
            if (is_watching) copyDest();
        });
}

function cssAdminTask() {
    return gulp.src('./less/admin/*.less')
        .pipe(sourcemaps.init())
        .pipe(less({ plugins: [autoprefix, cleanCSS] }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/css')).on('end', function () {
            if (is_watching) copyDest();
        });
}

let basejs = ['js/model/common.js', 'js/model/template.js', 'js/model/dialog.js', 'js/model/jquery.tag.js', 'js/model/datetime.init.js'];
let backsrces = basejs.concat(['js/model/map.js', 'js/backend.js']);
function backendTask() {
    return gulp.src(backsrces)
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['@babel/preset-env']
        }))
        .pipe(concat('backend.js'))
        .pipe(rename({ basename: 'app' }))
        .pipe(gulp.dest('./dest/admin/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/admin/js/')).on('end', function () {
            if (is_watching) copyDest();
        });
}

let frontsrces = basejs.concat(['js/front.js']);
function frontTask() {
    return gulp.src(frontsrces)
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['@babel/preset-env']
        }))
        .pipe(concat('front.js'))
        .pipe(rename({ basename: 'init' }))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end', function () {
            if (is_watching) copyDest();
        });
}

function mobileTask() {
    return gulp.src(['js/mobile.js'])
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['@babel/preset-env']
        }))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end', function () {
            if (is_watching) copyDest();
        });
}

function locationTask() {
    return gulp.src(['js/model/areas.js', 'js/model/location.js'])
        .pipe(sourcemaps.init())
        .pipe(concat('location.js'))
        .pipe(gulp.dest('./dest/js/'))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./dest/js/')).on('end', function () {
            if (is_watching) copyDest();
        });
}

function cleanTask(done) {
    deleteAsync('dest/**/*').then(function (paths) {
        if (paths && paths.length) {
            console.log('Deleted files and folders:\n', paths.join('\n'));
        } else {
            console.log('No files were deleted.');
        }
        done()
    });
}

function copyDest(done) {
    console.log('Copy dest to public...');
    copy(['dest/**/*.css', 'dest/**/*.css.map', 'dest/**/*.min.js', 'dest/**/*.min.js.map'], '../src/public/static/', function () {
        if (done) done()
    });
}

function watchAll() {
    is_watching = true;
    console.log('Starting watch all files...');
    gulp.watch(['./less/*.less', './less/model/*.less'], cssTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['./less/model/_dialog.less', './less/admin/*.less'], cssAdminTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(backsrces, backendTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(frontsrces, frontTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['js/mobile.js'], mobileTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
    gulp.watch(['js/model/areas.js', 'js/model/location.js'], locationTask, (event) => {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
}

const build = gulp.series(cleanTask, gulp.parallel(cssTask, cssAdminTask, backendTask, frontTask, mobileTask, locationTask), copyDest);

gulp.task('default', gulp.series(build, watchAll));
gulp.task('clean', cleanTask);
gulp.task('watch', watchAll);
gulp.task('build', build);
gulp.task('dest', copyDest);
