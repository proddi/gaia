<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:32 PM
 */

class gaiaAppFormTextarea extends gaiaAppFormAbstract {
    public function markup() {
        return '<textarea class="text" name="' . $this->name . '" id="' . $this->id . '">' . $this->value . '</textarea>';
    }
}