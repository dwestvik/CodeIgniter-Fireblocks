<?php
/**
 * page layout handler
 * @todo Refactor this
 * Handles the loading and rendering of page layouts.
 *
 * DW - Ibiza Version - New parm processing
 * DW - Lib Version - Defines Blocks as Lib items vs Models.
 */
class layout {

    public  $rootPage = '';
    private $pages;         // Pages Node of layout.xml file
    private $configXml;     // Config Node of layout.xml file
    private $layoutConfig;  // Config passed in constructor (from config/layout.php file)
    private $_macros = array();
    private $_sess = NULL;

    public function  __construct($cfg=array()) {
        // Load the blocks helper when layout is created.
        $ci = &get_instance();
        $ci->load->helper('blocks');
        $this->layoutConfig = $cfg; // Config passed or most likley from config/layout.php 
    }
    
    public function setConfig($cfg) {
        $this->layoutConfig = $cfg;
    }

   /**
     * Filter arguments to expand macros
     * @param type $arg
     */
    protected function _expand($pval) {
        // Check for internal macro expansions (From system config)
        $ci = &get_instance();
        if(substr($pval, 0,1) == '^') {
            // Try to get config data from layout.xml - else, from system config
            $tag = trim(substr($pval,1));
            @$temp = $this->configXml->$tag;
            if(empty($temp)) {$temp = $ci->config->item(trim(substr($pval,1)));}
            $pval = strval($temp);
        }

        // Session Data 
        if(substr($pval, 0,1) == '@') {
            // Get data from session
            $tag = trim(substr($pval,1));
            @$temp =  $ci->session->userdata($tag);
            if(empty($temp)) {$temp = '';}
            $pval = $temp;
        }
        
        // Now, look for macros
        if(substr($pval, 0,1) == '%') {
            // get value from macro array
            $tag = trim(substr($pval,1));
            @$temp = $this->_macros[$tag];
            if(empty($temp)) {$temp = '';}
            $pval = $temp;
        }
        
        
        return($pval);
    }
            
    
    /**
     * Set macro array
     * @param array $macs - key=macro / value=expanded value
     */
    public function setMacros($macs) {$this->_macros = $macs; }

    /**
     * Set Macro Value
     * @param type $_key
     * @param type $_value 
     */
    public function setMacro($_key,$_value) {$this->_macros[$_key] = $_value;}

    /**
     * Render the current layout (Starting at Root Page 
     */
    public function render() { 
        $ci =& get_instance();
        if(!empty($this->rootPage)) { 
            $ci->{$this->rootPage}->render();
        } else {
            die('Missing Root Layout');
            // Or.. Use below if you want execptions
            //throw new Exception('Root Layout not Set',9);
        }
    }

    // For now, just call the loadPage handler
    public function loadUpdates($handle,$layoutFile='layout') {
        $this->loadPage($handle,$layoutFile);
        return $this;
    }

    // New 'loadLayout' call. Now, just call 'loadPage'
    public function loadLayout($handle='default',$layoutFile='layout') {
        $this->loadPage($handle,$layoutFile);
        return $this;
    }
    
    /**
     * Load page layout from XML layout file
     *
     * @param <type> $handle
     * @param <type> $layoutFile
     */
    public function loadPage($handle='default',$layoutFile='layout') {
        //$this->rootPage = '';
        $basedir = dirname(dirname(__FILE__));
        
        // Path List
        $loadpaths[] = $basedir . '/etc/'.$layoutFile.'.xml';
        $loadpaths[] = 'Application/etc/'.$layoutFile.'.xml';
        $loadpaths[] = 'Application/etc/local.xml';

        // Search paths for layout file
        foreach($loadpaths as $_path) {
            if(file_exists($_path)) {
                $layoutXml = simplexml_load_file($_path);
                break;
            }
        }

        $pages = $layoutXml->pages;
	$this->pages = $pages;
	$this->configXml = $layoutXml->config;

        // get the root (page) block for that handle
        @$page = $layoutXml->pages->$handle;
        if(!empty($page)) {
            // Load blocks of the page
            $this->loadBlocks($page);
        } else {
            die('Layout for "'. $handle . '" Not Found');
        }
        
        return $this;
    }


   /**
     * Load blocks and sub-blocks. 
     * loadBlocks is called recursively to process child blocks/actions.
     *
     * @param simpleXml $xml This is the 'parent block definition'
     */
    public function loadBlocks($xml,$curr_block=null) {

        $ci =& get_instance();
	// Root /parent node
        if(is_object($curr_block)) {
            @$current_block_name = $curr_block->getName();
        }
		
	// Loop through each child tag
        foreach($xml->children() as $child_xml) {

            // Process node depending on it's type (Tag Name)
            $nodeTag = strtolower($child_xml->getName());
		
            // Call action in parent block - ^config_var used to pass config values
            if($nodeTag == 'action') {
                // Process sub-tags
                $methodArgs = array();
                // parse out xml tag attributes
                foreach( $child_xml->attributes() as $_name=>$actionArg) {
                    if($_name == 'method') {
                        $method = strval($actionArg);  
                    } else {
                        $argval = $this->_expand(strval($actionArg));
                        $methodArgs[$_name] = strval($argval);                 
                    } 
                }
                
                // parse out xml child tags - They are built into an array and passed to the function
                $childArgs = array();
                foreach($child_xml->children() as $_name=>$actionArg) {
                    $argval = $this->_expand(strval($actionArg));
                    $childArgs[$_name] = $this->_expand(strval($actionArg));
                }
                if(!empty($childArgs)) {$methodArgs['_childArgs'] = $childArgs;}
                
                // Check if Child Arg Tags 
                if(!empty($methodArgs)) {
                     call_user_func_array(array($curr_block,$method), $methodArgs);
                } else {
                     call_user_func_array(array($curr_block,$method));                    
                }
            }
            
            // Set property in parent block - (Allow config vars as 'action' above)
            // Update: dw - Jan2013 - build setter method to allow overloaded processing by block 
            if($nodeTag == 'property') {
                $pname = strval($child_xml['name']);
                $pval = $this->_expand(strval($child_xml['value']));
                $method = 'set'.ucfirst($pname);
                //$curr_block->setData($pname,$pval);
                $curr_block->$method($pval);
            }
			
            // Selects block for updates - Used to load update to a block layout
            // Blocks are just models so you they are in the CI 'object' path
            if($nodeTag == 'reference') {
                $refname = strval($child_xml['name']);
                $ref_blk = $ci->$refname;
                if(count($child_xml->children()) > 0) {
                    $this->loadBlocks($child_xml,$ref_blk);
                }                
            }

            // Define new block
            if($nodeTag == 'block') {
                
                // Extract block tag attributes                
                $type = strval($child_xml['type']); // Class type
                $name = strval($child_xml['name']); // Name of block

                @$template = strval($child_xml['template']);
                @$useEngine = strval($child_xml['engine']);
                @$blockAlias = strval($child_xml['alias']);

                
                
                // CREATE NEW BLOCK (Load the model)
                $type_path = 'blocks/' . $type;
//                $ci->load->model($type_path,$name);  
                $ci->load->library($type_path,'',$name);  
                $new_blk = $ci->$name;
                // Inject layout lib into block
                $new_blk->setLayoutLib($this);                

                // If rootPage not defined, set it to this block (The first block)
                if(empty($this->rootPage)) {
                    $this->rootPage = $name;
                    $new_blk->setSession($this->_sess);
                }
                
                
                // Set Block attributes
                $new_blk->setName($name);
                if(!empty($blockAlias)) { $new_blk->setAlias($blockAlias);}                
                if(!empty($template)) {$ci->$name->setTemplate($template);}
                if(!empty($useEngine)) {$ci->$name->setEngine($useEngine);}

                
                // Check if there is a current (parent) block.
                if(is_object($curr_block)) {
                    $curr_block->addChild($new_blk);
                    //echo $curr_block->getName() . '->' . $type_path . '-'.$new_blk->getName() . '<br/>' ;
                }
                
                // Assign block data from <block> attributes
                $new_blk->Init($child_xml->attributes());


                // If there are child tags on current block, load them.
                if(count($child_xml->children()) > 0) {
                    $this->loadBlocks($child_xml,$new_blk);
                }
            }

            // Bind a model to the block
            if($nodeTag == 'model') {
                $type = strval($child_xml['type']); // Class type
                $name = strval($child_xml['name']); // Name of block
                
                $ci->load->model($type,$name);
                if(is_object($curr_block)) {
                    $mdl = $ci->$name;
                    $curr_block->setModel($mdl);
                }
                // Check for child tags (May be actions or properties)
                if(count($child_xml->children()) > 0) {
                    $this->loadBlocks($child_xml,$mdl);
                }                                
            }
        }
    }
        

    /**
     * Create a new block
     * 
     * @param type $type
     * @param type $name
     * @param type $parent 
     */
    function createBlock($type,$name,$parent=null) {
        $ci = &get_instance();
        $ci->load->library('blocks/'.$type,'',$name);
        $blk = $ci->$name;
        $blk->setName($name);
        if(is_object($parent)) {$parent->addChild($blk);}
    }    

    /**
     * Find a block by name and return it's instance
     * (May need to return by reference...)
     *
     * @param string $blk
     * @return block
     */
    public function getBlock($blk) {
        $ci = & get_instance();

        return($ci->$blk);
    }

    /**
     * Return an array of block names loaded
     *
     * @return array
     */
    public function getBlockNames() {
        $blks = array();
        $ci = & get_instance();
        foreach($ci as $_value) {
            if(is_object($_value)) {
                if(is_a($_value,'core_block')) {
                    $blks[] = $_value->getName();
                }
            }
        }
        return($blks);
    }

    /**
     * Get array of block refernces  $blk[blockname] = $block_instance;
     *
     * @return array
     */
    public function getBlocks() {
        $blks = array();
        $ci = & get_instance();
        foreach($ci as $_value) {
            if(is_object($_value)) {
                if(is_a($_value,'core_block')) {
                    $blks[$_value->getName()] = $_value;
                }
            }
        }
        return($blks);
    }

    //********************* RENDERING ENGINES **************************
    public function callEngine($engine,$block) {
        $ci =& get_instance();        
        $ci->load->driver('layout_engine');
    
        $content = $ci->layout_engine->$engine->render($block);

        return $content;
    }
    

    /**
     * Return the raw block data Array
     * @param <type> $block 
     */
    public function xdataEngine($block) {
        $ci =& get_instance();
        $html = 'dataEngine: ';
        foreach($block->getDataArray() as $_key=>$_data) {
            $html .= $_data . ' ';
        }
        return $html;
    }

    
    // Set Engine in each child
    public function setChildEngines($block,$engine,$isChild=FALSE) {
        if($isChild) {$block->setEngine($engine);}
        foreach($block->getChildren() as $blk) {
            $this->setChildEngines($blk,$engine,TRUE);
        }
    }
}
