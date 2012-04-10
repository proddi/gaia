<?php
require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../../../scratchbox/simple/libs/scratch', 'scratch');

$app = new scratchApp(array(
    'pdo.dsn' => 'sqlite:../data/sqlite.sqlite'
));

$app->use('scratchAppMiddlewarePdo');

$app->get('/', function(scratchApp $app) {
    // simple query
    $sql = 'SELECT idx, name FROM users';
    $app->response()->send($app->view()->render('example', array(
        'title' => 'Simple query',
        'desc' => 'This example shows the normal query function. No execute() is needed. Data gets fetched as associative array.',
        'code' => '$app->query("' . $sql . '")->allAssoc()',
        'data' => $app->query($sql)->allAssoc()
    )));

    // prepared statement
    $sql = 'SELECT * FROM users WHERE name LIKE ?';
    $app->response()->send($app->view()->render('example', array(
        'title' => 'Prepared statement',
        'desc' => 'This example shows the chaining of execute() function and object fetching.',
        'code' => '$app->prepare("' . $sql . '")->execute("%a%")->allObj()',
        'data' => $app->prepare($sql)->execute('%a%')->allObj()
    )));

});

$app();