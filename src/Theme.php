<?php

namespace Caffeinated\Themes;

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
     * Create a new collection.
     *
     * @param  mixed  $items
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * Register and set the currently active theme.
     *
     * @param  string  $theme
     */
    public function set($theme)
    {
        list($theme, $parent) = $this->resolveTheme($theme);

        if (! $this->isCurrent($theme->get('slug')) and (! is_null($this->getCurrent()))) {
            $this->removeRegisteredLocation();
        }

        $this->addRegisteredLocation($theme, $parent);

        $this->setCurrent($theme->get('slug'));
    }

    /**
     * Get the relative path of the given theme file.
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

        return config('themes.path')."/$theme/$file";
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
        
        $theme = ucfirst($theme);

        if (! file_exists(public_path('themes/'.$this->current))) {
            if (! file_exists(public_path('themes'))) {
                app()->make('files')->makeDirectory(public_path('themes'));
            }

            app()->make('files')->link(
                $this->path('public'), public_path('themes/'.$this->current)
            );
        }

        app()->register("Themes\\$theme\\Providers\\ThemeServiceProvider");
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
    public function isCurrent($theme)
    {
        return $this->current === $theme;
    }

    /**
     * Format the given name as the directory basename.
     * 
     * @param  string  $name
     * @return string
     */
    private function format($name)
    {
        return ucfirst(camel_case($name));
    }
}
