<?php
/**
 * block_email
 * Used to format and send HTML based emails.
 * THIS IS NOT SAFE FOR END USERS TO SEND EMAILS!
 *
 * Set the following data items:
 * to, from, subject, setBody or Template.
 *
 * @author dane.westvik
 */
using_block('core/block_abstract');
class Block_email extends Block_abstract {

    public function  __construct() {
        parent::__construct();
        $this->_data['mime-boundry'] = 'ref-' . md5(date('r', time())) . '-games';
    }

    protected function _xfilterData($dat,$var) {
        return $this->_html($dat);
    }

    protected function _getHeaders() {
        $from = $this->_data['from'];

        $content_type = "Content-Type: multipart/alternative; boundary=\"" . $this->_data['mime-boundry'] . "\"\r\n";
        $headers = "From: $from\r\nReply-To: $from\r\n" . $content_type;
        $headers .= "\r\n";

        return $headers;
    }

    protected function _getBody() {
        $email_body = $this->_body;
        $email_body .= parent::toHTML();
        $email_body .= "\r\n";
        // (Ending boundry not needed. Caused 0byte attachment) $email_body .= '--' . $this->_data['mime-boundry'] . "\r\n\r\n";
        return $email_body;
    }

    // Render - output email
    public function render() {
        return;
    }

    public function send() {
        foreach($this->_blockList as $_name=>$_blk) {
            $_blk->setBoundry('--' . $this->_data['mime-boundry']);
        }

        $to = $this->_data['to'];
        $from = $this->_data['from'];
        $subject = $this->_data['subject'];

        $headers = $this->_getHeaders();
        $email_body = $this->_getBody();

        $mail_sent = mail( $to, $subject, $email_body, $headers );

        return $mail_sent;
        
    }


    // Block can act like a string.
    public function  __toString() {
        return $this->toHTML();
    }

}



