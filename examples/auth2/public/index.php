<?php

require_once('../../../libs/gaia.php');
require_once('auth.php');

$app = new gaiaApp();

$app->use('gaiaAppMiddlewareSession');
$app->use('authMiddleware');

$app->map('/*', function(gaiaApp $app) {
    $app->requireUser(); // force a logged user
//    $app->user()->requireGroup(); // require a special group
//    $app->user()->requireUser();  // require a special user
    //
//    $app->user()->name ....

//    $app->response()->send('Valid user area (' . $app->user->name . ')');

    $app->response()->resource('assets/style.css');
    $app->response()->send($app->view()->render('layout', array(
        'baseUrl' => $app->request()->baseUrl(),
        'authorized' => true,
        'user' => $app->user,
        'res' => $app
    )));
});

$app();
exit;

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