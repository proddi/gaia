<?php

require_once('../../../libs/gaia.php');

$validateUser = function(&$req, &$res, $data, $user, $pass) {
//    unset($_SESSION['user']);
    if (isset($_SESSION['user']) && $_SESSION['user']->authenticated) {
        $req->user = $_SESSION['user'];
        return $req->user->authenticated;
    }

    $validated = ($user === 'user' && $pass === 'pass');
    if ($validated) {
        $req->user = (object) array(
            'authenticated' => true,
            'id' => 23,
            'name' => $user,
            'email' => 'foobar@foo.bar',
            'role' => 'user'
        );
    } else {
        $req->user = (object) array(
            'authenticated' => false,
            'name' => 'Guest',
            'role' => 'guest'
        );
    }
    $_SESSION['user'] = $req->user;
    return $validated;
};

session_start();

gaiaServer::run(
    gaiaServer::requireBasicAuth(
        $validateUser,
        function($req, $res) {
            $res->send('This message will only shown to authorized users .... like: <b>' . $req->user->name .'</b>'. LF);
        }
    ),

    // apply layout
    function($req, &$res) {
        $res->send(var_export($req->user, true));
        $res->send(gaiaView::render('layout', array('res' => $res)));
    }
);

?>