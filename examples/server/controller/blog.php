<?php

class controllerBlog {

    // static entry point for lazy loading
    static public function proceed($post, scratchApp $app) {
        return call_user_func_array(new static(), func_get_args());
    }

    public function __invoke($post, scratchApp $app) {
        $app->response()->send($app->view()->render('blog', array(
            'post' => $post
        )));
    }

}

?>
