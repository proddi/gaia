<?php

class campusModelPage extends scratchModel {

    static protected $properties = array('idx', 'pageId', 'title', 'text');
    static protected $modifier = array(
        'idx' => scratchModel::TYPE_INT,
//        'age' => scratchModel::TYPE_INT
    );

    static public function byIdx($idx) {
        return new static($idx);
    }
    static public function byPageId($pageId) {
        return new static($pageId, 'pageId');
    }

    protected function loadProp($prop) {
        if (($values = $this->pdo()->prepare('SELECT idx, pageId, title, content FROM pages WHERE ' . $this->_key . '=?')
                                   ->execute($this->_keyVal)
                                   ->map())) {
            $this->applyValues($values);
            $this->text = $this->content;
            return true;
        }
    }

    public function save() {
        $this->pdo()->prepare('UPDATE pages SET title=?, content=? WHERE idx=?')
                    ->execute($this->title, $this->text, $this->idx);
    }

    public function create() {
        // assume having pageId
        $this->pdo()->prepare('INSERT INTO pages (pageId, title) VALUES (?, ?)')
                    ->execute($this->pageId, ucfirst($this->pageId) . ' page');

        $this->idx = $this->pdo()->lastInsertId();
        $this->key('idx');
    }

}