<?php
/**
 * rain layout driver engine example.
 * You must put the Rain.TPL php file in libraries.
 * 
 * Used to render using Rain.TPL render system
 */
class Layout_engine_rain extends CI_Driver {
    
    /**
     * Driver function - render
     * 
     * @param core_block $block
     * @return text - HTML output from driver
     */
    public function render($block) {
        $ci =& get_instance();
        $ci->load->library('raintpl');
                
        // Build the view data. Block data array + reference to the block itself
        $data = $block->getDataArray();
        $data['block'] = $block;
        $tpl = $block->getTemplate();
        //$rain = new RainTPL();
        RainTPL::configure('tpl_dir','application/views/');
        RainTPL::configure( 'tpl_ext', 'rain' );

        $ci->raintpl->assign($data);
        $html = $ci->raintpl->draw($tpl, $return_string=true);

        return $html;        
    }
}
