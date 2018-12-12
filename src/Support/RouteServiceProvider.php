<?php

namespace Caffeinated\Themes\Support;

use Caffeinated\Themes\Concerns\GetsManifest;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvider;

class RouteServiceProvider extends LaravelRouteServiceProvider
{
    use GetsManifest;
}
