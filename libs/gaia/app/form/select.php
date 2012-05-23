<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/4/12
 * Time: 10:22 PM
 */

class gaiaAppFormSelect extends gaiaAppFormAbstract {

    protected $_options = array();

    public function setOptions($options) {
        $this->_options = $options;
        return $this;
    }

    public function markup() {
        $html = '<select name="' . $this->name . '" id="' . $this->id . '">'.PHP_EOL;
        foreach($this->_options as $value => $opt) {
            $html .= '<option value="'. $value .'">'. $opt .'</option>'.PHP_EOL;
        }
        $html .= '</select>'.PHP_EOL;


        return $html;
    }
}