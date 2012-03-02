<?php

abstract class gaiaResponseAbstract extends gaiaInvokable {

    const jsHeader  = 1;
    const jsInline  = 2;
    const cssHeader = 3;
    const cssInline = 4;

    protected $_title;
    protected $_resources = array();

    protected $_isFinish = false;

    protected $_content = array();
    public function header() {}

    public function send($ctx, $data = NULL) {
        if (NULL === $data) {
            $data = $ctx;
            $ctx = NULL;
        }
        if (array_key_exists($ctx, $this->_content)) $this->_content[$ctx] .= $data;
        else $this->_content[$ctx] = $data;
        return $this;
    }

    public function finish($data = '') {
        $this->send($data);
        $this->_isFinish = true;
        return $this;
    }

    public function replace($data) {
        $this->content = $data;
    }

    public function content($ctx = NULL) {
        $data = NULL;
        if (array_key_exists($ctx, $this->_content)) {
            $data = $this->_content[$ctx];
            unset($this->_content[$ctx]);
        }
        return $data;
    }

    public function resource($resource, $type = NULL) {
        if (!$type) {
            if (".js" === substr($resource, -3)) $type = 'js.header';
            else if (".css" === substr($resource, -4)) $type = 'css.header';
            else $type = 'js.inline';
        }
        if (!array_key_exists($type, $this->_resources)) {
            $this->_resources[$type] = array();
        }
        $this->_resources[$type][] = $resource;
    }

    public function isFinish() {
        return $this->_isFinish;
    }

    abstract public function streamOut();

}

?>