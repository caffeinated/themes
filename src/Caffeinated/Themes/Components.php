<?php
namespace Caffeinated\Themes;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Illuminate\View\Compilers\BladeCompiler;

class Components
{
	/**
	 * @var BladeCompiler
	 */
	protected $blade;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $groups = array();

	/**
	 * @var array
	 */
	protected $components = array();

	/**
	 * Constructor method.
	 *
	 * @param BladeCompiler $blade
	 * @param Container     $container
	 */
	public function __construct(BladeCompiler $blade, Container $container)
	{
		$this->blade     = $blade;
		$this->container = $container;
	}

	/**
	 * Register a new component.
	 *
	 * @param  string         $name
	 * @param  strin|callable $callback
	 * @return void
	 */
	public function register($name, $callback)
	{
		$this->components[$name] = $callback;

		$this->registerBlade($name);
	}

	/**
	 * Register Blade syntax for a specific component.
	 *
	 * @param  string $name
	 * @return void
	 */
	protected function registerBlade($name)
	{
		$this->blade->extend(function($view, $compiler) use ($name) {
			$pattern = $compiler->createMatcher($name);

			$replace = '$1<?php echo ThemeComponent::'.$name.'$2; ?>';

			return preg_replace($pattern, $replace, $view);
		});
	}

	/**
	 * Determine whether a component exists or not.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function exists($name)
	{
		return array_key_exists($name, $this->components);
	}

	/**
	 * Call a specific component.
	 *
	 * @param  string $name
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function call($name, array $parameters = array())
	{
		if ($this->groupExists($name)) return $this->callGroup($name, $parameters);

		if ($this->exists($name)) {
			$callback = $this->components[$name];

			return $this->getCallback($callback, $parameters);
		}

		return null;
	}

	/**
	 * Get a callback from a specific component.
	 *
	 * @param  mixed $callback
	 * @param  array $parameters
	 * @return mixed
	 */
	protected function getCallback($callback, array $parameters)
	{
		if ($callback instanceof Closure) {
			return $this->createCallableCallback($callback, $parameters);
		} elseif (is_string($callback)) {
			return $this->createStringCallback($callback, $parameters);
		} else {
			return null;
		}
	}

	/**
	 * Get a result from a string callback.
	 *
	 * @param  string $callback
	 * @param  array  $parameters
	 * @return mixed
	 */
	protected function createStringCallback($callback, array $parameters)
	{
		if (function_exists($callback)) {
			return $this->createCallableCallback($callback, $parameters);
		}		
	}

	/**
	 * Get a result from a callable callback.
	 *
	 * @param  callable $callback
	 * @param  array    $parameters
	 * @return mixed
	 */
	protected function createCallableCallback($callback, array $parameters)
	{
		return call_user_func_array($callback, $parameters);
	}

	/**
	 * Create a new component group.
	 *
	 * @param  string $name
	 * @param  array  $components
	 * @return void
	 */
	public function group($name, array $components)
	{
		$this->groups[$name] = $components;

		$this->registerBlade($name);
	}

	/**
	 * Determine whether a group of components exists or not
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function groupExists($name)
	{
		return array_key_exists($name, $this->groups);
	}

	/**
	 * Call a specific group of components.
	 *
	 * @param  string $name
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function callGroup($name, $parameters = array())
	{
		if (! $this->groupExists($name)) return null;

		$result = '';

		foreach ($this->groups[$name] as $key => $component) {
			$result .= $this->call($component, array_get($parameters, $key, array()));
		}

		return $result;
	}

	/**
	 * Handle magic __call methods against the class.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters = array())
	{
		return $this->call($method, $parameters);
	}
}