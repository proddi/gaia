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

    fetchViaPreparedStatement();
    fetchViaPreparedStatement2();
    fetchViaPdo();
    fetchIntoObject();

} catch (gaiaDbException $e) {
    echo "<pre>gaiaDbException: " . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
}

function fetchViaPreparedStatement() {
    $q = gaiaDb::prepare('SELECT idx, name FROM users WHERE idx=?');
    $q->execute(3);
    while(list($idx, $name) = $q->fetch(gaiaDb::fetchNum)) {
        echo '<li>'.$name.'</li>';
    }
}

function fetchViaPreparedStatement2() {
    $q = gaiaDb::query('SELECT idx, name FROM users WHERE 1');
    while(list($idx, $name) = $q->fetch(gaiaDb::fetchNum)) {
        echo '<li>'.$name.'</li>';
    }
}

function fetchViaPdo() {
    $q = gaiaDb::query('SELECT idx, name FROM users WHERE 1');
    foreach ($q->fetchAll(gaiaDb::fetchObj) as $item) {
        echo '<li>'.$item->name.'</li>';
    }
}

function fetchIntoObject() {
    $q = gaiaDb::query('SELECT idx, name FROM users WHERE 1');
    foreach ($q->fetchAll(gaiaDb::fetchObj) as $item) {
        echo '<li>'.$item->name.'</li>';
    }
}

?>