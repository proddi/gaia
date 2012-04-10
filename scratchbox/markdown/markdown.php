<?php

/**
 * Description of markdown
 *
 * @author proddi@splatterladder.com
 */
class Markdown {

    static public function transform($markdown) {
        $parser = new static();
        return $parser->_transform($markdown);
    }

    static protected $_pattern = array(
        'header' => array(
            array(__CLASS__, 'headerParser'),
            array(__CLASS__, 'headerMarkup')
        ),
        'code' => array(
            array(__CLASS__, 'codeParser'),
            array(__CLASS__, 'codeMarkup')
        ),
        'ul' => array(
            array(__CLASS__, 'ulParser'),
            array(__CLASS__, 'ulMarkup')
        ),
        'ol' => array(
            array(__CLASS__, 'olParser'),
            array(__CLASS__, 'olMarkup')
        ),
        'default' => array(
            NULL,
            array(__CLASS__, 'defaultMarkup')
        )
    );

    protected function _transform($markdown) {
        $lines = preg_split("/\r?\n/", $markdown);
        $result = array();
        $currentBlock = '';
        $currentResult = array();
        $parsers = static::$_pattern;
        $data = (object) array(
            'block' => ''
        );
        while (count($lines)) {
            $line = array_shift($lines);
            $data->line = $line; // new obj model
            $parsed = NULL;
            $block = NULL;

            // run current parser
            if ($currentBlock && $parsers[$currentBlock][0]) {
                if (!is_null($parsed = call_user_func($parsers[$currentBlock][0], $data, $line, $lines, $currentBlock))) {
                    $block = $currentBlock;
                };
            }

            if (!$block) {
                foreach ($parsers as $code => $parser) {
                    if (($code === $currentBlock) || !$parser[0]) continue;
                    if (!is_null($parsed = call_user_func($parser[0], $data, $line, $lines, $currentBlock))) {
                        $block = $code;
                        break;
                    }
                }
            }

            if (!$block) {
                $block = 'default';
                $parsed = $line;
            }

            if ($block !== $currentBlock) {
                if ($currentBlock) {
                    $result[] = call_user_func($parsers[$currentBlock][1], $data, $currentResult);
                }
                $currentResult = array();
                $currentBlock = $block;
            }
            $currentResult[] = $parsed;
        }

        return join("\n", $result);
    }

    static protected function headerParser($data, $line) {
        if (preg_match('/(#+) (.*)/', $line, $matches)) {
            $lvl = strlen($matches[1]);
            return "<h$lvl>" . $matches[2] . "</h$lvl>";
        }
    }

    static protected function headerMarkup($data, $lines) {
        return join("\n", $lines);
    }

    static protected function codeParser($data, $line, $lines, $block) {
        if ('    ' === substr($data->line, 0, 4)) {
            return substr($data->line, 4);
        }
        if ('' === $data->line
                && 'code' === $block
                && count($lines) > 0
                && '    ' === substr($lines[0], 0, 4)) {
            return $data->line;
        }
    }

    static protected function codeMarkup($data, $lines) {
        return '<pre><code>' . join("\n", $lines) . '</code></pre>';
    }

    static protected function ulParser($data, $line, $lines, $block) {
        if (preg_match('/^\*\W+(.*)$/', $line, $matches)) {
            return $matches[1];
        }
        // continues?
        if ('ul' === $block
                && preg_match('/^\w/', $line)) {
            return '` ' . $line;
        }
    }

    static protected function ulMarkup($data, $lines) {
        return "<ul>\n<li>" . join("</li>\n<li>", $lines) . "</li>\n</ul>";
    }

    static protected function olParser($data, $line, $lines, $block) {
        if (preg_match('/^[0-9]\.\W+(.*)$/', $line, $matches)) {
            return $matches[1];
        }
    }

    static protected function olMarkup($data, $lines) {
        return "<ol>\n<li>" . join("</li>\n<li>", $lines) . "</li>\n</ol>";
    }

    static protected function defaultMarkup($data, $lines) {
        return '<p>' . join("\n", $lines) . '</p>';
    }

}

class __code__ {

    public function test($line, &$lines) {
        echo "code.test: " . $line . "<br>\n";
    }

}