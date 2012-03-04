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
class gaiaView {

    static protected $_instance;

    /**
	 * adapter instance
	 *
	 * @return gaiaDbAbstract
	 */
	public static function getInstance() {
		if (!self::$_instance instanceof ownView) {
			self::$_instance = new self(self::$_staticConfig);
		}
		return self::$_instance;
	}

    protected $_config;

    public function __construct(array $config = NULL) {
        $this->_config = array_merge(self::$_staticConfig, $config);
    }

    static protected $_staticConfig = array();
    public static function config(array $config = NULL) {
        if (isset($config)) self::$_staticConfig = array_merge(self::$_staticConfig, $config);
        return self::$_staticConfig;
    }

    // support functions
    static public function ephpCompiler() {
        return function($template, $sourceFn) {
            return preg_replace_callback('/<%(=|-) (.*?) %>/', function($args) {
                $values = explode(' | ', $args[2]);
                $value = array_shift($values);
                $value = '$v->' . $value;
                foreach ($values as $filter) {
                    $fun = explode(',', $filter, 2);
                    $value = '$filters->' . $fun[0] . '(' . $value . (count($fun) > 1 ? ',' . $fun[1] : '') . ')';
                }
                if ('=' === $args[1]) $value = 'htmlspecialchars(' . $value . ')';
                return '<? echo ' . $value . ' ?>';
            }, $sourceFn($template));
        };
    }
    static public function fileSource($url = '', $ext = 'view.php') {
        $url .= '/';
        $ext = '.' . $ext;
        return function($template) use ($url, $ext) {
            $url = $url . $template . $ext;
            $source = file_get_contents($url);
            return (false !== $source) ? $source : ('[unable to load view from "' . $url . '"]');
        };
    }

    public static function parser() {
        return function($template, $args, $config) {
            $content = $config['compiler']($template, $config['source']);
            // this state is cachable
            $v = new gaiaInvokable($args);
            $v->partial = function($partialName, $partialArgs = NULL) { return gaiaView::render($partialName, $partialArgs); };
            $filters = $config['filters'];
            ob_start();
            eval('?>' . $content . '<?;');
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        };
    }

    protected static $_filters;
    public static function filters() {
        if (!isset(self::$_filters)) {
            self::$_filters = new gaiaInvokable();
            self::$_filters->capitalize = function($str) {
                return ucwords(strtolower($str));
            };
            self::$_filters->truncate = function($str, $limit) {
                if (strlen($str) <= $limit) return $str;
                return substr($str, 0, $limit - 3) . '...';
            };
            self::$_filters->join = function($arr, $glue = ', ') {
                return implode($glue, $arr);
            };
            self::$_filters->first = function($arr) {
                return $arr[0];
            };
            self::$_filters->dump = function($data) {
                return var_export($data, true);
            };
        }
        return self::$_filters;
    }

    public function render($template, array $args = NULL, array $options = NULL) {
        if (isset($this) && $this instanceof self) {
            $config = $this->_config;
            if ($options) $config = array_merge($config, $options);
            return $config['parser']($template, $args, $config);
        } else {
            return self::getInstance()->render($template, $args, $options);
        }
    }

}

// default values
gaiaView::config(array(
    'parser'   => gaiaView::parser(),
    'compiler' => gaiaView::ephpCompiler(),
    'source'   => gaiaView::fileSource('../views', 'ephp'),
    'filters'  => gaiaView::filters()
));

?>