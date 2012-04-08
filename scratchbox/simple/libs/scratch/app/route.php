<?php

class scratchAppRoute {

    protected $_router;
    protected $path;
    protected $callable;

    public function __construct($router, $path, $callable) {
        $this->_router = $router;
        $this->path = $this->_preparePath($path);
        $this->callable = $callable;
    }

    public function via($method) {
        return $this;
    }

    public function name($name) {
        $this->_router->namedRoute($name, $this);
        return $this;
    }

    public function dispatch($app) {
        $request = $app->request();
        $oldUri = $request->uri();
        $oldBaseUri = $request->baseUri();
        if (preg_match($this->path, $request->uri(), $matches)) {

            $l = count($matches);
            if (array_key_exists('_uri', $matches)) {
                $uriLeft = $matches['_uri'];
                $l -= 2;
            } else {
                $uriLeft = '';
            }

            // extrahiere matches
            $args = array();
            for ($i = 1; $i < $l; $i++) $args[] = $matches[$i];
            $args[] = $app;

            // modify request url's to match subroutes
            $request->baseUri($oldBaseUri . substr($oldUri, 0, strlen($oldUri) - strlen($uriLeft) - ($uriLeft ? 1 : 0)));
            $request->uri('/' . $uriLeft);

            // execute callable
            call_user_func_array($this->callable, $args);

            // reset request url's
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
        $path = preg_replace('/\:(\w+)/', '([^\/]+)\/?', $path);
        $path = str_replace('*', '(?P<_uri>.*)', $path);
        $path = '/^' . $path . '$/';
        return $path;
    }

}

?>