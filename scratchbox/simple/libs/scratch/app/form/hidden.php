<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:36 PM
 */

class scratchAppFormHidden extends scratchAppFormAbstract {
    public function markup() {
        return '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
    }

    public function __toString() {
        return $this->markup();
    }
}