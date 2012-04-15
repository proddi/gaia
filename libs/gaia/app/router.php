<?php

class gaiaAppRouter extends gaiaAppMiddleware {

    /**
     * @var array
     */
    protected $_routes = array();

    /**
     * @var callable
     */
    protected $_on404;

    public function routes() {
        return $this->_routes;
    }

    public function map($path, $callable) {
        $route = new gaiaAppRoute($this, $path, $callable);
        $this->_routes[] = $route;
        return $route;
    }

    /**
     *
     * @param callable $callback
     */
    public function on404($callback) {
        if (isset($callback)) $this->_on404 = $callback;
        return $this->_on404;
    }

    protected $_namedRoutes = array();
    public function namedRoute($name, gaiaAppRoute $route = NULL) {
        if ($route) {
            $this->_namedRoutes[$name] = $route;
        }
        return $this->_namedRoutes[$name];
    }

    public function __invoke($app, $stack) {
        $dispatched;
        foreach ($this->routes() as $route) {
            if (($dispatched = $route->dispatch($app))) break;
        }
        if (!$dispatched) {
            if (($callable = $this->_on404)) {
                $callable($app);
            }
        }
        parent::__invoke($app, $stack);
    }

}

?>