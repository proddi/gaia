<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view
 *
 * @author tosa
 */
class scratchboxView {

    static protected $_config;

    static public function config($key, $value = NULL) {
    }

    static public function render($template, array $params = array()) {
        return 'scratchboxView::render(' . $template . ') not implemented<br>';
    }

    // helper functions
    static public function fileSource($url = '', $ext = 'v.php') {
        $url .= '/';
        $ext = '.' . $ext;
        return function($template) use ($url, $ext) {
            $url = $url . $template . $ext;
            $source = file_get_contents($url);
            return (false !== $source) ? $source : ('[unable to load view from "' . $url . '"]');
        };
    }

    protected static $_filters;
    public static function filters() {
        if (!isset(self::$_filters)) {
            self::$_filters = new gaiaInvokable(array(
                'capitalize' => function($str) { return ucwords(strtolower($str)); },
                'truncate' => function($limit, $str) { if (strlen($str) <= $limit) return $str; return substr($str, 0, $limit - 3) . '...'; },
                'join' => function($arr, $glue = ', ') { return implode($glue, $arr); },
                'explode' => function($arr, $glue = ', ') { return explode($glue, $arr); },
                'first' => function($arr) { return $arr[0]; },
                'dump' => function($data) { return var_export($data, true); },
                'upper' => function($str) { return strtoupper($str); },
                'lower' => function($str) { return strtolower($str); },
                'escape' => function($str) { return htmlentities($str); },
                'asset' => function($str) { return 'http://some.host/' . $str; },
                'link' => function($str, $title) { return '<a href="' . $str . '">' . $title . '</a>'; },
                'default' => function($str, $default) { return empty($str) ? $default : $str; }
            ));
        }
        return self::$_filters;
    }

}

?>
