<?php

require_once('../../../libs/gaia.php');
require_once('../data/data.php');

gaiaServer::run(
    function($req, $res) use($docs) {
        $res->send(gaiaView::render('overview', array(
            'foo' => array('bar','foo'),
            'docs' => $docs
            )));
    },
    function($req, $res) {
        $res->resource('assets/style.css');
        $res->resource('assets/sh_style.css');
        $res->resource('http://code.jquery.com/jquery-1.7.1.js');
        $res->resource('assets/sh_main.js');
        $res->resource('assets/sh_yate.js');
        $res->resource('sh_highlightDocument();');
    }
);

?>