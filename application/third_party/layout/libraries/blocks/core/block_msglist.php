<?php
/**
 * Block_msglist
 * 
 * Used to output result messages
 * Set classes to 'bootstrap' based messages
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_msglist extends Block_abstract {


     public function  __construct() {
        parent::__construct();
     }

    /**
     * Message List
     * @var array 
     */
    private $_messages = array();

    private $_classes = array(
        'default'=>'text-default',
        'warn'=>'text-warning',
        'error'=>'text-error',
        'success' => 'text-success',
        'info' => 'text-info');
    /**
     * First level html tag name for messages html output
     *
     * @var string
     */
    protected $_firstTag = 'div';

    protected $_firstLevelClass = 'message_blocks';

    /**
     * Second level html tag name for messages html output
     *
     * @var string
     */
    protected $_secondTag = 'div';

    protected $_escapeMessageFlag = FALSE;

    protected function getClass($type) {return($this->_classes[$type]);}

    /**
     * Add message to message list
     * @param string $msgArray
     */
    protected function add($msgText,$type='default') {
        $msgArray = array('text'=>$msgText,'class'=>$this->getClass($type));
        $this->_messages[] = $msgArray;
    }


    /**
     * Add Normal message to list
     * @param string $txt
     */
    public function addMessage($txt) {
        $this->add($txt);
    }

    public function addSuccess($txt) {
        $this->add($txt,'success');
    }


    public function addWarning($txt) {
        $this->add($txt,'warn');
    }

    public function addError($txt) {
        $this->add($txt,'error');
    }

    public function addInfo($txt) {
        $this->add($txt,'info');
    }
    
    public function hasMessages() {
        return ((count($this->_messages))? true: false);
    }

    private function processFlashMessages() {
        $sess = $this->getRoot()->getSession();
        if(is_object($sess)) {
            $flashes = array('success','warn','notice','error');
            foreach($flashes as $msgtype) {
                $msg = $sess->getFlash($msgtype);
                if(!empty($msg)) {$this->add($msg,$msgtype);}
            }
        }
    }
    
    // Return Content ONLY if there are messages
    private function getHTML() {
        $html = '';
        $this->processFlashMessages();        
        if(count($this->_messages)) {
            $html = '<' . $this->_firstTag . ' id="page_messages" class="'.$this->_firstLevelClass.'">';
            foreach ($this->_messages as $message) {
                //$html.= '<' . $this->_secondTag . ' class="'.$message['class'].'">'
                //    . ($this->_escapeMessageFlag) ? $this->_htmlEscape($message['text']) : $message['text']
                //    . '</' . $this->_secondTag . '>';
                $html .= '<'.$this->_secondTag.' class="'.$message['class'].'">';
                $html .= $message['text'];
                $html .= '</'.$this->_secondTag.'>';
            }
            $html .= '</' . $this->_firstTag . '>';
        }
        return $html;
    }

    protected  function  _toHTML() {
        //parent::_toHTML();
        return($this->getHTML());

    }
}
