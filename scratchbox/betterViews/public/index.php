<?php
/*
echo '<pre>';
$s = 'foo 500 "500" \'foo\' foo["foo"] foo->bar|foo(foo) '."\n";
//$s = 'foo=NULL; bar=(object) array("foo" => i5 < bar)'."\n";
//$s = "\$v->filters->join(array('a', 'b') , ', ')\n";
echo $s;
echo preg_replace_callback('/(?:("|\')(.*?)\1|(?<![\$|>|\w])(([a-zA-Z][\w->]*)(?![\(|\w])))/', function($args) {
//    var_dump($args);
    if (isset($args[3])) {
        if (in_array($args[3], array('NULL', 'object'))) return $args[3];
        return '$v->' . $args[3];
    }
    return $args[1].$args[2].$args[1];
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
/*
gaiaView::config(array(
    'source' => scratchboxView::fileSource('../views', 'yate'),
    'filters' => scratchboxView::filters()
//    scratchboxViewYate::config()
));
*/
$view = new scratchboxViewYate(array(
    'source' => scratchboxView::fileSource('../views', 'yate'),
    'filters' => scratchboxView::filters()
));

$view->config(array(
    'foo' => 'bar'
));

gaiaServer::run(
    function($req, $res) use($view, $docs) {
//        return $res->send(highlight_string($view->compile('overview', false), true));
        $res->send($view->render('overview', array(
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