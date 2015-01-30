<?php

namespace Caffeinated\Themes;

use Caffeinated\Themes\Handlers\ThemesHandler;
use Illuminate\Support\ServiceProvider;

class ThemesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerResources();

		$this->registerServices();

		$this->configureTwig();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['themes', 'themes.components', 'themes.engine',];
	}

	/**
	 * Register the package resources.
	 *
	 * @return void
	 */
	protected function registerResources()
	{
		$userConfigFile    = app()->configPath().'/caffeinated/themes.php';
		$packageConfigFile = __DIR__.'/../../config/config.php';
		$config            = $this->app['files']->getRequire($packageConfigFile);

		if (file_exists($userConfigFile)) {
			$userConfig = $this->app['files']->getRequire($userConfigFile);
			$config     = array_replace_recursive($config, $userConfig);
		}

		$this->app['config']->set('caffeinated::themes', $config);
	}

	/**
	 * Register the package services.
	 *
	 * @return void
	 */
	protected function registerServices()
	{
		$this->app->bindShared('themes', function($app) {
			return new Themes($app['files'], $app['config'], $app['view']);
		});

		$this->app->bindShared('themes.engine', function ($app) {
			$engine = ucfirst($this->app['config']->get('caffeinated::themes.engine'));

			return $app->make('\Caffeinated\Themes\Engines\\'.$engine.'Engine');
		});

		$this->app->bindShared('themes.components', function($app) {
			return new Components($app, $app['themes.engine']);
		});

		$this->app->booting(function($app) {
			$app['themes']->register();
		});
	}

	/**
	 * Configure Twig
	 *
	 * Registers the necessary Caffeinated Themes extensions and facades
	 * with Twig; only if Twig is set as the template engine.
	 *
	 * @return null
	 */
	protected function configureTwig()
	{
		$engine = $this->app['config']->get('caffeinated::themes.engine');
		
		if ($engine == 'twig') {
			$this->app['config']->push(
				'twigbridge.extensions.enabled',
				'Caffeinated\Themes\Twig\Extensions\Component'
			);
		}
	}
}
