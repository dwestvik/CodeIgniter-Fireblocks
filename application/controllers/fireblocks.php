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
    
}