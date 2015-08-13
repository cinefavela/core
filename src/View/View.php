<?php 

namespace CineFavela\Core\View;

class View {
    
    private $template = null;
    private $children = array();
    
    public function add($child) {
        array_push($this->children, $child);        
    }
    
    public function toHtml() {
        
    }
}

?>