<?php

namespace CineFavela\Core\Response;

class Response
{
    public $template, $vars, $headers;

    public function __construct($template, $vars, $headers)
    {
        $this->template = $template;
        $this->vars = $vars;
        $this->headers = $headers;
    }
}

?>