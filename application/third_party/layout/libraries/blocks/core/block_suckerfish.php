<?php
/**
 * Block_suckerfish
 * View block for menus
 *
 * Generate menu in code. (No Template)
 * Menu format can be changed via configuration (using setters)
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
using_interface('iMenuProvider');
class Block_suckerfish extends Block_abstract {

    // Define default menu tags and class
    private $_itemClass = 'nav';
    private $_wrap = 'div';
    private $_wrapClass = 'nav_bar';
    private $_wrapClassActive = 'active';
    private $_itemFormat = '<a href="%s" class="%s">%s</a>';
    private $_text;
    private $_menu;

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

    public function setWrap($wrap) {
        $this->_wrap = $wrap;
    }

    public function setWrapClass($class) {
        $this->_wrapClass = $class;
    }

    public function setFormat($fmt) {
        $this->_itemFormat = $fmt;
    }

    public function _toHtml() {
        $menu = $this->_model->getMenuContent($this->_data['menuname']);
        $beg = ''; $end = '';
        if(!empty($this->_wrap)) {
            $beg = '<' . $this->_wrap . ' id="' . $this->getAlias() . '" class="'. $this->_wrapClass .'" >';
            $end = '</' . $this->_wrap . '>';
        }
        
       // $html = '<!-- Auto Generated -->' . "\n";
        $html .= $beg;
        $html .= '<ul id="sddm">';
        $mcount = 0;
        foreach($menu as $_prompt=>$_link) {
            if(is_array($_link)) {
                $mcount++;
                $html .= '<li><a href="#" ';
                $html .= 'onmouseover="mopen(' . "'m" . $mcount . "'" . ')" ' ;
                $html .= 'onmouseout="mclosetime()" ' ;
                $html .= '>'. $_prompt . '</a>';
                $html .= '<div id="m' . $mcount . '"';
                $html .= 'onmouseover="mcancelclosetime()" onmouseout="mclosetime()" > ';

                foreach($_link as $_subprompt=>$_sublink) {
                    $html .= '<a href="' . $_sublink . '">' . $_subprompt . '</a>' . "\n";
                }
                $html .= '</div>';
                $html .= '</li>';
            } else {
                $html .= '<li><a href="' . $_link . '">' . $_prompt . '</a></li>' . "\n";
                $html .= "\n";
            }
        }
        //$html .= '<li>what<div><span class="ssdmtext">' . $this->_text . '</span></div></li>';
        $html .= '<li class="navtext"><span class="navtext">' . $this->_text . '</span></li>' . "\n";
        $html .= '</ul>' . $end;
        $html .= '<div style="clear:both"></div>';

        return $html;
    }

    // Render HTML directly from code (No Templates)
    public function x_toHtml() {
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

