<?php
/**
 * Block Image
 * Defines an image block with optional link
 * 
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_img extends Block_abstract {
    private $_isSet = FALSE;
    private $_imgPath = '';

    public function  __construct() {
        parent::__construct();
        $ci =& get_instance();
        $vars = array('img_src','class','id','style','alt','title','href');
        foreach($vars as $_var) {
            $this->_data[$_var] = '';
        }
        $this->_imgPath = '';
    }

    // Process custom attributes
    public function _init($attr) {
        parent::_init($attr);
    }

    public function setSrc($img) {
        $this->_data['img_src'] = $img;
        $this->_isSet = TRUE;
        return $this;
    }
    
    public function getImg() {
        return($this->_toHtml());
    }
    
    /**
     * Override of setName to also set the Wrapper ID 
     * @param string $name Block name to use as ID attribute for wrapper tag
     */
    public function setName($name) {
        parent::setName($name);
        $this->setId($name);
    }    

    public function setPath($path='') {
        $this->_imgPath = $path . '/';
    }

    public function _toHtml() {
        $htmlout = '';
        $pre = ''; $post = '';
        if($this->_isSet) {
            if(substr($this->_data['img_src'],0,1) == '/') {
                $this->_imgPath = '';
            }
            if(!empty($this->_data['href'])) {
                $pre = '<a href="'.$this->_data['href'].'">';
                $post = '</a>';
            }
            
            $img = '<img src="'.$this->_imgPath . $this->_data['img_src'].'" ';
            $class = ((empty($this->_data['class']))? '' :' class="'. $this->_data['class'] .'" ');
            $alt = ((empty($this->_data['alt']))? '' :' alt="' . $this->_data['alt'] . '" ');
            $id = ((empty($this->_data['id']))? '' :' id="'. $this->_data['id'] .'" ');
            $title = ((empty($this->_data['title']))? '' : ' title="'. $this->_data['title'] .'" ');
            $style = ((empty($this->_data['style']))? '' :' style="'. $this->_data['style'] .'" ');
            $htmlout = $pre.$img.$id.$title.$class.$style.$alt.'/>'.$post;
        }
        
        return($htmlout);
    }

    
}
