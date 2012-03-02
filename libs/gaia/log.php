<?php

class gaiaLog {

    static public function consoleSupport() {
        return function($req, $res) {
                $res->log = function($message) use($res) {
                        $res->resource('console.log("' . join(array_map(function($item){ return addslashes($item); }, func_get_args()), '", "') . '")', $res::jsInline);
                    };
                $res->warn = function($message) use($res) {
                        $res->resource('console.warn("' . join(array_map(function($item){ return addslashes($item); }, func_get_args()), '", "') . '")', $res::jsInline);
                    };
                $res->error = function($message) use($res) {
                        $res->resource('console.error("' . join(array_map(function($item){ return addslashes($item); }, func_get_args()), '", "') . '")', $res::jsInline);
                    };
            };
    }

}

?>
