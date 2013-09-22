<?php
/**
 * Block_Text
 * Used to render plain text.
 * You must use the 'setText' method to load the text into the block.
 *
 * When rendered (via _toHTML), the text will be returned directly.
 * This only holds a single string value (ie: This is not a collection).
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_text extends Block_abstract {

    public function  __construct() {
        parent::__construct();
        $this->setText('');
    }

    public function prependText($txt) {
        $this->setText($txt . $this->getText());
    }
    
    public function addText($txt) {
            $this->setText($this->getText() . $txt);
    }
    
    public function getText() {
        return $this->getData('text');
    }

    public function setText($txt) {
        $this->setData('text', $txt);
    }

    // Render text as body (No Sub blocks rendered)
    protected function  _toHTML() {
        $this->_body = $this->getText();
        return($this->_body);
    }
}
