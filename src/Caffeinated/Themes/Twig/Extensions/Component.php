<?php
namespace Caffeinated\Themes\Twig\Extensions;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

class Component extends Twig_Extension
{
	public function getName()
	{
		return 'component';
	}

	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction('component_*', function ($name) {
					$arguments = array_slice(func_get_args(), 1);

					return \Component::call($name, $arguments);
				}, ['is_safe' => ['html']]
			),
		];
	}
}
