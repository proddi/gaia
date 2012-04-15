# Gaia - code less, create more!

> This is a blockquote with two paragraphs. Lorem ipsum dolor sit amet,
> consectetuer adipiscing elit. Aliquam hendrerit mi posuere lectus.
> Vestibulum enim wisi, viverra nec, fringilla in, laoreet vitae, risus.
>
> Donec sit amet nisl. Aliquam semper ipsum sit amet velit. Suspendisse
> id sem consectetuer libero luctus adipiscing.


> ## This is a header.
>
> 1.   This is the first list item.
> 2.   This is the second list item.
>
> Here's some example code:
>
>     return shell_exec("echo $input | $markdown_script");
>     return shell_exec("echo $input | $markdown_script");
>
> This is the first level of quoting.
>
> > This is nested blockquote.
>
> Back to the first level.


Please don't use any `<blink>` tags (<http://example.com/>).
For parsing **markdown** code use the `transform()` method. Might that method should have a better name like `toHtml()` or like this.
    require_once('../libs/gaia.php');

    $app = new scratchApp();

    $app->get('/hello/:name', function($name, scratchApp $app) {
        $app->response()->send("Hello $name!");
    });

    $app();


```php
require_once('../libs/gaia.php');

$app = new scratchApp();

$app->get('/hello/:name', function($name, scratchApp $app) {
    $app->response()->send("Hello $name!");
});

$app();
```

## view template engine
```yate
{{ form | decorator }}
```


*   foo
*   bar

1.  foo
2.  bar

*   Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
Aliquam hendrerit mi posuere lectus. Vestibulum enim wisi,
viverra nec, fringilla in, laoreet vitae, risus.
*   Donec sit amet nisl. Aliquam semper ipsum sit amet velit.
Suspendisse id sem consectetuer libero luctus adipiscing.


    require_once('../../libs/gaia.php');

    gaiaServer::run(
        gaiaServer::router(array(
            '/' => function($req, $res) {
                $res->send('Hello World!');
            },
            '/admin' => array(
                gaiaServer::requireBasicAuth(authCallable),
                loadUser,
                andRestrictTo('admin'),
                function($req, $res) {
                    $res->send('Hello Admin!');
                }
            )
        )),
        function($req, $res) {
            $res->write(gaiaView::render('layout', array('content' => $res->content())));
        }
    );

    function loadUser($req, $res) {
        // fetch user data from db or session
        $req->user = (object) array(
            'name' => 'Foo user',
            'role' => 'user'
        );
    }

    function andRestrictTo($role) {
        return function($req, $res) use ($role) {
            if ($req->user->role !== $role) return gaiaServer::BREAKCHAIN;
        }
    }
