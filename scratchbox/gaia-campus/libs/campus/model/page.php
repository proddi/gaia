<?php

class campusModelPage extends gaiaModelAbstract {

    const BY_INDEX = 0x30;
    const BY_PAGE_ID = 0x32;

    protected $__type;

    protected $_idx;
    protected $_pageId;
    protected $_title;
    protected $_text;

    public function __construct($uid=null, $type=self::BY_INDEX) {
        $this->uid = $uid;
        $this->__type = $type;
    }

    protected function __load($name = null) {
        switch ($this->__type) {
            case self::BY_PAGE_ID: $sqlWhere = 'pageID='.gaiaDb::quote($this->uid);
                break;
            default: $sqlWhere = 'idx=' . intval($this->uid);
        }
        if (!list($this->_idx, $this->_pageId, $this->_title, $this->_text) = gaiaDb::fetchRow('SELECT idx, pageId, title, content FROM pages WHERE ' . $sqlWhere)) {
            throw new gaiaException('Page doesn\'t exists');
        }
    }

    public function update() {
        switch ($this->__type) {
            case self::BY_PAGE_ID: $sqlWhere = 'pageID='.gaiaDb::quote($this->uid);
                break;
            default: $sqlWhere = 'idx=' . intval($this->uid);
        }

        gaiaDb::exec('UPDATE pages SET content='.gaiaDb::quote($this->_text).' WHERE '. $sqlWhere);
    }
}

?>
