<?php

class scratchModelUser extends scratchModel {
/*
    static protected $keyLoader = array(
        'idx' => 'loadByIdx',
        'name' => 'loadByName'
    );
*/

    static protected $properties = array('idx', 'name', 'age', 'quote');
    static protected $modifier = array(
        'idx' => scratchModel::TYPE_INT,
        'age' => scratchModel::TYPE_INT
    );

    static public function byIdx($idx) {
        return new static($idx);
    }
    static public function byName($name) {
        return new static($name, 'name');
    }

    protected function loadProp($prop) {
        if (($values = $this->pdo()->prepare('SELECT idx, name, age, quote FROM users WHERE ' . $this->_key . '=?')
                                   ->execute($this->_keyVal)
                                   ->map())) {
            $this->applyValues($values);
            $this->key('idx');  //force idx to primary key
            return true;
        }
    }

    public function save() {
//        $this->load();
        scratchModel::db()->query('UPDATE users SET name=?, age=?, quote=? WHERE idx=?')
                          ->execute(array($this->name, $this->age, $this->quote, $this->idx));
    }

}