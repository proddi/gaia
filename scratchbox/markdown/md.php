<?php

/**
 * Description of md
 */
class md {

    static public function transform($markdown) {
        $md = new static($markdown);
        return $md->_transform();
    }

    protected $_markdown;

    public function __construct($markdown) {
        $this->_markdown = $markdown;
    }

    protected function _transform() {

        $i = 10000;
        $bestMatches;
        $bestCallback;
        foreach (static::$_parsers as $pattern => $callback) {
            if (preg_match($pattern, $this->_markdown, $matches, PREG_OFFSET_CAPTURE)) {
                if ($matches[0][1] < $i) {
                    $bestMatches = $matches;
                    $bestCallback = $callback;
                    $i = $matches[0][1];
                }
//                var_dump($matches[0][1], $matches);
            }
        }

        if ($bestCallback) call_user_func($bestCallback, $bestMatches, $i);
        else {
            var_dump($bestCallback, $i);
        }
    }

    static protected $_parsers = array(
        '/(#+) (.*)\n/' => array(__CLASS__, 'headerParser'),
        '/    /' => array(__CLASS__, 'codeParser'),
    );

    static protected function headerParser(array $matches) {

    }

    static protected function codeParser(md $md) {

    }

}