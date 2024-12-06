const mix = require('laravel-mix');

mix.css('resources/css/app.css', 'public/css')
   .js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
   ]);
