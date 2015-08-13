<?php
namespace CineFavela\Core\Module;

class ModuleManager
{

    private $container = null;

    private $modules = array();

    private function getContainer()
    {
        if ($this->container == null) {
            $this->container = \CineFavela\Core\Application::getContainer();
        }
        
        return $this->container;
    }

    private function setUpModule($module)
    {
        $this->configuresRoute($module);
        $this->configuresTemplate($module);
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

    public function register(Module $module)
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