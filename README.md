Caffeinated Themes
==================
[![Laravel 5.0](https://img.shields.io/badge/Laravel-5.0-orange.svg?style=flat-square)](http://laravel.com)
[![Laravel 5.1](https://img.shields.io/badge/Laravel-5.1-orange.svg?style=flat-square)](http://laravel.com)
[![Source](http://img.shields.io/badge/source-caffeinated/themes-blue.svg?style=flat-square)](https://github.com/caffeinated/themes)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

Caffeinated Themes gives the means to group together a set of views and assets for Laravel 5.0 and Laravel 5.1. This gives an easy way to further decouple the way your web application looks from your code base.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code. At the moment the package is not unit tested, but is planned to be covered later down the road.

Features
--------
- Supports Caffeinated Modules
- Supports both the Blade and Twig templating engines
- Intelligent fallback view support
- Child/parent theme inheritance
- Theme components, easily create re-usable UI components

Documentation
-------------
You will find user friendly and updated documentation in the wiki here: [Caffeinated Themes Wiki](https://github.com/caffeinated/themes/wiki)

Quick Installation
------------------
Begin by installing the package through Composer. Depending on what version of Laravel you are using (5.0 or 5.1), you'll want to pull in the `~1.0` or `~2.0` release, respectively:

#### Laravel 5.0.x
```
composer require caffeinated/themes=~1.0
```

#### Laravel 5.1.x
```
composer require caffeinated/themes=~2.0
```

Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

#### Laravel 5.0.x
##### Service Provider
```php
'Caffeinated\Themes\ThemesServiceProvider',
```

##### Facade
```php
'Theme' => 'Caffeinated\Themes\Facades\Theme',
```

#### Laravel 5.1.x
##### Service Provider
```php
Caffeinated\Themes\ThemesServiceProvider::class,
```

##### Facade
```php
'Theme' => Caffeinated\Themes\Facades\Theme::class,
```

And that's it! With your coffee in reach, start building some awesome themes!
