<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/own', 'own');

gaiaServer::run(
        // mixin console support
        gaiaLog::consoleSupport(),
        // a router
        gaiaServer::router(array(
                '/hello/:id*' => new ownController(),
                '*' => function($req, $res) { $res->log(2); }
            )),
        // using gaiaView
        function($req, $res) { $res->send(gaiaView::render('index', array('content' => $res->content(), 'msg' => $res->content('msg')))); }
    );

?>