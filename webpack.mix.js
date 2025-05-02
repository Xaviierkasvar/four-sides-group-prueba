// webpack.mix.js
const mix = require('laravel-mix');

// Compilar el archivo SCSS principal
mix.sass('resources/sass/app.scss', 'public/css')
   .js('resources/js/app.js', 'public/js')
   .sourceMaps();
