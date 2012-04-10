<?php
/**
 * Static View interface to support view functionality
 *
 * <code>
 * gaiaView::render('layout', array('user' => new User()));setConfig(array(
 * </code>
 *
 * @package gaia
 * @subpackage view
 * @author proddi@splatterladder.com
 */
class gaiaView {

    static protected $_view;

    static public function view($view = NULL) {
        if (is_object($view)) self::$_view = $view;
        else if (!isset(self::$_view)) self::$_view = new gaiaViewYate();
        return self::$_view;
    }

    static public function compile($template) {
        $view = self::view();
        return $view->compile($template);
    }

    static public function render($template, array $params = array()) {
        $view = self::view();
        return $view->render($template, $params);
    }

    // helper functions
    static public function fileSource($url = '', $ext = 'v.php') {
        $url .= '/';
        $ext = '.' . $ext;
        return function($template) use ($url, $ext) {
            $url = $url . $template . $ext;
            $source = @file_get_contents($url);
            if (false === $source) throw new Exception('Unable to load view from file "' . $url . '"');
            return (false !== $source) ? $source : ('[unable to load view from "' . $url . '"]');
        };
    }

    public static function filters() {
        $dump;
        return new gaiaInvokable(array(
            'capitalize' => function($str) { return ucwords(strtolower($str)); },
            'truncate' => function($limit, $str) { if (strlen($str) <= $limit) return $str; return substr($str, 0, $limit - 3) . '...'; },
            'join' => function($arr, $glue = ', ') { return implode($glue, $arr); },
            'explode' => function($arr, $glue = ', ') { return explode($glue, $arr); },
            'first' => function($arr) { return $arr[0]; },
            'inspect' => function($data) { return highlight_string('<? '. var_export($data, true) . ' ?>', true); },
            'dump' => $dump = function($var, $root = true, $intend = 0) use (&$dump) {
                $c = function ($text, $color, $root = false) { return '<span style="color: ' . $color . '">' . $text . '</span>'; };
                $pre = $root ? '<code>' : '';
                $post = $root ? '</code>' : '';
                $preBlock = $root ? '<pre><code>' : '';
                $postBlock = $root ? '</code></pre>' : '';
                $id = '  ';
                $is = str_repeat($id, $intend);
                if (is_null($var)) return $pre . $c('NULL', 'blue') . $post;
                if (is_numeric($var)) return $pre . $c($var, 'blue') . $post;
                if (is_string($var)) return $pre . $c("'" . addslashes($var) . "'", 'red') . $post;
                if (is_bool($var)) return $pre . $c($var ? 'true' : 'false' , 'blue') .$post;
                if (is_resource($var)) return $pre . $c('resource of type (' . get_resource_type($var). ')', 'blue') . $post;
                if (is_array($var)) {
                    $markup = $c('array (', 'green') . "\n";
                    foreach (array_keys($var) as $key) {
                        $markup .= $is . $id . $dump($key, false) . ' => ' . $dump($var[$key], false, $intend+1) . ",\n";
                    }
                    $markup .= $is . $c(')', 'green');
                    return $preBlock . $markup . $postBlock;
                }
                if (is_object($var)) {
                    $markup = $c('object('. get_class($var). ')#' . substr(spl_object_hash($var), 15, 8) .' {', 'blue') . "\n";
                    foreach (get_object_vars($var) as $key => $val) {
                        $markup .= $is . $id . $c($key, 'green') . ': ' . $dump($var->$key, false, $intend+1) . "\n";
                    }
                    $markup .= $is . $c('}', 'blue');
                    return $preBlock . $markup . $postBlock;
                }
                return $pre . $var . $post;
             },
            'upper' => function($str) { return strtoupper($str); },
            'lower' => function($str) { return strtolower($str); },
            'escape' => function($str) { return htmlentities($str); },
            'urlencode' => function($str) { return urlencode($str); },
            'md5' => function($str) { return md5($str); },
            'asset' => function($str) { return 'http://some.host/' . $str; },
            'link' => function($str, $title) { return '<a href="' . $str . '">' . $title . '</a>'; },
            'default' => function($str, $default) { return empty($str) ? $default : $str; },
            'slice' => function($arr, $offset = NULL, $length = NULL) { return array_slice($arr, $offset, $length); },
            'date' => function($time, $format) { return date($format, $time); },
            'partial' => function($input, $template, array $values = array()) {
                return gaiaView::render($template, array_merge($values, array('value' => $input)));
             }
        ));
    }
}

?>