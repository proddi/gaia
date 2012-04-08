<?php

class scratchAppRouter {

    protected $_routes = array();

    public function routes() {
        return $this->_routes;
    }

    public function map($path, $callable) {
        $route = new scratchAppRoute($this, $path, $callable);
        $this->_routes[] = $route;
        return $route;
    }

    protected $_namedRoutes = array();
    public function namedRoute($name, scratchAppRoute $route = NULL) {
        if ($route) {
            $this->_namedRoutes[$name] = $route;
        }
        return $this->_namedRoutes[$name];
    }

}

?>
