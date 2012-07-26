<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/1/12
 * Time: 10:32 PM
 */

class gaiaAppFormTextarea extends gaiaAppFormAbstract {
    public function markup() {
        $placeholder = isset($this->placeholder) ? ' placeholder="' . htmlspecialchars($this->placeholder) . '"' : '';
        return '<textarea class="text" name="' . $this->name . '" id="' . $this->id . '"' . $placeholder . '>' . $this->value . '</textarea>';
    }
}