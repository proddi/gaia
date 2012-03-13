<?php

/**
 * This is the static base class of the gaia framework
 *
 * @package gaia
 * @author proddi@splatterladder.com
 * @version 0.1
 */

if (ini_get('register_globals')) die("I dont want run in a unsecure environment!");

error_reporting(error_reporting() ^ E_STRICT);

define ('LF',"<br>\n");

class GAIA {

	protected static $_timeStart;

    static public function Initialize() {
    	self::$_timeStart = getmicrotime();
    	spl_autoload_register('GAIA::requireClass');
    }

    static public function Finalize() {
        self::runShutdown();
        spl_autoload_unregister('GAIA::requireClass');
        return;
    }

    static public function getRunTime() {
    	return (getmicrotime() - self::$_timeStart);
    }

    public static $countFactory = 0;
    public static $countInclude = 0;
    private static $_path = __DIR__;
    private static $_nsPaths = array();

    /**
     * Sets the path for the gaia framework.
     * @param <String|Array> $path The path to the namespace or an Array of namespaces.
     * @param <String> [$ns=NULL] If namespace is specified, the path is only valid for this namespace.
     * @static
     */
    public static function registerNamespace($path, $ns=NULL) {
    	if (is_array($path)) {
    		foreach ($path as $ns => $p) {
		    	if ($ns) {
		    		self::$_nsPaths[$ns] = $p;
		    	} else {
		        	self::$_path = $p;
		    	}
    		}
    	} else {
	    	if ($ns) {
	    		self::$_nsPaths[$ns] = $path;
	    	} else {
	        	self::$_path = $path;
	    	}
    	}
    }

    /**
     * return the path for the gaia framework.
     * @return <string> $aPath
     */
    public static function getPath() {
        return self::$_path;
    }

    /**
	 * return a Singleton instance
	 * this use the Factory function, class rewrite and relocate is supported
	 *
	 * @param string $classname
	 * @param variant [$uid=NULL]
	 * @param variant [$id2=NULL]
	 * @return object
	 */
	public static function Singleton($className, $id=null, $id2=null) {
	    static $cache;
		$key = $className.strval($id).strval($id2);
		if (isset($cache[$key])) return $cache[$key];
		switch (func_num_args()) {
		    case 1: $o = new $className(); break;
		    case 2: $o = new $className(func_get_arg(1)); break;
		    case 3: $o = new $className(func_get_arg(1), func_get_arg(2)); break;
		    case 4: $o = new $className(func_get_arg(1), func_get_arg(2), func_get_arg(3)); break;
		    default: throw new gaiaException('Too many function paramater!');
		}
		return $cache[$key] = $o;
	}

	/**
	 * build a instance
	 * class rewrite and relocate is supported
	 *
	 * @param {String} $className
	 * @return {Class} The created class
	 * @throws gaiaException
	 */
	static public function factory($className, $id=null, $id2=null) {
		if (self::requireClass($className)) {
			return new $className($id, $id2);
		}
		throw new gaiaException('Class ' . $className . ' not found');
	}

	/**
	 * Load a class if needed. It maps classname to file structure.
	 * This function is used by autoloading feature.
	 *
	 * @param {String} $className
	 * @return unknown_type
	 */
	public static function requireClass($className) {
		if (class_exists($className)) return true;
		$nsPath = self::$_path;
		$classNameFile = $className;
		foreach (self::$_nsPaths as $ns => $path) {
			if (strpos($className, $ns) === 0) {
				$nsPath = $path;
				$classNameFile = lcfirst(substr($className, strlen($ns)));
				break;
			}
		}
		$classFile = $nsPath . '/' . strtolower(preg_replace('([[:upper:]])', '/${0}', $classNameFile)) . '.php';
        $res = file_exists($classFile) ? include_once $classFile : false;
		self::$countInclude++;
		return $res && class_exists($className);
	}

	/*
	 * shutdown feature
	 */
	protected static $_shutdownHandler = array();
	public static function registerShutdown($handler, $last=false) {
		// build an invert array to execute the events reverse
		if (!$last) array_unshift(self::$_shutdownHandler, $handler);
		else self::$_shutdownHandler[] = $handler;
		return $handler;
	}

	public static function runShutdown() {
		foreach (self::$_shutdownHandler as $handler) {
			if ($handler instanceof gaiaEventHandler) {
				$handler->Raise();
			} else if ($handler instanceof Closure) {
				$handler();
			} else {
				// TODO define what we should do here (no compatible handler)
			}
		}
	}

}

/**
 * The GAIA shutdown function
 * @ignore
 */
register_shutdown_function('__shutdown');
function __shutdown() {
	GAIA::Finalize();
}

/**
 * Extend Exception with development/logging options
 */
class gaiaException extends Exception {
//	const NOT_IMPLEMENTED = 'not implemented';

	protected $debug = '';

	public function __construct($message, $code = 0, $debug = '') {
		$this->debug = $debug;
		return parent::__construct($message, $code);
	}
/*
	public function __toString() {
		// for debug mode we print special detailed errors
		if (true) {
			return parent::__toString();
		} else {
			// in production, we print only a small hint
			return __CLASS__ . ": [{$this->code}]\n";
		}
	}
*/
}
//class gaiaAutoloadException extends gaiaException {}

/**
 * gaiaBase
 * /
class gaiaBase {
	public function bind(&$var) {
		return $var = $this;
	}

	/**
	 * Getter for underlined properties (magic function)
	 * @param string $name The name
	 * @return mixed
	 * @ignore
	 * /
	public function __get($name) {
		//Search first for Get$name
		$method='get'.$name;
		if (method_exists($this,$method)) return ($this->$method());
		// Check int vars
		$intName = '_'.$name;
		if (property_exists($this, $intName)) {
			if (!isset($this->$intName)) {
				// Try Value Init function
				$method = 'init'.$name;
				if (method_exists($this, $method)) 	$this->$method();
				// not set again ? then call onGet for autoload
				if (!isset($this->$intName)) $this->_onGet($name);
			}
			return $this->$intName;
		}
		throw new gaiaException('Property not found (get): '.get_class($this).".".$name);
	}

	/**
	 * @todo: onGet ?
	 * @param <type> $name
	 * /
	protected function _onGet($name) {}

	/**
	 * Setter for underlined properties (magic function)
	 * @param string $name The name
	 * @param mixed $value The value
	 * @return mixed
	 * @ignore
	 * /
	public function __set($name, $value) {
		$method='set'.$name;
		if (method_exists($this,$method)) return $this->$method($value);
		// Try default
		$intname = '_'.$name;
		if (property_exists($this, $intname)) {
			if ($this->$intname === $value);// $this->_onChange($name, $value);
			$this->$intname = $value;
			return $this; // for chaining
		}
		throw new gaiaException('Property not found ('.get_class($this).'::'.$name.')');
	}

	/**
	 * implement getter + setter call syntax
	 * @param string $name
	 * @return class $this;
	 * @ignore
	 * /
	public function __call($name, $args) {
		switch (substr($name, 0 ,3)) {
			case 'get':
				$property = '_'.lcfirst(substr($name, 3));
				if (property_exists($this, $property)) return $this->$property;
				throw new gaiaException('Call to xyz failed, property not exist!');
				break;
			case 'set':
				$property = '_'.lcfirst(substr($name, 3));
				if (property_exists($this, $property)) $this->$property = current($args);
				else throw new gaiaException('Call to xyz failed, property not exist!');
				break;
			default:
				throw new gaiaException('Call to '.__CLASS__.'::'.$name.' failed (property does not exist)');
		}
		return $this;
	}
}
*/

/**
 * gaiaEventHandler
 * @package gaia
 */ /*
class gaiaEventHandler {
	protected $Owner = null;
	protected $Method = '';
	protected $Param = null;
	public function __construct($owner, $method, $param=null) {
		$this->Owner = $owner;
		$this->Method = $method;
		$this->Param = $param;
	}
	public function raise($param=null, $param2=null) {
		$method = $this->Method;
		return $this->Owner->$method($param ? $param : $this->Param, $param2);
	}
}
*/
//class_alias('gaiaEventHandler', 'gaiaBind');

//function gaiaBind($o, $method, $param1, $param2) {
//	if ($param2) {
//		return function($value) use ($o, $method, $param1, $param2) {
//			$o->$method($param1, $param2, $value);
//		};
//	}
//	if ($param1) {
//		return function($value) use ($o, $method, $param1) {
//			$o->$method($param1, $value);
//		};
//	}
//	return function($value) use ($o, $method) {
//		$o->$method($value);
//	};
//};

/**
 * Implement the getmicrotime function if the system does not provide it.
 * @return float Time in microseconds
 */
if (false === function_exists('getmicrotime')) {
	function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}

/*
 * Initialize / Run framework
 */

GAIA::Initialize();

?>