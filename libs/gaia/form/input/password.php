<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:35 PM
 */

class gaiaFormInputPassword extends gaiaFormInputAbstract {
    public function markup() {
        return '<input type="password" class="text" name="' . $this->name . '" id="' . $this->id . '" autocomplete="disabled" />';
    }
}