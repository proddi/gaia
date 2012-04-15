<?php

/**
 * Middleware for DB support.
 *
 * This middleware creates an accessor to PDO and beautifys the PDOStatement a little bit.
 */
class gaiaAppMiddlewarePdo extends gaiaAppMiddleware {

    protected $_pdo;

    public function __construct($app) {
        $dsn = $app->config('pdo.dsn');
    	$this->_pdo = new PDO($dsn, $app->config('pdo.username'), $app->config('pdo.password'));
        /* if ($this->_config['exceptions'])*/ $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $app->register('query', array($this, 'query'));
        $app->register('prepare', array($this, 'prepare'));
        $app->register('lastInsertId', array($this->_pdo, 'lastInsertId'));
    }

    public function query() {
        return new __pdostatementwrapper__(call_user_func_array(array($this->_pdo, 'query'), func_get_args()));
    }
    public function prepare() {
        return new __pdostatementwrapper__(call_user_func_array(array($this->_pdo, 'prepare'), func_get_args()));
    }

}

/**
 * PDOStatement wrapper to prettify usage
 *
 * it modifies:
 * PDOStatement::execute:
 *   - execute(array) returns PDOStatement instead of (bool)success
 *   - execute() accept array and multiple arguments as well
 *
 * data fetchers:
 *   - obj()       = ->fetch(PDO::FETCH_OBJ)
 *   - map()       = ->fetch(PDO::FETCH_ASSOC)
 *   - values()    = ->fetch(PDO::FETCH_NUM)
 *   - allObj()    = ->fetchAll(PDO::FETCH_OBJ)
 *   - allMap()    = ->fetchAll(PDO::FETCH_ASSOC)
 *   - allValues() = ->fetchAll(PDO::FETCH_NUM)
 */
class __pdostatementwrapper__ {

    /**
     * @var PDOStatement
     */
    protected $_stmt;

    public function __construct($stmt) {
        $this->_stmt = $stmt;
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->_stmt, $name), $arguments);
    }

    public function execute($input_parameters = NULL) {
        if (!is_array($input_parameters)) {
            $input_parameters = func_get_args();
        }
        $this->_stmt->execute($input_parameters);
        return $this;
    }

    public function obj() {
        return $this->_stmt->fetch(PDO::FETCH_OBJ);
    }

    public function map() {
        return $this->_stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function values() {
        return $this->_stmt->fetch(PDO::FETCH_NUM);
    }

    public function allObj() {
        return $this->_stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function allMap() {
        return $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allValues() {
        return $this->_stmt->fetchAll(PDO::FETCH_NUM);
    }

}