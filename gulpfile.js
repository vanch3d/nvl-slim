const del = require('del');
const gulp = require('gulp');
const debug = require('gulp-debug');
const rename = require('gulp-rename');
const sass = require('gulp-sass');
const nano = require('gulp-cssnano');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const image = require('gulp-image');
const merge = require('merge-stream');
const exec = require('gulp-exec');
const run = require('child_process').exec;

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
    config.bowerDir + '/jquery-fullscreen/jquery.fullscreen.js',
    config.bowerDir + '/Snap.svg/dist/snap.svg.js',
    config.bowerDir + '/holderjs/holder.js',
    config.bowerDir + '/isotope-layout/dist/isotope.pkgd.js',
    config.bowerDir + '/galleria/dist/galleria.js',
    config.bowerDir + '/Smartjax/smartjax.js',
    config.bowerDir + '/handlebars/handlebars.js',
    config.resDir   + '/scripts/app.js'
];

// list of JS plugins to uglify but keep separate
var jsPluginList = [
    config.resDir + '/_plugins/nvl-galleria/nvl-galleria.js',
    config.bowerDir + '/StoryMapJS/compiled/js/storymap.js'
];

// list of D3-v3 plugins to combine
var jsD3v3List = [
    config.bowerDir + '/d3-bower/d3.js',
    config.bowerDir + '/d3-narrative/narrative.js',
    config.bowerDir + '/d3-process-map/dist/colorbrewer.js',
    config.bowerDir + '/d3-process-map/dist/geometry.js',
    config.bowerDir + '/d3-process-map/dist/d3-process-map.js'
];


// list of CSS files to combine
var cssList = [
    config.bowerDir + '/bootstrap/dist/css/bootstrap.css',
    config.bowerDir + '/font-awesome/css/font-awesome.css',
    config.bowerDir + '/academicons/css/academicons.css',
    config.bowerDir + '/multi-pushmenu/css/component.css',
    config.bowerDir + '/multi-pushmenu/css/icons.css',
    config.bowerDir + '/StoryMapJS/compiled/css/storymap.css',
    config.resDir   + '/styles/app.scss',
    config.resDir   + '/_plugins/nvl-galleria/css/nvl-galleria.css'
];

// list of font & font-icons to copy
var iconList = [
    config.bowerDir + '/font-awesome/fonts/**.*',
    config.bowerDir + '/academicons/fonts/**.*',
    config.bowerDir + '/multi-pushmenu/fonts/**/**/*.*'
];

// Hack for icons & fonts in css/icons folder
var iconCSSList = [
    config.bowerDir + '/StoryMapJS/compiled/css/icons/**.*'
];

// list of images to process and copy
var imgList = [
    config.resDir + '/images/**/**/*.*',
    config.resDir + '/_plugins/nvl-galleria/images/**/**/*.*',
    config.bowerDir + '/PubReader/img/**/**/*.*',
    config.bowerDir + '/swagger-ui/dist/*.png'

];

// list of images to process and copy
var pubReader = {
    js : [
        config.bowerDir + '/PubReader/js/jr.boots.js',
        config.bowerDir + '/PubReader/dist/pubreader.min.js',
        config.bowerDir + '/PubReader/dist/pubreader-lib.min.js'
    ],
    css : [
        config.bowerDir + '/font-awesome/css/font-awesome.min.css',
        config.bowerDir + '/PubReader/dist/pubreader.min.css'
    ]
};

gulp.task('js-pubReader', function () {
    // pubreader scripts
    return gulp.src(pubReader.js)
        .pipe(debug({title: 'pubreader:'}))
        .pipe(gulp.dest('./public/js'));
});

gulp.task('js-swagger', function () {
    // swagger-ui scripts
    var jsSwagger = gulp.src([
        config.bowerDir + '/swagger-ui/dist/swagger-ui-bundle.js',
        config.bowerDir + '/swagger-ui/dist/swagger-ui-standalone-preset.js',
        config.bowerDir + '/swagger-ui/dist/swagger-ui.js'])
        .pipe(debug({title: 'swagger:'}))
        .pipe(gulp.dest('./public/js'));
    // swagger-ui css
    var cssSwagger = gulp.src([
        config.bowerDir + '/swagger-ui/dist/swagger-ui.css'])
        .pipe(debug({title: 'swagger:'}))
        .pipe(gulp.dest('./public/css'));

    return merge(jsSwagger,cssSwagger);
});


gulp.task('js-plugins', function () {
    // individual plugins
    return gulp.src(jsPluginList)
        .pipe(debug({title: 'plugin:'}))
        .pipe(gulp.dest('./public/js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/js'));
});

gulp.task('js-d3v3', function () {
    // d3 bundle: d3 & all addons minified in a single package, separate from core
    return gulp.src(jsD3v3List)
        .pipe(debug({title: 'd3 (v3):'}))
        .pipe(concat('d3.nvl-bundle.js'))
        .pipe(gulp.dest('./public/js'))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/js'));
});

gulp.task('js-core', function () {
    // core application scripts
    return gulp.src(jsList)
        .pipe(debug({title: 'script:'}))
        .pipe(concat('app.js'))
        .pipe(gulp.dest('./public/js'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./public/js'));
});

gulp.task('scripts', ['js-swagger','js-pubReader','js-plugins','js-d3v3','js-core']);

gulp.task('css-pubReader', function () {
    // pubreader styles
    return  gulp.src(pubReader.css)
        .pipe(debug({title: 'pubreader:'}))
        .pipe(concat('pubreader-nvl.min.css'))
        .pipe(gulp.dest('./public/css'));
});

gulp.task('css-core', function () {
    // core application styles
    return gulp.src(cssList)
        .pipe(debug({title: 'stylesheet:'}))
        .pipe(sass())
        .pipe(nano())
        .pipe(concat('app.css'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./public/css'))
});

gulp.task('styles', ['css-pubReader','css-core']);

gulp.task('icons', function() {
    gulp.src(iconCSSList)
        .pipe(debug({title: 'icons:'}))
        .pipe(gulp.dest('./public/css/icons'));

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

gulp.task('tool:clean-cache', function () {
    return del([
        '.cache/**/*'
    ]);
});

gulp.task('tool:build-  swagger', function (cb) {
    run('"./vendor/bin/swagger" -e vendor ./resources/', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    });

    /*gulp.src('./resources/api/')
        .pipe(exec('"./vendor/bin/swagger" -e vendor -o ./apidoc/ <%= file.path %>'))
        .pipe(exec.reporter());*/

});


gulp.task('watch-build', function () {
    // /**/**/*.ext pour inclure les éventuels subdir
    gulp.watch('resources/assets/styles/**/**/*.scss', ['styles']);
    gulp.watch('resources/assets/scripts/**/**/*.js', ['scripts']);
    gulp.watch('app/Controllers/**/**/*.php', ['apidoc']);
});

gulp.task('build:pubReader', ['js-pubReader','css-pubReader']);

gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', ['default', 'watch-build']);
