<?php

namespace Caffeinated\Themes;

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
}
