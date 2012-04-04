<?php

//error_reporting(0);

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

session_start();

gaiaServer::run(
    gaiaServer::path('/clear', function($req, &$res) {
        unset($_SESSION['comments']);
        $res = new gaiaResponseRedirect($req->getBaseUri());
    }),
    function($req, $res, $data) {
        $data->comments = array_key_exists('comments', $_SESSION)
            ? $_SESSION['comments']
            : ($_SESSION["comments"] = array());
    },

    gaiaForm::xform('postAsGuest',
        gaiaForm::text('login', array('value' => 'name'))
            ->validate(gaiaForm::validateMinLength(5, 'min 5 characters'))
            ->validate(gaiaForm::validateMaxLength(20, 'max 20 characters')),
        gaiaForm::text('email', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateEmail('looks not like a valid email address')),
        gaiaForm::textarea('text', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateMinLength(10, 'min 10 characters')),
        new gaiaFormInputCaptcha('captcha'),
        gaiaForm::submit('submit', array('value' => 'absenden'))
    )
        ->onSubmit(function($req, $res, $data) {
            $data->message = 'Message sent!';
            $form = $req->forms->postAsGuest;
            $data->comments[] = (object) array( // fake comment model
                'text' => $form->text->value,
                'author' => (object) array( // fake user model
                    'name' => $form->login->value,
                    'email' => $form->email->value
                ),
                'date' => time()
            );
            var_dump("ON GUEST SUBMIT!!!");
            $_SESSION['comments'] = $data->comments;
        })
    ,

    gaiaForm::xform('postAsAdmin',
        gaiaForm::textarea('text', array('watermark' => 'you@email.com'))
            ->validate(gaiaForm::validateMinLength(10, 'min 10 characters')),
        new gaiaFormInputCaptcha('captcha'),
        gaiaForm::submit('submit', array('value' => 'absenden'))
    )
        ->onSubmit(function($req, $res, $data) {
            $data->message = 'Message sent!';
            $form = $req->forms->postAsAdmin;
            $data->comments[] = (object) array( // fake comment model
                'text' => $form->text->value,
                'author' => (object) array( // fake user model
                    'name' => 'admin',
                    'email' => 'admin@newworld.order'
                ),
                'date' => time()
            );
            var_dump("ON ADMIN SUBMIT!!!");
            $_SESSION['comments'] = $data->comments;
        })
    ,

    function($req, $res, $data) {
        $res->send('guestForm', gaiaView::render('form', array(
            'form' => $req->forms->postAsGuest,
            'req' => $req
        )));

        $res->send('adminForm', gaiaView::render('form', array(
            'form' => $req->forms->postAsAdmin,
            'req' => $req
        )));

        $res->send('comments', gaiaView::render('comments', array(
            'comments' => $data->comments
        )));
    },

    function($req, $res) {
        $res->send(gaiaView::render('layout', array(
            'comments' => $res->content('comments'),
            'guestForm' => $res->content('guestForm'),
            'adminForm' => $res->content('adminForm'),
            'baseUri' => $req->getBaseUri()
        )));
    }
);

?>