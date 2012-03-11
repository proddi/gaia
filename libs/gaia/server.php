<?php

class gaiaServer {

    const BREAKCHAIN = 'break';

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
    public function _executeMiddleware(array $middleware, gaiaRequestAbstract &$req, gaiaResponseAbstract &$res, &$data = NULL, &$exceptionHandler = NULL) {
        try {
            foreach ($middleware as $mw) {
                if (gaiaServer::BREAKCHAIN === self::_proceedMiddleware($mw, $req, $res, $data)
                        || $res->isFinish()) {
                    break;
                }
            }
        } catch (Exception $e) {
            if (is_callable($exceptionHandler)) $data = $exceptionHandler($req, $res, $e, $data);
        }
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
    static public function _proceedMiddleware($mw, gaiaRequestAbstract &$req, gaiaResponseAbstract &$res, &$data) {
        if (is_array($mw)) {
            return self::_executeMiddleware($mw, $req, $res, $data);
        }
        return $mw($req, $res, $data);
    }

    //------------------------------------------------------------------------------------------------------------------
    public function run(/* args... */) {
        if (isset($this) && $this instanceof self) {
            $req = new gaiaRequestHttp();
            $res = new gaiaResponseHtml();
            $this->_executeMiddleware(func_get_args(), $req, $res, new gaiaInvokable(), $this->_exceptionHandler);
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
        return function(&$req, &$res, &$data) use ($_routes) {
            $uri = $req->getUri();
            $oldParams = isset($req->params) ? (array) $req->params : array();
            foreach ($_routes as $route => $mw) {
                if (preg_match($route, $uri, $matches)) {
                    $req->setUri(array_key_exists('_uri', $matches) ? $matches['_uri'] : '');
                    $req->params = (object) array_merge($oldParams, $matches);
                    gaiaServer::_proceedMiddleware($mw, $req, $res, $data);
                    break;
                }
            }
            $req->setUri($uri);
        };
    }

    //------------------------------------------------------------------------------------------------------------------
    static public function tryCatch(/* args..., exceptionHandler */) {
        $mw = array_slice(func_get_args(), 0, -1);
        $exceptionHandler = func_get_arg(func_num_args()-1);
        return function(&$req, &$res, &$data) use ($mw, $exceptionHandler) {
            gaiaServer::_executeMiddleware($mw, $req, $res, $data, $exceptionHandler);
        };
    }

    //------------------------------------------------------------------------------------------------------------------
    static public function ajax($ctx, $mw, $finish = false) {
        return function(&$req, &$res, &$data) use ($ctx, $mw, $finish) {
            if ($req->isAjax()) {
                if (!$res instanceof gaiaResponseAjax) $res = new gaiaResponseAjax($res);
                gaiaServer::_proceedMiddleware($mw, $req, $res, $data);
                if ($finish) $res->finish();
            } else {
                gaiaServer::_proceedMiddleware($mw, $req, $res, $data);
                $res->send($ctx, '<span id="ajax_'.$ctx.'">' . $res->content($ctx) . '</span>');
            }
        };
    }

    //------------------------------------------------------------------------------------------------------------------
    static public function requireBasicAuth($validator /* mw, mw, ... */) {
        $mw = array_slice(func_get_args(), 1);

        return function(&$req, &$res, &$data) use ($validator, $mw) {
            $authenticated = false;

            if (isset($_SERVER['PHP_AUTH_USER'])) $authenticated = $validator($req, $res, $data, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
            else $validator($req, $res, $data, NULL, NULL); // get default values

            if (!$authenticated) {
                header('WWW-Authenticate: Basic realm="Please enter your credentials..."');
                header('HTTP/1.0 401 Unauthorized');
            } else {
                gaiaServer::_executeMiddleware($mw, $req, $res, $data);
            }
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