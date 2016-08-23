<?php

namespace Caffeinated\Themes;

use URL;
use Caffeinated\Themes\Exceptions\FileMissingException;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;

class Themes
{
	/**
	 * @var string
	 */
	protected $active;

	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var string
	 */
	protected $layout;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var View
	 */
	protected $viewFactory;

	/**
	 * Constructor method.
	 *
	 * @param Filesystem  $files
	 * @param Repository  $config
	 * @param ViewFactory $viewFactory
	 */
	public function __construct(Filesystem $files, Repository $config, ViewFactory $viewFactory)
	{
		$this->config      = $config;
		$this->files       = $files;
		$this->viewFactory = $viewFactory;
	}

	/**
	 * Register custom namespaces for all themes.
	 *
	 * @return null
	 */
	public function register()
	{
		foreach ($this->all() as $theme) {
			$this->registerNamespace($theme);
		}
	}

	/**
	 * Register custom namespaces for specified theme.
	 *
	 * @param string $theme
	 * @return null
	 */
	public function registerNamespace($theme)
	{
		$this->viewFactory->addNamespace($theme, $this->getThemePath($theme).'views');
	}

	/**
	 * Get all themes.
	 *
	 * @return Collection
	 */
	public function all()
	{
		$themes = [];

		if ($this->files->exists($this->getPath())) {
			$scannedThemes = $this->files->directories($this->getPath());

			foreach ($scannedThemes as $theme) {
				$themes[] = basename($theme);
			}
		}

		return new Collection($themes);
	}

	/**
	 * Check if given theme exists.
	 *
	 * @param  string $theme
	 * @return bool
	 */
	public function exists($theme)
	{
		return in_array($theme, $this->all()->toArray());
	}

	/**
	 * Gets themes path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path ?: $this->config->get('themes.paths.absolute');
	}

	/**
	 * Sets themes path.
	 *
	 * @param string $path
	 * @return self
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Gets active theme.
	 *
	 * @return string
	 */
	public function getActive()
	{
		return $this->active ?: $this->config->get('themes.active');
	}

	/**
	 * Sets active theme.
	 *
	 * @return Themes
	 */
	public function setActive($theme)
	{
		$this->active = $theme;

		return $this;
	}

	/**
	 * Get theme layout.
	 *
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Sets theme layout.
	 *
	 * @return Themes
	 */
	public function setLayout($layout)
	{
		$this->layout = $this->getView($layout);

		return $this;
	}

	/**
	 * Gets the given view file.
	 *
	 * @param  string  $view
	 * @return string|null
	 */
	public function getView($view)
	{
		$activeTheme = $this->getActive();
		$parent      = $this->getProperty($activeTheme.'::parent');

		$views = [
			'theme'  => $this->getThemeNamespace($view),
			'parent' => $this->getThemeNamespace($view, $parent),
			'module' => $this->getModuleView($view),
			'base'   => $view
		];

		foreach ($views as $view) {
			if ($this->viewFactory->exists($view)) {
				return $view;
			}
		}

		return false;
	}

	/**
	 * Render theme view file.
	 *
	 * @param string $view
	 * @param array $data
	 * @return View
	 */
	public function view($view, $data = array())
	{
		if (! is_null($this->layout)) {
			$data['theme_layout'] = $this->getLayout();
		}

		return $this->viewFactory->make($this->getView($view), $data);
	}

	/**
	 * Checks if the given view file exists (anywhere).
	 *
	 * @param  string  $view
	 * @return bool
	 */
	public function viewExists($view)
	{
		return ($this->getView($view)) ? true : false;
	}

	/**
	 * Return a new theme view response from the application.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function response($view, $data = array(), $status = 200, array $headers = array())
	{
		return new Response($this->view($view, $data), $status, $headers);
	}

	/**
	 * Gets the specified themes path.
	 *
	 * @param string $theme
	 * @return string
	 */
	public function getThemePath($theme)
	{
		return $this->getPath()."/{$theme}/";
	}

	/**
	 * Get path of theme JSON file.
	 *
	 * @param  string $theme
	 * @return string
	 */
	public function getJsonPath($theme)
	{
		return $this->getThemePath($theme).'/theme.json';
	}

	/**
	 * Get theme JSON content as an array.
	 *
	 * @param  string $theme
	 * @return array|mixed
	 */
	public function getJsonContents($theme)
	{
		$theme = strtolower($theme);

		$default = [];

		if ( ! $this->exists($theme))
			return $default;

		$path = $this->getJsonPath($theme);

		if ($this->files->exists($path)) {
			$contents = $this->files->get($path);

			return json_decode($contents, true);
		} else {
			$message = "Theme [{$theme}] must have a valid theme.json manifest file.";

			throw new FileMissingException($message);
		}
	}

	/**
	 * Set theme manifest JSON content property value.
	 *
	 * @param  string $theme
	 * @param  array  $content
	 * @return integer
	 */
	public function setJsonContents($theme, array $content)
	{
		$content = json_encode($content, JSON_PRETTY_PRINT);

		return $this->files->put($this->getJsonPath($theme), $content);
	}

	/**
	 * Get a theme manifest property value.
	 *
	 * @param  string      $property
	 * @param  null|string $default
	 * @return mixed
	 */
	public function getProperty($property, $default = null)
	{
		list($theme, $key) = explode('::', $property);

		return array_get($this->getJsonContents($theme), $key, $default);
	}

	/**
	 * Set a theme manifest property value.
	 *
	 * @param  string $property
	 * @param  mixed  $value
	 * @return bool
	 */
	public function setProperty($property, $value)
	{
		list($theme, $key) = explode('::', $property);

		$content = $this->getJsonContents($theme);

		if (count($content)) {
			if (isset($content[$key])) {
				unset($content[$key]);
			}

			$content[$key] = $value;

			$this->setJsonContents($theme, $content);

			return true;
		}

		return false;
	}

	/**
	 * Generate a HTML link to the given asset using HTTP for the
	 * currently active theme.
	 *
	 * @return string
	 */
	public function asset($asset)
	{
		$segments = explode('::', $asset);
		$theme    = null;

		if (count($segments) == 2) {
			list($theme, $asset) = $segments;
		} else {
			$asset = $segments[0];
		}

		return url($this->config->get('themes.paths.base').'/'
			.($theme ?: $this->getActive()).'/'
			.$this->config->get('themes.paths.assets').'/'
			.$asset);
	}

	/**
	 * Generate a HTML link to the given asset using HTTPS for the
	 * currently active theme.
	 *
	 * @return string
	 */
	public function secureAsset($asset)
	{
		return preg_replace("/^http:/i", "https:", $this->asset($asset));
	}

	/**
	 * Get the specified themes View namespace.
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getThemeNamespace($key, $theme = null)
	{
		if (is_null($theme)) {
			return $this->getActive()."::{$key}";
		} else {
			return $theme."::{$key}";
		}
	}

	/**
	 * Get module view file.
	 *
	 * @param  string $view
	 * @return null|string
	 */
	protected function getModuleView($view)
	{
		if (class_exists('Caffeinated\Modules\Modules')) {
			$viewSegments = explode('.', $view);

			if ($viewSegments[0] == 'modules') {
				$module = $viewSegments[1];
				$view   = implode('.', array_slice($viewSegments, 2));

				return "{$module}::{$view}";
			}
		}

		return null;
	}
}
