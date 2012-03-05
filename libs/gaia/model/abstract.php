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
	 * Getter for private or non-existing properties
	 *
	 * @param string $name The name
	 * @return mixed
	 * @ignore
	 */
    public function __get($name) {
		// try an getter
		$getter='get'.ucfirst($name);
		if (method_exists($this, $getter)) return ($this->$getter());

		// check for private props
		if (property_exists($this, $name)) {
			if (!$this->__loaded && !isset($this->$name)) {
				$this->__loaded = $this->__load($name);
			}
			return $this->$name;
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
		$setter='set'.ucfirst($name);
		if (method_exists($this, $setter)) return $this->$setter($value);

		// check for private props
		if (property_exists($this, $name)) {
			$this->$name = $value;
		} else {
			throw new gaiaException('gaiaModel.setter: Property not found. / ' . get_class($this) . '::' . $name);
		}
	}

	abstract protected function __load($name = null);

}

?>