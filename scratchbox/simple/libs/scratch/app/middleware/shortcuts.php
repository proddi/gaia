<?php

class scratchAppMiddlewareShortcuts extends scratchAppMiddleware {

    public function __construct($app) {
        parent::__construct($app);
        $app->register('render', array($this, 'render'));
    }

    public function render($ctx, $template = NULL, $data = NULL) {
        $app = $this->_app;
        if (is_string($template)) {
            $app->response()->send($ctx, call_user_func_array(array($app->view(), 'render'), array_slice(func_get_args(), 1)));
        } else {
            $app->response()->send(call_user_func_array(array($app->view(), 'render'), func_get_args()));
        }
    }
}

?>