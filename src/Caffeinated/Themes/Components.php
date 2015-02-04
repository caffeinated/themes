<?php
namespace Caffeinated\Themes;

use Closure;
use Caffeinated\Themes\Engines\Engine;
use Illuminate\Container\Container;
use Illuminate\View\Compilers\BladeCompiler;

class Components
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var BladeCompiler
	 */
	protected $blade;

	/**
	 * @var Engine
	 */
	protected $engine;

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
	 * @param Container     $container
	 * @param BladeCompiler $blade
	 */
	public function __construct(Container $container, BladeCompiler $blade)
	{
		$this->container = $container;
		$this->blade     = $blade;
	}

	/**
	 * Register a new component.
	 *
	 * @param  string          $name
	 * @param  string|callable $callback
	 * @return void
	 */
	public function register($name, $callback)
	{
		$this->components[$name] = $callback;

		$this->registerTag($name, 'Component::');
	}

	/**
	 * Register Blade syntax for a specific component.
	 *
	 * @param  string $method
	 * @param  string $namespace
	 * @return void
	 */
	protected function registerTag($method, $namespace = '')
	{
		$this->blade->extend(function($view, $compiler) use ($method, $namespace) {
			$pattern = $compiler->createMatcher('component_'.$method);

			$replace = '$1<?php echo '.$namespace.$method.'$2; ?>';

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

		$this->registerTag($name, 'Component::');
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
	 * @return null|string
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