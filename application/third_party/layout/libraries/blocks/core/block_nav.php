<?php
/**
 * Block_nav
 * View block for menus
 *
 * Generate menu in code. (No Template)
 * Menu format can be changed via configuration (using setters)
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
using_interface('iMenuProvider');
class Block_nav extends Block_abstract {

    // Define default menu tags and class

    protected $_itemWrap = 'span';
    protected $_itemClass = 'nav_item';
    protected $_itemClassActive = 'active';    
    
    protected $_menuWrap = 'div';    
    protected $_menuClass = 'nav_wrapper';


    protected $_text;
    protected $_menu;

    public function  __construct() {
        parent::__construct();
        //$this->_view = 'templates/navbar';
        $this->_menu = array();
    }

    protected  function  _init($attr) {
        if(!empty($attr['menu'])) {$this->selectMenu($attr['menu']); }
    }
    
    protected function _formatItem($href,$txt) {
        $format = '<a href="%s" class="%s">%s</a>';
        $a_class = '';
        $menuItem = sprintf($format,$href,$a_class,$txt);
        return $menuItem;
    }

    // Make sure the model impliments the iMenuProvoder interface.
    public function setModel(iMenuProvider $mdl) {
        parent::setModel($mdl);
    }

    public function selectMenu($mnu) {
        $this->_data['menuname'] = $mnu;
    }
    // set Menu array (url => title)
    public function setMenu($menu) {
        $this->_menu = $menu;
    }

    // Sets right hand text
    public function setMenuText($txt) {
        $this->_text = $txt;
    }

    // Sets class for each menu item
    public function setMenuItemClass($class) {
        $this->_itemClass = $class;
    }
    
    public function setMenuItemWrap($wrap) {
        $this->_itemWrap = $wrap;
    }

    public function setWrap($wrap) {
        $this->_menuWrap = $wrap;
    }

    public function setWrapClass($class) {
        $this->_menuClass = $class;
    }

    public function setFormat($fmt) {
        $this->_itemFormat = $fmt;
    }

    // Render HTML directly from code (No Templates)
    public function _toHtml() {
        // Check if menu data set, if not, call model to get the menu content.
        if(empty($this->_data['menu'])) {
            $this->_data['menu'] = $this->_model->getMenuContent($this->_data['menuname']);
        }
        
        // Set wrap begin and end values for menu and items.
        $wrap_b = '';
        $wrap_e = '';
        if(!empty ($this->_menuWrap)) {
            $wrap_b = '<' . $this->_menuWrap . ' class="'.$this->_menuClass.'">';
            $wrap_e = '</' . $this->_menuWrap . '>';
        }
        
        // Set item wraps
        $item_b = '';
        $item_b_active = '';
        $item_e = '';
        if(!empty ($this->_itemWrap)) {
            $item_b = '<' . $this->_itemWrap . ' class="'.$this->_itemClass.'">';
            $item_b_active = '<' . $this->_itemWrap . ' class="'.$this->_itemClassActive.'">';                    
            $item_e = '</' . $this->_itemWrap . '>';
        }
        
        // Render the menu. Start with menu wrap begin (wrap_b)
        $html = $wrap_b;
        // Render menu items (Key = href / value = prompt)
        foreach($this->_data['menu'] as $_key=>$_value):
            $html .= $item_b . $this->_formatItem($_key, $_value)  .$item_e;
        endforeach;
        $html .= '<span class="text">' . $this->_text . '</span>';
        $html .= $wrap_e;

        //$html = parent::_toHTML();
        return($html);
    }
}

