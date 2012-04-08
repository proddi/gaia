<?php

/**
 * TODO:
 * - router extrahieren ... wegen ... kann auch nen filerouter geben, der auf FS mappt
 * - '/sub/:foo' => function($foo, $app)
 */

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
    'db' => 'scratchDbSqlite',
    'db.config' => array(
        'dbname' => '../data/sqlite.sqlite'
    )
));

$app->use('scratchAppMiddlewareShortcuts');
$app->use('scratchDbMiddleware');

$app->get('/foo/:bar/:param*', function($bar, $param, $app) {
    scratchModel::db($app->db()); // register global db

    $user = scratchModelUser::byName('Hans');

    $app->render('hello', array(
        'bar' => $bar,
        'param' => $param,
        'user' => $user
    ));
//    $app->stop(); // might $app->finish() / ->halt() / ->continue()
//    throw new Exception('Foo', 23);
})->name('foo-route');

// IDEA for subrouter
$app->get('/sub/:foo', function($foo, $app) {
    $app->get('/sub/foo', function($app) {
        $app->response()->send('subroute / handler');
//        throw new Exception('Foo', 23);
    });
    $app();
})->name('sub-route');

// index
$app->get('/', function() use ($app) {
    $app->response()->send('call with /foo/bar/blubb/demo<br>');
    $app->response()->send('call with /sub/foo<br>');
});

$app->notFound(function($app) {
    $app->response->send('404 Not found!');
});

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