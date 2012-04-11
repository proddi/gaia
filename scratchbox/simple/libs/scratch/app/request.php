<?php

class scratchAppRequest {

    protected $_baseUrl;
    protected $_url;
    protected $_method;

    public function __construct() {
        $this->_url = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
        // Fix: workaround for lighttp rewrite rules, pse check for better solution
        if ('/index.php' === substr($this->_url,0, 10))
            $this->_url = substr($this->url, 10);

        if (!($this->_url = rtrim($this->_url, '/'))) {
            $this->_url = '/';
        }
        $this->_baseUrl = dirname($_SERVER['SCRIPT_NAME']);

        $this->_method = @$_SERVER['REQUEST_METHOD'];
    }
    public function method() {
        return @$_SERVER['REQUEST_METHOD'];
    }

    /**
     * Is this a GET request?
     * @return bool
     */
    public function isGet() {
        return 'GET' === $this->_method;
    }

    /**
     * Is this a POST request?
     * @return bool
     */
    public function isPost() {
        return 'POST' === $this->_method;
    }

    public function post($key) {
        return @stripslashes($_POST[$key]);
    }

    public function headers() {}

    public function url($url = NULL) {
        if (isset($url)) $this->_url = $url;
        return $this->_url;
    }

    public function baseUrl($baseUrl = NULL) {
        if (isset($baseUrl)) $this->_baseUrl = $baseUrl;
        return $this->_baseUrl;
    }

    public function requestUrl() {
        return $_SERVER['REQUEST_URI'];
    }
}

?>