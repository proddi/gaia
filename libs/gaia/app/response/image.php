<?php

class gaiaAppResponseImage {

    /** @ignore */
    protected $_image = '';

    public function __construct($image) {
        $this->_image = $image;
        $this->_isFinish = true;
    }

    /** @see gaia/libs/gaia/response/gaiaResponseAbstract#streamOut() */
    public function streamOut() {
        header('Content-Type: image/png');
        imagepng($this->_image);
        imagedestroy($this->_image);
    }

}
