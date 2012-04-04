<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:35 PM
 */

class gaiaFormInputPassword extends gaiaFormInput {
    public function markup() {
        return '<input autocomplete="disabled" class="text" name="' . $this->name . '" type="password" />';
    }
}