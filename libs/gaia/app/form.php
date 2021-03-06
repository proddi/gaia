<?php

/**
 * Form class
 *
 * @package gaia
 * @subpackage app
 * @author proddi@splatterladder.com
 */
class gaiaAppForm implements Iterator {

    public $name = 'default';
    protected $_fields = array();
    protected $_position = 0;
    protected $_isSubmit = false;
    protected $_app;

    public $valid;
    public $begin = '<form>';
    public $end = '</form>';

    public function isSubmit() { return $this->_isSubmit; }
    public function __construct($name) {
        $this->name = $name;
        $this->add(new gaiaAppFormHidden('__gaiaFormId', array('value' => $name)));
    }

    public function proceed($app) {
        $this->_app = $app;

        $req = $app->request();
        $this->begin = '<form action="' . $req->requestUrl() . '" method="post">';

        // Post to me?
        if ($req->post('__gaiaFormId') === $this->name) {
            $this->_isSubmit = true;
        }

        // proceed input fields
        foreach ($this->_fields as $input) {
            $input->proceed($app);
        }

        if ($this->_isSubmit) {
            $this->valid = true;
            foreach ($this->_fields as $input) {
                $this->valid &= $input->validateNow();
            }

            // call callbacks
            if ($this->_isSubmit) {
                foreach ($this->_onSubmits as $cb) $cb($this, $app);
                foreach (($this->valid ? $this->_onValids : $this->_onInvalids) as $cb) $cb($this, $app);
            }
        }
    }

    /**
     * render the whole form with all inputs
     *
     * @return string
     */
    public function __toString() {
        $markup = $this->begin.PHP_EOL;
        foreach($this->_fields as $input) {
            $markup .= $input->__toString().PHP_EOL;
        }
        $markup .= $this->end.PHP_EOL;

        return $markup;
    }

    public function add(gaiaAppFormAbstract $input) {
        $this->_fields[] = $input;
        $this->{$input->name} = $input;
        $input->form($this);
    }

    protected $_onSubmits = array();
    public function onSubmit($callback) {
        if ($this->_app) {
            if ($this->_isSubmit) $callback($this, $this->_app);
        } else {
            $this->_onSubmits[] = $callback;
        }
        return $this;
    }
    protected $_onValids = array();
    public function onValid($callback) {
        if ($this->_app) {
            if ($this->_isSubmit && $this->valid) $callback($this, $this->_app);
        } else {
            $this->_onValids[] = $callback;
        }
        return $this;
    }
    protected $_onInvalids = array();
    public function onInvalid($callback) {
        if ($this->_app) {
            if ($this->_isSubmit && !$this->valid) $callback($this, $this->_app);
        } else {
            $this->_onInvalids[] = $callback;
        }
        return $this;
    }

    final function rewind() { $this->_position = 0; }
    final function current() { return $this->_fields[$this->_position]; }
    final function key() { return $this->_position; }
    final function next() { ++$this->_position; }
    final function valid() { return isset($this->_fields[$this->_position]); }

    static public function text($name, array $cfg = array()) {
        return new gaiaAppFormText($name, $cfg);
    }

    static public function textarea($name, array $cfg = array()) {
        return new gaiaAppFormTextarea($name, $cfg);
    }

    static public function select($name, array $cfg = array()) {
        return new gaiaAppFormSelect($name, $cfg);
    }

    static public function submit($name, array $cfg = array()) {
        return new gaiaAppFormSubmit($name, $cfg);
    }

    static public function dropzone($name, array $cfg = array()) {
        return new gaiaAppFormDropzone($name, $cfg);
    }

    static public function autocomplete($name, array $cfg = array()) {
        return new gaiaAppFormAutocomplete($name, $cfg);
    }

}