<?php

require_once('../../../libs/gaia.php');

$app = new gaiaApp();

$app->map('/', function(gaiaApp $app) {
    $app->response()->send($app->view()->render('qml-code'));

    $baseUri = $app->request()->baseUrl();
    $app->response()->resource($baseUri . '/../assets/style.css');
    $app->response()->resource($baseUri . '/../assets/sh_style.css');
    $app->response()->resource($baseUri . '/../assets/sh_main.js');
    $app->response()->resource($baseUri . '/../assets/sh_yate.js');
    $app->response()->resource($baseUri . '/../assets/sh_php.js');
    $app->response()->resource($baseUri . '/../assets/sh_qml.js');
    $app->response()->resource('sh_highlightDocument();');
});

$app();