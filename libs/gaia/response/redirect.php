<?php

/**
 * Redirect response
 * @package gaia
 * @subpackage response
 * @author proddi@splatterladder.com
 */

class gaiaResponseRedirect extends gaiaResponseAbstract {

    /** @ignore */
    protected $_target = '';

    public function __construct($target) {
        $this->_target = $target;
        $this->_isFinish = true;
    }

    /** @see gaia/libs/gaia/response/gaiaResponseAbstract#streamOut() */
    public function streamOut() {
        header('Location: '.$this->_target);
    }

}

?>