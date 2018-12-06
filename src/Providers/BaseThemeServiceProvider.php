<?php

namespace Caffeinated\Themes\Providers;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

abstract class BaseThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom($this->getDirectory(), 'DummySlug');
        $this->loadViewsFrom($this->getDirectory(), 'DummySlug');
        $this->loadMigrationsFrom($this->getDirectory(), 'DummySlug');
        $this->loadFactoriesFrom($this->getDirectory());
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get directory of inheriting class rather.
     *
     * @param string $path
     * @return string
     * @throws \ReflectionException
     */
    protected function getDirectory($path = '')
    {
        $reflector = new ReflectionClass(get_class($this));
        $base = dirname($reflector->getFileName());
        $path = ltrim($path, '/');

        return "$base/$path";
    }
}
