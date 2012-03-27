<?php

class campusControllerPage {

//    public function __construct() {
//    }

    public function __invoke(&$req, &$res, &$data) {
        $data->model = new campusModelPage($req->params->pageId, campusModelPage::BY_PAGE_ID);
        $data->rootUri = $req->getRootUri();
        $router = gaiaServer::router(array(
            '/edit*' => campusServer::call($this, 'edit'),
            '*' => campusServer::call($this, 'show')
        ));
        return $router($req, $res, $data);
    }


    public function show(&$req, &$res, &$data) {
/*
        if ($data->model->exists()) {
            $this->patchFilters();
            $res->send(gaiaView::render('page', array(
                'page' => $data->model,
                'baseUrl' => $req->getRootUri()
            )));
        } else {
            $res->send(gaiaView::render('page-notexists', array(
                'page' => $data->model,
                'baseUrl' => $req->getRootUri()
            )));
        }
  */
        $this->patchFilters();
        $res->send(gaiaView::render('page', array(
            'page' => $data->model,
            'baseUrl' => $req->getRootUri()
        )));
    }

    public function edit(&$req, &$res, &$data) {
        $form = scratchForm::xform('content',
            scratchForm::textarea('text', array('value' => $data->model->text))
                ->validate(scratchForm::validateMinLength(10, 'min 10 characters')),
            scratchForm::submit('submit', array('value' => 'absenden'))
        )       ->onSubmit(function(&$req, &$res, &$data) {
                    $form = $req->forms->content;
                    $data->model->text = $form->text->value;
                    $data->model->update();
                    $res = new gaiaResponseRedirect($data->rootUri);
                });
        gaiaServer::mw(array(
            $form,
            function($req, $res, $data) {
                $res->send(gaiaView::render('page-edit', array(
                    'form' => $req->forms->content,
                    'page' => $data->model
                )));
            }
        ), $req, $res, $data);
    }

    protected function patchFilters() {
        $cfg = gaiaView::view()->config();
        $cfg['filters']->page = function($data) {
            $res = '';
            $format = '';
            $currFormat = '';
            $closeHtml = '';
            foreach (explode("\n", $data) as $line) {
                if ('    ' === substr($line, 0, 4)) {
                    $format = 'code';
                    $line = substr($line, 4);
                } else $format = '';
                  // apply format
                if ($format === $currFormat) {
                    $res .= $line . "\n";
                } else {
                    $res .= $closeHtml . "\n";
                    $closeHtml = '';
                    switch ($format) {
                        case 'code': $res .= '<pre class="sh_yate">' . $line . "\n";
                                     $closeHtml = '</pre>';
                                     break;
                        default: $res .= $line . "\n";
                    }
                    $currFormat = $format;
                }
            }
            return $res . $closeHtml;
        };
    }
}

?>