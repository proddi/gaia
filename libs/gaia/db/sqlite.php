<?php
/**
 * SQLite DB driver
 *
 * @package gaia
 * @subpackage db
 */

class gaiaDbSqlite extends gaiaDbAbstract {
	protected $__dbLink;

	/**
	 * create my query class
	 *
	 * @return gaiaDbSqlQuery
	 */
	protected function createQuery() {
		return new gaiaDbSqliteQuery($this, $this->__dbLink);
	}

	/**
	 * create a statement class, but sqlite does not support prepared statements
	 *
	 * @throws gaiaDbException
	 */
	protected function createStatement() {
		throw new gaiaDbException('sqlite does not support prepared statements');
	}

	public function open(array $config = null) {
		if (!$this->__dbLink = sqlite_open($this->_config['dbname'], NULL, $err))
			throw new gaiaDbException('Cannot connect to database: ' . $err);
		GAIA::registerShutdown(new gaiaEventHandler($this, 'close'));
		$this->_isOpen = true;
		return $this;
	}

	/**
	 * close db connection
	 *
	 * @return class $this
	 */
	public function close() {
		if (isset($this->__dbLink)) {
			sqlite_close($this->__dbLink);
			unset($this->__dbLink);
		}
		$this->_isOpen = false;
		return $this;
	}

	/**
	 * quote a string or value
	 *
	 * @param string $statement
	 * @return string
	 */
	public function quote($statement) {
		return sqlite_escape_string($statement);
	}

}

class gaiaDbSqliteQuery extends gaiaDbQueryAbstract {
	protected $__dbLink;
	protected $__queryResult = null;
	protected $_Db;

	public function __construct(gaiaDbSqlite $db, $dbLink) {
		$this->_Db = $db;
		$this->__dbLink = $dbLink;
	}

	/**
	 * data selection/execution
	 */
	public function exec($statement) {
		$this->free();
		$this->_statement = $statement;
		$this->_isFail = !sqlite_exec($this->__dbLink, $statement);
		if ($this->_isFail) throw new gaiaDbException('sqlite query failed: '.$this->ErrorMessage.' ('.$this->statement.')');
		$this->_rowCount = sqlite_changes($this->__dbLink);
		return $this;
	}

	public function select($statement) {
		$this->free();
		$this->_statement = $statement;
//		$tmp = $this->Db->beginQuery($this);
		$this->_isFail = ($this->__queryResult = sqlite_query($this->__dbLink, $this->_statement)) === false;
//		$this->Db->endQuery($this, $tmp);
		if ($this->_isFail) throw new gaiaDbException('sqlite select failed: '.$this->ErrorMessage.' ('.$this->statement.')');
		$this->_currPos = 0;
		$this->_rowCount = sqlite_num_rows($this->__queryResult);
		return $this;
	}

	/**
	 * free query memory
	 *
	 * @return unknown
	 */
	public function free() {
		if (isset($this->__queryResult)) unset($this->__queryResult);
		return $this;
	}

	/**
	 * function for fetching selected data
	 */
	public function fetch($fetchMode = null) {
		if ($this->eof) return false;
		$this->_currPos++;
		switch (isset($fetchMode) ? $fetchMode : $this->Db->fetchMode) {
//			case gaiaDb::fetchAssoc:	return mysqli_fetch_assoc($this->__queryResult); break;
			case gaiaDb::fetchObj:		return sqlite_fetch_object($this->__queryResult); break;
			case gaiaDb::fetchArray:	return new gaiaUtilsArray(sqlite_fetch_array($this->__queryResult, SQLITE_ASSOC)); break;
			default:					return sqlite_fetch_array($this->__queryResult, SQLITE_NUM); break;
		}
		throw new gaiaDbException('fetch method not implemented');
	}

	/**
	 * status values
	 */
	protected function getLastInsertId() {
		return sqlite_last_insert_rowid($this->__dbLink);
	}
	protected function getErrorCode() {
		return $this->_errorCode = sqlite_last_error($this->__dbLink);
	}
	protected function getErrorMessage() {
		return $this->_errorMessage = sqlite_error_string($this->errorCode);
	}

}

?>