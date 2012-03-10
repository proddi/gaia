<?php

require_once('../../../libs/gaia.php');

$validateUser = function($login, $pass, &$user) {
    if (strlen($pass) < 4) {
        return 'MÖÖÖÖÖÖP! Für dieses Beispiel musst du ein Password mit mindestens 4 Zeichen eingeben. Das Passwort ansichist egal. TolleSicherheit was?';
    }
    $user = (object) array(
        'name' => $login
    );
};

function requireUser($validationFun, array $options = NULL) {
    return function(&$req, &$res, &$data) use ($validationFun) {
        if ('/logout' === $req->getUri()) {
            unset($_SESSION['authenticated']);
            unset($_SESSION['user']);
        }
        $error = '';
        // is already authenticated ?
        if (!empty($_SESSION['authenticated'])) {
            $req->user = $_SESSION['user'];
            return;
        }

        if ($req->isPost() && '/login' === $req->getUri() && $_POST['login']) {
            // todo: validation
            $user = NULL;
            $error = $validationFun($_POST['login'], $_POST['password'], $user);
            if (!$error) {
                $_SESSION['user'] = $req->user = $user;
                $_SESSION['authenticated'] = true;
                $res = new gaiaResponseRedirect($req->getBaseUri());
                // TODO: redirect to $req->getBaseUri();
                return;
            }
        }

        $res->finish(gaiaView::render('login', array(
            'baseUri' => $req->getBaseUri(),
            'login' => $_POST['login'],
            'error' => $error
        )));
    };
}

session_start();

gaiaServer::run(
    requireUser($validateUser),

    // apply layout
    function($req, &$res) {
        $res->resource('assets/style.css');
        $res->send(gaiaView::render('layout', array(
            'baseUri' => $req->getBaseUri(),
                'user' => $req->user,
            'res' => $res
        )));
    }
);

?>