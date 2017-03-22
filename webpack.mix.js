const { mix } = require('laravel-mix');

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
.combine([
	'node_modules/bootstrap/dist/css/bootstrap.min.css', 
	'node_modules/font-awesome/css/font-awesome.min.css',
	'resources/assets/css/app.css',
	'node_modules/leaflet/dist/leaflet.css',
	'resources/assets/css/leaflet/Leaflet.Coordinates.min.css',
	'node_modules/leaflet-draw/dist/leaflet.draw.css',
	'node_modules/leaflet-dialog/Leaflet.Dialog.css'], 
	'public/css/vendor.css')
.combine([ 
	'resources/assets/css/app.css'], 
	'public/css/app.css')
.copy('node_modules/font-awesome/fonts', 'public/fonts')
.js([
	'node_modules/jquery/dist/jquery.js',
	'node_modules/bootstrap/dist/js/bootstrap.min.js',
	'node_modules/angular/angular.min.js',
	'node_modules/leaflet/dist/leaflet.js',
	'node_modules/leaflet-providers/leaflet-providers.js',
	'node_modules/leaflet-path-drag/dist/L.Path.Drag.js',
	'node_modules/leaflet-draw/dist/leaflet.draw.js',
	'node_modules/leaflet-draw-drag/dist/Leaflet.draw.drag.js',
	'node_modules/leaflet-path-transform/dist/L.Path.Transform.js',
	'resources/assets/js/leaflet/Leaflet.Graticule.js',
	'resources/assets/js/leaflet/Leaflet.Coordinates.min.js',
	'resources/assets/js/leaflet/Leaflet.Editable.js',
	'resources/assets/js/leaflet/Leaflet.Control.Custom.js',
	'node_modules/leaflet-dialog/Leaflet.Dialog.js'], 
	'public/js/vendor.js')
.js([
	'resources/assets/js/app.js',
	'resources/assets/js/controllers.js',
	'resources/assets/js/services.js'],
	'public/js/app.js').sourceMaps();
