<?php

class campusControllerDocs {

    static public function map(scratchApp $app) {
        $controller = new static();
        $app->get('/:pageId', array($controller, 'get'));
        $app->map('/:pageId/edit', array($controller, 'edit'))
            ->via('GET', 'POST');
        $app->get('/:pageId/new', array($controller, 'create'));
        $app->get('/', array($controller, 'index'));
        $app();

        $baseUri = $app->request()->baseUrl();
        $app->response()->resource($baseUri . '/../assets/style.css');
        $app->response()->resource($baseUri . '/../assets/sh_style.css');
        $app->response()->resource($baseUri . '/../assets/sh_main.js');
        $app->response()->resource($baseUri . '/../assets/sh_yate.js');
        $app->response()->resource($baseUri . '/../assets/sh_php.js');
        $app->response()->resource('sh_highlightDocument();');
        $app->render('layout', array(
            'content' => $app->response()->content()
        ));
    }

    public function get($pageId, scratchApp $app) {
        $page = campusModelPage::byPageId($pageId)->pdo($app);
        if ($page->exists()) {
            $app->render('page', array(
                'page' => $page,
                'pageText' => campusMarkdown::transform($page->text),//static::wikiFilter($page->text),
                'baseUrl' => $app->request()->baseUrl()
            ));
        } else {
            $app->render('page-new', array(
                'page' => $page,
                'baseUrl' => $app->request()->baseUrl()
            ));
        }
    }

    public function edit($pageId, scratchApp $app) {
        $page = campusModelPage::byPageId($pageId)->pdo($app);

        $form = $app->form('content',
            scratchAppForm::text('title', array('value' => $page->title))
                ->label('Page title')
                ->validate(gaiaForm::validateRequired('The field can not be emtpy.')),
            scratchAppForm::textarea('text', array('value' => $page->text))
                ->label('Content (later markdown markup)')
                ->validate(gaiaForm::validateRequired('The field can not be emtpy.')),
//            scratchAppForm::text('somenumber', array())
//                ->label('Enter some number')
//                ->filter('int'),
            scratchAppForm::submit('submit', array('value' => 'absenden'))
        )->onValid(function($form, $app) use (&$page) {
            $page->text = $form->text->value;
            $page->title = $form->title->value;
            $page->save();
            $app->response()->redirect($app->request()->baseUrl() . '/../');
            $app->stop();
        });

        $app->render('page-edit', array(
            'form' => $form,
            'page' => $page
        ));
    }

    public function create($pageId, scratchApp $app) {
        $page = campusModelPage::byPageId($pageId)->pdo($app);
        if (!$page->exists()) {
            $page->create();
            $app->response()->redirect($app->request()->baseUrl() . '/../edit');
            $app->stop();
        } else {
            $app->response()->redirect($app->request()->baseUrl() . '/../');
            $app->stop();
        }
    }

    public function index(scratchApp $app) {
        $pages = campusModelPages::byParentIdx(0)->pdo($app);

        $app->render('page-index', array(
            'pages' => $pages,
            'baseUrl' => $app->request()->baseUrl()
        ));
    }

}