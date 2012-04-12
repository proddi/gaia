<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../../simple/libs/scratch', 'scratch');
GAIA::registerNamespace('../libs/campus', 'campus');

/* Middleware setup with custom functions: */
$app = new scratchApp(array(
    'pdo.dsn' => 'sqlite:../data/campus.sqlite'
));

$app->use('scratchAppMiddlewareShortcuts');
$app->use('scratchAppMiddlewarePdo');
$app->use('scratchAppFormMiddleware');
$app->use('scratchAppMiddlewareSession');

$app->map('/docs*', array('campusControllerDocs', 'map'))
    ->name('docs');
//$app->get('/docs/:pageId*', array('campusControllerPage', 'get'))
//    ->name('docs');

$app();