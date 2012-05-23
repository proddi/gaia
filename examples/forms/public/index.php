<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 5/2/12
 * Time: 9:46 PM
 * To change this template use File | Settings | File Templates.
 */
require_once('../../../libs/gaia.php');

/* Middleware setup with custom functions: */
$app = new gaiaApp();

$app->use('gaiaAppFormMiddleware');
$app->use('gaiaAppMiddlewareSession');

$app->map('/books*', function(gaiaApp $app) {
    $form = $app->form('new',
        gaiaAppForm::text('title', array('value' => 'foo'))
                ->label('Book name'),
        new gaiaAppFormCaptcha('captcha'),
        gaiaAppForm::submit('submit', array('value' => 'Add Book'))

    );
    $form->captcha->label('Are you human');
    $form->onValid(function($form, gaiaApp $app) {
                $app->response()->send('<p><h2>Form is valid!</h2></p>');
//                $app->stop();
            });
    $app->response()->send($form);

    $app->response()->send('You are visitor no ' + $app->session()->visits++);
});

$app();