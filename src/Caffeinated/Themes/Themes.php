<?php

namespace Caffeinated\Themes;

use Caffeinated\Themes\Handlers\ThemesHandler;

class Themes
{
	/**
	 * @var ModulesHandler
	 */
	protected $handler;

	/**
	 * Constructor method.
	 *
	 * @param ModulesHandler $handle
	 * @param Repository $config
	 * @param Translator $lang
	 */
	public function __construct(ThemesHandler $handler)
	{
		$this->handler = $handler;
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
	protected function registerNamespace($theme)
	{
		$this->handler->registerNamespace($theme);
	}

	/**
	 * Get all themes.
	 *
	 * @return Collection
	 */
	public function all()
	{
		return $this->handler->all();
	}

	/**
	 * Get themes path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->handler->getPath();
	}

	/**
	 * Set themes path in "RunTime" mode.
	 *
	 * @return string
	 */
	public function setPath($path)
	{
		return $this->handler->setPath($path);
	}

	/**
	 * Gets active theme.
	 *
	 * @return string
	 */
	public function getActive()
	{
		return $this->handler->getActive();
	}

	/**
	 * Sets active theme during runtime.
	 *
	 * @return Self
	 */
	public function setActive($theme)
	{
		return $this->handler->setActive($theme);
	}

	/**
	 * Render view from defined theme.
	 */
	public function view($view, $data = array(), $mergeData = array())
	{
		return $this->handler->view($view, $data, $mergeData);
	}
}