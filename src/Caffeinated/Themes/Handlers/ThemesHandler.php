<?php

namespace Caffeinated\Themes\Handlers;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;

class ThemesHandler
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
	 * Constructor method.
	 *
	 * @param Filesystem $files
	 * @param Repository $confid
	 */
	public function __construct(Filesystem $files, Repository $config, ViewFactory $viewFactory)
	{
		$this->config       = $config;
		$this->files        = $files;
		$this->viewFactory  = $viewFactory;
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
	 * Gets themes path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path ?: $this->config->get('themes::config.path');
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
		return $this->active ?: $this->config->get('themes::config.active');
	}

	/**
	 * Gets active theme.
	 *
	 * @return Self
	 */
	public function setActive($theme)
	{
		$this->active = $theme;

		return $this;
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
	 * Render theme view file.
	 *
	 * @param string $view
	 * @param array $data
	 * @param array $mergeData
	 * @return View
	 */
	public function view($view, $data = array())
	{
		$themeView = $this->getThemeNamespace($view);

		if (class_exists('Caffeinated\Modules\Modules')) {
			if ( ! $this->viewFactory->exists($themeView)) {
				$viewSegments = explode('.', $view);

				if ($viewSegments[0] == 'modules') {
					$module = $viewSegments[1];

					$view = implode('.', array_slice($viewSegments, 2));

					$moduleView = "{$module}::{$view}";

					return $this->viewFactory->make($moduleView, $data);
				}
			} else {
				return $this->viewFactory->make($themeView, $data);
			}
		} else {
			return $this->viewFactory->make($themeView, $data);
		}		
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
	 * Register custom namespaces for specified theme.
	 *
	 * @param string $theme
	 * @return null
	 */
	public function registerNamespace($theme)
	{
		$this->viewFactory->addNamespace($theme, $this->getThemePath($theme).'views/');
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
}