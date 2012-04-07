<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sqlite
 *
 * @author tosa
 */
class scratchPdoSqlite {

    protected $_config;

    protected $_dbh;

    public function __construct(array $config = array()) {
        $this->_config = array_merge(array(
            'username' => 'root',
            'password' => '',
            'exceptions' => true
        ), $config);
    }

    public function query($query, $values = array()) {
        if (!$this->_dbh) $this->open();
        if (func_num_args() >= 2 && !is_array($values)) $values = array_slice (func_get_args (), 1);
        return new scratchPdoStatement($this->_dbh, $query, $values);
    }

    public function open() {
        if ($this->_dbh) return;
//		try {
			$this->_dbh = new PDO($this->getDsn(), $this->_config['username'], $this->_config['password']);
            if ($this->_config['exceptions']) $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//		} catch (PDOException $e) {
//			throw new Exception('PDO::DB Connection failed: '.$e->getMessage());
//		}
    }

	protected function getDsn() {
		return 'sqlite:'.$this->_config['dbname'];
	}

}

?>