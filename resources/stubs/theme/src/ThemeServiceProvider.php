<?php

namespace DummyNamespace;

use Caffeinated\Themes\Providers\BaseThemeServiceProvider;

class ThemeServiceProvider extends BaseThemeServiceProvider
{
    public function boot()
    {
        parent::boot();

        //
    }

    public function register()
    {
        $this->register(RouteServiceProvider::class);
    }
}
