<?php
namespace Wiratama\Appearances\Theme;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) return $path;

        $root = $this->getRootUrl($this->getScheme($secure));
        $theme = Appearances::current();
        return $this->removeIndex($root).'/themes/'.$theme->getPath().'/'.trim($path, '/');
    }
}
