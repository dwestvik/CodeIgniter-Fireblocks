<?php
/**
 * Block_html
 * 
 * Provides a 'wrapped' html output
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_html extends Block_abstract {

    public function  __construct() {
        parent::__construct();
        $vars = array('html'=>'','class'=>'','id'=>'','style'=>'','wrap'=>'div');
        foreach($vars as $_key=>$_value) {
            $this->_data[$_key] = $_value;
        }
    }

    // Process custom block attributes
    protected function _init($attr) {
        foreach($attr as $_attr=>$_value) {
            $this->_data[$_attr] = $_value;
        }
    }

    /**
     * Override of setName to also set the Wrapper ID 
     * @param string $name Block name to use as ID attribute for wrapper tag
     */
    public function setName($name) {
        parent::setName($name);
        $this->setId($name);
    }
    
    public function setText($txt) {
        $this->_data['html'] = htmlentities($txt);
    }

    public function addHtml($html) {
        $this->_data['html'] .= $html;
    }
        
    public function _toHtml() {
        $htmlout = '';
        $beg = ''; $end = '';
        $html = $this->_data['html'];

        if(!empty($this->_data['wrap'])) {
            $class = ((empty($this->_data['class']))? '' :' class="'. $this->_data['class'] .'" ');
            $id = ((empty($this->_data['id']))? '' :' id="'. $this->_data['id'] .'" ');
            $style = ((empty($this->_data['style']))? '' :' style="'. $this->_data['style'] .'" ');
            
            $tag = $this->_data['wrap'];
            $beg = '<' . $tag.$id.$class.$style . '>';
            $end = '</' . $tag . '>';
        }

        $htmlout = $beg.$html.$end;
        $this->_data['html_out'] = $htmlout;

        $this->setBody($htmlout);
        return(parent::_toHTML());
    }
}
