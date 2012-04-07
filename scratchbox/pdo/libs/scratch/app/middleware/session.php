<?php

class scratchAppMiddlewareSession {

    public function __construct($app) {
    }

    public function __invoke($app, $next) {
        $next();
    }

}

?>
