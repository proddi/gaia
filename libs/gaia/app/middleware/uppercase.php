<?php

class gaiaAppMiddlewareUppercase extends gaiaAppMiddleware {

    public function __invoke($app, $stack) {
        parent::__invoke($app, $stack);
        $res = $app->response();
        $res->send(strtoupper($res->content()));
    }

}

?>
