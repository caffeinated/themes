<?php
namespace Caffeinated\Themes\Twig\Extensions;

use Caffeinated\Themes\Themes as ThemeHandler;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

class Themes extends Twig_Extension
{
	/**
	 * @var Caffeinated\Themes\Themes
	 */
	protected $theme;

	/**
	 * Create a new Themes Twig_Extension instance.
	 *
	 * @param Caffeinated\Themes\Themes  $theme
	 */
	public function __construct(ThemeHandler $theme)
	{
		$this->theme = $theme;
	}

	public function getName()
	{
		return 'Caffeinated_Themes_Extension_Themes';
	}

	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction('theme_asset', [$this->theme, 'asset']),
			new Twig_SimpleFunction('theme_secure_asset', [$this->theme, 'secureAsset']),
		];
	}
}
