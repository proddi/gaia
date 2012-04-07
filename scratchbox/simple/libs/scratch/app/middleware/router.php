<?php

class scratchAppMiddlewareRouter {

    protected $routes = array();

    public function __construct($app) {
        $app->register('get', array($this, 'get'));
    }

    public function __invoke($app, $next) {
        foreach ($this->routes as $route) {
            if ($route->dispatch($app)) break;
        }
        return $next();
    }

    public function get($path, $callable) {
        $route = new scratchAppRoute($this, $path, $callable);
        $this->routes[] = $route;
        return $route;
    }

    public function bar() {}
}

?>
