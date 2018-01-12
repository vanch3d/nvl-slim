const del = require('del');
const gulp = require('gulp');
const debug = require('gulp-debug');
const sass = require('gulp-sass');
const nano = require('gulp-cssnano');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const apidoc = require('gulp-apidoc');
const image = require('gulp-image');
//const autoprefixer = require('gulp-autoprefixer');

var config = {
    sassPath: './resources/sass',
    bowerDir: './bower_components',
    resDir:   './resources/assets',
    destDir:  './public'
}

var jsList = [
    config.bowerDir + '/jquery/dist/jquery.js',
	config.bowerDir + '/bootstrap/dist/js/bootstrap.js',
    config.bowerDir + '/multi-pushmenu/js/classie.js',
    config.bowerDir + '/multi-pushmenu/js/modernizr.custom.js',
    config.bowerDir + '/multi-pushmenu/js/mlpushmenu.js',
    config.resDir   + '/scripts/app.js'
];

var cssList = [
    config.bowerDir + '/bootstrap/dist/css/bootstrap.css',
    config.bowerDir + '/font-awesome/css/font-awesome.css',
    config.bowerDir + '/academicons/css/academicons.css',
    config.bowerDir + '/multi-pushmenu/css/component.css',
    config.bowerDir + '/multi-pushmenu/css/icons.css',
    config.resDir   + '/styles/app.scss'
];

var iconList = [
    config.bowerDir + '/font-awesome/fonts/**.*',
    config.bowerDir + '/academicons/fonts/**.*',
    config.bowerDir + '/multi-pushmenu/fonts/**/**/*.*'
];

gulp.task('scripts', function () {
	return gulp.src(jsList)
        .pipe(debug({title: 'script:'}))
		.pipe(concat('app.js'))
		.pipe(uglify())
        .pipe(gulp.dest('./public/js'))
});

gulp.task('styles', function () {
	return gulp.src(cssList)
        .pipe(debug({title: 'stylesheet:'}))
        .pipe(sass())
        .pipe(nano())
        .pipe(concat('app.css'))
        .pipe(gulp.dest('./public/css'))
});

gulp.task('icons', function() {
    return gulp.src(iconList)
        .pipe(debug({title: 'icons:'}))
        .pipe(gulp.dest('./public/fonts'));
});

gulp.task('images', function () {
    gulp.src(config.resDir + '/images/**/**/*.*')
        .pipe(image({
            concurrent: 10
        }))
        .pipe(gulp.dest('./public/images'));
});

gulp.task('apidoc', function(done){
    apidoc({
        src: './',
        dest: 'apidoc/',
        // debug: true,
        includeFilters: [ '.*\\.php$' ]
    }, done);
});

gulp.task('clean:cache', function () {
    return del([
        '.cache/**/*'
    ]);
});


gulp.task('watch-build', function () {
	// /**/**/*.ext pour inclure les éventuels subdir
	gulp.watch('resources/assets/styles/**/**/*.scss', ['styles']);
	gulp.watch('resources/assets/scripts/**/**/*.js', ['scripts']);
	gulp.watch('app/Controllers/**/**/*.php', ['apidoc']);
});

gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', ['default', 'watch-build']);