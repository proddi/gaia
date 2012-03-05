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
    $q = gaiaDb::select('SELECT idx, name FROM users');
    while(list($idx, $name) = $q->fetch(gaiaDb::fetchNum)) {
        echo '<li>'.$name.'</li>';
    }
    $q->free();
} catch (gaiaDbException $e) {
    echo "<pre>gaiaDbException: " . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
}

?>