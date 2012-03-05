<?php

require_once('../../../libs/gaia.php');

gaiaServer::run(
    // render time
    function($req, &$res) {
        $cnt = gaiaView::render('time', array('current' => date("r")));
        if ($req->isAjax()) {
            $res = new gaiaResponseAjax($res);
            return $res->finish('time', $cnt);
        }
        $cnt = '<div id="ajax_time" style="width: 40%; margin: auto; background-color: #EEE; border: 3px solid #CCC; text-align: center">' . $cnt . '</div>';
        $res->send('time', $cnt);
    },

    // apply layout
    function($req, &$res) {
        $res->resource('http://code.jquery.com/jquery-1.7.1.js');
        $res->resource($req->getBaseUri() . 'assets/script.js');
        $res->resource($req->getBaseUri() . 'assets/styles.css');

        $res->send(gaiaView::render('layout', array(
            'res' => $res,
            'baseUri' => $req->getBaseUri()
        )));
    }
);

?>