<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pages
 *
 * @author tosa
 */
class campusModelPages extends gaiaModelList {

    static public function byParentIdx($parentIdx) {
        return new static($parentIdx, 'parentIdx');
    }

    protected function loadByParentIdx() {
        $pages = array();
        foreach ((($this->pdo()->prepare('SELECT idx, pageId, title FROM pages WHERE parentIdx=?')
                    ->execute($this->parentIdx)
                    ->allMap())) as $res) {
            $page = campusModelPage::byIdx($res['idx']);
            $page->pageId = $res['pageId'];
            $page->title = $res['title'];
            $pages[] = $page;
        }
        return $pages;
    }

}