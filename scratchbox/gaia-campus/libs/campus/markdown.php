<?php

/**
 * A (incomplete) markdown parser
 *
 * Syntax: http://daringfireball.net/projects/markdown/syntax
 *
 * @author proddi@splatterladder.com
 */
class campusMarkdown {

    static public function transform($markdown) {
        $parser = new static($markdown);
        return $parser->_transform2();
    }

    static protected $_blockParsers = array(
//        ''       => array(__CLASS__, 'defaultParser2'),
        'header' => array(__CLASS__, 'headerParser'),
        'code'   => array(__CLASS__, 'codeParser'),
        'quote'  => array(__CLASS__, 'quoteParser'),
    );

    protected $_line;
    protected $_prevLine;
    protected $_lines = array();

    protected $_block = '';

    protected $_data = array();

    public function __construct($markdown) {
        $this->_lines = preg_split("/\r?\n/", $markdown);
    }

    protected function preprocess($line, $default = NULL) {
        if (isset($line)) {
            foreach ($this->_preprocessors as $preprocessor) {
                $line = $preprocessor($line);
                if (!isset($line)) {
                    break;
                }
            }
        }
        return isset($line) ? $line : $default;
    }
    protected function next() {
        $this->_line = $this->preprocess($this->_prevLine = array_shift($this->_lines));
        return isset($this->_line);
    }

    protected function prev() {
        array_unshift($this->_lines, $this->_prevLine);

    }

    protected function line($line = NULL) {
        if (func_num_args()) $this->_line = $line;
        return $this->_line;
    }

    protected function lineAt($index) {
        if (count($this->_lines) > $index) {
            return $this->preprocess($this->_lines[$index], '');
        }
        return '';
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

    protected $_content = '';
    protected function write($data) { $this->_content .= $data . PHP_EOL; }
    protected function _transform2() {
        $oldContent = $this->_content;
        $this->_content = '';

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
                $this->write('<p>' . static::lineParser($this->line()) . '</p>'. "\n");
            }
        }

        // finalize request
        if ($this->block()) {
            call_user_func(static::$_blockParsers[$this->block()], $this);
        }

        $html = $this->_content;
        $this->_content = $oldContent;
        return $html;
    }

    protected $_preprocessors = array();
    protected function transformEmbedded($preprocessor) {
        $currentBlock = $this->block();
        $this->block('');
//        $content = $this->_content;
//        $this->_content = '';
        array_push($this->_preprocessors, $preprocessor);

        $html = $this->_transform2();

        array_pop($this->_preprocessors);
//        $this->_content = $content;
        $this->block($currentBlock);
//        var_dump($html);
        return $html;
    }
//    static protected function defaultParser(Markdown $md) {

//    }

    static protected function lineParser($line) {
        $line = preg_replace_callback('/(`|``|\*\*|__|\*|_)(.*?)\1/', function($matches) {
            $text = htmlspecialchars($matches[2]);
            switch ($matches[1]) {
                case '_':
                case '*': return '<em>' . $text . '</em>';
                case '__':
                case '**': return '<strong>' . $text . '</strong>';
                case '`': return '<code>' . $text . '</code>';
                case '`': return '<p><code>' . $text . '</code></p>';
            }
        }, $line);
        // link TODO: neews url scheme detection
        $line = preg_replace_callback('/<(http|https|ftp|mailto):\/\/(.*?)>/', function($matches) {
            $url = $matches[1] . '://' . $matches[2];
            return '<a href="' . $url . '">' . $url . '</a>';
        }, $line);
        return $line;
    }

    static protected function headerParser(self $md) {
        // # This is an H1 / ## This is an H2
        if (preg_match('/(#+) (.*)/', $md->line(), $matches)) {
            $lvl = strlen($matches[1]);
            $md->write("<h$lvl>" . static::lineParser($matches[2]) . "</h$lvl>");
            if (!$md->lineAt(0)) $md->next();
            return true;
        }
    }

    static protected function codeParser(self $md) {
//        if (count($md->_preprocessors))
//            echo '<pre>:'.  htmlspecialchars($md->line()).'</pre>' . PHP_EOL;

        // matches 4 spaces intend code blocks
        if ('    ' === substr($md->line(), 0, 4)) {
            $code = substr($md->line(), 4);
            while ($md->next()) {
                if ('    ' === substr($md->line(), 0, 4)
                        || ('' === $md->line()
                            && '    ' === substr($md->lineAt(0), 0, 4))) {
                    $code .= PHP_EOL . substr($md->line(), 4);
                } else {
                    break;
                }
            }
            $md->write('<pre><code>' . htmlspecialchars($code) . '</code></pre>');
            return true;
        }

        // matches code blocks in ```lang ... ```
        if (preg_match('/^```(\w*)$/', $md->line(), $matches)) {
            $lang = $matches[1];
            $code = '';
            while ($md->next()) {
                if ('```' === $md->line()) {
                    break;
                } else $code .= $md->line() . PHP_EOL;
            }
            $md->write('<pre' . ($lang ? ' class="sh_' . $lang . '"' : '') . '><code>' . htmlspecialchars($code) . '</code></pre>');
            return true;
        }
    }

    static protected function quoteParser(self $md) {
       if (preg_match('/^> ?(.*)$/', $md->line(), $matches)) {
           $md->prev();
           $html = $md->transformEmbedded(function($line) {
               if (preg_match('/^> ?(.*)$/', $line, $matches)) {
                   return $matches[1];
               }
           });

           $md->write('<blockquote>' . $html . '</blockquote>' . PHP_EOL);
           return true;
       }
    }
/*
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
*/
}