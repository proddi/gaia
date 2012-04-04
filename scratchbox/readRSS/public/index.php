<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../models', 'feed');

gaiaServer::run(

    // a router
    gaiaServer::router(array(
        '/:feed'       => function($req, $res) {
                                    $feed = new feedRss($req->params->feed);
                                    $res->send(gaiaView::render('body', array(
                                                                            'feed' => $feed,
                                                                            'baseUri' => $req->getBaseUri().$req->params->feed.'/'
                                                                        )));
                                },
        '/:feed/:guid'       => function($req, $res) {
                                    $feed = new feedRss($req->params->feed);
                                    $item = $feed->byGuid($req->params->guid);
                                    $res->send(gaiaView::render('detail', array('item' => $item)));
                                },
        '*' => function($req, $res) {
            $res->send('RSS Reading');
        }
    )),

    function($req, $res) {
        $res->send('header', gaiaView::render('header', array(
            'baseUri' => $req->getBaseUri()
        )));
    },

    function($req, $res) {
        $res->send('footer', gaiaView::render('footer', array(
            'baseUri' => $req->getBaseUri()
        )));
    },

    function($req, $res) {
        $res->send(gaiaView::render('layout', array(
            'baseUri'   => $req->getBaseUri(),
            'header'    => $res->content('header'),
            'content'   => $res->content(),
            'footer'    => $res->content('footer')
        )));
     }
);

?>