<?php
class Layout_engine_debug extends CI_Driver {
    
    public function render($block) {
        $ci =& get_instance();
        $html = "debugEngine: <pre>\n";
        foreach($block->getDataArray() as $_key=>$_data) {
            $html .= '<hr/><h1>[' . $_key . ']</h1><br/>'. htmlentities($_data);
            $html .= "\n";
        }
        return $html . '</pre>';        
    }
}