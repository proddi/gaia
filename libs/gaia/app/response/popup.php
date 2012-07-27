<?php

class gaiaAppResponsePopup extends gaiaAppResponseAjax {

    public function close() {
        $this->resource('popup.close(2)', gaiaAppResponse::jsInline);
    }

}