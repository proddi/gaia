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
        $oldUrl = $request->url();
        $oldBaseUrl = $request->baseUrl();
        if (preg_match($this->path, $request->url(), $matches)) {
//var_dump($matches);
            // TODO: path modifications looks like a hack. please refactor if possible.
            $l = count($matches);
            if (array_key_exists('_url', $matches)) {
                $urlLeft = ltrim($matches['_url'], '/');
                $l -= 2;
            } else {
                $urlLeft = '';
            }

            // extrahiere matches
            $args = array();
            for ($i = 1; $i < $l; $i++) $args[] = $matches[$i];
            $args[] = $app;

//            echo "->" . $request->baseUrl() . "<br>\n";
//            echo "->" . $request->url() . "<br>\n";
//            echo "->" . $uriLeft . "<br>\n";

            // modify request url's to match subroutes
            $request->baseUrl($oldBaseUrl . substr($oldUrl, 0, strlen($oldUrl) - strlen($urlLeft) - ($urlLeft ? 1 : 0)));
            $request->url('/' . $urlLeft);

            // execute callable
            call_user_func_array($this->callable, $args);

            // reset request url's
            $request->url($oldUrl);
            $request->baseUrl($oldBaseUrl);
            return true;
        }
        return false; // $app->finished();
    }

    public function url() {
        return $this->_app->request()->baseUrl() . 'a url';
    }

    protected function _preparePath($path) {
        $path = str_replace('/', '\/', $path);
        $path = preg_replace('/\:(\w+)/', '([^\/]+)\/?', $path);
        $path = str_replace('*', '(?P<_url>.*)', $path);
        $path = '/^' . $path . '$/';
        return $path;
    }

}

?>