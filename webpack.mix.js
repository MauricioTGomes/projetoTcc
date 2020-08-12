const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js/app.js')
    .sass('resources/sass/app.scss', 'public/css');

mix.js('resources/vue/main.js', 'public/js/mainVue.js');

/*
mix.babel([
    'resources/js/layout/jquery.easing.min.js',
    'resources/js/layout/jquery.min.js',
    'resources/js/layout/bootstrap.bundle.js',
    'resources/js/layout/bootstrap.min.js',
    'resources/js/layout/Chart.min.js',
    'resources/js/layout/sb-admin-2.min.js',
    'resources/js/layout/demo/chart-bar-demo.js',
    'resources/js/layout/demo/chart-pie-demo.js',
    'resources/js/layout/demo/datatables-demo.js',
], 'public/js/layout-one.js');

mix.styles([
    'resources/css/layout/sb-admin-2.min.css',
    'resources/css/layout/all.min.css',
], 'public/js/layout-one.css');
mix.browserSync('127.0.0.1:8000');
*/