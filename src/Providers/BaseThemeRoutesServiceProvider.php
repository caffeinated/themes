<?php

namespace Caffeinated\Themes\Providers;


use Caffeinated\Themes\Traits\GetsManifest;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class BaseThemeRoutesServiceProvider extends RouteServiceProvider
{
    use GetsManifest;
}
