<?php

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

?>