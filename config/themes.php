<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Theme Path
	|--------------------------------------------------------------------------
	|
	| Define the path where your themes will reside. By default we will assign
	| themes to live at base of your Laravel application. Because themes
	| can extend Laravel, this makes the most sense as the default.
	*/

	'path' => base_path('themes'),

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
	| Default Author
	|--------------------------------------------------------------------------
	|
	| Define your default author name. This is used when generating themes.
	| We will use this value in the generated theme manifest file so that
	| you may reference the author of your themes in your application.
	|
	*/

	'author' => '',

	/*
	|--------------------------------------------------------------------------
	| Default Vendor
	|--------------------------------------------------------------------------
	|
	| Define your default vendor name. This is used when generating themes.
	| We will use this value in the generated composer file so that you
	| may register your themes as a composer package as well.
	|
	*/
	
	'vendor' => 'vendor',

];
