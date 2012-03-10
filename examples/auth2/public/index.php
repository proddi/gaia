<?php

require_once('../../../libs/gaia.php');
require_once('auth.php');

gaiaServer::run(
    array(
        // this chain needs a valid user

        // show a login form if user hasn't logged in
        requireUser($validateUser),

        // now a user has logged in and ($req->user) is available (see $validateUser function)
        function($req, $res) {
            $res->send('Valid user area (' . $req->user->name . ')');
        }
    ),

    // layout is for every request
    function($req, &$res) {
        $res->resource('assets/style.css');

        $res->send(gaiaView::render('layout', array(
            'baseUri' => $req->getBaseUri(),
            'authorized' => !empty($req->user),
            'user' => isset($req->user) ? $req->user : null,
            'res' => $res
        )));
    }
);

?>