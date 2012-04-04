<?php

class gaiaRequestHttp extends gaiaRequestAbstract {

    public $baseUri;
    public $uri;
    public $requestUri;
    protected $_uri = '';
    protected $_rootUri = '';

    public function __construct() {
        // override actions (idee taken from: http://flourishlib.com/browser/fRequest.php)
        //                    BIG THANKS!!!
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 8) == 'action::') {
                $_POST['action'] = substr($key, 8);
                unset($_POST[$key]);
            }
        }

        $this->post = (object)$_POST;
        foreach($this->post as $key => $value) $this->post->$key = stripslashes($value);
        $this->_uri = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
        // Fix: workaround for lighttp rewrite rules, pse check for better solution
        if ('/index.php' === substr($this->_uri,0, 10))
            $this->_uri = substr($this->_uri, 10);
        $this->_rootUri = $this->getBaseUri() . substr($this->_uri, 1);

        $i = strrpos($_SERVER['SCRIPT_NAME'], '/') + 1;
        $this->baseUri = substr($_SERVER['REQUEST_URI'], 0, $i);

        $this->uri = $this->_uri;
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    public function addToBaseUri($add) {
        var_dump($add);
        $this->baseUri .= ('/' === $add[0]) ? substr($add, 1) : $add;
    }
    public function isPost() {
        return 'POST' === $_SERVER['REQUEST_METHOD'];
    }
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri() {
        return $this->_uri;
    }

    public function setUri($uri) {
        $this->_uri = $uri;
    }

    public static function getBaseUri() {
        $i = strrpos($_SERVER['SCRIPT_NAME'], '/') + 1;
        return substr($_SERVER['REQUEST_URI'], 0, $i);
    }

    public function getRootUri() {
        return substr($this->_rootUri, 0, strlen($this->_rootUri) - strlen($this->_uri));
    }

    public function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function getAction() {
        return isset($_POST['action']) ? $_POST['action'] : NULL;
    }

    public function getParam($name) {
        return isset($_POST[$name]) ? $_POST[$name] : NULL;
    }

}

?>