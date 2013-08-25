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
    /** @var string Name of Block */
    protected $_blockname = 'core'; 
    
    protected $_alias = '';

    /** @var bool Enable/Disable block output */
    protected $_enabled = TRUE;

    /** @var array Primary block data array. Passed to view at render time */
    protected $_data = array();

    /** @var string Name of template (view) */
    protected $_template ='';

    /** @var string Name of rendering driver */
    protected $_engine = 'view';

    /** @var object Reference to layout library */
    protected $_layoutLib = null;

    /** @var array List of child blocks */
    protected $_blockList = array();

    /** @var CI_Model Reference to bound model */
    protected $_model;

    /** @var string Block's parent */
    protected $_parent = NULL;

    /** @var string Blocks rendered output */
    protected $_body = '';

    /** @var array Attributes from <block> tag in layout.xml */
    protected $_attr = array();
    
    protected $_session = NULL;

    public function   __construct() {
        //parent::__construct();
        //$this->_blockname = strtolower(get_class($this));
    }

    /** Sets layout library instance */
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
    * @param string $arg
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

        // Session Data 
        if(substr($pval, 0,1) == '@') {
            // Get data from session
            $tag = trim(substr($pval,1));
            @$temp =  $ci->session->userdata($tag);
            if(empty($temp)) {$temp = '';}
            $pval = $temp;
        }
                           
        return($pval);
    }    
    
    /**
     * Called by init() method to provide 'hook' for sub-classes
     * 
     * @param array $attr
     * @return null 
     */
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

    /**
     * Process any custom XML attributes from block tag
     * 
     * @param array $attr Name/Value pairs
     */
    public function init($attr) {
        // Build array from attributes
        foreach($attr as $_key=>$_value) {
            $this->_attr[$_key] = strval($_value);
        }

        $this->_init($this->_attr);
    }

    /**
     * Sets render engine.  Since bocks are loaded by the $this->layout lib, we can assume it's there ($this->layout)
     * 
     * @param string $engine - Name of engine (default='view')
     * @return \Block_abstract 
     */
    public function setEngine($engine='view') {
        //$engineFunc = $engine.'Engine';
        //$engineFunc = $engine;
        //if(method_exists($this->layout, $engineFunc)) {}
        //if(function_exists($engineFunc)) {}
        $this->_engine = $engine;        
        return $this;
    }

    /**
     * Sets rendering engine in ALL child blocks (and their children)
     * Most used to replace rendering engine to a debug engine that renders raw data
     * 
     * @param string $engine - Name of engine
     * @param bool $isChild - Used ONLY for recursive calls. Never pass this.
     */
    public function setChildEngine($engine='view',$isChild=FALSE) {
        $this->setEngine($engine);
        foreach($this->_blockList as $blk) {
            $blk->setChildEngine($engine,TRUE);
        }
    }
    

    /**
     * Enable or Disable block output
     * 
     * @param bool $state
     * @return \Block_abstract 
     */
    public function enabled($state=TRUE) {
        $this->_enabled = $state;
		return $this;
    }

    /** @return bool Is block enabled */
    public function isEnabled() {return $this->_enabled; }

    /**
     *  Sets Html of block
     * @param string Html 
     * @return Block_abstract 
     */
    public function setBody($html) {$this->_body = $html; return $this;}

    /** Appends HTML to body of block */
    public function appendBody($html) {$this->_body .= $html;  return $this;}

    /** Returns block body html */
    public function getBody() {return $this->_body;}

    /** Return config value */
    public function configValue($cfg) {$ci=&get_instance(); $this->_data[$cfg] = $ci->config->item($cfg);}

    /**
     * Data Setter via  setVariable('value') => _data['variable'] = 'value'
     * setter: $this->setVarname('value')
     * getter: $this->getVarname();
     * render: $this->renderChildname();  // May remove in future.
     * 
     * @param string $name
     * @param mixed $arguments
     * @return mixed on get 
     */
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

    /**
     * Return value from _data array
     * 
     * @param string $name
     * @return string
     */
    public function getData($name) {
        $varname = strtolower($name);
        $ret = null;
        if(array_key_exists($varname, $this->_data))  $ret = $this->_data[$varname];
        return $ret;
    }

    /**
     * Return block complete _data array
     * 
     * @return array  ($this->_data)
     */
    public function getDataArray() {
        return $this->_data;
    }


    /**
     * @deprecated
     * Shortcut to return data value.
     * Used in templates  $B->D('var')
     * 
     * @param string $var
     * @return string 
     */
    public function D($var) {return $this->getData($var);}
    
    /**
     * Return data from root (global) or curent block 
     * Allows you to set data in root block to be access from any other block.
     * 
     * @param string $var
     * @return string 
     */
    public function G($var) {   // Return "Global" data (Local or Root Page)
        $rdata = trim($this->getRoot()->getData($var));
        $tdata = trim($this->getData($var));
        return (empty($tdata))? $rdata: $tdata;
    }    

    /**
     * Set Block Name and default alias
     * 
     * @param string $name
     * @return \Block_abstract 
     */
    public function setName($name) {$this->_blockname = $name; $this->_alias = $name;  return $this;}

    /** Get Block Name */
    public function getName() {return strval($this->_blockname); }

    /**
     * Defines a child block name that is different from the block instance name.
     * This is the name used to reference the block in templates.
     * 
     * @param string $name
     * @return \Block_abstract 
     */
    public function setAlias($name) {$this->_alias = $name; return $this;}
    public function getAlias() {return $this->_alias; }
    
    /**
     *  Set Block Template 
     * @param string $tmp - Name of template (view)
     * @return \Block_abstract
     */
    public function setTemplate($tpl) {$this->_template =  $tpl; return $this;}

    /** Get template Name */
    public function getTemplate() { return $this->_template;  }
    
    /**
     * Inject model instance into block
     * Allows block / template to refernce model properties and methods
     * 
     * @param CI_Model $mdl
     * @return \Block_abstract 
     */
    public function setModel($mdl) { $this->_model = $mdl; return $this;}

    /** Get currently bound model */
    public function getModel() {return $this->_model;}
    
    // Parent/Child block handling methods
    /**
     * Sets refernce to parent block (Used when creating blocks)
     * 
     * @param \Block_abstract $mom - Parent Block
     * @return \Block_abstract  - Current Block
     */
    public function setParent($mom) {$this->_parent = $mom; return $this;} 
    /** Return parent block */
    public function &getParent() { return $this->_parent;  }
    
    public function setSession($sess) {$this->_session = $sess;}
    public function getSession() {return $this->_session;}

    /** Get list of child blocks as array of \Block_abstract */
    public function getChildren() {return $this->_blockList;}
    
    /**
     * Set a data item in each child
     * 
     * @param string $var
     * @param mixed $value
     * @param bool $isChild  - Used for recursive call (Do not set on call)
     */
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
    
    /**
     * Get named child block
     * 
     * @param type $blk
     * @return type 
     */
    public function getChild($blk) {return $this->_blockList[$blk];}

    /** Alias of getChild() */
    public function Child($key) {return $this->_blockList[$key];}

    /**
     * Render child block and output
     * 
     * @param string $childname - Name of child block
     */
    public function renderChild($childname) {
        if(!empty($this->_blockList[$childname])) {
            echo $this->getChildHtml($childname);
        }
    }

    /**
     * Add child block(s) [Handle array of blocks via recursive call]
     * 
     * @param mixed $block - Can be array of blocks or single block reference.
     * @return \Block_abstract 
     */
    public function addChild($block) {
        if(is_array($block)) {
            foreach($block as $blk) {
                    $this->addChild($blk);
            }
        } else {
                $name = $block->getAlias();
                $this->_blockList[$name] = $block;
                $block->setParent($this);
        }
		return $this;
    }

    // *** RENDERING METHODS ***

    /**
     * Render Block and output HTML (or whatever) 
     * Calls toHTML() to build and return output string
     */
    public function render() {
        $htmlOut = $this->toHTML();
        echo $htmlOut;
    }

    /**
     *  Render all the child blocks (Just call em and write em)
     */
    public function renderChildren() {
        if(!empty($this->_blockList)) {
            foreach($this->_blockList as $_key=>$_block) {
                echo $this->getChildHtml($_key);
            }
        }
    }

    /**
     * Get an individual childs rendered output.
     * 
     * @param string $cname
     * @return string HTML Output 
     */
    public function getChildHtml($cname) {
        $html = '';
        if(!empty($this->_blockList)) {
            $child = $this->_blockList[$cname];
            @$html = $this->_onChildRender($cname, $child->toHTML());
        }
        return $html; 
    }

    /**
     * Return Block output.
     * Delegate to protected _toHTML() to allow subclass to override rendering
     * Disabled blocks return empty string.
     * 
     * @return type 
     */
    public function toHTML() {
        $html = '';
        if($this->_enabled) {
            $html = $this->_onRender($this->_toHTML());
        }
        return $html;
    }

    /**
     * Default function is to render child block html into $_data[ChildBlockName] = ChildBlockHtml;
     * Render block HTML into the _body property then return it.
     * NOTE: This will render a block even if it is disabled.
     * 
     * @return string HTML Output (Body of block)
     */
    protected function _toHTML() {

        // Render all child blocks as html into the _data array by block name (alias)
        if(!empty($this->_blockList)) {
            // Call each block and get it's HTML output into parent block _data collection
	    // Disabled blocks still load _data with blanks. That way the _data block will contain a valid block key.
            foreach($this->_blockList as $_name=>$_blk) {
                    $blkname = $_blk->getAlias();
                    $this->_data[$blkname] = $this->getChildHtml($_name);
            }
        }

        // Render the View. If no template defined, output the rendered blocks
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
    
    /**
     * Return block output as string
     * 
     * @return string HTML Output of block
     */
    public function  __toString() {
        return $this->toHTML();
    }    
}
