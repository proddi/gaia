<?php

class gaiaResponseHtml extends gaiaResponseAbstract {

    protected $_cssHeader = array();
/*
    protected $_responseCodes = array(
        400 => 'HTTP/1.0 400 Bad Request',
        401 => 'HTTP/1.0 401 Unauthorized',
        403 => 'HTTP/1.0 403 Forbidden',
        404 => 'HTTP/1.0 404 Not Found',
        405 => 'HTTP/1.0 405 Method Not Allowed',
        500 => 'HTTP/1.0 500 Internal Server Error',
        501 => 'HTTP/1.0 501 Not Implemented'
    );
*/
    public function __construct(gaiaResponseAbstract $response = NULL) {
    }
/*
    public function write($code, $ctx = NULL, $data = NULL) {
        if (is_int($code)) {
            if (array_key_exists($code, $this->_responseCodes)) {
                header($this->_responseCodes[$code]);
            }
            return parent::write($ctx, $data);
        }
        return parent::write($code, $ctx);
    }


    public function send($data, $code = 200) {
        if (array_key_exists($code, $this->_responseCodes)) {
            header($this->_responseCodes[$code]);
        }
        return parent::send($data);
    }
*/
    public function redirect($to) {}

    /*
     * output method
     */
    public function streamOut() {
    	$lf = "\n";
        echo '<!DOCTYPE html>' . $lf; // just html5
        echo '<head>' . $lf;
        echo '  <title>' . $this->_title . '</title>' . $lf;
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
        $footer[] = 'Resources included: '.(GAIA::$countInclude);
//      $footer[] = 'Sql queries: '.(gaiaDb::$countQueries);
//      $footer[] = 'Sql time: '.round(gaiaDb::$timeQueries,3).'ms';
        $footer[] = 'GAIA time: ' . round(GAIA::getRunTime(), 3) . 'ms';
        echo '<pre class="gaia_summary" style="border-top: 1px solid black; padding-top: 4px; opacity: .5">' . implode(' &nbsp; ~ &nbsp; ', $footer) . '</pre>' . $lf;
        echo '</body>' . $lf;
        echo '</html>';
    }

}

?>