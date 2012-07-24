<?php
/**
 * Implement YATE - Yet Another Template Engine
 *
 * YATE is inspired by http://twig.sensiolabs.org but comes in less than 200 lines of code.
 *
 * For a detailed information have a look an the yate example
 *
 * @package gaia
 * @subpackage view
 * @author proddi@splatterladder.com
 */
class gaiaViewYate {

    protected $data = array();
//    protected $filters;

    protected $_rewriteFun;
    public function __construct(array $config = NULL) {
        $this->config($config);
//        $this->filters = gaiaView::filters();
    }

    protected $_config = array();
    public function config(array $config = NULL) {
        if (!isset($config)) {
            if (empty($this->_config)) $this->_config = self::defaultConfig();
        } else $this->_config = array_merge($this->_config, $config);
        return $this->_config;
    }

    public function filters() {
        $cfg = $this->config();
        return $cfg['filters'];
    }

    // returns the default config (also static)
    public function defaultConfig() {
        return array(
            'compiler' => self::compiler(),
            'parser'   => self::parser(),
            'source'   => gaiaView::fileSource('../views', 'yate'),
            'filters'  => gaiaView::filters($this),
            'tags'     => self::tags()
        );
    }

    // compiles the template
    public function compile($template) {
        return $this->_config['compiler']($this->_config, $template);
    }

    // compiles and renders the template
    public function render($template, array $values = array()) {
        $template = $this->_config['compiler']($this->_config, $template);

        extract(array_merge($values, $this->data));
        $__filters = $this->_config['filters'];

        $that = $this; // for operations on the view e.g. partials
        ob_start();
        $res = eval('?>' . $template . '<?;');
        $template = ob_get_contents();
        ob_end_clean();
        // check for error
//        if (!is_null($res)) throw new Exception('Unable to render view:' . $template, 10);
        return $template;
    }

    // returns a compile function
    public function compiler(/* cache function? */) {
        return function(array $config, $template) {
            $sourceFun = $config['source'];
            $rewriter = $config['parser'];
            $tags = $config['tags'];
            return preg_replace_callback('/{(?:(#)(!?) (.*?) #|({)(!?) (.*?) })}/s', function($args) use ($tags, $rewriter) {
                // comment ?
                if ('#' === $args[1]) {
                    if ('!' === $args[2]) return '{# ' . $args[3] . ' #}';
                    return '';
                }
                $expression = $args[6];
                if ('!' === $args[5]) return '{{ '. $expression . ' }}';
                // okay, we have to parse
                // check for tags
                foreach ($tags as $matcher => $tag) {
                    if (($matcher[0] === '/') && preg_match($matcher, $expression, $matches)) {
                        $matches[0] = $rewriter;
                        return '<? ' . call_user_func_array($tag, $matches) . ' ?>';
                    }
                }
                return '<? echo ' . $rewriter($expression) . ' ?>';
            }, $sourceFun ? $sourceFun($template) : $template);
        };
    }

    // expression rewriter;
    public function parser() {
        return function($syntax) {
            // filters
            $values = explode('|', $syntax);
            $syntax = array_shift($values);
            foreach ($values as $filter) {
                $filter = trim($filter);
                if (($i = strpos($filter, '(')) !== false)
                    $syntax = '$__filters->' . substr($filter, 0, $i + 1) . $syntax .', '. substr($filter, $i + 1);
                else
                    $syntax = '$__filters->'.$filter.'('.$syntax.')';
            }
            // object rewriting
            return preg_replace_callback('/(?:("|\')(.*?)\1|(?<![\$|>|\w])(([a-zA-Z][\w->]*)(?![\(|\w])))/', function($args) {
                if (isset($args[3])) {
                    if (in_array($args[3], array('NULL', 'object'))) return $args[3];
                    return '$' . $args[3];
                }
                return $args[1].$args[2].$args[1];
            }, $syntax);
        };
    }

    // returns the default config (also static)
    public function tags() {
        return array(
            '/^\s*for (.*) in (.*)\s*$/' => function($rewriter, $value, $values) {
                return 'foreach (' .$rewriter($values). ' as ' .$rewriter($value). ') {';
            },
            '/^\s*if (.*)$/' => function($rewriter, $expression) {
                    return 'if (' . $rewriter($expression) . ') {';
                },
            '/^\s*ifn (.*)$/' => function($rewriter, $expression) {
                    return 'if (!' . $rewriter($expression) . ') {';
                },
            '/^\s*else\s*/' => function() { return '} else {'; },
            '/^\s*end\s*$/' => function() { return '}'; },
            '/^\s*do (.*)/' => function($rewriter, $expression) {
                    return $rewriter($expression);
                },
            '/^partial (.*)\s*$/' => function($rewriter, $template) {
                    return 'echo "[[partials currently not supported]]"';
                },
        );
    }
}

?>