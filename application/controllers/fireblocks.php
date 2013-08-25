<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Called via  http://localhost/fireblock/index.php/fireblocks/ 
 */
class Fireblocks extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Use config/autoload 
        // $autoload['packages'] = array(APPPATH.'third_party'.'/layout/');
        // $autoload['libraries'] = array('layout');
        $this->load->add_package_path(APPPATH.'third_party'.'/layout/');
        $this->load->library('layout');  // Can be autoloaded.
        $this->load->config('layout');  // Can be autoloaded.
    }
    
    
    public function index() {
        $this->layout->loadLayout();
        Block('content')->setBody('<h1>Content Block</h1><a href="fireblocks/cms">CMS Page</a>');
        Block('footer')->addText(' ' . $this->config->item('layout_version'));
        $this->layout->render();
    }
    
    public function cms() {
        $this->layout->loadLayout()->loadUpdates('home_page');
        Block('content')->setBody('<h1>CMS Demo Page</h1>');
        Block('msg')->addWarning('Warning Message in "msg" block');
        Block('cmscontent')->setContentId('home_page');
        Block('footer')->addText(' ' . $this->config->item('layout_version'));
        $this->layout->render();
    }
    
    /* Demo manual creation of blocks (ie: no layout.xml) 
     * You still need the layout library in order to use render engines
     * 
     * Here is the layout.xml that could do the following
     * <manual_demo_handle>
     *    <block type="core/block_view" name="page" template="manual_demo">
     *       <block type="core/block_view" name="content">
     *           <action method="setBody" body="This is the content block." />
     *       </block>
     *    </block>
     * </manual_demo_handle>
     * 
     * Loaded via $this->layout->loadLayout('manual_demo_handle');
     */
    public function manual() {
        
        // Create blocks by hand
        $this->load->library('blocks/core/block_view','','page');
        $this->load->library('blocks/core/block_view','','content');

        // page block is using template so it needs to reference layout lib. to use render engines.
        $this->page->setLayoutLib($this->layout);
        
        // Set internal block names (Used to reference child block in template)
        $this->content->setName('content');
        
        // Make 'content' a child of 'page' block
        $this->page->addChild($this->content);
        
        // set the 'page' template
        $this->page->setTemplate('templates/manual_demo');

        // Set some content directly into the 'content' (child) block
        $this->content->setBody('This is the content block.');
        
        // Block('content')->setBody('Body set via Helper');
        
        // Render the 'page' block (See manual_demo template how it outputs the 'content' child block)
        $this->page->render();
    }
}