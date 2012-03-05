<?php

class exampleUser extends gaiaModelAbstract {

    const INDEX = 0x30;
    const NAME = 0x32;

    protected $__type;

    protected $idx;
    protected $name;
    protected $age;
    protected $quote;

    public function __construct($uid=null, $type=self::INDEX) {
        $this->uid = $uid;
        $this->__type = $type;
    }

    protected function __load($name = null) {
        switch ($this->__type) {
//            case self::INDEX: $sqlWhere = 'idx=' . $this->uid;
//                break;
            case self::NAME:  $sqlWhere = 'name LIKE '.gaiaDb::quote('%'.$this->uid.'%');
                break;
            default:          $sqlWhere = 'idx=' . $this->uid;
        }
//        $sqlWhere = (self::INDEX === $this->__type) ? 'idx='.$this->uid : 'name='.gaiaDb::quote($this->uid);
        list($this->idx, $this->name, $this->age, $this->quote) = gaiaDb::fetchRow('SELECT idx, name, age, quote FROM users WHERE '.$sqlWhere);
    }

}

?>
