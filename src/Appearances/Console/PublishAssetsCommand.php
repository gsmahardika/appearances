<?php

namespace Wiratama\Appearances\Console;

use Wiratama\Appearances\Theme\Theme;
use Appearances;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\Console\Input\InputArgument;

class PublishAssetsCommand extends Command
{
    protected $name = 'appearances:publish';

    protected $description = 'Publish themes assets.';

    public function handle()
    {
        $this->setupThemes();
        $this->publishAssets();

        $this->info('Assets published.');
    }

    protected function setupThemes()
    {
        $this->laravel['events']->dispatch('stylist.publishing');

        $themes = Stylist::themes();

        foreach ($themes as $theme) {
            $path = $theme->getPath();

            if ($this->laravel['files']->exists($path.'assets/')) {
                $this->laravel['stylist']->registerPath($path);
            }
        }
    }

    protected function publishAssets()
    {
        $themes = $this->laravel['stylist']->themes();
        $requestedTheme = $this->argument('theme');

        if ($requestedTheme) {
            $theme = $this->laravel['stylist']->get($requestedTheme);

            return $this->publishSingle($theme);
        }

        foreach ($themes as $theme) {
            $this->publishSingle($theme);
        }
    }

    protected function publishSingle(Theme $theme)
    {
        $themePath = public_path('themes/' . $theme->getAssetPath());

        $this->laravel['files']->copyDirectory($theme->getPath().'/assets/', $themePath);

        $this->info($theme->getName().' assets published.');
    }

    public function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'Name of the theme you wish to publish']
        ];
    }

}
