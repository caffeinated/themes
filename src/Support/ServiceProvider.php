<?php

namespace Caffeinated\Themes\Support;

use Caffeinated\Themes\Facades\Theme;
use Caffeinated\Themes\Concerns\GetsManifest;
use Caffeinated\Modules\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    use GetsManifest;

    public function boot()
    {
        $slug = $this->getManifest()['slug'];

        if (Theme::getCurrent() === $slug) {
            $this->loadTranslationsFrom(Theme::path('resources/lang'), 'theme');
            $this->loadViewsFrom(Theme::path('resources/views'), 'theme');
        }
    }
}
