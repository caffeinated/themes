<?php

namespace Caffeinated\Themes;

use Illuminate\Support\Collection;

class Theme extends Collection
{
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
        
        if (! $this->isCurrent($theme->get('slug')) and (! is_null($this->getCurrent()))) {
            $this->removeRegisteredLocation($theme, $parent);
        }
        
        $this->addRegisteredLocation($theme, $parent);
        
        $this->setCurrent($theme->get('slug'));
    }
    
    /**
     * Set or fetch the default layout.
     *
     * @param  null|string  $layout
     * @return string|void
     */
    public function layout($layout = null)
    {
        if (is_null($layout)) {
            return $this->layout;
        }
        
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
    public function isCurrent($theme)
    {
        return $this->current === $theme;
    }
    
    /**
     * Get the absolute path of the given theme.
     *
     * @param  string  $theme
     * @return string
     */
    public function getAbsolutePath($theme)
    {
        return config('themes.paths.absolute').'/'.$theme;
    }
    
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
