<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

$cfg = gaiaView::view()->config();
$cfg['filters']->decorator = function($input) {
    return gaiaView::render('decorator', array('input' => $input));
};

gaiaServer::run(
    scratchForm::xform('login',
        scratchForm::text('login', array('value' => 'please login'))
            ->validate(scratchForm::validateMinLength(5, 'min: error message'))
            ->validate(scratchForm::validateMaxLength(10, 'max: error message')),
        scratchForm::text('email', array('watermark' => 'you@email.com'))
            ->validate(scratchForm::validateEmail('looks not like a valid email address')),
        scratchForm::password('password'),
        scratchForm::submit('submit', array('value' => 'absenden'))
    )
        ->onSubmit(function($req, $res, $data) {
            var_dump("ON SUBMIT!!!");
        })
    ,

    scratchForm::xform('something_others',
        scratchForm::text('login', array('value' => 'please login'))
            ->validate(scratchForm::validateMinLength(5, 'min: error message')) // add validator
            ->validate(scratchForm::validateMaxLength(10, 'max: error message')), // add validator
        scratchForm::text('email', array('watermark' => 'you@email.com'))
            ->validate(scratchForm::validateEmail('looks not like a valid email address')), // add validator
        scratchForm::textarea('text'),
        scratchForm::submit('submit', array('value' => 'absenden'))
    )
        ->onSubmit(function($req, $res, $data) {
            var_dump("ON SUBMIT something_others!!!");
        })
        ->onInvalidate()
    ,

    function($req, $res) {
        $res->send(gaiaView::render('form', array(
            'form' => $req->form->login,
            'others' => $req->form->something_others,
            'req' => $req
        )));
    }

);

?>