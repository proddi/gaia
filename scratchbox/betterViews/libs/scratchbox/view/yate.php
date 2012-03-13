<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * variables: {{ foo }}
 *
 * filters: {{ foo | join(', ') | capitalize }}
 *
 * structures: {{ if foo }} ... {{ end }}
 *
 * structures {{ for user in users }} ...{{ user->username | escape }}... {{ end }}
 */

/**
 * Description of yate
 *
 * @author proddi@splatterladder.com
 */
class scratchboxViewYate {

    protected $_rewriteFun;
    public function __construct(array $config = NULL) {
        $this->config($config);
        $this->_rewriteFun = function($syntax) {
            // filters
            $values = explode('|', $syntax);
            $syntax = array_shift($values);
            foreach ($values as $filter) {
                $filter = trim($filter);
                if (($i = strpos($filter, '(')) !== false)
                    $syntax = '$filters->' . substr($filter, 0, $i + 1) . $syntax .', '. substr($filter, $i + 1);
                else
                    $syntax = '$filters->'.$filter.'('.$syntax.')';
            }
            // object rewriting
            return preg_replace_callback('/([^\w\"\\\'>\$])([a-zA-Z][\w]+)([^\(\w\"\\\'])/', function($args) {
                if (in_array($args[2], array('NULL', 'object'))) return $args[1].$args[2].$args[3];
                return $args[1].'$v->'.$args[2].$args[3];
            }, ' ' . $syntax . ' ');
        };
    }

    protected $_config = array();
    public function config(array $config = NULL) {
        $this->_config = array_merge($this->_config, $config);
        if (!isset($this->_config['tags'])) $this->_config['tags'] = $this->_buildDefaultTags();
    }

    //put your code here
    public function compile($template) {
        return $this->_compile($template, $this->_config['source']);
    }
    //put your code here
    public function render($template, array $values = array()) {
        $template = $this->_compile($template, $this->_config['source']);

        // execute
        if (isset($this->_config['values'])) $v = new gaiaInvokable(array_merge($this->_config['values'], $values));
        else $v = new gaiaInvokable($values);
//        $v->partial = function($partialName, $partialArgs = NULL) { return gaiaView::render($partialName, $partialArgs); };
        $filters = $this->_config['filters'];
        $view = $this; // for partials
        ob_start();
        eval('?>' . $template . '<?;');
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    protected function _compile($template, $sourceFun) {
        $tags = $this->_config['tags'];
        $rewriter = $this->_rewriteFun;
        return preg_replace_callback('/{(?:(#)(!?) (.*?) #|({)(!?) (.*?) })}/s', function($args) use ($tags, $rewriter) {
            // comment ?
            if ('#' === $args[1]) {
                if ('!' === $args[2]) return '{# ' . $args[3] . ' #}';
                return '';
            }
            $modifier = $args[4];
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
            return '<? echo' . $rewriter($expression) . '?>';
        }, $sourceFun ? $sourceFun($template) : $template);
    }

    protected function _buildDefaultTags() {
        return array(
            '/^\s*for (.*) in (.*)\s*$/' => function($rewriter, $value, $values) {
                return 'foreach (' .$rewriter($values). ' as ' .$rewriter($value). ') {';
            },
            'if' => function($cond, $rewriter) {
                    return 'if (' . $rewriter($cond) . ') {';
                },
            'ifn' => function($cond, $rewriter) {
                    return 'if (!' . $rewriter($cond) . ') {';
                },
            'else' => function() { return '} else {'; },
            '/^\s*end\s*$/' => function() { return '}'; },
            '/^\s*do (.*)/' => function($rewriter, $expression) {
                    return $rewriter($expression);
                },
            'partial' => function() { return 'echo "[[partials currently not supported]]"'; },
        );
    }
}

?>
