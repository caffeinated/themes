<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Default Active Theme
	|--------------------------------------------------------------------------
	|
	| Assign the default active theme to be used if one is not set during
	| runtime. This is especially useful if you're developing a very basic
	| application that does not require dynamically changing the theme.
	|
	*/

	'active' => 'bootstrap',
	
	/*
	|--------------------------------------------------------------------------
	| Templating Engine
	|--------------------------------------------------------------------------
	|
	| Switch between using either Blade or Twig as youe templating engine. To
	| use Twig, be sure to install the twigbridge package and register its
	| service provider BEFORE the Caffeinated Themes service provider.
	|
	| Available Settings: "blade", "twig"
	|
	*/

	'engine' => 'blade',

	/*
	|--------------------------------------------------------------------------
	| Theme Paths
	|--------------------------------------------------------------------------
	|
	*/

	'paths' => [

		/*
		|----------------------------------------------------------------------
		| Absolute Path
		|----------------------------------------------------------------------
		|
		| Define the absolute path where you'd like to store your themes. Note
		| that if you choose a path that's outside of your public directory, you
		| will still need to store your assets within your public directory.
		|
		*/

		'absolute' => public_path('themes'),

		/*
		|----------------------------------------------------------------------
		| Base Path
		|----------------------------------------------------------------------
		|
		| Define the base path where your themes will be publically available.
		| This is used to generate the correct URL when utilizing both the
		| asset() and secureAsset() methods.
		|
		*/

		'base' => 'themes',

		/*
		|----------------------------------------------------------------------
		| Assets Path
		|----------------------------------------------------------------------
		|
		| Define the path that will store all assets for each of your themes.
		| This is used to generate the correct URL when utilizing both the
		| asset() and secureAsset() methods.
		|
		*/

		'assets' => 'assets',

	]

];