<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

gaiaServer::run(

    scratchForm::form(
        scratchForm::text('login', array('value' => 'please login'))
            ->validate(scratchForm::validateMinLength(5, 'min: error message')) // add validator
            ->validate(scratchForm::validateMaxLength(5, 'max: error message')) // add validator
            ->decorator(scratchForm::viewDecorator('decorators/text', array('title' => 'Title'))), // add decorator
        scratchForm::password('password'),
        scratchForm::submit('submit', array('value' => 'absenden')),
        // store form for later rendering
        function($req, $res, $form) { $req->form = $form; }
    ),

    function($req, $res) {
        $res->send(gaiaView::render('form', array('form' => $req->form, 'req' => $req)));
    }

);

?>