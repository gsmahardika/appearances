<?php

namespace Wiratama\Appearances\Theme\Exceptions;

class ThemeNotFoundException extends \Exception
{
	public function __construct($themeName)
    {
        $this->message = "Theme [$themeName] is not registered in Appearances.";
    }
}