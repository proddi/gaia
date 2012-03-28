<?php

// Configure DB
$config = array (
    'adapter' => 'pdoSqlite',
    'database' => array(
        'host' => 'localhost',
        'dbname' => '../data/campus.sqlite',
        'username' => 'root',
        'password' => ''
    )
);
gaiaDb::setConfig($config);

// add exception handler
gaiaServer::onException(function($req, $res, Exception $e) {
    $res->content();
    $res->finish(gaiaView::render('error.500', array('error' => $e)));
});

?>