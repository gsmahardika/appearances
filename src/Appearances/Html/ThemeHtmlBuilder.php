<?php
namespace Wiratama\Appearances\Html;

use Collective\Html\HtmlBuilder;
use Wiratama\Appearances\Facades\AppearancesFacade;
use Illuminate\Routing\UrlGenerator;

class ThemeHtmlBuilder
{
    private $html;

    private $url;

    public function __construct(HtmlBuilder $html, UrlGenerator $url)
    {
        $this->html = $html;
        $this->url = $url;
    }

    public function script($url, $attributes = array(), $secure = null)
    {
        return $this->html->script($this->assetUrl($url), $attributes, $secure);
    }

    public function style($url, $attributes = array(), $secure = null)
    {
        $styles = [];
        $theme = AppearancesFacade::current();

        if ($theme->hasParent()) {
            $parent = AppearancesFacade::get($theme->getParent());
            AppearancesFacade::activate($parent);
            $styles[] = $this->style($url, $attributes, $secure);
            AppearancesFacade::activate($theme);
        }

        $styles[] = $this->html->style($this->assetUrl($url), $attributes, $secure);

        return implode("\n", $styles);
    }

    public function image($url, $alt = null, $attributes = array(), $secure = null)
    {
        return $this->html->image($this->assetUrl($url), $alt, $attributes, $secure);
    }

    public function url($file = '')
    {
        return url($this->assetUrl($file));
    }

    public function linkAsset($url, $title = null, $attributes = array(), $secure = null)
    {
        return $this->html->linkAsset($this->assetUrl($url), $title, $attributes, $secure);
    }

    protected function assetUrl($url)
    {
        if ($this->url->isValidUrl($url)) {
            return $url;
        }
        $theme = AppearancesFacade::current();

        if ($theme) {
            $themePath = $theme->getAssetPath();
            $url = "themes/$themePath/$url";
        }

        return $url;
    }
}
