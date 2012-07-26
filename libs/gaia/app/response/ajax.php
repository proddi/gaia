<?php

class gaiaAppResponseAjax extends gaiaAppResponse {

    public function streamOut() {
        echo json_encode((object)array(
            'title' => $this->title,
            'content' => '' . $this->content(),
            'resources' => (object)array(
//                'js.header' => $this->_resources[$this::jsHeader],
//                'css.header' => $this->_resources[$this::cssHeader]
            )
        ));
        return;
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

}