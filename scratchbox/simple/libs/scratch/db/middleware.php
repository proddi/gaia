<?php

class scratchDbMiddleware extends scratchAppMiddleware {

    protected $_db;

    public function __construct($app) {
        $adapter = $app->config('db');
        if (is_string($adapter)) {
            $adapter = new $adapter($app->config('db.config'));
        }
        $this->_db = $adapter;
        $app->register('query', array($this->_db, 'query'));
        $app->register('db', array($this, 'db'));
    }

    public function db() {
        return $this->_db;
    }
}

?>