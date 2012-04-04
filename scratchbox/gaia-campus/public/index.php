<?php

//error_reporting(0);

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/campus', 'campus');

require_once 'config-dev.php';

//session_start();

gaiaServer::run(
    gaiaServer::router(array(
        '/docs/:pageId*' => campusServer::controller('campusControllerPage')
    )),

    function($req, $res) {
        $baseUri = $req->getBaseUri();
        $res->resource($baseUri . 'assets/style.css');
        $res->resource($baseUri . 'assets/sh_style.css');
        $res->resource($baseUri . 'assets/sh_main.js');
        $res->resource($baseUri . 'assets/sh_yate.js');
        $res->resource('sh_highlightDocument();');
        $res->send(gaiaView::render('layout', array(
            'content' => $res->content()
        )));
    }
);

?>