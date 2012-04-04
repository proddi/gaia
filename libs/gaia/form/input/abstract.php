<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:21 PM
 */

//TODO: attribute per option array
//TODO: form __toString() soll komplette Form ausgeben {{ form }}
//TODO: Form example erstellen + alle forms in scratchBox löschen


/**
 * abstract Input Class
 */
abstract class gaiaFormInputAbstract {
    public $name;
    public $label   = null;
    public $id      = '';
    public $value   = '';
    public $valid;
    public $errors  = array();
    public $form;

    /**
     * @param $name
     * @param array $cfg
     */
    public function __construct($name, array $cfg = array()) {
        $this->name = $name;
        foreach ($cfg as $key => $value) { $this->$key = $value; }
    }

    /**
     * call in form::add()
     * @param form|null $form
     * @return form|null
     */
    public function form(form $form = NULL) {
        if ($form) {
            $this->form = $form;
            $this->id = 'id_'.$this->form->name.'_'.$this->name;
        }
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
        return '<div class="field">'
                    . ($this->label ? '<label for="'. $this->id .'">'. $this->label .'</label>' : '')
                    . $this->markup()
                .'</div>';
    }

    //input html markup function should implement all child classes
    abstract public function markup();
}