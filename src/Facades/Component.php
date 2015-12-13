<?php
namespace Caffeinated\Themes\Facades;

use Illuminate\Support\Facades\Facade;

class Component extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'themes.components';
	}
}