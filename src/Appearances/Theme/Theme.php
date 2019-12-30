<?php
namespace Wiratama\Appearances\Theme;

use File;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Theme implements Arrayable
{
    private $name;

    private $description;

    private $parent;

    private $path;

    public function __construct($name, $description, $path, $parent = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->parent = $parent;
        $this->path = $path;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function hasParent()
    {
        return !!$this->parent;
    }

    public function getAssetPath()
    {
        return Str::slug($this->getName());
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
