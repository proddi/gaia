<?php

require_once('../../../libs/gaia.php');
require_once('auth.php');

$app = new gaiaApp();

$app->use('gaiaAppMiddlewareSession');
$app->use('gaiaAppMiddlewareAuth', function($login, $secret) {
    if (strlen($secret) < 4) {
        return 'MÖÖÖÖÖÖP! Für dieses Beispiel musst du ein Password mit mindestens 4 Zeichen eingeben. Das Passwort ansich ist egal. TolleSicherheit was?';
    }
    return (object) array(
        'name' => $login
    );
});

$app->map('/*', function(gaiaApp $app) {
    $app->requireUser('owner'); // force a logged user and check if it's an admin
    $app->requireRole('consumer', 'conductor', 'strangeGuys'); // force a logged user and check if it's an admin
    // or custom require function

    $app->response()->resource('assets/style.css');
    $app->response()->send($app->view()->render('layout', array(
        'baseUrl' => $app->request()->baseUrl(),
        'authorized' => true,
        'user' => $app->user,
        'res' => $app
    )));
});

$app();


/*
    $app->requireUser();
    if ($app->user()->name !== 'owner') throw new Exception();
    $app->user()->isRole('role2');
 */