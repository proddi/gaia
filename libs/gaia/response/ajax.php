<?php

// gaiaLog::warn('gaiaResponseAjax is experimental and still under development.');

/**
 * Ajax response
 * @package gaia
 * @subpackage response
 * @author proddi@splatterladder.com
 */

class gaiaResponseAjax extends gaiaResponseAbstract {

    /**
     * cass json-root with special render method
     *
     * @param guiBase $o
     */
//  public function write(guiBase $o) {
//      if ($this->_callRender) $this->_Content .= $o->renderJson();
//  }

    public function streamOut() {
        $json = array('content' => $this->_content);
//        if ($this->_jsInline) $json['script'] = array_unique($this->_jsInline);
        echo json_encode($json);
    }

}

?>