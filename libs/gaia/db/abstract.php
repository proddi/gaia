<?php
/**
 * new db interface (PDO)
 * http://de.php.net/manual/en/book.pdo.php
 * @package gaia
 * @subpackage db
 * @abstract
 */

/**
 * DB interface
 *
 */
abstract class gaiaDbAbstract {
	protected $_config;
	public $fetchMode = gaiaDb::fetchNamed;
	protected $_queriesCount = 0;
	protected $_queriesTime = 0;
	/**
	 *
	 */
	public function __construct(array $config = null) {
		if ($config) $this->setConfig($config);
	}

	/**
	 * close the database on destroy
	 */
	public function __destruct() {
		$this->close();
	}

	public function setConfig(array $config) {
		$this->_config = $config;
		return $this;
	}
	abstract protected function createQuery();
	abstract protected function createStatement();

	/**
	 * data select/execute
	 *
	 */
	public function execute($statement) {
		return $this->exec($statement);
	}
	public function exec($statement) {
		if (!$this->_isOpen) $this->open();
		$query = $this->createQuery();
		$query->exec($statement);
		return $query;
	}
	public function select($statement) {
		if (!$this->_isOpen) $this->open();
		$query = $this->createQuery();
		$query->select($statement);
		return $query;
	}
	public function fetchOne($statement) {
		$query = $this->select($statement);
//		var_dump($query);
//		if ($query->eof) return null;
		list($value) = $query->fetch();
		return $value;
	}
	public function fetchRow($statement) {
		$query = $this->select($statement);
//		var_dump($query);
//		if ($query->eof) return null;
		return $query->fetch();
	}
	public function prepare($statement) {
		if (!$this->_isOpen) $this->open();
		$query = $this->createQuery();
		$query->prepare($statement);
		return $query;
	}

	public function insert2($statement) {
		echo '***';
		var_dump($this);
	}

	/**
	 * connectivity
	 */
	protected $_isOpen = false;
	abstract public function open(array $config = null);
	abstract public function close();

	/**
	 * helper functions
	 */
	abstract public function quote($statement);
	public function escape($statement) {
		return $this->quote($statement);
	}
	protected function getCurrentTime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}

/**
 * DB interface QUERY class
 * @abstract
 */
abstract class gaiaDbQueryAbstract {
	protected $_statement;
	protected $_rowCount = 0;
	protected $_currPos = 0;
	protected $_isFail;
	protected $_errorMessage;
	protected $_errorCode;

	/**
	 * Statements for data selection/execution
	 *
	 */
	// select
	abstract public function select($statement);
	// execute
	abstract public function exec($statement);
	// prepared statement
	abstract public function free();


	/**
	 * function for fetching selected data
	 */
	abstract public function fetch($fetchMode = null);
		// FETCH_ASSOC, FETCH_BOTH, FETCH_BOUND, FETCH_CLASS, FETCH_INTO, FETCH_LAZY, FETCH_NUM, FETCH_OBJ
		// default style = default fetch method

//	public abstract function fetchAll();

	/**
	 * status values
	 */
	protected function getEof() {
		return $this->_currPos>=$this->_rowCount;
	}
	abstract protected function getLastInsertId();
	abstract protected function getErrorCode();
	abstract protected function getErrorMessage();

}

/**
 * db statement class for prepared statements
 * @abstract
 */
abstract class gaiaDbStatementAbstract {
	protected $_statement;
	protected $_rowCount = 0;
	protected $_currPos = 0;
	protected $_isFail;
	protected $_errorMessage;
	protected $_errorCode;

	abstract public function prepare($sql);
	abstract public function execute(array $params = null);
	abstract public function fetch($fetchMode = null);

	/**
	 * status values
	 */
	protected function getEof() {
		return $this->_currPos>=$this->_rowCount;
	}
	abstract protected function getLastInsertId();
	abstract protected function getErrorCode();
	abstract protected function getErrorMessage();
}

?>