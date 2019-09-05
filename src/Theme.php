<?php

namespace Caffeinated\Themes;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Caffeinated\Themes\Concerns\RegistersViewLocations;

class Theme extends Collection
{
    use RegistersViewLocations;

    /**
     * @var string
     */
    protected $current;

    /**
     * @var string|null
     */
    protected $layout = null;

    /**
     * Register and set the currently active theme.
     *
     * @param  string  $theme
     */
    public function set($theme)
    {
        list($theme, $parent) = $this->resolveTheme($theme);

        if (! $this->isCurrently($theme->get('slug')) and (! is_null($this->getCurrent()))) {
            $this->removeRegisteredLocation();
        }

        $this->setCurrent($theme->get('slug'));
        
        $this->registerAutoload($this->format($theme->get('slug')));
        $this->addRegisteredLocation($theme, $parent);
        $this->symlinkPublicDirectory();
        $this->registerServiceProvider($this->format($theme->get('slug')));
    }

    /**
     * Get the path of the given theme file.
     *
     * @param  string  $file
     * @param  string  $theme
     * @return string
     */
    public function path($file = '', $theme = null)
    {
        if (is_null($theme)) {
            $theme = $this->getCurrent();
        }

        $theme = $this->format($theme);

        return base_path("themes/{$theme}/{$file}");
    }

    /**
     * Get the layout property.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set the layout property.
     *
     * @param  string  $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    /**
     * Set the current theme property.
     *
     * @param  string  $theme
     */
    public function setCurrent($theme)
    {
        $this->current = $theme;
    }

    /**
     * Get the current theme property.
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Determine if the given theme is the currently set theme.
     *
     * @param  string  $theme
     * @return bool
     */
    public function isCurrently($theme)
    {
        return $this->current === $theme;
    }

    /**
     * Format the given name as the directory basename.
     * 
     * @param  string  $name
     * @return string
     */
    protected function format($name)
    {
        return ucfirst(Str::camel($name));
    }

    /**
     * Symlink the themes public directory so its accesible
     * by the web.
     * 
     * @return void
     */
    protected function symlinkPublicDirectory()
    {
        if (! file_exists(public_path('themes/'.$this->getCurrent()))) {
            if (! file_exists(public_path('themes'))) {
                app()->make('files')->makeDirectory(public_path('themes'));
            }

            app()->make('files')->link(
                $this->path('public'), public_path('themes/'.$this->getCurrent())
            );
        }
    }

    /**
     * Register the theme's service provider.
     * 
     * @param  string  $theme
     * @return void
     */
    protected function registerServiceProvider($theme)
    {
        app()->register("Themes\\$theme\\Providers\\ThemeServiceProvider");
    }

    /**
     * Register the themes path as a PSR-4 reference.
     * 
     * @param  string  $theme
     * @return void
     */
    protected function registerAutoload($theme)
	{
        $composer = require(base_path('vendor/autoload.php'));
        
        $class = 'Themes\\'.$theme.'\\';
        $path  = $this->path('src/');

        if (! array_key_exists($class, $composer->getClassMap())) {
            $composer->addPsr4($class, $path);
        }
	}
}
