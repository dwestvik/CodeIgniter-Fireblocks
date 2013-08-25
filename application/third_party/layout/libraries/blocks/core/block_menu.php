<?php
/**
 * Block_menu
 * View block for menus
 *
 * Generate menu in code. (No Template)
 * Menu format can be changed via configuration (using setters)
 *
 * @author dane.westvik
 */
using_interface('iMenuProvider');
using_block('core/block_abstract');
class  Block_menu extends Block_abstract {

    //Define default menu tags and class
    private $_itemClass = 'nav';
    private $_wrap = 'span';
    private $_wrapClass = '';
    private $_wrapClassActive = 'active';
    private $_itemFormat = '<a href="%s" class="%s">%s</a>';
    private $_text;

    public function  __construct() {
        parent::__construct();
        $this->_view = 'templates/navbar';
        $this->_menu = array();
    }

    protected  function  _init($attr) {
        if(!empty($attr['menu'])) {$this->selectMenu($attr['menu']); }
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
        $this->_data['menu'] = $menu;
    }

    // Sets right hand text
    public function setMenuText($txt) {
        $this->_text = $txt;
    }

    // Sets class for each menu item
    public function setMenuItemClass($class) {
        $this->_itemClass = $class;
    }

    public function setWrap($wrap) {
        $this->_wrap = $wrap;
    }

    public function setWrapClass($class) {
        $this->_wrapClass = $class;
    }

    public function setFormat($fmt) {
        $this->_itemFormat = $fmt;
    }

    // Render HTML directly from code (No Templates)
    public function _toHtml() {
        if(empty($this->_data['menu'])) {
            $this->_data['menu'] = $this->_model->getMenuContent($this->_data['menuname']);
        }
        $wrap_b = '';
        $wrap_e = '';
        if(!empty ($this->_wrap)) {
            $wrap_b = '<' . $this->_wrap . ' class="'.$this->_wrapClass.'">';
            $wrap_b_active = '<' . $this->_wrap . ' class="'.$this->_wrapClassActive.'">';
            $wrap_e = '</' . $this->_wrap . '>';
        }
        $html = '';
        foreach($this->_data['menu'] as $_key=>$_value):
            $menuItem = sprintf($this->_itemFormat,$_key,$this->_itemClass,$_value);
            $html .= $wrap_b . $menuItem  .$wrap_e;
        endforeach;
        $html .= '<span class="text">' . $this->_text . '</span>';

        //$html = parent::_toHTML();
        return($html);
    }
}

