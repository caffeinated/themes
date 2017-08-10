<?php

namespace Caffeinated\Themes\Traits;

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
    protected function removeRegisteredLocation($theme)
    {
        $current         = $this->where('slug', $this->getCurrent())->first();
        $currentLocation = config('themes.paths.absolute').'/'.$current->get('slug').'/views';
        app('view.finder')->removeLocation($themeLocation);
        
        if ($current->has('parent')) {
            $parent         = $this->where('slug', $current->get('parent'))->first();
            $parentLocation = config('themes.paths.absolute').'/'.$current->get('slug').'/views';
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
            $parentLocation = config('themes.paths.absolute').'/'.$parent->get('slug').'/views';
            app('view.finder')->prependLocation($parentLocation);
        }
        
        $themeLocation = config('themes.paths.absolute').'/'.$theme->get('slug').'/views';
        app('view.finder')->prependLocation($themeLocation);
    }
}
