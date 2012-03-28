<?php

/**
 * Description of form
 *
 * @author proddi@splatterladder.com
 */
class scratchForm {


    static public function xform($name /*, middleware */) {
        return new form($name, array_slice(func_get_args(), 1));
    }

    // input stub
    static public function text($name, array $cfg = array()) {
        return new input('text', $name, $cfg);
    }

    static public function textarea($name, array $cfg = array()) {
        return new inputTextarea('textarea', $name, $cfg);
    }

    static public function password($name) {
        return new inputPassword('password', $name);
    }

    static public function submit($name, $value) {
        return new input('submit', $name, $value);
    }

    static public function hidden($name, $value) {
        return new inputHidden('hidden', $name, $value);
    }

    // validators
    static public function validateMinLength($min, $msg) {
        return function($value, &$error) use ($min, $msg) {
            $error = $msg;
            return strlen($value) >= $min;
        };
    }
    static public function validateMaxLength($max, $msg) {
        return function($value, &$error) use ($max, $msg) {
            $error = $msg;
            return strlen($value) <= $max;
        };
    }
    static public function validateEmail($msg) {
        return self::validateRegExp('/^[\w!#$%&\'*+\/=?`{|}~^-]+(?:\.[\w!#$%&\'*+\/=?`{|}~^-]+)*@(?:[A-Z0-9-]+.)+[A-Z]{2,6}$/i' , $msg);
    }
    static public function validateRegExp($regExp, $msg) {
        return function($value, &$error) use ($regExp, $msg) {
            $error = $msg;
            return preg_match($regExp, $value) > 0;
        };
    }

}

// ---- the input field ----
class input {
    protected $_type;
    public $name;
    public $value = '';
    public $valid;
    public $errors = array();
    public $form;

    public function __construct($type, $name, array $cfg = array()) {
        $this->type = $type;
        $this->name = $name;
        foreach ($cfg as $key => $value) { $this->$key = $value; }
    }

    public function form(form $form = NULL) {
        if ($form) $this->form = $form;
        return $form;
    }

    protected $_validators = array();
    public function validate($validator) {
        $this->_validators[] = $validator;
        return $this;
    }

    public function validateNow() {
        $this->valid = true;
        $error = NULL;
        foreach ($this->_validators as $cb) {
            if (!$cb($this->value, $error)) {
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
        $method = 'render' . ucfirst($this->type);
        return $this->$method();
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

class inputPassword extends input {
    public function __toString() {
        return '<input autocomplete="disabled" class="text" id="bar" name="' . $this->name . '" type="password" />';
    }
}

class inputTextarea extends input {
    public function __toString() {
        return '<textarea class="text" id="bar" name="' . $this->name . '">' . $this->value . '</textarea>';
    }
}

class inputHidden extends input {
    public function __toString() {
        return '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
    }
}

class inputCaptcha extends input {
    protected $_imgUrl;
    protected $_captcha;

    public function validateNow() {
        $this->valid = $this->value == $this->_captcha->value;
        var_dump($this->value, $this->_captcha->value);
        if (!$this->valid) {
            $this->errors[] = 'Captcha invalid';
        }
        return $this->valid;
    }

    public function __invoke(&$req, &$res) {
        $a = '__captcha_' . $this->form->name . '_' . $this->name;
        $this->_imgUrl = $req->getRootUri() . $a;
        $this->_captcha = $_SESSION[$a] = $this->_getCaptcha(@$_SESSION[$a]);

;
        if (substr($req->getUri(), 1) === $a) {
            $res = new gaiaResponseImage($this->_createImage());
        }
        parent::__invoke($req, $res);
    }

    public function __toString() {
        return '<image src="' . $this->_imgUrl . '"><input type="text" name="' . $this->name . '" value="' . $this->value . '" />';
    }

    protected function _createImage() {
        $img = imagecreatetruecolor(60, 30);
        $text_color = imagecolorallocate($img, 233, 14, 91);
        imagestring($img, 5, 5, 5, $this->_captcha->value, $text_color);
        return $img;
    }

    protected function _getCaptcha($captcha = null) {
        if ($captcha && $captcha->expire > time()) return $captcha;
        return (object) array(
            'value' => rand(100, 999),
            'expire' => time() + 60 * 5
        );

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
        $this->add(new inputHidden('hidden', '__gaiaFormId', array('value' => $name)));
        foreach ($mw as $input) {
            if ($input instanceof input) $this->add($input);
        }
    }

    public function __invoke(&$req, &$res, &$data) {
        // register form
        if (!isset($req->forms)) $res->forms = new gaiaInvokable();
        $req->forms->{$this->name} = $this;

        $this->begin = '<form action="'.$req->getRootUri().'" method="post">';

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
        return '[a form "'.$this->name.'"]';
    }

    public function add(input $input) {
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