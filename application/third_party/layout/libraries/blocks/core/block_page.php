<?php
/**
 * Block_page
 * 
 * Typical parent block for entire page 
 * Currently just implimentation of block_abstract
 *
 * @author dane.westvik
 */

using_block('core/block_abstract');
class Block_page extends Block_abstract {
        
    public function  __construct() {
        parent::__construct();
    }
}
