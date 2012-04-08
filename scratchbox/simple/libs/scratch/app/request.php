<?php

class scratchAppRequest {

    protected $_baseUri;
    protected $_uri;

    public function __construct() {
        $this->_uri = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
        // Fix: workaround for lighttp rewrite rules, pse check for better solution
        if ('/index.php' === substr($this->_uri,0, 10))
            $this->_uri = substr($this->uri, 10);

        $this->_uri = rtrim($this->_uri, '/');
        $this->_baseUri = dirname($_SERVER['SCRIPT_NAME']);
    }
    public function method() {
        return @$_SERVER['REQUEST_METHOD'];
    }

    public function headers() {}

    public function uri($url = NULL) {
        if (isset($url)) $this->_uri = $url;
        return $this->_uri;
    }

    public function baseUri($baseUrl = NULL) {
        if (isset($baseUrl)) $this->_baseUri = $baseUrl;
        return $this->_baseUri;
    }
}

?>