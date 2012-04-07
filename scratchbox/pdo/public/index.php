<pre>
<?php

require_once('../../../libs/gaia.php');
GAIA::registerNamespace('../libs/scratch', 'scratch');

/* Minimal db setup & interface */
$pdo = new scratchPdoSqlite(array(
    'host' => 'localhost',
    'dbname' => '../data/sqlite.sqlite',
    'username' => 'root',
    'password' => '',
//    'exceptions' => false
));

$user = $pdo->query('SELECT idx, name FROM users WHERE idx=?', array(2))->into(new stdClass());
var_dump($user);

// echo 'effected rows: ' . $pdo->query('INSERT INTO users (name, age) VALUES(?, ?)', 'Proddi', 35)->rows() . "\n";

$q = $pdo->query('SELECT idx, name FROM users');
while(list($idx, $name) = $q->values()) {
    echo "<li>$idx: $name</li>";
}
$q->close();

$q = $pdo->query('SELECT idx, name FROM users WHERE idx=?');
$user = $q->execute(array(2))->into(new stdClass());
var_dump($user);


?>