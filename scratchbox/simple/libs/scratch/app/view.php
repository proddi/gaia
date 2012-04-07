<?php

class scratchAppView {

    protected $context = NULL;
    protected $content = array();

    public function to($context) {
        $this->content = $content;
        return $this;
    }
    public function render($template, array $data = array()) {
        $this->context = NULL;
        return $this;
    }

    public function content($context = NULL, $default = '') {
        if (array_key_exists($context, $this->content)) {
            $default = $this->content[$context];
            unset($this->content[$context]);
        }
        return $default;
    }

}

?>