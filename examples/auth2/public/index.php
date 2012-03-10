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
        session_start();

        if ('/logout' === $req->getUri()) {
            unset($_SESSION['authenticated']);
            unset($_SESSION['user']);
            $res = new gaiaResponseRedirect($req->getBaseUri());
            return;
        }
        $error = '';
        // is already authenticated ?
        if (!empty($_SESSION['authenticated'])) {
            $req->user = $_SESSION['user'];
            return;
        }
        // is login request ?
        if ($req->isPost() && '/login' === $req->getUri() && $_POST['login']) {
            // todo: validation
            $user = NULL;
            $error = $validationFun($_POST['login'], $_POST['password'], $user);
            if (!$error) {
                $_SESSION['user'] = $req->user = $user;
                $_SESSION['authenticated'] = true;
                $res = new gaiaResponseRedirect($req->getBaseUri());
                return;
            }
        }

        $res->send(gaiaView::render('login', array(
            'baseUri' => $req->getBaseUri(),
            'login' => isset($_POST['login']) ? $_POST['login'] : '',
            'error' => $error
        )));
        return gaiaServer::BREAKCHAIN;
    };
}

gaiaServer::run(
    array(
        // this chain needs a valid user

        // show a login form if user hasn't logged in
        requireUser($validateUser),

        // now a user has logged in and ($req->user) is available (see $validateUser function)
        function($req, $res) {
            $res->send('Valid user area (' . $req->user->name . ')');
        }
    ),

    // layout is for every request
    function($req, &$res) {
        $res->resource('assets/style.css');

        $res->send(gaiaView::render('layout', array(
            'baseUri' => $req->getBaseUri(),
            'authorized' => !empty($req->user),
            'user' => isset($req->user) ? $req->user : null,
            'res' => $res
        )));
    }
);

?>