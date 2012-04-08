<?php

class scratchAppMiddlewareSession extends scratchAppMiddleware {

    public function __construct($app) {
        parent::__construct($app);
        $app->register('session', array($this, '__getSessionObject'));
    }

    public function __invoke($app, $stack) {
        session_start();
        parent::__invoke($app, $stack);
        session_write_close();
    }

    public function __getSessionObject() {
        return $this;
    }

    public function destroy() {
        return session_destroy();
    }

    public function regenerate() {
        return session_regenerate_id(true);
    }

    public function __get($name) {
        return @$_SESSION[$name];
    }

    public function __set($name, $value) {
        return @$_SESSION[$name] = $value;
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

}