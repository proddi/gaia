<?php

class scratchPdoStatement {

    protected $_dbh;
    protected $_sth;
    protected $_executed;
    protected $_values;

    public function __construct($dbh, $statement, array $values) {
        $this->_dbh = $dbh;
        $this->_statement = $statement;
        $this->_values = $values;
        $this->_sth = $this->_dbh->prepare($statement);
    }

    public function execute(array $values = array()) {
        if ($this->_executed) throw new Exception(__CLASS__ . ' already execute a statement');
        $this->_executed = $this->_sth->execute($values);
        return $this;
    }

    public function close() {
        if ($this->_executed) {
            $this->_sth->closeCursor();
            $this->_executed = false;
        }
    }

    public function fetch($style = PDO::FETCH_BOTH) {
        if (!$this->_executed) $this->execute($this->_values);
        return $this->_sth->fetch($style);
    }

    public function values() {
        return $this->fetch(PDO::FETCH_NUM);
    }

    public function map() {
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    public function into($obj) {
        if (!$this->_executed) $this->execute($this->_values);
        $this->_sth->setFetchMode(PDO::FETCH_INTO, $obj);
        return $this->_sth->fetch();
    }

    public function rows() {
        if (!$this->_executed) $this->execute($this->_values);
        return $this->_sth->rowCount();
    }

}

?>