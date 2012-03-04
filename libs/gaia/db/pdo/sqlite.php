<?php
/**
 * Sqlite pdo driver
 * (support sqlite version 3.x)
 *
 * @package gaia
 * @subpackage db
 */

class gaiaDbPdoSqlite extends gaiaDbPdoAbstract {

	protected function getDsn() {
		return 'sqlite:'.$this->_config['dbname'];
	}

}

?>