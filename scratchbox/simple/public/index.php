<?php
/**
 * TODO:
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
$app->use('scratchAppFormMiddleware');

$app->get('/foo/:bar/:param*', function($bar, $param, $app) {
    scratchModel::db($app->db()); // register global db

    $user = scratchModelUser::byName('Hans');

    $app->render('hello', array(
        'bar' => $bar,
        'param' => $param,
        'user' => $user
    ));
    $app->stop(); // might $app->finish() / ->halt() / ->continue()
    throw new Exception('Foo', 23);
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
$app->get('/form/:foo*', function($foo, $app) {
    $form = $app->form('postAsGuest',
        scratchAppForm::text('login', array('value' => 'name'))
            ->validate(gaiaForm::validateMinLength(5, 'min 5 characters'))
            ->validate(gaiaForm::validateMaxLength(20, 'max 20 characters')),
        scratchAppForm::text('email', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateEmail('looks not like a valid email address')),
//        gaiaForm::textarea('text', array('watermark' => 'you@email.com'))
//            ->validate(gaiaForm::validateMinLength(10, 'min 10 characters')),
        new scratchAppFormInputCaptcha('captcha'),
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

// index
$app->get('/', function($app) {
    $app->response()->send('call with /foo/bar/blubb/demo<br>');
    $app->response()->send('call with /sub/foo<br>');
});

$app->on404(function($app) {
    $app->response()->send('404 Not found!');
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
//    var_dump($app->request()->url(), $app->request()->baseUrl());
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