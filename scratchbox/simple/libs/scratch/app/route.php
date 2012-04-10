<?php

class scratchAppRoute {

    protected $_router;
    protected $path;
    protected $callable;
    protected $_methods;
    protected $_conditions = array();

    public function __construct($router, $path, $callable) {
        $this->_router = $router;
        $this->path = $this->_preparePath($path);
        $this->callable = $callable;
    }

    /**
     * Restrict that route to the given method(s).
     * @param string $method
     * @return scratchAppRoute
     */
    public function via($method/* method2, method3, ...*/) {
        $this->_methods = func_get_args();
        return $this;
    }

    /**
     * Set a name for that route to get an access point for the url helper.
     * @param string $name
     * @return scratchAppRoute
     */
    public function name($name) {
        $this->_router->namedRoute($name, $this);
        return $this;
    }

    /**
     * Add a condition to the route. Every condition must be true for that route.
     * @param callable $condition
     * @return scratchAppRoute
     */
    public function when($condition) {
        $this->_conditions[] = $condition;
        return $this;
    }

    public function dispatch($app) {
        if ($this->_methods && !in_array($app->request()->method(), $this->_methods)) {
            return;
        }
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

            // check conditions
            $ok = true;
            foreach ($this->_conditions as $condition) $ok &= call_user_func($condition);
            if (!$ok) return false; // not valid

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