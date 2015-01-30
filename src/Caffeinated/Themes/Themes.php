<?php
namespace Caffeinated\Themes;

use Caffeinated\Themes\Engines\Engine;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;

class Themes
{
	/**
	 * @var Repository
	 */
	protected $config;
	
	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var View
	 */
	protected $viewFactory;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $active;

	/**
	 * @var string
	 */
	protected $layout;

	/**
	 * @var string
	 */
	protected $layoutView;

	/**
	 * Constructor method.
	 *
	 * @param Filesystem  $files
	 * @param Repository  $config
	 * @param ViewFactory $viewFactory
	 * @param Engine      $engine
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
		return $this->path ?: $this->config->get('caffeinated.themes.path');
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
		return $this->active ?: $this->config->get('caffeinated.themes.active');
	}

	/**
	 * Sets active theme.
	 *
	 * @return Self
	 */
	public function setActive($theme)
	{
		$this->active = $theme;

		return $this;
	}

	/**
	 * Sets theme layout.
	 *
	 * @return Self
	 */
	public function setLayout($layout)
	{
		$this->layout = $this->getThemeNamespace($layout);

		return $this;
	}

	/**
	 * Setup layout.
	 *
	 * @return null
	 */
	protected function setupLayout()
	{
		if (! is_null($this->layout)) {
			$this->layoutView = $this->viewFactory->make($this->layout);
		}
	}

	/**
	 * Render theme view file.
	 *
	 * @param string $view
	 * @param array $data
	 * @param array $mergeData
	 * @return View
	 */
	public function view($view, $data = array())
	{
		$this->setupLayout();

		$viewNamespace = $this->getThemeNamespace($view);

		$this->autoloadComponents($this->getActive());

		// Caffeinated Modules support
		if (class_exists('Caffeinated\Modules\Modules')) {
			if ( ! $this->viewFactory->exists($viewNamespace)) {
				$viewSegments = explode('.', $view);

				if ($viewSegments[0] == 'modules') {
					$module        = $viewSegments[1];
					$view          = implode('.', array_slice($viewSegments, 2));
					$viewNamespace = "{$module}::{$view}";
				}
			}
		}

		return $this->renderView($viewNamespace, $data);
	}

	/**
	 * Renders the defined view.
	 *
	 * @param  string $view
	 * @param  mixed  $data
	 * @return viewFactory
	 */
	protected function renderView($view, $data)
	{
		$engine = $this->config->get('caffeinated.themes.engine');

		if (! is_null($this->layout) and $engine == 'blade') {
			return $this->layoutView->nest('child', $view, $data);
		} elseif(! is_null($this->layout) and $engine == 'twig') {
			$data['layout'] = $this->layout;
		}

		return $this->viewFactory->make($view, $data);
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
	 * Get the specified themes View namespace.
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getThemeNamespace($key)
	{
		return $this->getActive()."::{$key}";
	}

	/**
	 * Autoload a themes compontents file.
	 *
	 * @param  string $theme
	 * @return null
	 */
	protected function autoloadComponents($theme)
	{
		$path               = $this->getPath();
		$themePath          = $path.'/'.$theme;
		$componentsFilePath = $themePath.'/components.php';

		if (file_exists($componentsFilePath)) {
			include ($componentsFilePath);
		}		
	}
}
