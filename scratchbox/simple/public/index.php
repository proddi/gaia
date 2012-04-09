<?php
/**
 * TODO:
 */

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

/* Middleware setup with custom functions: */
$app = new scratchApp(array(
    'db' => 'scratchDbSqlite',
    'db.config' => array(
        'dbname' => '../data/sqlite.sqlite'
    )
));

$app->use('scratchAppMiddlewareShortcuts');
$app->use('scratchDbMiddleware');
$app->use('scratchAppFormMiddleware');
$app->use('scratchAppMiddlewareSession');

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
$app->get('/sub/:foo*', function($foo, $app) {
    $app->get('/', function($app) {
        $app->response()->send('subroute / handler');
//        throw new Exception('Foo', 23);
    });
    $app();
})->name('sub-route');

// index
$app->get('/form*', function($app) {
    $form = $app->form('postAsGuest',
        scratchAppForm::text('login', array('value' => 'name'))
            ->validate(gaiaForm::validateMinLength(5, 'min 5 characters'))
            ->validate(gaiaForm::validateMaxLength(20, 'max 20 characters')),
        scratchAppForm::text('email', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateEmail('looks not like a valid email address')),
        scratchAppForm::textarea('text', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateMinLength(10, 'min 10 characters')),
        new scratchAppFormCaptcha('captcha'),
        scratchAppForm::submit('submit', array('value' => 'absenden'))
    )->onSubmit(function($form, $app) {
        echo "form->onSubmit() name={$form->login->value}<br>\n";
    })->onValid(function($form, $app) {
        echo "form->onValid()<br>\n";
    })->onInvalid(function($form, $app) {
        echo "form->onInvalid()<br>\n";
    });

    $app->render('form', array(
        'form' => $form
    ));
});

$app->get('/session', function($app) {
    $app->session()->view++;
    echo 'Visit #' . $app->session()->view . " (/session/destroy to remove session data)" . "<br>\n";
});
$app->get('/session/destroy', function($app) {
    $app->session()->destroy();
    echo 'session destroyed' . "<br>\n";
});


// index
$app->get('/', function($app) {
    $app->response()->send('call with /foo/bar/blubb/demo<br>');
    $app->response()->send('call with /sub/foo<br>');
});

$app->on404(function($app) {
    $app->response()->send('404 Not found!');
});

$app();