<?php

/**
 * db exception class
 *
 * @package gaia
 * @subpackage db
 * @author proddi@splatterladder.com
 */
class gaiaDbException extends gaiaException {}

/**
 * Static DB interface
 *
 * <code>
 * <?php
 * gaiaDb::setConfig($config);
 * echo $q = gaiaDb::SelectValue('SELECT UNIX_TIMESTAMP();');
 *
 * $db = new gaiaDbMysql($config['database']);
 * echo $q = $db->SelectValue('SELECT UNIX_TIMESTAMP();');
 * ?>
 * </code>
 *
 * @package gaia
 * @subpackage db
 * @author proddi@splatterladder.com
 */
class gaiaDb {

	const fetchNum = 3;
	const fetchAssoc = 2;
	const fetchNamed = 11;
	const fetchObj = 5;
	const fetchObject = 5;
	const fetchArray = 20;

	public static $adapterName = 'mysql';
	protected static $_Adapter;
	protected static $_config = null;

    /**
     * set a global config
     * @static
     * @param <gaiaConfig> $config
     */
	public static function setConfig(array $config) {
		self::$adapterName = $config['adapter'];
		self::$_config = $config['database'];
	}

	/**
	 * adapter instance
	 *
	 * @return gaiaDbAbstract
	 */
	public static function getAdapter() {
		if (!self::$_Adapter instanceof gaiaDbAbstract) {
			// create a adapter (default or named by config)
			$adapterName = __CLASS__.ucfirst(self::$adapterName);
			self::$_Adapter = new $adapterName(self::$_config);
		}
		return self::$_Adapter;
	}

	public static function close() {
		if (self::$_Adapter) {
			self::$_Adapter->close();
			self::$_Adapter = null;
		}

	}

	public static function select($statement) {
		$adapter = self::getAdapter();
		return $adapter->select($statement);
	}
/*
	public static function SelectValue($sql) {
		$adapter = self::getAdapter();
		return $adapter->SelectValue($sql);
	}

	public static function Execute($sql) {
		$adapter = self::getAdapter();
		return $adapter->Execute($sql);
	}
*/
	/**
	 * Zend interface ?
	 * --> http://de.php.net/manual/en/book.pdo.php
	 *
	 * @param string $sql
	 * @return value
	 */
	// select sql, return one value
	public static function fetchOne($sql) {
		$adapter = self::getAdapter();
		return $adapter->fetchOne($sql);
	}
	// return a STMI object
	public static function query($sql) {
		$adapter = self::getAdapter();
		return $adapter->query($sql);
	}
	// execute sql
	public static function exec($sql) {
		$adapter = self::getAdapter();
		return $adapter->Execute($sql);
	}
	public static function update($sql) {
		$adapter = self::getAdapter();
		return $adapter->update($sql);
	}
	public static function insert($sql) {
		$adapter = self::getAdapter();
		return $adapter->insert($sql);
	}
	public static function quote($statement) {
		$adapter = self::getAdapter();
		return $adapter->quote($statement);
	}

}

?>