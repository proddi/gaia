<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/4/12
 * Time: 10:22 PM
 */

class gaiaFormInputText extends gaiaFormInputAbstract {
    public function markup() {
        return '<input type="text" class="text" name="' . $this->name . '" id="' . $this->id . '">' . $this->value . '</input>';
    }
}