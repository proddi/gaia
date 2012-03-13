<?php
/*
echo '<pre>';
$s = ' foo 500 "500" foo foo["foo"] foo->bar|foo(foo) '."\n";
echo $s;
echo preg_replace_callback('/([^\w\"\\\'>])([a-zA-Z][\w]+)([^\(\w\"\\\'])/', function($args) {
    return $args[1].'$v->'.$args[2].$args[3];
}, $s);

echo '</pre>';
exit;
*/

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
        'code' =>        '<h1>Members</h1>' . "\n" . '<ul>' . "\n" . '    {{! for user in users }}' . "\n" . '        <li>{{ user->username | escape }}</li>' . "\n" . '    {{! end }}' . "\n" . '</ul>'
    ),
    (object) array(
        'title' =>       'Conditions',
        'description' => '...',
        'code' =>        "{{! if foo->bar }}\n    foo->bar is true\n{{! else }}\n    foo->bar is false\n{{! end }}"
    ),
    (object) array(
        'title' =>       'Comments',
        'description' => '...',
        'code' =>        '{{# note: disabled template because we no longer use this }}'
    )
);

# Yet Another Template Engine - YATE
require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratchbox', 'scratchbox');

$view = new scratchboxViewYate(array(
    'source' => scratchboxView::fileSource('../views', 'yate'),
    'filters' => scratchboxView::filters()
));

$view->config(array(
    'foo' => 'bar'
));

gaiaServer::run(
    function($req, $res) use($view, $docs) {
//        $res->send(highlight_string($view->compile('overview', false), true));
        $res->send($view->render('overview', array(
            'foo' => array('bar','foo'),
            'docs' => $docs
            )));
    },
    function($req, $res) {
        $res->resource('assets/style.css');
        $res->resource('http://shjs.sourceforge.net/sh_main.js');
        $res->resource('assets/sh_yate.js');
        $res->resource('http://shjs.sourceforge.net/sh_style.css');
        $res->resource('assets/sh_style.css');
        $res->resource('sh_highlightDocument();');
    }
);

?>