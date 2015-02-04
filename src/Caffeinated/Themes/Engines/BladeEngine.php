<?php
namespace Caffeinated\Themes\Engines;

use Illuminate\View\Compilers\BladeCompiler;

class BladeEngine implements Engine
{
	/**
	 * @var BladeCompiler
	 */
	protected $blade;

	public function __construct(BladeCompiler $blade)
	{
		$this->blade = $blade;
	}

	public function registerCustomTag($method, $namespace = '')
	{
		$this->blade->extend(function($view, $compiler) use ($method, $namespace) {
			$pattern = $compiler->createMatcher('component_'.$method);

			$replace = '$1<?php echo '.$namespace.$method.'$2; ?>';

			return preg_replace($pattern, $replace, $view);
		});
	}
}