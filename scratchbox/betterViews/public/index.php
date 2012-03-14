<?php

$docs = array(
    (object) array(
        'title'       => 'Variables',
        'description' => '...',
        'code'        => "{{ foo }}\n{{ foo->bar }}\n{{ foo['bar'] }}\n{{ 'foo' }}\n{{ 23 }}"
    ),
    (object) array(
        'title' =>       'Filters',
        'description' => '...',
        'code' =>        "{{ name | striptags | title }}\n{{ list|join(', ') }}"
    ),
    (object) array(
        'title' =>       'Control Structures',
        'description' => '...',
        'code' =>        '<h1>Members</h1>' . "\n" . '<ul>' . "\n" . '    {{ for user in users }}' . "\n" . '        <li>{{ user->username | escape }}</li>' . "\n" . '    {{ end }}' . "\n" . '</ul>'
    ),
    (object) array(
        'title' =>       'Conditions',
        'description' => '...',
        'code' =>        "{{ if foo->bar }}\n    foo->bar is true\n{{ else }}\n    foo->bar is false\n{{ end }}"
    ),
    (object) array(
        'title' =>       'Comments',
        'description' => '...',
        'code' =>        "{# todo: the developer have to implement this data field\n    {{ user->name }} \n #}"
    )
);

# Yet Another Template Engine - YATE
require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratchbox', 'scratchbox');

gaiaServer::run(
    function($req, $res) use($docs) {
//        return $res->send(highlight_string(scratchboxView::compile('overview', false), true));
        $res->send(scratchboxView::render('overview', array(
            'foo' => array('bar','foo'),
            'docs' => $docs
            )));
    },
    function($req, $res) {
        $res->resource('assets/style.css');
        $res->resource('assets/sh_main.js');
        $res->resource('assets/sh_yate.js');
        $res->resource('assets/sh_style.css');
        $res->resource('sh_highlightDocument();');
    }
);

?>