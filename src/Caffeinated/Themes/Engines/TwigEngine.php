<?php
namespace Caffeinated\Themes\Engines;

use TwigBridge\Twig\Loader;

class TwigEngine implements Engine
{
	public function registerCustomTag($method, $namespace = '')
	{
		// Handled automatically within the package's registration method
	}
}