<?php

/**
 * the list of model class
 *
 * @package gaia
 * @subpackage model
 * @author proddi@splatterladder.com
 * @abstract
 */
abstract class gaiaModelList extends gaiaModelAbstract implements IteratorAggregate {

	protected $__type;
	protected $__items;
	protected $__loaded = false;

    public function __construct($uid=null, $type = 'default') {
        $this->__type = $type;
        parent::__construct($uid);
    }

    /**
     * magic function, called by foreach
     * @return Iterator ArrayIterator An iterator with the loaded elements
     */
	public function getIterator() {
		if (!$this->__loaded) {
			$this->__items = array();
			$method = '__load' . ucfirst(strtolower($this->__type));
//			echo $method."<br>\n";
			$this->$method();
			$this->__loaded = true;
		}
        return new ArrayIterator($this->__items);
    }

}

?>