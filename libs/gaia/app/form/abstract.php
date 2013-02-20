<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:21 PM
 */

//TODO: Form example erstellen + alle forms in scratchBox löschen

/**
 * abstract Input Class
 */
abstract class gaiaAppFormAbstract {
    public $name;
    public $label   = null;
    public $id      = '';
    public $value   = '';
    public $valid;
    public $errors  = array();
    public $form;
    protected $_focused = false;

    /**
     * @param $name
     * @param array $cfg
     */
    public function __construct($name, array $cfg = array()) {
        $this->name = $name;
        foreach ($cfg as $key => $value) { $this->$key = $value; }
    }

    static public function create($name, array $cfg = array()) {
        return new static($name, $cfg);
    }

    /**
     * call in form::add()
     * @param form|null $form
     * @return form|null
     */
    public function form(gaiaAppForm $form = NULL) {
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

    public function focus() {
        $this->_focused = true;
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
            $error = $cb($this->value, $this);
            if (is_string($error)) {
                $this->valid = false;
                $this->errors[] = $error;
            }
        }
        return $this->valid;
    }

    /**
     * Filters the value during submit
     * @param callable|string $filter
     * @param mixed $parem
     * @param string $message
     * @return gaiaAppFormAbstract
     */
    public function filter($filter, $parem = NULL) {
        return $this;
    }

    // event handler to catch form events, no rendering at this point
    public function proceed($app) {
        if ($this->form->isSubmit()) {
            $this->value = $app->request()->post($this->name);
        }
    }

    // event handler to catch form events, no rendering at this point
    public function __invoke($req, $res) {
        if ($this->form->isSubmit()) {
            $this->value = $req->post->{$this->name};
        }
    }

    /**
     * render the whole input element
     *
     * @return string
     */
    public function __toString() {
        return '<div class="field'. ($this->errors ? ' error' : '') .'">'
                    . ($this->label ? '<label for="'. $this->id .'">'. $this->label .'</label>' : '')
                    . $this->markup()
                    . ($this->errors ? '<span class="error">'. implode(PHP_EOL, $this->errors) .'</span>' : '')
                .'</div>';
    }

    //input html markup function should implement all child classes
    abstract public function markup();
}