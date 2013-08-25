<?php
/**
 * Block_container
 * This has no rendering support for templates.
 * This will call each child, in order and render the output.
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_container extends Block_abstract {
        
    public function  __construct() {
        parent::__construct();
    }

    public function _toHTML() {
        $html = '';
        foreach($this->_blockList as $_name=>$_blk) {
                $blkname = $_blk->getAlias();
                $this->appendBody($this->getChildHtml($_name));
        }
        
        return $this->_body;
    }
}
