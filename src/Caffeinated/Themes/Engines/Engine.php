<?php
namespace Caffeinated\Themes\Engines;

interface Engine
{
	public function registerCustomTag($method, $namespace);
}