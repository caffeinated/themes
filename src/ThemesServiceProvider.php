<?php
namespace Caffeinated\Themes;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Caffeinated\Themes\Facades\Theme;

class ThemesServiceProvider extends ServiceProvider {

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

        Blade::directive('includeFromTheme', function($view) {
            $currentView =Theme::partialView($view);
            return  "<?php echo \$__env->make( '$currentView', array_except(get_defined_vars(), array('__data', '__path')) )->render(); ?>";


        });
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
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return ['themes'];
	}

	/**
	 * Register the package services.
	 *
	 * @return void
	 */
	protected function registerServices()
	{
		$this->app->singleton('themes', function($app) {
			return new Themes($app['files'], $app['config'], $app['view']);
		});

		$this->app->booting(function($app) {
			$app['themes']->register();
		});
	}
}
