<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/campus', 'campus');

/* Middleware setup with custom functions: */
$app = new gaiaApp(array(
    'pdo.dsn' => 'sqlite:../data/campus.sqlite'
));

$app->use('gaiaAppMiddlewareShortcuts');
$app->use('gaiaAppMiddlewarePdo');
$app->use('gaiaAppFormMiddleware');
$app->use('gaiaAppMiddlewareSession');

$app->map('/docs*', array('campusControllerDocs', 'map'))
    ->name('docs');
//$app->get('/docs/:pageId*', array('campusControllerPage', 'get'))
//    ->name('docs');

$app();