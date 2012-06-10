<?php

abstract class gaiaModel {

    const TYPE_INT = 'intval';

    // static interface
    static protected $_db;

    static public function db($db = null) {
        if (func_num_args() > 0) static::$_db = $db;
        return static::$_db;
    }

    static protected function intval($data) {
        return intval($data);
    }

    // pdo interface
    protected $_pdo;
    public function pdo($pdo = null) {
        if (isset($pdo)) {
            $this->_pdo = $pdo;
            return $this;
        }
        return $this->_pdo;
    }

    // model interface

    static protected $keyLoader = array();

    static protected $properties = array('idx');

    static protected $modifier = array();

    public function __construct($value, $key = 'idx') {
        $this->$key = $value;
        $this->_key = $key;
        $this->_keyVal = $value;
    }

    protected $_key;
    protected $_keyVal;

    public function key($key = NULL) {
        if ($key) $this->_key = $key;
        return $this->_key;
    }

    public function __get($prop) {
        $this->load($prop);
        return $this->$prop;
    }

    public function __isset($prop) {
        $this->load($prop);
        return property_exists($this, $prop);
    }

    protected $_loaded;
    public function load($prop = NULL) {
        if (!isset($this->_loaded)) {
            if (!($loader = @static::$keyLoader[$prop])) $loader = 'loadProp';
            $this->_loaded = $this->$loader($prop);
            if (!isset($this->_loaded)) $this->_loaded = true;
        }
        return $this->_loaded;
    }

    abstract protected function loadProp($prop);

    // TODO: calling inside loadProp triggers a sql query for every property because of isset()
    protected function applyValues(array $values) {
        foreach ($values as $key => $val) {
            if (!isset($this->$key)) {
                if (($modifier = @static::$modifier[$key])) $val = call_user_func(array(__CLASS__, $modifier), $val);
                $this->$key = $val;
            }
        }
    }

    public function exists() {
        $exists = true;
        foreach (static::$properties as $prop) {
            $exists = $exists && isset($this->$prop);
        }
        return $exists;
    }

}