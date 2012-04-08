<?php

class scratchAppRouter extends scratchAppMiddleware {

    /**
     * @var array
     */
    protected $_routes = array();

    /**
     * @var callable
     */
    protected $_notFound;

    public function routes() {
        return $this->_routes;
    }

    public function map($path, $callable) {
        $route = new scratchAppRoute($this, $path, $callable);
        $this->_routes[] = $route;
        return $route;
    }

    /**
     *
     * @param callable $callback
     */
    public function notFound($callback) {
        $this->_notFound = $callback;
    }

    protected $_namedRoutes = array();
    public function namedRoute($name, scratchAppRoute $route = NULL) {
        if ($route) {
            $this->_namedRoutes[$name] = $route;
        }
        return $this->_namedRoutes[$name];
    }

    public function __invoke($app, $stack) {
        try {
            foreach ($this->routes() as $route) {
                if ($route->dispatch($app)) break;
            }
        } catch (scratchAppExceptionStop $e) {
        }
        parent::__invoke($app, $stack);
    }

}

?>