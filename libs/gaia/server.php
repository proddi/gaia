<?php

class gaiaServer {

    //------------------------------------------------------------------------------------------------------------------
    static protected $_instance;
    static public function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self(/*self::$_staticConfig*/);
        }
        return self::$_instance;
    }

    //------------------------------------------------------------------------------------------------------------------
    protected $_exceptionHandler;
    public function onException($handler) {
        if (isset($this) && $this instanceof self) {
            $this->_exceptionHandler = $handler;
        } else {
            return self::getInstance()->onException($handler);
        }
    }

    //------------------------------------------------------------------------------------------------------------------


//    protected $_routes = array();

//    public function __construct(array $routes = array()) {
//    }
/*
    public function add($path, $handler = NULL) {
        if (is_array($path)) {
            foreach ($path as $path => $handler) {
                $this->add($path, $handler);
            }
        } else {
            $this->_routes[$this->_preparePath($path)] = $handler;
        }
    }

    public function run($req, &$res, $data) {
        $uri = $req->getUri();
        foreach ($this->_routes as $route => $handler) {
            if (preg_match($route, $uri, $matches)) {
                $req->setUri(array_key_exists('_uri', $matches) ? $matches['_uri'] : '');
                $req->params = (object) $matches;
                $data = $this->_proceedHandler($handler, $req, $res, $data);
                break;
            }
        }
        $req->setUri($uri);
        return $data;
    }

    protected function _proceedHandler($handler, $req, &$res, $data) {
        if (is_array($handler)) {
            foreach ($handler as $oneHandler) {
                $data = $oneHandler($req, $res, $data);
                if ($res->isFinish()) break;
            }
        } else {
            $data = $handler($req, $res, $data);
        }
        return $data;
    }

    protected function _preparePath($path) {
        $path = str_replace('/', '\/', $path);
        $path = preg_replace('/\:(\w+)/', '(?P<$1>\w+)', $path);
//        $path = str_replace('+', '(?P<_uri>.+)', $path);
        $path = str_replace('*', '(?P<_uri>.*)', $path);
        $path = '/^' . $path . '$/';
        return $path;
    }

    /**
     * STATIC experimental
     * @param type $chain
     * @return type
     * /
    static public function create(array $routes) {
        $router = new self($routes);
        return function($req, &$res, $data) use ($router) {
            return $router->run($req, $res, $data);
        };
    }
*/

    public function _executeMiddleware(array $middleware, gaiaRequestAbstract &$req, gaiaResponseAbstract &$res, $data = NULL, &$exceptionHandler = NULL) {
        try {
            foreach ($middleware as $mw) {
                $data = self::_proceedMiddleware($mw, $req, $res, $data);
                if ($res->isFinish()) break;
            }
        } catch (Exception $e) {
            if (is_callable($exceptionHandler)) $data = $exceptionHandler($req, $res, $e, $data);
        }
        return $data;
    }

    /**
     * Executes a missleware function (or array of middleware functions)
     *
     * @param function|array $handler Middleware function or array to be executed
     * @param gaiaRequestAbstract $req
     * @param gaiaResponseAbstract $res
     * @param variant $data Data passed from the previous middleware
     * @return variant Data to pass to the next middleware
     */
    static public function _proceedMiddleware($handler, gaiaRequestAbstract &$req, gaiaResponseAbstract &$res, $data) {
        if (is_array($handler)) {
            foreach ($handler as $mw) {
                $data = self::_proceedMiddleware ($mw, $req, $res, $data);
                if ($res->isFinish()) break;
            }
            return $data;
        }
        return $handler($req, $res, $data);
    }

    //------------------------------------------------------------------------------------------------------------------
    public function run(/* args... */) {
        if (isset($this) && $this instanceof self) {
            $req = new gaiaRequestHttp();
            $res = new gaiaResponseHtml();
            $this->_executeMiddleware(func_get_args(), $req, $res, NULL, $this->_exceptionHandler);
            $res->streamOut();
        } else {
            return call_user_func_array(array(self::getInstance(), 'run'), func_get_args());
        }
    }

    //------------------------------------------------------------------------------------------------------------------
    static public function router(array $routes) {
        $_routes = array();
        foreach ($routes as $route => $mw) {
            $_routes[self::_routerPreparePath($route)] = $mw;
        }
        return function(&$req, &$res, $data) use ($_routes) {
            $uri = $req->getUri();
            foreach ($_routes as $route => $mw) {
                if (preg_match($route, $uri, $matches)) {
                    $req->setUri(array_key_exists('_uri', $matches) ? $matches['_uri'] : '');
                    $req->params = (object) $matches;
                    $data = gaiaServer::_proceedMiddleware($mw, $req, $res, $data);
                    break;
                }
            }
            $req->setUri($uri);
            return $data;
        };
    }

    //------------------------------------------------------------------------------------------------------------------
    static public function tryCatch(/* args..., exceptionHandler */) {
        $mw = array_slice(func_get_args(),0, -1);
        $exceptionHandler = func_get_arg(func_num_args()-1);
        return function(&$req, &$res, $data) use ($mw, $exceptionHandler) {
            return gaiaServer::_executeMiddleware($mw, $req, $res, $data, $exceptionHandler);
        };
    }

    static protected function _routerPreparePath($path) {
        $path = str_replace('/', '\/', $path);
        $path = preg_replace('/\:(\w+)/', '(?P<$1>\w+)', $path);
//        $path = str_replace('+', '(?P<_uri>.+)', $path);
        $path = str_replace('*', '(?P<_uri>.*)', $path);
        $path = '/^' . $path . '$/';
        return $path;
    }
}

?>