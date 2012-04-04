<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:38 PM
 */

class gaiaFormInputCaptcha extends gaiaFormInput {
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
//        $this->_captcha =
        if (substr($req->getUri(), 1) === $a) {
            $res = new gaiaResponseImage($this->_createImage());
        }
        parent::__invoke($req, $res);
    }

    public function markup() {
        return '<image src="' . $this->_imgUrl . '"><input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
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