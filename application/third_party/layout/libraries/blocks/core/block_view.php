<?php
/** 
 * Block_view
 * Basic template view.
 * Same as page which just sub-classes Block_abstract... For now
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_view extends Block_abstract {
        
    public function  __construct() {
        parent::__construct();
    }
}
