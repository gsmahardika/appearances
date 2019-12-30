<?php

namespace Wiratama\Appearances\Theme;

use File;
use Wiratama\Appearances\Theme\Exceptions\ThemeJsonNotFoundException;

class Json
{
    private $themePath;

    private $json;

    public function __construct($themePath)
    {
        $this->themePath = $themePath;
    }

    public function getJson()
    {
        if ($this->json) {
            return $this->json;
        }

        $themeJsonPath = $this->themePath.'/theme.json';

        if (!File::exists($themeJsonPath)) {
            throw new ThemeJsonNotFoundException($this->themePath);
        }

        return $this->json = json_decode(File::get($themeJsonPath));
    }

    public function getJsonAttribute($attribute)
    {
        $json = $this->getJson();

        if (isset($json->$attribute)) {
            return $json->$attribute;
        }

        return null;
    }
}
