const del = require('del');
const gulp = require('gulp');
const debug = require('gulp-debug');
const rename = require('gulp-rename');
const sass = require('gulp-sass');
const nano = require('gulp-cssnano');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const apidoc = require('gulp-apidoc');
const image = require('gulp-image');

//const autoprefixer = require('gulp-autoprefixer');

var config = {
    bowerDir: './bower_components',
    resDir:   './resources/assets',
    destDir:  './public'
};

// list of core JS files to combine
var jsList = [
    config.bowerDir + '/jquery/dist/jquery.js',
	config.bowerDir + '/bootstrap/dist/js/bootstrap.js',
    config.bowerDir + '/multi-pushmenu/js/classie.js',
    config.bowerDir + '/multi-pushmenu/js/modernizr.custom.js',
    config.bowerDir + '/multi-pushmenu/js/mlpushmenu.js',
    config.bowerDir + '/i18next/i18next.js',
    config.bowerDir + '/fitvids/jquery.fitvids.js',
    config.bowerDir + '/jquery-oembed-all/jquery.oembed.js',
    config.bowerDir + '/jquery.easing/js/jquery.easing.js',
    config.bowerDir + '/Snap.svg/dist/snap.svg.js',
    config.bowerDir + '/holderjs/holder.js',
    config.bowerDir + '/isotope-layout/dist/isotope.pkgd.js',
    config.resDir   + '/scripts/app.js'
];

// list of JS plugins to uglify but keep separate
var jsPluginList = [
    config.resDir + '/_plugins/nvl-galleria/nvl-galleria.js'
];

// list of CSS files to combine
var cssList = [
    config.bowerDir + '/bootstrap/dist/css/bootstrap.css',
    config.bowerDir + '/font-awesome/css/font-awesome.css',
    config.bowerDir + '/academicons/css/academicons.css',
    config.bowerDir + '/multi-pushmenu/css/component.css',
    config.bowerDir + '/multi-pushmenu/css/icons.css',
    config.resDir   + '/styles/app.scss',
    config.resDir   + '/_plugins/nvl-galleria/css/nvl-galleria.css',

];

// list of font & font-icons to copy
var iconList = [
    config.bowerDir + '/font-awesome/fonts/**.*',
    config.bowerDir + '/academicons/fonts/**.*',
    config.bowerDir + '/multi-pushmenu/fonts/**/**/*.*'
];

// list of images to process and copy
var imgList = [
    config.resDir + '/images/**/**/*.*',
    config.resDir + '/_plugins/nvl-galleria/images/**/**/*.*',
];

gulp.task('scripts', function () {
    gulp.src(jsPluginList)
        .pipe(debug({title: 'plugin:'}))
        .pipe(gulp.dest('./public/js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/js'));

	return gulp.src(jsList)
        .pipe(debug({title: 'script:'}))
		.pipe(concat('app.js'))
        .pipe(gulp.dest('./public/js'))
		.pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/js'))
});

gulp.task('styles', function () {
    return gulp.src(cssList)
        .pipe(debug({title: 'stylesheet:'}))
        .pipe(sass())
        .pipe(nano())
        .pipe(concat('app.css'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/css'))
});

gulp.task('icons', function() {
    return gulp.src(iconList)
        .pipe(debug({title: 'icons:'}))
        .pipe(gulp.dest('./public/fonts'));
});

gulp.task('images', function () {
    return gulp.src(imgList)
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