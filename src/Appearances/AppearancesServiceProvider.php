<?php
namespace Wiratama\Appearances;

use Cache;
use Config;
use Wiratama\Appearances\Html\ThemeHtmlBuilder;
use Wiratama\Appearances\Theme\Loader;
use Wiratama\Appearances\Theme\Appearances;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AppearancesServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        'Collective\Html\HtmlServiceProvider'
    ];

    public function register()
    {
        parent::register();

        $this->registerConfiguration();
        $this->registerAppearances();
        $this->registerAliases();
        $this->registerThemeBuilder();
        $this->registerCommands();
    }

    public function boot()
    {
        $this->bootThemes();
    }

    protected function bootThemes()
    {
        $appearances = $this->app['appearances'];
        $paths = $this->app['config']->get('appearances.themes.paths', []);

        foreach ($paths as $path) {
            $themePaths = $appearances->discover($path);
            $appearances->registerPaths($themePaths);
        }

        $theme = $this->app['config']->get('appearances.themes.activate', null);

        if (!is_null($theme)) {
            $appearances->activate($theme, true);
        }
    }

    protected function registerAppearances()
    {
        $this->app->singleton('appearances', function($app)
        {
            return new Appearances(new Loader, $app);
        });
    }

    protected function registerThemeBuilder()
    {
        $this->app->singleton('appearances.theme', function($app)
        {
            return new ThemeHtmlBuilder($app['html'], $app['url']);
        });
    }

    private function registerAliases()
    {
        $aliasLoader = AliasLoader::getInstance();

        $aliasLoader->alias('Appearances', 'Wiratama\Appearances\Facades\AppearancesFacade');
        $aliasLoader->alias('Theme', 'Wiratama\Appearances\Facades\ThemeFacade');

        $this->app->alias('appearances', 'Wiratama\Appearances\Theme\Appearances');
    }

    private function registerCommands()
    {
        $this->commands(
            'Wiratama\Appearances\Console\PublishAssetsCommand'
        );
    }

    protected function registerConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('appearances.php')
        ], 'appearances');
    }

    public function provides()
    {
        return array_merge(parent::provides(), [
            'Appearances',
            'Theme'
        ]);
    }

}
