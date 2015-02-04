Caffeinated Themes
==================
[![Laravel](https://img.shields.io/badge/Laravel-5.0-orange.svg?style=flat-square)](http://laravel.com)
[![Source](http://img.shields.io/badge/source-caffeinated/themes-blue.svg?style=flat-square)](https://github.com/caffeinated/themes)
[![Build Status](http://img.shields.io/travis/caffeinated/themes/master.svg?style=flat-square)](https://travis-ci.org/caffeinated/themes)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

Caffeinated Themes gives the means to group together a set of views and assets for Laravel 5.0. This gives an easy way to further decouple the way your web application looks from your code base.

Features
--------
- Supports Caffeinated Modules
- Supports both the Blade and Twig templating engines
- Intelligent fallback view support
- Child/parent theme inheritance
- Theme components, easily create re-usable UI components

Documentation
-------------
You will find user friendly documentation here: [Themes Documentation](http://codex.caffeinated.ninja/themes)

Quick Installation
------------------
Begin by installing the package through Composer. The best way to do this is through your terminal via Composer itself:

```
composer require caffeinated/themes
```

Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

### Service Provider
```
'Caffeinated\Themes\ThemesServiceProvider',
```

### Facades
```
'Theme'     => 'Caffeinated\Themes\Facades\Theme',
'Component' => 'Caffeinated\Themes\Facades\Component',
```

And that's it! With your coffee in reach, start building some awesome themes!