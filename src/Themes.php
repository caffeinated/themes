<?php

namespace Caffeinated\Themes;

use URL;
use Caffeinated\Themes\Exceptions\FileMissingException;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


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
     * If the view name first token is a "module" namespace (f.e: sitepages::pages.about)
     * the search will be like:
     *          * Current Theme views folder
     *          * Parent theme views folder
     *          * Module theme views folder
     *          * Default views folder
     * The first found will be returned
     *
     * @param  string  $view
     * @return string|null
     */
    public function getView($view)
    {
        $viewPosibleLocations = $this->getAllPosibleViews($view);

        foreach ($viewPosibleLocations as $view) {
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


    public function partialView($view, $data = array())
    {
        Log::debug("Themes:partialView : INIT", [$view]);
        $segments = explode('::', $view);
        $theme    = null;
        $parentTheme=null;
        $grandParentTheme=null;

        if (count($segments) == 2  ) {
            list($theme, $viewName) = $segments;
            if($theme=="Theme"){
                $viewPosibleLocations = $this->getAllPosibleViews($viewName);

                foreach ($viewPosibleLocations as $view) {
                    if ($this->viewFactory->exists($view)) {
                        Log::debug("\tfoundView: ", [$view]);
                        return $view;
                    }
                }
                Log::debug("\tfoundView: ", [false]);
                return false;
            }
        }
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
    public function asset($asset, $version = null, $buildPath = 'build')
    {
        $segments = explode('::', $asset);
        $source    = null;

        if(!isset($version)) {
            $version = Config::get('basebone.general.enable_version_system');
        }

        //This function allows the search of assets in the current theme and it parent (if exists)
        //TODO: Added recursive call to get all ancestors of a theme
        if (count($segments) == 2  ) {
            list($source, $asset) = $segments;
            // If first asset source is Theme::, asset comes from the Themes module,
            // In any other case, the asset comes from the module
            // 3-level search is implemented and working.
            if ($source == "Theme") {

                $currentTheme =  $this->getActive();
                $parentTheme = $this->getProperty($currentTheme.'::parent');
                $grandParentTheme = $this->getProperty($parentTheme.'::parent');
                $themes = array();

                array_push($themes, $currentTheme);
                isset($parentTheme) ? array_push($themes, $parentTheme) : null;
                isset($grandParentTheme) ? array_push($themes, $grandParentTheme) : null;

                foreach($themes as $theme){
                    // Add a
                    $themeAssetURL = $this->config->get('themes.paths.base').'/'.$theme .'/'.
                                        $this->config->get('themes.paths.assets').'/'.$asset;
                    $themeAssetPath = public_path().'/'.$themeAssetURL;
                    $assetPossibleLocation[$theme] = [$themeAssetURL, $themeAssetPath];
                }

                foreach($assetPossibleLocation as $location){
                    if(file_exists($location[1])) {
                        $themeAssetURL = $location[0];
                        break;
                    }
                }

            } else {
                // TODO: IMPLEMENT SEARCH FOR MODULE ASSETS GIVING PRIORITY TO ASSETS FROM THEMES MODULE
                $themeAssetURL = $asset;
            }
        } else {
            $themeAssetURL = $segments[0];
        }

        // If versioning is active, use the processed asset route as a parameter to elixir, which will return
        // the versioned file route from the assets manifest on /public/build/rev-manifest.json
        // If versioning is not active, encapsulate URL using url() in order to get the absolute URL instead
        // of relative to the view requesting the asset
        if($version) {
            $themeAssetURL = elixir($themeAssetURL, $buildPath);
        } else {
            $themeAssetURL = url($themeAssetURL);
        }


        return $themeAssetURL;

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

    protected function getThemeModuleNamespace($key, $theme = null)
    {
        $key =str_replace("::",".",$key);
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

    protected function getAllPosibleViews($view){
        $themeView = null;
        $parentThemeView= null;
        $grandParentThemeView= null;

        Log::debug("getView INIT",[$view]);
        $activeTheme = $this->getActive();


        //THEME VIEW
        $themeView =  $this->getThemeNamespace($view);
        Log::debug("\tTheme: $activeTheme");
        Log::debug("\t\tThemeView: $themeView");


        $parentTheme      = $this->getProperty($activeTheme.'::parent');
        Log::debug("\tParentTheme: $parentTheme");


        if($parentTheme != null){
            $parentThemeView = $this->getThemeNamespace($view, $parentTheme);
            Log::debug("\t\tParentThemeView: $parentThemeView");
            $grandParentTheme=  $this->getProperty($parentTheme.'::parent');
            if($grandParentTheme!=null) {
                Log::debug("\tgrandParentTheme: $grandParentTheme");
                $grandParentThemeView = $this->getThemeNamespace($view, $grandParentTheme);
                Log::debug("\t\tgrandParentView: $grandParentThemeView");
            }
        }

        $newViews= [];
        if($themeView!=null){
            $newViews['themeView'] =$themeView;
        }
        if($parentThemeView!=null){
            Log::debug("\tParent Theme View: $parentThemeView");
            $newViews['parentThemeView'] =$parentThemeView;
        }
        if($grandParentThemeView!=null){
            Log::debug("\tGrandparent Theme View: $grandParentThemeView");
            $newViews['grandParentThemeView'] =$grandParentThemeView;
        }

        $moduleView=  $this->getModuleView($view);
        if($moduleView){
            $newViews['moduleView'] =$moduleView;
        }

        $themeModuleView= $this->getThemeModuleNamespace($view);
        if($themeModuleView){
            $newViews['themeModuleView'] =$themeModuleView;

        }

        if($parentTheme){
            $parentThemeModuleView= $this->getThemeModuleNamespace($view, $parentTheme);
            if($parentThemeModuleView){
                $newViews['parentThemeModuleView'] =$parentThemeModuleView;
            }
        }

        $newViews['view'] = $view;

        return $newViews;

    }

}
