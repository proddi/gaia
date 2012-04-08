<?php

class scratchAppForm implements Iterator {

    public $name = 'default';
    protected $_fields = array();
    protected $_position = 0;
    protected $_isSubmit = false;
    protected $_app;

    public $valid;
    public $begin = '<form>';
    public $end = '</form>';

    public function isSubmit() { return $this->_isSubmit; }
    public function __construct($name, array $mw = array() /* middlewares */) {
        $this->name = $name;
        $this->add(new scratchAppFormInputHidden('__gaiaFormId', array('value' => $name)));
        foreach ($mw as $input) {
            if ($input instanceof gaiaFormInputAbstract) $this->add($input);
        }
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

    public function __invoke(&$req, &$res, &$data) {
        // register form
        if (!isset($req->forms)) $res->forms = new gaiaInvokable();
        $req->forms->{$this->name} = $this;

        $this->begin = '<form action="' . $req->requestUri . '" method="post">';

        // TODO: path check, might other form was submitted, not me
        if ($req->isPost() && ($req->post->__gaiaFormId === $this->name)) {
            $this->_isSubmit = true;
        }

        // call the fields and exit if needed
        if (gaiaServer::BREAKCHAIN === gaiaServer::mw($this->_fields, $req, $res, $data)
                || $res->isFinish()) {
            return;
        }

        // validate
        if ($this->_isSubmit) {
            $this->valid = true;
            foreach ($this->_fields as $input) {
                $this->valid &= $input->validateNow();
            }

            // and onSubmit
            if ($this->valid && $this->_isSubmit) {
                foreach ($this->_onSubmits as $cb) $cb($req, $res, $data);
            }
        }
    }

    public function __toString() {
        return '[a form "' . $this->name .'"]';
    }

    public function add(scratchAppFormInputAbstract $input) {
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
        return new scratchAppFormInputText($name, $cfg);
    }

    static public function submit($name, array $cfg = array()) {
        return new scratchAppFormInputSubmit($name, $cfg);
    }

}

?>