<?php
/**
 * Block_cms - Render content provided by a ContentProvider model.
 *
 * @author dane.westvik
 */
using_interface('iContentProvider');
using_block('core/block_abstract');
class Block_cms extends Block_abstract {
    
    public function  __construct() {
        parent::__construct();
        $this->_data['precontent'] = '';
        $this->_data['cmscontent'] = '';
        $this->_data['postcontent'] = '';
        $this->_data['contentid'] = '';
        $this->_data['_wrap'] = 'div';
        $this->_data['_class'] = 'cms_block';
        
    }

    /**
     * Sets the block model - requires model to impl iContentProvider interface.
     * @param iContentProvider $cmsMdl
     */
    public function setModel(iContentProvider $cmsMdl) {
        parent::setModel($cmsMdl);
    }

    public function setPreContent($html) {
        $this->_data['precontent'] = $html;
    }

    public function setContentId($id) {
        $this->_data['contentid'] = $id;
    }

    public function setPostContent($html) {
        $this->_data['postcontent'] = $html;
    }

    public function setWrap($tag='div') {
        $this->_data['_wrap'] = $tag;
    }

    public function setClass($cls) {
        $this->_data['_class'] = $cls;
    }
   
    
    // Render text 
    protected function  _toHTML() {
        // Render content into _data array as 'cmscontent'
        if(!empty($this->_model)) {
            $this->setData('cmscontent', $this->_model->getContent( $this->_data['contentid'] ) );
        }

        $beg = ''; $end = '';
        if(!empty($this->_data['_wrap'])) {
            $beg = '<' . $this->_data['_wrap'] . ' id="cms_' . $this->_data['contentid'] . '" class="'.$this->_data['_class'].'" >';
            $end = '</' . $this->_data['_wrap'] . '>';
        }

        // Build body - Sets default output.
        if(empty($this->_template)) {
            $this->appendBody($beg);
            $this->appendBody($this->_data['precontent']);
            $this->appendBody($this->_data['cmscontent']);
            $this->appendBody($this->_data['postcontent']);
            $this->appendBody($end);
        } else {
            // Render Template - Output found in _body var
            // This also allows child blocks if you wish.
            parent::_toHTML();
        }

        //$html = parent::_toHTML();
        return($this->getBody());
    }
}
