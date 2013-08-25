<?php
/**
 * block_head
 * 
 * Define and inject items into page head
 * 
 * - Change to use 'title' and 'page-title' on <title> block
 * - Currently using XHTML style tags. (Future, change to HTML5)
 */
using_block('core/block_abstract');
class Block_head extends Block_abstract {
    
    private $_cssList = array();
    private $_jsList = array();
    private $_linkList = array();
    private $_styleList = array();
    private $_metaList = array();


    public function  __construct() {
        parent::__construct();
    }

    /**
     * Add CSS link to page header
     * @param string $href - URL to css
     * @param string $media - screen/print/etc
     */
    public function addCss($href,$media='screen') {
       $path = $this->getData('csspath') . '/' . $href;
       $link = '<link rel="stylesheet" auto="yes" type="text/css" href="'.$path.'.css" media="" />';

       $this->_cssList[] = $link;
    }
    
    /**
     * Add Javascript reverence
     * @param type $jsName 
     */
    public function addJs($jsName) {
       $path = $this->getData('jspath') . '/' . $jsName;
       //$link = '<link rel="stylesheet" auto="yes" type="text/css" href="'.$path.'.css" media="" />';
       $script = '<script type="text/javascript"  src="' .$path. '" ></script>';
       $this->_jsList[] = $script;
    }

    /**
     * Add Link element. (Defines relationship between document and external resource)
     * 
     * @param string $rel - Relationship type 
     * @param string $type - typically a mime type (ex: text/css)
     * @param string $href - uri of external resource
     */
    public function addLink($rel,$type,$href) {
        $link = '<link rel"' . $rel . '" type="' . $type . '" href="' . $href . '" />';
        $this->_linkList[] = $link;
    }

    public function addMeta($metadata) {
        $meta = '<meta ';        
        foreach($metadata as $_var=>$_value) {
            $meta .= $_var . '="' . $_value . '" ';
        }
        $meta .= ' />';
        $this->_metaList[] = $meta;
    }


    
    /**
     * Add manual style enteries. 
     * @param array $css - 1 line per element
     */
    public function addStyle($css) {
        foreach($css as $_item) {
            $this->_styleList[] = $_item . "\n";
        }
    }
    
        
    // Render Functions 
    private function renderList($list) {
        $html = '';
        foreach($list as $Link) {
            $html .= "\n" . $Link;
        }
        return $html;        
    }

    
    public function renderJs() { return $this->renderList($this->_jsList); }
    
    public function renderCss() {return $this->renderList($this->_cssList);   }   

    public function renderLinks() {return $this->renderList($this->_linkList);  }

    public function renderStyles() {
        $styles = '';
        if(!empty($this->_styleList)) {
            $styles = "<style>\n" . $this->renderList($this->_styleList) . "</style>\n";
        }
        return $styles;
    }
    
    public function renderMeta() {return $this->renderList($this->_metaList); }
    
    
    // Render Page header title.
    public function renderTitle() {
        $sep = '';
        $title = $this->getData('apptitle');
        $pagetitle = $this->getData('pagetitle');
        if(!empty($pagetitle)) $sep = '-';
         $html = "<title>$title $sep $pagetitle</title>";
         
         return $html;
    }
    
    protected function _toHTML() {
        // Render CSS and JS tags into data (As if they where child blocks)
        $this->_data['css'] = $this->renderCss();
        $this->_data['js'] = $this->renderJs();
        $this->_data['title'] = $this->renderTitle();
        $this->_data['links'] = $this->renderLinks();
        $this->_data['meta'] = $this->renderMeta();
        $this->_data['style'] = $this->renderStyles();
        
        // Render page via standard view engine logic (from core_block)
        $html = parent::_toHTML();
        
        return($html);
    }
}
