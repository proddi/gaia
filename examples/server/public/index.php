<?php

error_reporting(error_reporting() ^ E_STRICT);
require_once('../../../libs/gaia.php');

// add exception handler
gaiaServer::onException(function($req, $res, Exception $e) {
    $res->error($e->getMessage().' Exception '.$e->getFile());
    $res->finish(gaiaView::render('error.500', array('error' => $e)));
});

gaiaServer::run(
        // mixin console support
        gaiaLog::consoleSupport(),
        // log output
        function($req, $res) { $res->log('Ohhh\' wie ist das schön...'); },
        // a router
        gaiaServer::router(array(
                '/hello/:id*' => function($req, $res) { $res->finish(gaiaView::render('hello', array('params' => $req->params))); },
                '*' => function($req, $res) { $res->log(2); }
            )),
        // middleware exception handling
        gaiaServer::tryCatch(
                function($req, $res) { $res->log('tryCatch.1');},
                function($req, $res) { $res->log('tryCatch.2'); throw new Exception('Foo'); /* */},
                function($req, $res) { $res->log('tryCatch.3');},
                function($req, $res, $e) { $res->error('tryCatch.ex', $e->getMessage()); }
            ),
        // middleware can also be an array
        array(
                function($req, $res) { $res->log(4.1); },
                function($req, $res) { $res->log(4.2); },
                function($req, $res) { $res->log(4.3); }
            ),
        // using gaiaView
        function($req, $res) { $res->send(gaiaView::render('index')); }
    );

?>