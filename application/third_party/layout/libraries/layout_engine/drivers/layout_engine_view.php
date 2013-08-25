<?php
/**
 * View layout driver engine
 * 
 * Used to render using default CI view system
 */
class Layout_engine_view extends CI_Driver {
    
    /**
     * Driver function - render
     * 
     * @param core_block $block
     * @return text - HTML output from driver
     */
    public function render($block) {
        $ci =& get_instance();
        $data = array();

        // Build the view data. Block data array + reference to the block itself
        // Add extra data. Do this here incase core->_toHTML is overridden
        
        // Shorthand access to block resources.
        $data['B'] = $block;
        $data['M'] = $block->getModel();
        $data['D'] = $block->getDataArray();        
        
        // Alternate values to access block resources
        $data['_block'] = $block;
        $data['_model'] = $block->getModel();
        $data['_data'] = $block->getDataArray();
        $tpl = $block->getTemplate();
        if(empty($tpl)) {exit("NO TEMPLATE FOR VIEW");}
        $html = $ci->load->view($block->getTemplate(),$data,true); 
        return $html;
        
    }
}
