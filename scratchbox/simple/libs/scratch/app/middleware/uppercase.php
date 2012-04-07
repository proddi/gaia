<?php

class scratchAppMiddlewareUppercase {

    public function __invoke($app, $next) {
        $next();
        $res = $app->response();
        $res->send(strtoupper($res->content()));
    }

}

?>
