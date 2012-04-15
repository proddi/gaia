<?php

require_once('../../../libs/gaia.php');

// GAIA::registerNamespace('../../../scratchbox/simple/libs/scratch', 'scratch');
GAIA::registerNamespace('../controller', 'controller');

$app = new gaiaApp();

// have inline function that return something via view
$app->get('/hello/:name*', function($name, gaiaApp $app) {
    $app->response()->send("Hello $name");
});

// show default exception handling
$app->get('/exception', function(gaiaApp $app) {
    throw new Exception('Foo');
});

// using lazy loading
$app->get('/blog/:post', array('controllerBlog', 'proceed'));

// index using view
$app->get('/', function(gaiaApp $app) {
    $app->response()->send($app->view()->render('index'));
});

// run app instance
$app();