<?php

class scratchAppRoute {

    protected $_app;
    protected $path;
    protected $callable;

    public function __construct($app, $path, $callable) {
        $this->_app = $app;
        $this->path = $this->_preparePath($path);
        $this->callable = $callable;
    }

    public function name($name) {
        $this->_app->route($name, $this);
        return $this;
    }

    public function dispatch($app) {
        $request = $app->request();
        $oldUri = $request->uri();
        $oldBaseUri = $request->baseUri();
        if (preg_match($this->path, $request->uri(), $matches)) {
            $uriLeft = $matches['_uri'];
            $baseUri = $request->baseUri();
//                $baseUri = $uriLeft ? substr($request->requestUri, 0, -strlen($uriLeft)) : $req->requestUri;
//                if ('/' !== $baseUri[strlen($baseUri)-1]) $baseUri .= '/';
            $request->baseUri($baseUri);
            $request->uri($uriLeft ? '/' . $uriLeft : '');

            call_user_func_array($this->callable, array());

            // execute callable
            $request->uri($oldUri);
            $request->baseUri($oldBaseUri);
            return true;
        }
        return false; // $app->finished();
    }

    public function url() {
        return $this->_app->request()->baseUri() . 'a url';
    }

    protected function _preparePath($path) {
        $path = str_replace('/', '\/', $path);
        $path = preg_replace('/\:(\w+)/', '(?P<$1>[^\/]+)\/?', $path);
//        $path = str_replace('*', '(?P<_uri>.*)', $path);
        $path = '/^' . $path . '(?P<_uri>.*)$/';
        return $path;
    }

}

?>