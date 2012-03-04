<?php
require_once('../../../libs/gaia.php');

// Configure
$config = array (
    'adapter' => 'pdoSqlite',
    'database' => array(
        'host' => 'localhost',
        'dbname' => '../data/sqlite.sqlite',
        'username' => 'root',
        'password' => ''
    )
);

gaiaDb::setConfig($config);

try {
    $html = new gaiaResponseHtml();

    $q = gaiaDb::select('SELECT idx, name, age, quote FROM users');
    $html->send(gaiaView::render('index', array('q' => $q)));

    $q->free();

    $html->streamOut();
} catch (gaiaDbException $e) {
    echo "<pre>gaiaDbException: " . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
}

?>