<?php

class ownController {
    public function __invoke(&$req, &$res, $data) {
        $method = 'on' . ucfirst($req->getAction());
        if (method_exists($this, $method)) {
            if ($this->$method($req, $res, $data)) return;
        }
        return $this->render($req, $res, $data);
    }

    protected function render($req, $res) {
        $res->send(gaiaView::render('controller', array('url' => $req->getBaseUri() . 'index.php/hello/proddi')));
    }

    protected function onFoo($req, $res) {
        $res->send('msg', 'foo action');
    }

    protected function onBar($req, $res) {
        $res->send('msg', 'bar action');
    }

}

?>
