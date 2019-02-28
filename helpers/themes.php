<?php

if (!function_exists('theme_path')) {
    /**
     * Return the path to the given theme file.
     *
     * @param  string  $file
     * @param  string  $theme
     * @return string
     */
    function theme_path($file = '', $theme = null)
    {
        return Theme::path($file, $theme);
    }
}

if (!function_exists('theme_asset')) {
    /**
     * Return the asset url to the given theme file.
     *
     * @param  string  $file
     * @param  string  $theme
     * @return string
     * @author Ahmet Bora <byybora@gmail.com>
     */
    function theme_asset($file = '', $theme = null)
    {
        $theme = $theme ?? Theme::getCurrent();
        
        return asset("themes/{$theme}/{$file}");
    }
}
