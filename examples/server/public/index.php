<?php

require_once('../../../libs/gaia.php');

GAIA::registerNamespace('../../../scratchbox/simple/libs/scratch', 'scratch');
GAIA::registerNamespace('../controller', 'controller');

$app = new scratchApp();

// have inline function that return something via view
$app->get('/hello/:name*', function($name, scratchApp $app) {
    $app->response()->send("Hello $name");
});

// show default exception handling
$app->get('/exception', function(scratchApp $app) {
    throw new Exception('Foo');
});

// using lazy loading
$app->get('/blog/:post', array('controllerBlog', 'proceed'));

// index using view
$app->get('/', function(scratchApp $app) {
    $app->response()->send($app->view()->render('index'));
});

// run app instance
$app();