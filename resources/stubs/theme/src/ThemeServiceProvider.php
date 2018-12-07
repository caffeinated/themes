<?php

namespace DummyNamespace\Providers;

use Caffeinated\Themes\Providers\BaseThemeServiceProvider;

class ThemeServiceProvider extends BaseThemeServiceProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', $this->getSlug());
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->getSlug());
        $this->loadMigrationsFrom(__DIR__.'/../resources/migrations', $this->getSlug());
        $this->loadFactoriesFrom(__DIR__.'/../resources/lang');
    }

    public function register()
    {
        //
    }
}
