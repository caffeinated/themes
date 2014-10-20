Caffeinated Themes
==================
[![Build Status](https://travis-ci.org/caffeinated/themes.svg?branch=master)](https://travis-ci.org/caffeinated/themes)

Laravel 5.0 Themes Package with support for the Caffeinated Modules package.

---

Caffeinated Themes gives the means to group together a set of views and assets for Laravel 5.0. This gives an easy way to further decouple the way your web application looks from your code base.

The Caffeinated Themes package comes with support for the Caffeinated Modules package as well. Views will intelligently cascade through a fallback system is using and loading module view files.

- Checks if the active theme has the requested module view file (e.g. `/public/themes/bootstrap/views/modules/blog/index.blade.php`)
- If the theme does not have the requested module view file, it will fallback to loading the view file supplied with your module (e.g. `/app/Modules/Blog/Resources/Views/index.blade.php`)

---

Installation
------------
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