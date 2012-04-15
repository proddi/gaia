<?php

/**
 * the list of model class
 *
 * @package gaia
 * @subpackage model
 * @author proddi@splatterladder.com
 * @abstract
 */
abstract class scratchModelList extends scratchModel implements IteratorAggregate {

    public function __construct($value, $key = 'idx') {
        static::$keyLoader[$key] = 'loadBy' . ucfirst($key);
        parent::__construct($value, $key);
    }
    /**
     * magic function, called by foreach
     * @return Iterator ArrayIterator An iterator with the loaded elements
     */
	public function getIterator() {
        return new ArrayIterator($this->load($this->_key));
    }

    protected function loadProp($prop) {
        throw new Exception('Normal property load not implemented');
    }

}

?>