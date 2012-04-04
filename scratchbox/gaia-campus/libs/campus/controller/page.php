<?php

error_reporting(E_ALL);
class campusControllerPage {

//    public function __construct() {
//    }

    public function __invoke(&$req, &$res, &$data) {
        $data->model = new campusModelPage($req->params->pageId, campusModelPage::BY_PAGE_ID);

        $data->rootUri = $req->baseUri;

        $router = gaiaServer::router(array(
            '/edit*' => campusServer::call($this, 'edit'),
            '*' => campusServer::call($this, 'show')
        ));

        return $router($req, $res, $data);
    }

    public function show(&$req, &$res, &$data) {
        $mw = array(
            gaiaServer::path('/page-:id', campusServer::controller('campusPageEdit')),
            function($req, $res) { $res->send(" world!");}
        );
        gaiaServer::mw($mw, $req, $res, $data);

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
            'baseUrl' => $req->baseUri
        )));
    }

    public function edit(&$req, &$res, &$data) {
        $form = gaiaForm::xform('content',
            gaiaForm::textarea('text', array('value' => $data->model->text))
                ->label('Type a Entry')
                ->validate(gaiaForm::validateRequired('The field can not be emtpy.')),
            gaiaForm::submit('submit', array('value' => 'absenden'))
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

    // filters
    protected function patchFilters() {
        $cfg = gaiaView::view()->config();
        $cfg['filters']->wiki = function($data) {
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