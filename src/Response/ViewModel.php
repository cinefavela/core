<?php
namespace CineFavela\Core\Response;

class ViewModel extends Response
{
    
    private $viewEngine;
    
    public function __construct($template, $vars) {
        $this->viewEngine = \CineFavela\Core\Application::getViewEngine();
        parent::__construct($template, $vars, null);
    }
    
    public function render() {
        return $this->viewEngine->render($this->template, $this->vars);
    }
    
}

?>