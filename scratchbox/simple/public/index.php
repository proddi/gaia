<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

/* Minimal setup: * /
$app = new scratchApp();
$app->use(function($app) {
    $app->response()->send('Hello World!');
});
$app();
exit; /* */

/* Middleware setup with custom functions: */
$app = new scratchApp(array(
    'pdo' => 'scratchPdoSqlite',
    'pdo.config' => array(
        'dbname' => '../data/sqlite.sqlite'
    )
));

$app->use('scratchAppMiddlewareShortcuts');
$app->use('scratchAppMiddlewarePdo');

$app->get('/foo', function() use ($app) {
    $user = $app->query('SELECT idx, name, age FROM users WHERE idx=?', 2)->obj();
    $app->render('hello', array(
        'config' => $app->route('foo-route')->url(),
        'user' => $user
    ));
//    $app->stop();
//    throw new Exception('Foo', 23);
})->name('foo-route');

// IDEA for subrouter
$app->get('/sub', function() use ($app) {
    $app->get('/bar', function() {});
    $app();
})->name('sub-route');

$app();
exit; /* */






$app = new scratchApp(array(
    'view' => new scratchViewYate()
));


// $app->environment('production', function() {});
// $app->environment('staging', function() {});
// $app->environment('development', function() {});


// $app->middleware('scratchAppMiddlewareRouter');
/*
$app->get('/foo', function() use ($app) {
//    var_dump($app->request()->uri(), $app->request()->baseUri());
//    echo highlight_string($app->view()->compile('foo', array('foo' => 'bar')));
    $app->render('foo', array('foo' => 'bar'));
//    $app->renderTo('menu', 'template', array());
//    echo $app->content();
// ----------->    $app->response();
//    $app->response(new scratchResponseImage($image));
})->name('foo');
*/

$app();

?>