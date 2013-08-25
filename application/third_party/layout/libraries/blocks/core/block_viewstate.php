<?php
/**
 * Block_viewstate
 * 
 * This one allows state data to be rendered on a form (similar to ASPX pages)
 * 
 * TODO: Need lib to load the viewstate back into ... somthing.
 * 
 * @author dane.westvik
 */
include_once 'block_abstract.php';
class Block_viewstate extends Block_abstract {

    public function  __construct() {
        parent::__construct();
    }

    // Render text as body (No Sub blocks rendered)
    protected function  _toHTML() {
        // Render all data values into viewstate var
        $viewstate = '';
        
        foreach($this->_data as $_var=>$_value) {
            if(!empty($viewstate)) {
                $viewstate .= '&';
            }
            $viewstate .= $_var . '=' . $_value;
        }
        $this->_body = '<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="' . base64_encode($viewstate) . '" />';
        
        return($this->_body);
    }

    // Block can act like a string.
    public function  __toString() {
        return $this->toHTML();
    }

}
