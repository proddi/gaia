# Gaia - code less, create more!

  Small and powerful rails style PHP mini framework for professional projects.

```php
require_once('../libs/gaia.php');

$app = new gaiaApp();

$app->get('/hello/:name', function($name, gaiaApp $app) {
    $app->response()->send("Hello $name!");
});

$app();
```
