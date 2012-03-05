<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../models', 'example');

// Configure
$config = array (
    'adapter' => 'pdoSqlite',
    'database' => array(
        'host' => 'localhost',
        'dbname' => '../data/user.sqlite',
        'username' => 'root',
        'password' => ''
    )
);
gaiaDb::setConfig($config);

// add exception handler
gaiaServer::onException(function($req, $res, Exception $e) {
    echo $e->__toString();
//    $res->error($e->getMessage().' Exception '.$e->getFile());
//    $res->finish(gaiaView::render('error.500', array('error' => $e)));
});

//$cfg = gaiaView::config();
//$cfg['filters']->query = function($text) { return str_replace('one', '---', $text); };
//gaiaView::config($cfg);

gaiaServer::run(
        // a router
        gaiaServer::router(array(
            '/query/user' => function($req, $res) {
                $users = new exampleUsers(exampleUsers::QUERY, $_GET['q']);
                $res->send(gaiaView::render('users', array(
                    'users' => $users,
                    'baseUri' => $req->getBaseUri(),
                    'quote' => $_GET['q']
                )));
            },
            '/user/:id*' => function($req, $res) {
                $user = new exampleUser($req->params->id, exampleUser::INDEX);
                $res->send(gaiaView::render('user', array('user' => $user)));
            },
            '*' => function($req, $res) {
                $users = new exampleUsers(exampleUsers::INDEX);
                $res->send(gaiaView::render('users', array(
                    'users' => $users,
                    'baseUri' => $req->getBaseUri()
                )));
            }
        )),

        function($req, &$res) {
            $res->resource($req->getBaseUri() . 'assets/styles.css');

            $res->send(gaiaView::render('layout', array(
                'res' => $res,
                'baseUri' => $req->getBaseUri()
            )));
        }

    );

?>