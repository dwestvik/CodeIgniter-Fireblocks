<?php
class Demo_cms_provider extends CI_Model implements iContentProvider {
    
    public function getContent($id) {
        $cms = 'This is sample CMS Content for key (' . $id . ')';
        return $cms;
    }
}