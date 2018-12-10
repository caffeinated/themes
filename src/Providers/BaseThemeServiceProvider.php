<?php

namespace Caffeinated\Themes\Providers;

use Caffeinated\Modules\Support\ServiceProvider;
use Caffeinated\Themes\Facades\Theme;
use Caffeinated\Themes\Traits\GetsManifest;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class BaseThemeServiceProvider extends ServiceProvider
{
    use GetsManifest;

    public function boot()
    {
        $slug = $this->getManifest()['slug'];

        if (Theme::getCurrent() === $slug) {
            $this->loadTranslationsFrom($this->getDirectory().'/../resources/lang', $slug);
            $this->loadViewsFrom($this->getDirectory().'/../resources/views', $slug);
        }
    }
}
