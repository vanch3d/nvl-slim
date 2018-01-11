var gulp = require('gulp');
var sass = require('gulp-sass');
var nano = require('gulp-cssnano');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var autoprefixer = require('gulp-autoprefixer');
var apidoc = require('gulp-apidoc');

var config = {
    sassPath: './resources/sass',
    bowerDir: './bower_components'
}

var scripts = [
    config.bowerDir + '/jquery/dist/jquery.js',
	config.bowerDir + "/bootstrap/dist/js/bootstrap.js",
    config.bowerDir + "/multi-pushmenu/js/classie.js",
    config.bowerDir + "/multi-pushmenu/js/modernizr.custom.js",
    config.bowerDir + "/multi-pushmenu/js/mlpushmenu.js",
	"resources/assets/scripts/app.js"
];

gulp.task('scripts', function () {
	return gulp.src(scripts)
		.pipe(concat('app.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./public/js'))
});

gulp.task('styles', function () {
	return gulp.src([
    	'bower_components/bootstrap/dist/css/bootstrap.css',
        'bower_components/font-awesome/css/font-awesome.css',
        'bower_components/academicons/css/academicons.css',
        config.bowerDir + "/multi-pushmenu/css/component.css",
        config.bowerDir + "/multi-pushmenu/css/icons.css",
		'resources/assets/styles/app.scss'
	])
	.pipe(sass())
	.pipe(nano())
	.pipe(concat('app.css'))
	.pipe(gulp.dest('./public/css'))
});

gulp.task('icons', function() {
    return gulp.src([
        config.bowerDir + '/font-awesome/fonts/**.*',
        config.bowerDir + '/academicons/fonts/**.*',
        config.bowerDir + '/multi-pushmenu/fonts/**/**/*.*'
    ])
        .pipe(gulp.dest('./public/fonts'));
});

gulp.task('apidoc', function(done){
    apidoc({
        src: "./",
        dest: "apidoc/",
        // debug: true,
        includeFilters: [ ".*\\.php$" ]
    }, done);
});

gulp.task('watch-build', function () {
	// /**/**/*.ext pour inclure les éventuels subdir
	gulp.watch('resources/assets/styles/**/**/*.scss', ['styles']);
	gulp.watch('resources/assets/scripts/**/**/*.js', ['scripts']);
	gulp.watch('app/Controllers/**/**/*.php', ['apidoc']);
});

gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', ['default', 'watch-build']);