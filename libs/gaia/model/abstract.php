<?php

/**
 * the model class
 *
 * @package gaia
 * @subpackage model
 * @author proddi@splatterladder.com
 * @abstract
 */
abstract class gaiaModelAbstract {

    private $__loaded = false;
    protected $uid;

    public function __construct($uid=null) {
        $this->uid = $uid;
    }

    /**
     * Fetches data from datasource if needed.
     */
    public function load() {
        if (!$this->__loaded)
            $this->__loaded = $this->__load($name);
    }

    /**
     * Getter for private or non-existing properties
     *
     * @param string $name The name
     * @return mixed
     * @ignore
     */
    public function __get($name) {
        // try an getter
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter))
            return ($this->$getter());

        // check for private props
        $propName = '_' . $name;
        if (property_exists($this, $propName)) {
            if (!$this->__loaded && !isset($this->$propName))
                $this->__loaded = $this->__load($name);
            return $this->$propName;
        }
        throw new gaiaException('gaiaModel.getter: Property not found. / ' . get_class($this) . '::' . $name);
    }

    /**
     * Setter for underlined properties (magic function)
     * @param string $name The name
     * @param mixed $value The value
     * @return mixed
     * @ignore
     */
    public function __set($name, $value) {
        // try an setter
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter))
            return $this->$setter($value);

        // check for private props
        $propName = '_' . $name;
        if (property_exists($this, $propName)) {
            $this->$propName = $value;
        } else {
            throw new gaiaException('gaiaModel.setter: Property not found. / ' . get_class($this) . '::' . $name);
        }
    }

    abstract protected function __load($name = null);
}

?>