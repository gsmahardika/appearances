<?php
namespace Wiratama\Appearances\Theme;

class AssetLocator
{
    private $url;

    private $urlGenerator;

    public function __construct($url, UrlGenerator $urlGenerator)
    {
        $this->url = $url;
        $this->urlGenerator = $urlGenerator;
    }


}
