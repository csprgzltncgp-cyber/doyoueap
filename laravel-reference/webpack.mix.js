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

mix.js('resources/js/client/master.js', 'public/js/client/');
mix.js('resources/js/video-chat.js', 'public/js/');
mix.js('resources/js/chat.js', 'public/js/');
mix.js('resources/js/live-webinar-start.js', 'public/js/');

mix.postCss('resources/css/client/master.css', 'public/assets/css/client', [
    require('tailwindcss'),
]);
