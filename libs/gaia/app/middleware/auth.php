<?php

class gaiaAppMiddlewareAuth extends gaiaAppMiddleware {
    public function __construct($app, $validateFun = NULL) {
        parent::__construct($app);
        $this->validationFun = $validateFun;
        $app->register('requireUser', array($this, 'requireUser'));
        $app->register('requireRole', array($this, 'requireRole'));
    }

    public function requireUser($login = NULL) {
        $app = $this->_app;
        $app->post('/login', array($this, 'loginAction'));
        $app->get('/logout', array($this, 'logoutAction'));
        $app->map('*', array($this, 'validateAction'));
        $app();
    }

    public function requireRole($role) {
        $this->requireUser();
        $app = $this->_app;
        // checkrole
        // $app->user()->isRole()
    }

    public function validateAction(gaiaApp $app) {
        if ($app->session()->authenticated) {
            $app->user = $app->session()->user;
            return;
        }

        $app->response()->send($app->view()->render('login', array(
            'baseUrl' => $app->request()->baseUrl(),
            'login' => $app->request()->post('login'),
            'error' => 'SOME ERROR'
        )));
        $app->stop();
    }

    public function loginAction(gaiaApp $app) {
//        var_dump('ACTION::'.__FUNCTION__);

        $login = $app->request()->post('login');
        $secret = $app->request()->post('secret');
        if ($login) {
            $response = call_user_func($this->validationFun, $login, $secret);
            if (is_object($response)) {
                $app->session()->authenticated = true;
                $app->session()->user = $response;
                $app->response()->redirect($app->request()->baseUrl().'/..');
                $app->stop();
            }
        }
        $app->next();
    }

    public function logoutAction(gaiaApp $app) {
//        var_dump('ACTION::'.__FUNCTION__);
        $app->session()->authenticated = false;
        $app->session()->user = NULL;
        $app->response()->redirect($app->request()->baseUrl().'/..');
        $app->stop();
    }

    protected $validationFun;

}
