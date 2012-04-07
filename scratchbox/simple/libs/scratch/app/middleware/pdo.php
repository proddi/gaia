<?php

class scratchAppMiddlewarePdo {

    protected $_pdo;

    public function __construct($app) {
        $adapter = $app->config('pdo');
        if (is_string($adapter)) {
            $adapter = new $adapter($app->config('pdo.config'));
        }
        $this->_pdo = $adapter;
        $app->register('query', array($this->_pdo, 'query'));
        $app->register('pdo', array($this, 'pdo'));
    }

    public function __invoke($app, $next) {
        $next();
    }

    public function pdo() {
        return $this->_pdo;
    }
}

?>
