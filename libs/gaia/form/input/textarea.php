<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:32 PM
 */

class gaiaFormInputTextarea extends gaiaFormInput {
    public function markup() {
        return '<textarea class="text" name="' . $this->name . '">' . $this->value . '</textarea>';
    }
}