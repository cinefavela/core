<?php
namespace CineFavela\Core\Module;

abstract class Module implements ModuleInterface
{
   

    protected $name;

    protected $version;

    protected $container;

    public function __construct($name, $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getContainer()
    {
        if (is_null($this->container)) {
            $this->container = \CineFavela\Core\Core::container();
        }
        
        return $this->container;
    }
}

?>