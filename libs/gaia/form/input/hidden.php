<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:36 PM
 */

class gaiaFormInputHidden extends gaiaFormInput {
    public function markup() {
        return '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
    }

    public function __toString() {
        return $this->markup();
    }
}