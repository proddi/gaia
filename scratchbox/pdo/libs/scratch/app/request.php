<?php

class scratchAppRequest {

    protected $baseUri;
    protected $uri;

    public function __construct() {
        $this->uri = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
        // Fix: workaround for lighttp rewrite rules, pse check for better solution
        if ('/index.php' === substr($this->uri,0, 10))
            $this->uri = substr($this->uri, 10);

//        $i = strrpos($_SERVER['SCRIPT_NAME'], '/') + 1;
//        $this->baseUri = substr($_SERVER['REQUEST_URI'], 0, $i);
    }
    public function method() {}

    public function headers() {}

    public function uri($url = NULL) {
        if (isset($url)) $this->uri = $url;
        return $this->uri;
    }

    public function baseUri($baseUrl = NULL) {
        if (isset($baseUrl)) $this->baseUri = $baseUrl;
        return $this->baseUri;
    }
}

?>