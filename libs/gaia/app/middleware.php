<?php

abstract class gaiaAppMiddleware {

    protected $_app;

    public function __construct($app) {
        $this->_app = $app;
    }

    public function __invoke($app, $stack) {
        if (($callable = array_shift($stack))) {
            $callable($app, $stack);
        }
    }

}