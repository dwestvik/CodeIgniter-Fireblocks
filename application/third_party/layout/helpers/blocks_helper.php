<?php
/**
 * Block Helpers
 */

function using_block($coreBlock) {
    $dir = dirname(dirname(__FILE__)) . '/libraries/blocks/';
    include_once $dir . $coreBlock . '.php';
    
}

function using_interface($interfaceName) {
    $dir = dirname(dirname(__FILE__)) . '/libraries/blocks/interfaces/';
    include_once $dir . $interfaceName . '.php';

}

function Block($blockname) {
    $ci = &get_instance();
    return $ci->layout->getBlock($blockname);
}
