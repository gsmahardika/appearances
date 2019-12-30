<?php

namespace Wiratama\Appearances\Theme;

use Cache;
use Wiratama\Appearances\Theme\Exceptions\ThemeNotFoundException;
use Illuminate\Container\Container;

class Appearances
{
    private $cacheKey = 'appearances.themes';

    protected $themes = [];

    protected $activeTheme;

    private $themeLoader;

    private $app;

    private $view;

    public function __construct(Loader $themeLoader, Container $app)
    {
        $this->themeLoader = $themeLoader;
        $this->app = $app;
        $this->view = $this->app->make('view');
    }

    public function register(Theme $theme, $activate = false)
    {
        if (!$this->has($theme->getName())) {
            $this->themes[] = $theme;
        }

        if ($activate) {
            $this->activate($theme);
        }
    }

    public function registerPath($path, $activate = false)
    {
        $realPath = realpath($path);
        $theme = $this->themeLoader->fromPath($realPath);

        $this->register($theme, $activate);
    }

    public function registerPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->registerPath($path);
        }
    }

    public function activate($theme)
    {
        if (!$theme instanceof $theme) {
            $theme = $this->get($theme);
        }

        $this->activeTheme = $theme;

        $this->activateFinderPaths($theme);
    }

    protected function activateFinderPaths(Theme $theme)
    {
        $this->view->addLocation($theme->getPath().'/views/');

        if ($theme->hasParent()) {
            $this->activateFinderPaths($this->get($theme->getParent()));
        }
    }

    public function current()
    {
        return $this->activeTheme;
    }

    public function has($themeName)
    {
        foreach ($this->themes as $theme) {
            if ($theme->getName() == $themeName) {
                return true;
            }
        }

        return false;
    }

    public function get($themeName)
    {
        foreach ($this->themes as $theme) {
            if ($theme->getName() === $themeName) {
                return $theme;
            }
        }

        throw new ThemeNotFoundException($themeName);
    }

    public function themes()
    {
        return $this->themes;
    }

    public function discover($directory)
    {
        $searchString = $directory.'/theme.json';

        $files = str_replace('theme.json', '', $this->rglob($searchString));

        return $files;
    }

    protected function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);

        if ($files) {
            return $files;
        }

        $files = [];

        $possibleFiles = glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT);

        if ($possibleFiles === false) {
            $possibleFiles = [];
        }

        foreach ($possibleFiles as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

    public function cache(array $themes = [])
    {
        $cacheJson = [];

        foreach ($themes as $theme) {
            $cacheJson[] = $theme->toArray();
        }

        Cache::forever($this->cacheKey, json_encode($cacheJson));
    }

    public function clearCache()
    {
        Cache::forget($this->cacheKey);
    }

    public function setupFromCache()
    {
        if (!Cache::has($this->cacheKey)) {
            return;
        }

        $this->themes = [];
        $cachedThemes = json_decode(Cache::get($this->cacheKey));

        foreach ($cachedThemes as $cachedTheme) {
            $this->themes[] = $this->themeLoader->fromCache($cachedTheme);
        }
    }

    public function cacheKey()
    {
        return $this->cacheKey;
    }
}
