<?php

class scratchAppFormMiddleware extends scratchAppMiddleware {

    protected $_app;

    public function __construct($app) {
        $this->_app = $app;
        $app->register('form', array($this, 'form'));
    }

    public function form($name /* input fields */) {
        $app = $this->_app;
        $form = new scratchAppForm($name);
        foreach (array_slice(func_get_args(), 1) as $input) {
            $form->add($input);
        }
        $form->proceed($app);
        return $form;
    }
}

?>