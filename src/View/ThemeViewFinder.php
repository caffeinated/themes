<?php

namespace Caffeinated\Themes\View;

use Theme;
use Illuminate\View\FileViewFinder;

class ThemeViewFinder extends FileViewFinder
{
    /**
     * Remove a location from the finder.
     *
     * @param  string  $location
     */
    public function removeLocation(string $location)
    {
        $key = array_search($location, $this->paths);
        
        if ($key) {
            unset($this->paths[$key]);
        }
    }
    
    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        list($namespace, $view) = $this->parseNamespaceSegments($name);
        
        $this->addThemeNamespaceHints($namespace);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }
    
    /**
     * Add namespace hints for the currently set theme.
     *
     * @param  string  $namespace
     * @return array
     */
    protected function addThemeNamespaceHints($namespace)
    {
        $theme = Theme::getCurrent();
        
        $hints   = array_reverse($this->hints[$namespace]);
        $hints[] = Theme::absolutePath('views/vendor/'.$namespace);
        
        if (class_exists(\Caffeinated\Modules\Modules::class)) {
            $hints[] = Theme::absolutePath('views/modules/'.$namespace);
        }
        
        $this->hints[$namespace] = array_reverse($hints);
    }
}
