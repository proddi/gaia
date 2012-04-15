<?php

class gaiaAppResponse {

    protected $title = 'A gaia site';

    protected $content = array();

    const jsHeader  = 1;
    const jsInline  = 2;
    const cssHeader = 3;
    const cssInline = 4;
    protected $_resources = array();

    protected $_status;

    public function send($ctx, $data = NULL) {
        if (func_num_args() === 1) {
            $data = $ctx;
            $ctx = NULL;
        }
        if (array_key_exists($ctx, $this->content)) $this->content[$ctx] .= $data;
        else $this->content[$ctx] = $data;
    }

    public function content($ctx = NULL) {
        $data = '';
        if (array_key_exists($ctx, $this->content)) {
            $data = $this->content[$ctx];
            unset($this->content[$ctx]);
        }
        return $data;
    }

    public function clear() {
        $this->content = array();
    }

    public function status($status = NULL) {
        if ($status) $this->_status = $status;
        return $this->_status;
    }

    public function resource($resource, $type = NULL) {
        if (!$type) {
            if ('.js' === substr($resource, -3)) $type = self::jsHeader;
            else if ('.css' === substr($resource, -4)) $type = self::cssHeader;
            else $type = self::jsInline;
        }
        if (!array_key_exists($type, $this->_resources)) {
            $this->_resources[$type] = array();
        }
        $this->_resources[$type][] = $resource;
    }

    public function streamOut() {
    	$lf = "\n";
        echo '<!DOCTYPE html>' . $lf; // just html5
        echo '<head>' . $lf;
        echo '  <title>' . $this->title . '</title>' . $lf;
        echo '  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />' . $lf;
        if (array_key_exists($this::jsHeader, $this->_resources))
            foreach ($this->_resources[$this::jsHeader] as $js) echo '  <script type="text/javascript" src="' . $js . '"></script>' . $lf;
        if (array_key_exists($this::cssHeader, $this->_resources))
            foreach ($this->_resources[$this::cssHeader] as $css) echo '  <link rel="stylesheet" href="' . $css . '" media="Screen,Projection,TV" />' . $lf;
        echo '</head>' . $lf;
        echo '<body>' . $lf;
        echo $this->content() . $lf;

        if (array_key_exists($this::jsInline, $this->_resources))
            echo '<script type="text/javascript">' . $lf . implode($this->_resources[$this::jsInline], ';'.$lf) . $lf . '</script>' . $lf;

        $footer = array();
        $footer[] = 'Instances created: '.(GAIA::$countFactory);
        $footer[] = 'Classes loaded: '.(GAIA::$countInclude);
//      $footer[] = 'Sql queries: '.(gaiaDb::$countQueries);
//      $footer[] = 'Sql time: '.round(gaiaDb::$timeQueries,3).'ms';
        $footer[] = 'GAIA time: ' . round(GAIA::getRunTime(), 3) . 'ms';
//        $footer[] = '<br>Classes loaded: ' . join(',<br>', get_declared_classes());
        echo '<pre class="gaia_summary" style="border-top: 1px dotted #333; padding-top: 4px; opacity: .5; text-align: right; font-size: .9em; text-shadow: 1px 1px 3px #000;">' . implode(' &nbsp; ~ &nbsp; ', $footer) . '</pre>' . $lf;
        echo '</body>' . $lf;
        echo '</html>';
    }

    public function redirect($target) {
        header('Location: '.$target);
    }

}