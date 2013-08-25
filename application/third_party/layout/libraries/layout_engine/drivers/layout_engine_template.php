<?php

class Layout_engine_template extends CI_Driver {
    
    public function render($block) {
       $ci =& get_instance();
        $ci->load->library('parser');
        $html = $ci->parser->parse($block->getTemplate().'.tpl',$block->getDataArray(),true);
        return $html;
    }
}
