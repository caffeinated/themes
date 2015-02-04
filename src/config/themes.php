<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Default Active Theme
	|--------------------------------------------------------------------------
	|
	| Assign the default active theme to be used if one is not set during
	| runtime.
	|
	*/

	'active' => 'bootstrap',
	
	/*
	|--------------------------------------------------------------------------
	| Templating Engine
	|--------------------------------------------------------------------------
	|
	| Switch between using either Blade or Twig as youe templating engine. To
	| use Twig, be sure to install the twigbridge package and register it's
	| service provider BEFORE the Caffeinated Themes service provider.
	|
	*/

	'engine' => 'blade',

	/*
	|--------------------------------------------------------------------------
	| Path to Themes
	|--------------------------------------------------------------------------
	|
	| Define the path where you'd like to store your themes. Note that if you
	| choose a path that's outside of your public directory, you will still need
	| to store your assets (CSS, images, etc.) within your public directory.
	|
	*/

	'path'   => public_path('themes'),

];