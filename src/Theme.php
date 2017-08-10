<?php

namespace Caffeinated\Themes;

use Illuminate\Support\Collection;
use Caffeinated\Themes\Traits\RegistersViewLocations;

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
        
        if (! $this->isCurrent($theme->get('slug')) and (! is_null($this->getCurrent()))) {
            $this->removeRegisteredLocation($theme, $parent);
        }
        
        $this->addRegisteredLocation($theme, $parent);
        
        $this->setCurrent($theme->get('slug'));
    }
    
    /**
     * Get the absolute path of the given theme file.
     *
     * @param  string  $file
     * @param  string  $theme
     * @return string
     */
    public function absolutePath($file = '', $theme = null)
    {
        if (is_null($theme)) {
            $theme = $this->getCurrent();
        }
        
        return config('themes.paths.absolute')."/$theme/$file";
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
        
        return config('themes.paths.base')."/$theme/$file";
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
}
