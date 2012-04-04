<?php

/**
 * Description of form
 *
 * @author proddi@splatterladder.com
 */
class gaiaForm {


    static public function xform($name /*, middleware */) {
        return new form($name, array_slice(func_get_args(), 1));
    }

    // input stub
    static public function text($name, array $cfg = array()) {
        return new gaiaFormInputText($name, $cfg);
    }

    static public function textarea($name, array $cfg = array()) {
        return new gaiaFormInputTextarea($name, $cfg);
    }

    static public function password($name) {
        return new gaiaFormInputPassword($name);
    }

    static public function submit($name, $value) {
        return new gaiaFormInputSubmit($name, $value);
    }

    static public function hidden($name, $value) {
        return new gaiaFormInputHidden($name, $value);
    }

    // validators
    static public function validateRequired($msg) {

        //for Input::validate(), exexcute validateNow()
        return function($value) use ($msg) {
            return !empty($value) ? true : $msg;
        };
    }
    static public function validateMinLength($min, $msg) {

        //for Input::validate(), exexcute validateNow()
        return function($value) use ($min, $msg) {
            return strlen($value) >= $min ? true : $msg;
        };
    }
    static public function validateMaxLength($max, $msg) {

        //for Input::validate(), exexcute validateNow()
        return function($value) use ($max, $msg) {
            return strlen($value) <= $max ? true : $msg;
        };
    }
    static public function validateEmail($msg) {
        return self::validateRegExp('/^[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[\w!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+.)+[A-Z]{2,6}$/i' , $msg);
    }
    static public function validateRegExp($regExp, $msg) {

        //for Input::validate(), exexcute validateNow()
        return function($value) use ($regExp, $msg) {
            return preg_match($regExp, $value) > 0 ? true : $msg;
        };
    }

}

// ---- the form ----
class form implements Iterator {
    public $name = 'default';
    protected $_fields = array();
    protected $_position = 0;
    protected $_isSubmit = false;
    public $valid;
    public $begin = '<form>';
    public $end = '</form>';

    public function isSubmit() { return $this->_isSubmit; }
    public function __construct($name, array $mw = array() /* middlewares */) {
        $this->name = $name;
        $this->add(new gaiaFormInputHidden('__gaiaFormId', array('value' => $name)));
        foreach ($mw as $input) {
            if ($input instanceof gaiaFormInputAbstract) $this->add($input);
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

    public function add(gaiaFormInputAbstract $input) {
        $this->_fields[] = $input;
        $this->{$input->name} = $input;
        $input->form($this);
    }

    protected $_onSubmits = array();
    public function onSubmit($callback) { $this->_onSubmits[] = $callback; return $this; }
    public function onInvalidate() { return $this; }

    final function rewind() { $this->_position = 0; }
    final function current() { return $this->_fields[$this->_position]; }
    final function key() { return $this->_position; }
    final function next() { ++$this->_position; }
    final function valid() { return isset($this->_fields[$this->_position]); }
}

?>