<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/4/12
 * Time: 10:22 PM
 */

class gaiaAppFormText extends gaiaAppFormAbstract {
    public function markup() {
        $placeholder = isset($this->placeholder) ? ' placeholder="' . htmlspecialchars($this->placeholder) . '"' : '';
        return '<input type="text" class="text" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value) . '"' . $placeholder . ' />';
    }
}