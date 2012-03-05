<?php
/**
 * gaia PDO class abstraction
 *
 * @package gaia
 * @subpackage db
 * @abstract
 */

abstract class gaiaDbPdoAbstract extends gaiaDbAbstract {
	protected $__dbLink;

	/**
	 * force drivers to implement PDO dsn string
	 */
	abstract protected function getDsn();

	protected function createQuery() {
		return new gaiaDbPdoQuery($this, $this->__dbLink);
	}
	protected function createStatement() {
		return new gaiaDbPdoStatement($this, $this->__dbLink);
	}

	public function open(array $config = null) {
		try {
			$this->__dbLink = new PDO($this->getDsn(), $this->_config['username'], $this->_config['password']);
		} catch (PDOException $e) {
			throw new gaiaDbException('PDO::DB Connection failed: '.$e->getMessage());
		}
        $that = $this;
		GAIA::registerShutdown(function() use ($that) {
            $that->close();
        });
		$this->_isOpen = true;
		return $this;
	}
	public function close() {
		if (isset($this->__dbLink)) unset($this->__dbLink);
		$this->_isOpen = false;
		return $this;
	}

	/**
	 * PDO supports prepared statements
	 *
	 * @param unknown_type $statement
	 * @return unknown
	 */
	public function prepare($statement) {
		if (!$this->_isOpen) $this->open();
		$query = $this->createStatement();
		$query->prepare($statement);
		return $query;
	}

	public function quote($statement) {
		if (!$this->_isOpen) $this->open();
		return $this->__dbLink->quote($statement);
	}
}

/**
 * result query for quick usage
 *
 */
class gaiaDbPdoQuery extends gaiaDbQueryAbstract {
	protected $__dbLink;
	protected $__queryResult = null;
	protected $_Db;

	public function __construct(gaiaDbPdoAbstract $db, $dbLink) {
		$this->_Db = $db;
		$this->__dbLink = $dbLink;
	}

	public function exec($statement) {
		$this->free();
		$this->_statement = $statement;
		if (($this->_rowCount = $this->__dbLink->exec($this->_statement)) === false)
			throw new gaiaDbException('pdo query failed'.$this->getErrorMessage(), 0, $this->ErrorMessage.'<br>'.$this->statement);
		return $this;
	}

	public function select($statement) {
		$this->free();
		$this->_statement = $statement;
		$this->_isFail = ($this->__queryResult = $this->__dbLink->query($this->_statement)) === false;
		if ($this->_isFail) throw new gaiaDbException('pdo select failed', 0, $this->ErrorMessage.'<br>'.$this->statement);
		$this->_currPos = 0;
		$this->_rowCount = $this->__queryResult->rowCount();
		// FIXME [proddi] pdo dont return a corrent rowCount
		return $this;
	}

	public function free() {
		if (isset($this->__queryResult)) unset($this->__queryResult);
		return $this;
	}

	/**
	 * fetching selected data
	 */
	public function fetch($fetchMode = null) {
//		if ($this->eof) return false;
		$this->_currPos++;
		switch (isset($fetchMode) ? $fetchMode : $this->_Db->fetchMode) {
//			case gaiaDb::fetchAssoc:	return mysqli_fetch_assoc($this->__queryResult); break;
//			case gaiaDb::fetchObj:		return mysqli_fetch_object($this->__queryResult); break;
			case gaiaDb::fetchArray:	if (!$result = $this->__queryResult->fetch(PDO::FETCH_ASSOC)) return false;
										return new gaiaUtilsArray($result); break;
			default:					return $this->__queryResult->fetch(PDO::FETCH_NUM); break;
		}
		throw new gaiaDbException('gaia pdo does not support fetch method');
	}

	/**
	 * status values
	 */
	protected function getLastInsertId() {
		return $this->__dbLink->lastInsertId();
	}
	protected function getErrorCode() {
		return $this->__dbLink->errorCode();
	}
	protected function getErrorMessage() {
		$error = $this->__dbLink->errorInfo();
		return $error[2];
	}

}

/**
 * query for prepared usage
 *
 */

class gaiaDbPdoStatement extends gaiaDbStatementAbstract {
	protected $_Db;
	protected $__dbLink;
	protected $__stmt = null;
	protected $__meta;
	protected $__keys;
	protected $__values;

	public function __construct(gaiaDbPdoAbstract $db, $dbLink) {
		$this->_Db = $db;
		$this->__dbLink = $dbLink;
	}

	public function prepare($statement) {
		$this->free();
		$this->_statement = $statement;
		$this->__stmt = $this->__dbLink->prepare($this->_statement);
		if (!$this->__stmt instanceof PDOStatement)
			throw new gaiaDbException('pdo prepare error: '.$statement.'/'.$this->ErrorMessage());
		return $this;
	}

	public function free() {
		if (isset($this->__stmt)) unset($this->__stmt);
		return $this;
	}

	/**
	 * execute a prepared statement
	 *
	 * @param array $params
	 * @return $this
	 */
	public function execute(array $params = null) {
		if (!isset($this->__stmt)) return $this;

		$this->_isFail = !$this->__stmt->execute($params);
		if ($this->_isFail) throw new gaiaDbException('pdo execute failed: '.$this->ErrorMessage.' ('.$this->_statement.')');
		return $this;
	}

	public function fetch($fetchMode = null) {
		$this->_currPos++;
		switch (isset($fetchMode) ? $fetchMode : $this->_Db->fetchMode) {
//			case gaiaDb::fetchAssoc:	return mysqli_fetch_assoc($this->__queryResult); break;
//			case gaiaDb::fetchObj:		return mysqli_fetch_object($this->__queryResult); break;
			case gaiaDb::fetchArray:	if (!$result = $this->__stmt->fetch(PDO::FETCH_ASSOC)) return false;
										return new gaiaUtilsArray($result); break;
			default:					return $this->__stmt->fetch(PDO::FETCH_NUM); break;
		}
		throw new gaiaDbException('gaia pdo does not support fetch method');
	}

	public function fetch2($fetchMode = null) {
		throw new gaiaDbException('n/i');
	}

	/**
	 * status values
	 */
	protected function getLastInsertId() {
		return $this->__dbLink->lastInsertId();
	}
	protected function getErrorCode() {
		return $this->__dbLink->errorCode();
	}
	protected function getErrorMessage() {
		$error = $this->__dbLink->errorInfo();
		return $error[2];
	}
}

?>