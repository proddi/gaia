<?php

require_once('../../../libs/gaia.php');

gaiaServer::run(

    // a router
    gaiaServer::router(array(
        '/postillon*'       => function($req, $res) {
                                    $res->send('postillon');
                                },
        '/sz*'              => function($req, $res) {
                                    $res->send('sueddeutsche');
                                },
        '/macnotes*'        => function($req, $res) {
                                    $res->send('macnotes');
                                },
        '*' => function($req, $res) {
            $res->send('RSS Reading');
        }
    )),

    function($req, $res) {
        $res->send(gaiaView::render('header', array(
            'baseUri' => $req->getBaseUri()
        )));
    },

    function($req, $res) {
        $res->send(gaiaView::render('footer', array(
            'baseUri' => $req->getBaseUri()
        )));
    }
);

?>