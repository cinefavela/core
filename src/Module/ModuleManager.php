<?php
namespace CineFavela\Core\Module;

class ModuleManager
{

    const MIGRATION_INSTALL_PREFIX = 'mysql-install-';

    const MIGRATION_UPDATE_PREFIX = 'mysql-update-';

    private $container = null;

    private $modules = array();

    public function init()
    {
        $modules = require_once (APPLICATION_PATH . '/modules/modules.php');
        
        foreach ($modules as $module) {
            $moduleClass = "\\CineFavela\\" . $module . "\\Module";
            $this->register(new $moduleClass());
        }
    }

    private function getContainer()
    {
        if ($this->container == null) {
            $this->container = \CineFavela\Core\Application::getContainer();
        }
        
        return $this->container;
    }

    private function setUpModule($module)
    {
        $this->configuresDatabase($module);
        $this->configuresRoute($module);
        $this->configuresTemplate($module);
    }

    private function configuresDatabase($module)
    {
        $mapper = $this->getContainer()->mapper;
        
        $installedModule = $mapper->module(array(
            'name' => $module->getName()
        ))
            ->fetch();
        
        if (! $installedModule) {
            $this->installModuleDatabase($module);
        } else {
            $this->updateModuleDatabase($module, $installedModule->version);
        }
    }

    private function getMigrationDir()
    {
        return APPLICATION_PATH . '/modules/' . ucfirst($this->getName()) . '/migration/';
    }

    private function installModuleDatabase($module)
    {
        $conn = $this->getContainer()->conn;
        
        $migrationFileToInstallDatabase = $this->getMigrationDir() . self::MIGRATION_INSTALL_PREFIX . $this->getVersion() . '.php';
        
        if (file_exists($migrationFileToInstallDatabase)) {
            $conn->query(require_once ($migrationFileToInstallDatabase));
        }
        
        $conn->query("INSERT INTO module VALUES (\"" . $this->getName() . "\",\"" . $this->getVersion() . "\")");
    }

    private function updateModuleDatabase($module, $from)
    {
        if ($from < $module->getVersion()) {
            $conn = $this->getContainer()->conn;
            
            $migrationFilToUpgradeDatabase = $this->getMigrationDir() . self::MIGRATION_UPDATE_PREFIX . $this->getVersion() . '.php';
            
            if (file_exists($migrationFileToUpgradeDatabase)) {
                $conn->query(require_once ($migrationFileToUpgradeDatabase));
            }
            
            $conn->query("UPDATE module SET version=\"" . $this->getVersion() . "\" WHERE name=\"" . $this->getName() . "\"");
        }
    }

    private function configuresRoute($module)
    {
        if (method_exists($module, "getRouteConfig")) {
            $container = $this->getContainer();
            $router = $container->router;
            $routeConfig = $module->getRouteConfig();
            $routes = $routeConfig["routes"];
            
            foreach ($routes as $route) {
                $router->$route["method"]($route["path"], $route["controller"]);
            }
        }
    }

    private function configuresTemplate($module)
    {
        if (method_exists($module, "getTemplateConfig")) {
            $container = $this->getContainer();
            $view = $container->twig;
            $viewLoader = $view->getLoader();
            
            $templateConfig = $module->getTemplateConfig();
            $templateDir = $templateConfig["templateDir"];
            foreach ($templateDir as $dir) {
                $viewLoader->addPath($dir);
            }
        }
    }

    private function initModule($module)
    {
        if (method_exists($module, "init")) {
            $module->init();
        }
    }

    private function register(Module $module)
    {
        $modules[$module->getName()] = $module;
        $this->setUpModule($module);
        $this->initModule($module);
    }

    public function getModule($name)
    {
        return $modules[$name];
    }
}

?>