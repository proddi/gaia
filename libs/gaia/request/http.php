<?php

class gaiaRequestHttp extends gaiaRequestAbstract {

    protected $_uri = '';

    public function __construct() {
        // override actions (idee taken from: http://flourishlib.com/browser/fRequest.php)
        //                    BIG THANKS!!!
        foreach ($_POST as $key => $val) {
            if (substr($key, 0, 8) == 'action::') {
                $_POST['action'] = substr($key, 8);
                unset($_POST[$key]);
            }
        }

        $this->_uri = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
    }

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri() {
        return $this->_uri;
        return $_SERVER["PATH_INFO"];
        $i = strrpos($_SERVER['SCRIPT_NAME'], '/') + 1;
        return substr($_SERVER['REQUEST_URI'], $i);
    }

    public function setUri($uri) {
        $this->_uri = $uri;
    }

    public static function getBaseUri() {
        $i = strrpos($_SERVER['SCRIPT_NAME'], '/') + 1;
        return substr($_SERVER['REQUEST_URI'], 0, $i);
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