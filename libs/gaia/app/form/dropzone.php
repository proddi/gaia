<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joerg
 * Date: 4/4/12
 * Time: 10:22 PM
 */

class gaiaAppFormDropzone extends gaiaAppFormAbstract {
    public function markup() {
        $html = '<div id="dropbox">'.PHP_EOL;
        $html .= '<span class="message">file drop zone <br /><i>(not working yet)</i></span>'.PHP_EOL;
        $html .= '</div>'.PHP_EOL;

        return $html;
    }

    // event handler to catch form events, no rendering at this point
    public function proceed($app) {
        if ($this->form->isSubmit()) {
            $this->value = $app->session()->foofiles;
        }
    }

}