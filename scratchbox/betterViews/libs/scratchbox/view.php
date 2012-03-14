<?php

/**
 * Static View interface to support view functionality
 *
 * <code>
 * <?php
 * $config->viewType = 'file';
 * $config->url = 'views/{id}.view';
 * gaiaView::setConfig((object)array(
 *     'compiler' => ownView::ephpCompiler(),
 *     'source' => ownView::fileSource('../views')
 * ));
 * echo gaiaView::render('user.index', array(...));
 * ?>
 * </code>
 *
 * @package gaia
 * @subpackage view
 * @author proddi@splatterladder.com
 */
class scratchboxView {

    static protected $_view;

    static public function view($view = NULL) {
        if (is_object($view)) self::$_view = $view;
        else if (!isset(self::$_view)) self::$_view = new scratchboxViewYate();
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
            $source = file_get_contents($url);
            return (false !== $source) ? $source : ('[unable to load view from "' . $url . '"]');
        };
    }

    public static function filters() {
        return new gaiaInvokable(array(
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

}

?>
