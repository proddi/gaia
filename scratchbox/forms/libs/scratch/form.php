<?php

/**
 * Description of form
 *
 * @author proddi@splatterladder.com
 */
class scratchForm {

    static public function form(/* middleware */) {
        $mw = func_get_args();
        $form = new form();
        foreach ($mw as $input) {
            if ($input instanceof input) $form->add($input);
        }
        return function($req, $res, $data) use ($mw, $form) {
            // TODO: path check, might other form was submitted, not me
            gaiaServer::_proceedMiddleware($mw, $req, $res, $form);
        };
    }

    // example stub
    static public function text($name, array $cfg = NULL) {
        return new input('text', $name, $cfg);
    }

    static public function password($name) {
        return new input('password', $name);
    }

    static public function submit($name, $value) {
        return new input('submit', $name, $value);
    }

    // validators
    static public function validateMinLength($min, $msg) {
        return function($value, &$error) use ($min, $msg) {
            $error = $msg;
            return strlen($value) < $min;
        };
    }
    static public function validateMaxLength($max, $msg) {
        return function($value, &$error) use ($max, $msg) {
            $error = $msg;
            return strlen($value) > $max;
        };
    }
    static public function validateRegExp($regexp, $msg) {
        return function($value, &$error) {
            $error = 'validateRegExp not implemented';
            return false;
        };
    }

    // decorators
    static function viewDecorator($template, array $params = NULL, $view = NULL) {}

}

// ---- the input field ----
class input {
    protected $_type;
    public $name;
    public $value;

    public function __construct($type, $name, array $cfg = NULL) {
        $this->type = $type;
        $this->name = $name;
        $this->value = isset($cfg['value']) ? $cfg['value'] : '';
    }

    public function validate() {
        return $this;
    }

    public function decorator() {
        return $this;
    }

    // event handler to catch form events, no rendering at this point
    public function __invoke($req, $res) {
        if ($req->isPost()) {
            if ('password' !== $this->type) $this->value = $req->post->{$this->name};
        }
//        echo "text->__invoke($this->_type, $this->name) => ${_POST[$this->name]}<br>\n";
    }

    public function __toString() {
        $method = 'render' . ucfirst($this->type);
        return $this->$method();
        return '<input autocapitalize="off" class="text" id="login_field" name="login" style="width: 15em;" tabindex="1" type="text" value="{{ login }}" />';
        return 'input->__toString(' . $this->type . ', ' . $this->name . ');';
    }

    protected function renderText() {
        return '<input autocapitalize="off" class="text" id="foo" name="' . $this->name . '" type="text" value="' . $this->value . '" />';
    }

    protected function renderPassword() {
        return '<input autocomplete="disabled" class="text" id="bar" name="' . $this->name . '" type="password" value="' . $this->value . '" />';
    }

    protected function renderSubmit() {
        return '<input name="' . $this->name . '" type="submit" value="' . $this->value . '" />';
    }

}

// ---- the form ----
class form implements Iterator {
    protected $_fields = array();
    protected $_position = 0;


    public function __toString() {
        return '[a form]';
    }

    public function add(input $input) {
        $this->_fields[] = $input;
        $this->{$input->name} = $input;
    }

    final function rewind() { $this->_position = 0; }
    final function current() { return $this->_fields[$this->_position]; }
    final function key() { return $this->_position; }
    final function next() { ++$this->_position; }
    final function valid() { return isset($this->_fields[$this->_position]); }
}

?>