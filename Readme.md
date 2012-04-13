# Gaia - code less, create more!

  Small and powerful rails style PHP mini framework for professional projects.

```php
require_once('../libs/gaia.php');

$app = new scratchApp();

$app->get('/hello/:name', function($name, scratchApp $app) {
    $app->response()->send("Hello $name!");
});

$app();

```
