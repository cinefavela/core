<?php
namespace CineFavela\Core;

class Application
{

    protected static $container = null;

    public static function getContainer()
    {
        if (is_null(self::$container)) {
            $config = APPLICATION_PATH . '/config/config.ini';
            self::$container = new \Respect\Config\Container($config);
        }
        
        return self::$container;
    }

    public static function getModuleManager()
    {
        return self::getContainer()->moduleManager;
    }

    public static function getRouter()
    {
        return self::getContainer()->router;
    }

    public static function getEntityManager()
    {
        return self::getContainer()->mapper;
    }

    public static function getViewEngine()
    {
        return self::getContainer()->twig;
    }

    public static function init()
    {
        self::getModuleManager()->init();
    }
}