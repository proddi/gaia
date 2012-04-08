<?php

abstract class scratchAppMiddleware {

    public function __invoke($app, $stack) {
        if (($callable = array_shift($stack))) {
            $callable($app, $stack);
        }
    }

}

?>