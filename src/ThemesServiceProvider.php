<?php

namespace Caffeinated\Themes;

use View;
use Caffeinated\Manifest\Manifest;
use Illuminate\Support\ServiceProvider;
use Caffeinated\Themes\View\ThemeViewFinder;

class ThemesServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the service provider.
	 *
	 * @return null
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/themes.php' => config_path('themes.php')
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
		    __DIR__.'/../config/themes.php', 'caffeinated.themes'
		);

		$this->registerServices();
        $this->registerNamespaces();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return ['caffeinated.themes', 'view.finder'];
	}

	/**
	 * Register the package services.
	 */
	protected function registerServices()
	{
		$this->app->singleton('caffeinated.themes', function($app) {
            $themes = [];
            $items  = [];

            if ($path = config('themes.paths.absolute')) {
                if (file_exists($path) && is_dir($path)) {
                    $themes = $this->app['files']->directories($path);
                }
            }

            foreach ($themes as $theme) {
                $manifest = new Manifest($theme.'/theme.json');
                $items[] = $manifest;
            }

            return new Theme($items);
		});

        $this->app->singleton('view.finder', function($app) {
            return new ThemeViewFinder($app['files'], $app['config']['view.paths'], null);
        });
	}

    /**
     * Register the theme namespaces.
     */
    protected function registerNamespaces()
    {
        $themes = app('caffeinated.themes')->all();

        foreach ($themes as $theme) {
            app('view')->addNamespace($theme->get('slug'), app('caffeinated.themes')->getAbsolutePath($theme->get('slug')).'/views');
        }
    }
}
