<?php
/**
 * Layout engine drivers
 */
class Layout_engine extends CI_Driver_Library {
    
    public function __construct() {
        $this->valid_drivers = array(
            'layout_engine_debug',
            'layout_engine_template',
            'layout_engine_view',
            'layout_engine_smarty',
            'layout_engine_rain');
    }    
    
}