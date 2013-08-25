<?php
/**
 * Block_mime
 * Output mime encoded parts for emails
 *
 * setBody
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_mime extends Block_abstract {

    public function  __construct() {
        parent::__construct();
        $this->_data['charset'] = 'iso-8859-1';
        $this->_data['content-type'] = 'text/plain';
        $this->_data['mime-boundry'] = '';
    }


    public function setBoundry($hash) {
        $this->_data['mime-boundry'] = $hash;
    }

    protected function _init($attr) {
        if(!empty($attr['mime'])) {$this->_data['content-type'] = $attr['mime'];}
    }
    /*    protected function _filterData($dat,$var) {
        return $this->_html($dat);
    }
*/

    // Render will do nothing..  It's an email
    public function render() {
        return;
    }


    // Output mime encoded block and enclose sub-blocks in mime wrapper.
    protected function  _toHTML() {
        $html .= $this->_data['mime-boundry'] . "\r\n";
        $html .= 'Content-Type: ' . $this->_data['content-type'] . ';charset="' . $this->_data['charset'] . '"';
        $html .= "\r\n";

        // IF the content type is HTML, render as is, otherwise strip the tags
        if($this->_data['content-type'] == 'text/html') {
            $html .= "Content-Transfer-Encoding: base64\r\n\r\n";
            //$html .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $this->_body;
            $body .= parent::_toHTML();
            $html .= chunk_split(base64_encode($body));
            //$html .= $body;
        } else {
            $html .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $html .= $this->_stripTags($this->_body);
            $html .= $this->_stripTags(parent::_toHTML());
        }

        return $html;
    }

    // Block can act like a string.
    public function  __toString() {
        return $this->toHTML();
    }

}



