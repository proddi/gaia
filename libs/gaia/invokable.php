<?php

/**
 * Description of invokable
 *
 * @author proddi@splatterladder.com
 */
class gaiaInvokable {
    public function __construct(array $o = NULL) {
        if ($o) foreach ($o as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __call($method, $args = NULL) {
        if (is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        } else throw new gaiaException('Unable to invoke the function named "' . $method . '"');
    }
}

?>