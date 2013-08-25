<?php
/**
 * Block_abstract
 *
 * A block is a rendering interface between a model and view.
 * This is the 'code behind' object for indivudial output areas.
 *
 * A block can represent a page structure area (like a column or main content)
 * or an single content item within a structure area.
 *
 * The primary output method is the toHTML() function. This is called to render
 * all output for the block. If there are child blocks, each child block
 * toHTML will be called in sequence and the resulting html saved in the parents
 * _data storage indexed by the child block name/alias. Then the parent block
 * is rendered. .
 *
 * @author dane.westvik
 * Update: 1-20-12: return $this for method chaining (php 5).
 *  - Clean up calls to render children to route through single child (getHtml) function that has hook handling.
 *  - Added _onRender(html) hook
 * 
 * New version... Jan 2013
 * Define a library (vs model)
 * 
 * * Future Version - Add Block cache
 */
class Block_abstract {
    protected $_blockname = 'core';
    protected $_alias = '';
    protected $_enabled = TRUE;

    protected $_data = array();
    protected $_template ='';
    protected $_engine = 'view';
    protected $_layoutLib = null;


    protected $_blockList = array();    // Child Blocks
    protected $_model;      // Block Model
    protected $_parent = NULL;

    protected $_body = '';
    protected $_attr = array();     // layout.xml extra <block> attribute values (Custom attributes)
    
    protected $_session = NULL;

    public function   __construct() {
        //parent::__construct();
        //$this->_layoutLib = $this->layout;  // Bind CI layout lib here.
        //$this->_blockname = strtolower(get_class($this));
    }

    public function setLayoutLib($lib) {
        $this->_layoutLib = $lib;
    }
    
    // Protected Functions
    /**
     * Clean HTML tags *Remove them*
     * @param string $html
     * @return string
     */
    protected function _clean($html) {
        $htmlOut = strip_tags($html);
        return $htmlOut;
    }
	
    /**
     * Escape HTML data for display
     * @param string $html
     * @return string
     */
    protected function _html($html) {
        $htmlOut = htmlentities($html);
        return $htmlOut;
    }
   
    protected function _stripTags($html) {
        $htmlOut = strip_tags($html);
        return $htmlOut;
    }

    // Override for custom data filtering
    /**
     * Custom data filter
     * Filters all data on set
     * 
     * @param mixed $data
     * @param string $var
     * @return mixed 
     */
    protected function _filterData($data,$var='') {
        return $data ;
    }

    protected function _onChildRender($blk,$content) {
        return $content;
    }
	
    protected function _onRender($content) {
            return $content;
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
            @$temp = $this->config->$tag;
            if(empty($temp)) {$temp = $ci->config->item(trim(substr($pval,1)));}
            $pval = $temp;
        }

        if(substr($pval, 0,1) == '@') {
            // Get data from session
            $tag = trim(substr($pval,1));
            //@$temp = Session Value Here;
            if(empty($temp)) {$temp = '';}
            $pval = $temp;
        }
        
        // Now, look for macros
           
        return($pval);
    }    
    protected function _init($attr) {
        // Default is to put all attributes into data array
        foreach($this->_attr as $_key=>$_value) {
            //$this->setData($_key, $_value);
            // Build setter to allow child class to override
            $method = 'set'.ucfirst($_key);
            $this->$method($_value);
            
        }
        return;
    }

    // Process any custom XML attributes from block tag
    public function init($attr) {
        // Build array from attributes
        foreach($attr as $_key=>$_value) {
            $this->_attr[$_key] = strval($_value);
        }

        $this->_init($this->_attr);
    }

    // Sets render engine.  Since bocks are loaded by the $this->layout lib, we can assume it's there ($this->layout)
    public function setEngine($engine='view') {
        //$engineFunc = $engine.'Engine';
        //$engineFunc = $engine;
        //if(method_exists($this->layout, $engineFunc)) {}
        //if(function_exists($engineFunc)) {}
        $this->_engine = $engine;        
        return $this;
    }

    public function setChildEngine($engine='view',$isChild=FALSE) {
        $this->setEngine($engine);
        foreach($this->_blockList as $blk) {
            $blk->setChildEngine($engine,TRUE);
        }
    }
    

    // Enable or Disable block output
    public function enabled($state=TRUE) {
        $this->_enabled = $state;
		return $this;
    }

    public function isEnabled() {return $this->_enabled; }

    public function setBody($html) {$this->_body = $html; return $this;}
    public function appendBody($html) {$this->_body .= $html;  return $this;}
    public function getBody() {return $this->_body;}
    public function configValue($cfg) {$ci=&get_instance(); $this->_data[$cfg] = $ci->config->item($cfg);}

    // Data Setter via  setVariable('value') => _data['variable'] = 'value'
    public function  __call($name, $arguments) {
        if(substr($name, 0,3) == 'set') {
            $var = substr($name,3);
            $this->setData($var, $arguments[0]);
        }
        if(substr($name, 0,3) == 'get') {
            $var = substr($name,3);
            return $this->getData($var);
        }
        if(substr($name,0,6) == 'render') {
            $child = strtolower(substr($name,6));
            $this->renderChild($child);
        }
    }
    
    // Try to resolve data value, if not, pass to model to resolve
    public function __get($name) {
        $value = $this->getData($name);
        //if($value==null) $value = parent::__get($name);
        return $value;
    }

    // Primary Data Setters and Getters
    public function setData($name,$val) {
        $varname = strtolower($name);
        $this->_data[$varname] = $this->_filterData($val,$varname);
         return $this;
    }

    public function setDataArray($data_in) {
        foreach($data_in as $_var=>$_value) {
            $this->setData($_var,$_value);
        }
    }

    public function getData($name) {
        $varname = strtolower($name);
        $ret = null;
        if(array_key_exists($varname, $this->_data))  $ret = $this->_data[$varname];
        return $ret;
    }

    public function getDataArray() {
        return $this->_data;
    }


    public function D($var) {return $this->getData($var);}
    public function G($var) {   // Return "Global" data (Local or Root Page)
        $rdata = trim($this->getRoot()->getData($var));
        $tdata = trim($this->getData($var));
        return (empty($tdata))? $rdata: $tdata;
    }
    
    // Return data value or set if value passed.
    // DEPRECATED
    public function data($var,$value='') {
        $ret = '[undefined:'. $this->_blockname . '-' .$var .']';
        if($value!='') {
            $this->setData($var,$value);
        }
        @$ret = $this->getData($var);
        return $ret;
    }


    // Set/Get block name (Used when block is refernced by a parent in a template or view)
    public function setName($name) {$this->_blockname = $name; $this->_alias = $name;  return $this;}
    public function getName() {return strval($this->_blockname); }
    public function setAlias($name) {$this->_alias = $name; return $this;}
    public function getAlias() {return $this->_alias; }
    
    // Set if block has a template or view 
    public function setTemplate($tmp) {$this->_template =  $tmp; return $this;}
    public function getTemplate() { return $this->_template;  }
    
    // Refernce the model (within any derived block)
    public function setModel($mdl) { $this->_model = $mdl; return $this;}
    public function getModel() {return $this->_model;}
    public function M() {return $this->_model;}
    
    // Parent/Child block handling methods
    public function setParent($blk) {$this->_parent = $blk; return $this;}    
    public function getParent() { return $this->_parent;  }
    
    public function setSession($sess) {$this->_session = $sess;}
    public function getSession() {return $this->_session;}

    public function getChildren() {return $this->_blockList;}
    
    // Set a dsta item in each child
    public function setInChildren($var,$value,$isChild=FALSE) {
        if($isChild) {$this->setData($var,$value);}
        foreach($this->_blockList as $blk) {
            $blk->setInChildren($var,$value,TRUE);
        }
    }


    /**
     * Return Root (Top Parent) block
     * @return Block_abstract
     */
    public function getRoot() {
        $pblock = $this->getParent();
        if(empty($pblock)) {return $this;}
        return $pblock->getRoot();
    }
    
    public function getChild($blk) {return $this->_blockList[$blk];}
    public function Child($key) {return $this->_blockList[$key];}

    public function renderChild($key) {
        if(!empty($this->_blockList[$key])) {
            echo $this->getChildHtml($key);
        }
    }
	
	// Add child block(s) [Handle array of blocks via recursive call]
    public function addChild($block) {
        if(is_array($block)) {
            foreach($block as $blk) {
                    $this->addChild($blk);
            }
        } else {
                $name = $block->getAlias();
                $this->_blockList[$name] = $block;
                $block->setParent($this);
                //echo 'Add: ' . $block->getName() . '->' . $this->_blockname . '<br/>';
        }
		return $this;
    }

    // *** RENDERING METHODS ***
    // Top level render function. Calls toHTML and echos output
    public function render() {
        $htmlOut = $this->toHTML();
        echo $htmlOut;
    }

    // Render all the child blocks (Just call em and write em)
    public function renderChildren() {
        if(!empty($this->_blockList)) {
            foreach($this->_blockList as $_key=>$_block) {
                echo $this->getChildHtml($_key);
            }
        }
    }


    //** Get HTML output from blocks **
    // Get an individual childs rendered output.
    public function getChildHtml($cname) {
        $html = '';
        if(!empty($this->_blockList)) {
            $child = $this->_blockList[$cname];
            @$html = $this->_onChildRender($cname, $child->toHTML());
        }
        return $html;
    }


    // Call protected _toHTML() function. Everything calls toHTML which allows us to wrap all calls
    // Disabled blocks return empty string.
    public function toHTML() {
        $html = '';
        if($this->_enabled) {
            $html = $this->_onRender($this->_toHTML());
        }
        return $html;
    }

    // Default function is to render child block html into $_data[ChildBlockName] = ChildBlockHtml;
    // Render block HTML into the _body property then return it.
    // NOTE: This will render a block even if it is disabled.
    protected function _toHTML() {

        // Render all child blocks as html into the _data array by block name
        if(!empty($this->_blockList)) {
            // Call each block and get it's HTML output into parent block _data collection
	    // Disabled blocks still load _data with blanks. That way the _data block will contain a valid block key.
            foreach($this->_blockList as $_name=>$_blk) {
                    $blkname = $_blk->getAlias();
                    $this->_data[$blkname] = $this->getChildHtml($_name);
            }
        }

        // Render the  View. If none defined, output the rendered blocks
        if(!empty($this->_template)) {
            // Render the template using the defined engine
            $this->appendBody($this->_layoutLib->callEngine($this->_engine,$this));
        } else {
            // If no template defined, just output the rendered child blocks.
            // If there are no child blocks, whatever is in the _body will be output.
            foreach($this->_blockList as $_name=>$_blk) {
                if($_blk->isEnabled()) {
                    $alias = $_blk->getAlias();
                    $this->appendBody($this->_data[$alias]);
                }
            }
        }
        
        return $this->getBody();
    }
    
    // Block can act like a string.
    public function  __toString() {
        return $this->toHTML();
    }    
}
