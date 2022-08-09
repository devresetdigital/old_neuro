let mix = require('laravel-mix');

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

mix
  .react('resources/assets/js/strategy-duplicate.js', 'public/js')
  .react('resources/assets/js/campaigns-status.js', 'public/js')
  .react('resources/assets/js/strategy-edit.js', 'public/js');
