<?php

namespace Caffeinated\Themes\Concerns;

trait RegistersViewLocations
{
    /**
     * Resolve and return the primary and parent themes.
     *
     * @param  string  $theme
     * @return array
     */
    protected function resolveTheme($theme)
    {
        $theme  = $this->where('slug', $theme)->first();
        $parent = null;

        if ($theme->has('parent')) {
            $parent = $this->where('slug', $theme->get('parent'))->first();
        }

        return [$theme, $parent];
    }

    /**
     * Remove the primary and parent theme from the view finder.
     *
     * @param  Manifest  $theme
     */
    protected function removeRegisteredLocation()
    {
        $current         = $this->where('slug', $this->getCurrent())->first();
        $currentLocation = config('themes.path').'/'.$this->format($current->get('slug')).'/resources/views';

        app('view.finder')->removeLocation($currentLocation);

        if ($current->has('parent')) {
            $parent         = $this->where('slug', $current->get('parent'))->first();
            $parentLocation = config('themes.path').'/'.$this->format($current->get('slug')).'/resources/views';
            app('view.finder')->removeLocation($parentLocation);
        }
    }

    /**
     * Register the primary and parent theme with the view finder.
     *
     * @param  Manifest  $theme
     * @param  Manifest  $parent
     */
    protected function addRegisteredLocation($theme, $parent)
    {
        if (! is_null($parent)) {
            $parentLocation = config('themes.path').'/'.$this->format($parent->get('slug')).'/resources/views';
            app('view.finder')->prependLocation($parentLocation);
        }

        $themeLocation = config('themes.path').'/'.$this->format($theme->get('slug')).'/resources/views';
        app('view.finder')->prependLocation($themeLocation);
    }

    /**
     * Format the name of the theme name to reference the correct directory.
     * 
     * @param  string  $name
     * @return string
     */
    protected function format($name)
    {
        return ucfirst(camel_case($name));
    }
}
