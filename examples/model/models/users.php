<?php

class exampleUsers extends gaiaModelList {

    const INDEX = 'default';
    const QUERY = 'query';

    protected $__param;

    // we don't need an uid
    public function __construct($type = self::INDEX, $param = NULL) {
        $this->__param = $param;
        parent::__construct(NULL, $type);
    }

    protected function __loadDefault() {
        $q = gaiaDb::select('SELECT idx FROM users');
        while (list($idx) = $q->fetch(gaiaDb::fetchNum)) {
            $this->__items[] = new exampleUser($idx);
        }
    }

    protected function __loadQuery() {
        $query = gaiaDb::quote('%'.$this->__param.'%');
        $q = gaiaDb::select('SELECT idx FROM users WHERE name LIKE '.$query.' OR quote LIKE '.$query);
        while (list($idx) = $q->fetch(gaiaDb::fetchNum)) {
            $this->__items[] = new exampleUser($idx);
        }
    }

}

?>
