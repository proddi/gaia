<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:21 PM
 */

// ---- the input field ----
abstract class gaiaFormInput {
    public $name;
    public $value = '';
    public $label = null;
    public $valid;
    public $errors = array();
    public $form;

    public function __construct($name, array $cfg = array()) {
        $this->name = $name;
        foreach ($cfg as $key => $value) { $this->$key = $value; }
    }

    public function form(form $form = NULL) {
        if ($form) $this->form = $form;
        return $form;
    }

    public function label($label) {
        $this->label = $label;
        return $this;
    }

    protected $_validators = array();
    public function validate($validator) {
        $this->_validators[] = $validator;
        return $this;
    }

    public function validateNow() {
        $this->valid = true;
        foreach ($this->_validators as $cb) {
            $error = $cb($this->value);
            if (is_string($error)) {
                $this->valid = false;
                $this->errors[] = $error;
            }
        }
        return $this->valid;
    }
    // event handler to catch form events, no rendering at this point
    public function __invoke($req, $res) {
        if ($this->form->isSubmit()) {
            $this->value = $req->post->{$this->name};
        }
    }

    public function __toString() {
        return '<div class="field">'. $this->markup() .'</div>';
    }

    //input html markup function should implement all child classes
    abstract public function markup();
}