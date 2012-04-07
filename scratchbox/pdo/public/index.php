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

echo "<h2>Create a single statement and fetches the result into an object:</h2>";
$user = $pdo->query('SELECT idx, name FROM users WHERE idx=?', 2)->into(new stdClass());
var_dump($user);

// echo "<h2>Perform a INSERT statement with parameters and return number of affected rows:</h2>";
// echo 'effected rows: ' . $pdo->query('INSERT INTO users (name, age) VALUES(?, ?)', 'Proddi', 35)->rows() . "\n";

echo "<h2>Loop over result of an statement:</h2>";
$q = $pdo->query('SELECT idx, name FROM users');
while(list($idx, $name) = $q->values()) {
    echo "<li>$idx: $name</li>";
}
$q->close();

echo "<h2>Create a reusable statement and query as map:</h2>";
$q = $pdo->query('SELECT idx, name FROM users WHERE idx=?');
$user = $q->execute(array(2))->map();
$q->close();
var_dump($user);


?>