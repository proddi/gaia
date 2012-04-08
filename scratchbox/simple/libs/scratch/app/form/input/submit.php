<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/4/12
 * Time: 8:32 PM
 */

class scratchAppFormInputSubmit extends scratchAppFormInputAbstract {
    public function markup() {
        return '<input type="submit" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
    }
}