<?php

/**
 * A markdown parser
 *
 * Syntax: http://daringfireball.net/projects/markdown/syntax
 *
 * @author proddi@splatterladder.com
 */
class Markdown {

    static public function transform($markdown) {
        $parser = new static($markdown);
        return $parser->_transform2();
    }

    static protected $_blockParsers = array(
//        ''       => array(__CLASS__, 'defaultParser2'),
        'header' => array(__CLASS__, 'headerParser'),
        'code'   => array(__CLASS__, 'codeParser'),
        'code2'   => array(__CLASS__, 'code2Parser'), // ``` ... ```
    );

    protected $_line;
    protected $_lines = array();

    protected $_block = '';

    protected $_data = array();

    public function __construct($markdown) {
        $this->_lines = preg_split("/\r?\n/", $markdown);
    }

    protected function line() { return $this->_line; }

    protected function lineAt($index) {
        return count($this->_lines) <= $index ? '' : $this->_lines[$index];
    }

    public function block($block = '') {
        if (func_num_args()) $this->_block = $block;
        return $this->_block;
    }

    public function isBlock($block) {
        return $block === $this->_block;
    }

    protected function data($item = NULL) {
        if (func_num_args()) {
            $this->_data[] = $item;
        } else {
            $data = $this->_data;
            $this->_data = array();
            return $data;
        }
    }

    protected function isData() {
        return count($this->_data) > 0;
    }

    protected function next() {
        return NULL !== ($this->_line = array_shift($this->_lines));
    }

    protected $_content = '';
    protected function write($data) { $this->_content .= $data . PHP_EOL; }
    protected function _transform2() {
        while ($this->next()) {
            $matchedBlock = NULL;

            // continue current block?
            if ($this->block()) {
                if (call_user_func(static::$_blockParsers[$this->block()], $this)) {
                    $matchedBlock = $this->block();
                }
            }

            // proceed other block parsers
            if (!$matchedBlock) {
                foreach (static::$_blockParsers as $block => $parser) {
                    if ($this->isBlock($block)) continue; // already executed by continuing
                    if (call_user_func($parser, $this)) {
                        $matchedBlock = $this->block($block);
                        break;
                    }
                }
            }

            // no parsers matched
            if (!$matchedBlock) {
//                static::$defaultParser($this);
                $this->block('');
                $this->write($this->line() . "<br>\n");
            }
        }
        echo $this->_content;
    }

//    static protected function defaultParser(Markdown $md) {

//    }
    static protected function headerParser(Markdown $md) {
        // # This is an H1 / ## This is an H2
        if (preg_match('/(#+) (.*)/', $md->line(), $matches)) {
            $lvl = strlen($matches[1]);
            $md->write("<h$lvl>" . $matches[2] . "</h$lvl>");
            return true;
        }
    }

    static protected function codeParser(Markdown $md) {
        $code = NULL;
        //     This is a code block.
        if ('    ' === substr($md->line(), 0, 4)) {
            $code = substr($md->line(), 4);
        } else if ('' === $md->line()
                && $md->isBlock('code')
                && '    ' === substr($md->lineAt(0), 0, 4)) {
            $code = '';
        }

        if (NULL !== $code) {
            $md->data($code);
            return true;
        } else if ($md->isData()) {
            $md->write('<pre><code>' . join(PHP_EOL, $md->data()) . '</code></pre>');
        }
    }

    static protected function code2Parser(Markdown $md) {
        $code = NULL;
        if (!$md->isBlock('code2')) {
            if (preg_match('/^```(\w*)$/', $md->line(), $matches)) {
                $code = $matches[1];
            }
        } else {
            if ('```' !== $md->line()) {
                $code = $md->line();
            } else {
            }
        }

        if (NULL !== $code) {
            $md->data($code);
            return true;
        } else if ($md->isData()) {
            $data = $md->data();
            $lang = array_shift($data);
            $md->write('<pre' . ($lang ? ' class="sh_' . $lang . '"' : '') . '><code>' . join(PHP_EOL, $data) . '</code></pre>');
        }
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

}