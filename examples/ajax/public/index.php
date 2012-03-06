<?php

require_once('../../../libs/gaia.php');

$quotes = array('When Life Gives You Questions, Google has Answers',
                '1f u c4n r34d th1s u r34lly n33d t0 g37 l41d',
                'If at first you don\'t succeed; call it version 1.0',
                'The glass is neither half-full nor half-empty: it\'s twice as big as it needs to be.',
                'I would love to change the world, but they won\'t give me the source code');

gaiaServer::run(

    // render time
    gaiaServer::ajax('time', function($req, $res) {
        $res->send('time', date('l jS \of F Y h:i:s'));
    }),

    gaiaServer::ajax('quote', function($req, $res) use ($quotes) {
        $res->send('quote', $quotes[time() / 3 % 5]);
    }, true),

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