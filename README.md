Caffeinated Themes
==================
[![Laravel](https://img.shields.io/badge/Laravel-5.0-orange.svg?style=flat-square)](http://laravel.com)
[![Source](http://img.shields.io/badge/source-caffeinated/themes-blue.svg?style=flat-square)](https://github.com/caffeinated/themes)
[![Build Status](http://img.shields.io/travis/caffeinated/themes/master.svg?style=flat-square)](https://travis-ci.org/caffeinated/themes)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/caffeinated/themes.svg?style=flat-square)](https://scrutinizer-ci.com/g/caffeinated/themes/?branch=master)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/caffeinated/themes.svg?style=flat-square)](https://scrutinizer-ci.com/g/caffeinated/themes/?branch=master)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

Caffeinated Themes gives the means to group together a set of views and assets for Laravel 5.0. This gives an easy way to further decouple the way your web application looks from your code base.

The Caffeinated Themes package comes with support for the Caffeinated Modules package as well. Views will intelligently cascade through a fallback system if using and loading module view files.

- Checks if the active theme has the requested module view file (e.g. `/public/themes/bootstrap/views/modules/blog/index.blade.php`)
- If the theme does not have the requested module view file, it will fallback to loading the view file supplied with your module (e.g. `/app/Modules/Blog/Resources/Views/index.blade.php`)

---

Documentation
-------------
You will find user friendly documentation here: [Themes Documentation](http://codex.caffeinated.ninja/themes)

The raw Markdown files for the documentation can be found here: [Caffeinated Docs Repository - Themes](https://github.com/caffeinated-docs/themes)

Quick Installation
------------------
Begin by installing the package through Composer. The best way to do this is through your terminal via Composer itself:

```
composer require caffeinated/themes
```

Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

### Service Provider
```
'Caffeinated\Themes\ThemesServiceProvider'
```

### Facade
```
'Theme' => 'Caffeinated\Themes\Facades\Theme'
```

And that's it! With your coffee in reach, start building some awesome themes!