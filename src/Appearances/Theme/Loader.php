<?php

namespace Wiratama\Appearances\Theme;

class Loader
{
    public function fromPath($path)
    {
        $themeJson = new Json($path);

        return new Theme(
            $themeJson->getJsonAttribute('name'),
            $themeJson->getJsonAttribute('description'),
            $path,
            $themeJson->getJsonAttribute('parent')
        );
    }

    public function fromCache(\stdClass $cache)
    {
        return new Theme(
            $cache->name,
            $cache->description,
            $cache->path,
            $cache->parent
        );
    }
}
