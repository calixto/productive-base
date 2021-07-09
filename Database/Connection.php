<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Productive\Database;

/**
 * Management of connection with database
 *
 * @author calixto
 */
class Connection {

    /**
     * Opened connections
     * @var array
     */
    private static $connection = [];
    /**
     * Point of connection this instance
     * @var type 
     */
    private $pointConnection;

    /**
     * Trunk
     */
    private final function __construct() {
        
    }

    /**
     * Create a connection with database
     * @param string $type 
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $username
     * @param string $passwd
     * @param string $options
     * @return Connection
     */
    public static final function create($type = null, $host = null, $port = null, $database = null, $username = null, $passwd = null, $options = null) {
        $type = $type === null ? $_SERVER['DB_TYPE'] : $type;
        $host = $host === null ? $_SERVER['DB_HOST'] : $host;
        $port = $port === null ? $_SERVER['DB_PORT'] : $port;
        $database = $database === null ? $_SERVER['DB_DATABASE'] : $database;
        $username = $username === null ? $_SERVER['DB_USER'] : $username;
        $passwd = $passwd === null ? $_SERVER['DB_PASSWORD'] : $passwd;

        $hash = md5($type . $host . $port . $database . $username . $passwd);
        if (!isset(self::$connection[$hash])) {
            $dsn = sprintf("%s:host=%s;port=%s;dbname=%s;charset=utf8", $type, $host, $port, $database);
            self::$connection[$hash] = new \PDO($dsn, $username, $passwd, $options);
            self::$connection[$hash]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection[$hash]->pointConnection = $hash;
        }
        return self::$connection[$hash];
    }
    
    private function connection(){
        return self::$connection[$this->pointConnection];
    }

    /**
     * Inicia uma transação
     * @return boolean
     */
    public function beginTransaction() {
        return $this->connection()->beginTransaction();
    }

    /**
     * Envia uma transação
     * @return boolean
     */
    public function commit() {
        return $this->connection()->commit();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle
     * @return string
     */
    public function errorCode() {
        return $this->connection()->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle
     * @return array
     */
    public function errorInfo() {
        return $this->connection()->errorInfo();
    }

    /**
     * Executa uma instrução SQL e retorna o número de linhas afetadas
     * @param string $statement
     * @return integer
     */
    public function exec($statement) {
        return $this->connection()->exec($statement);
    }

    /**
     * Recuperar um atributo da conexão com o banco de dados
     * @param integer $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
        return $this->connection()->getAttribute($attribute);
    }

    /**
     * Retorna um array com os drivers PDO disponíveis
     * @return array
     */
    public static function getAvailableDrivers() {
        return $this->connection()->getAvailableDrivers();
    }

    /**
     * Checks if inside a transaction
     * @return boolean
     */
    public function inTransaction() {
        return $this->connection()->inTransaction();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     * @param string $name
     * @return string
     */
    public function lastInsertId($name = NULL) {
        return $this->connection()->lastInsertId($name);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     * @param string $statement
     * @param array $driver_options
     * @return \PDOStatement
     */
    public function prepare($statement, $driver_options = array()) {
        return $this->connection()->prepare($statement, $driver_options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     * @param string $statement
     * @return \PDOStatement
     */
    public function query($statement) {
        return $this->connection()->query($statement);
    }

    /**
     * Quotes a string for use in a query
     * @param string $string
     * @param integer $parameter_type
     * @return string
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR) {
        return $this->connection()->quote($string, $parameter_type);
    }

    /**
     * Rolls back a transaction
     * @return boolean
     */
    public function rollBack() {
        return $this->connection()->rollBack();
    }

    /**
     * Set an attribute
     * @param integer $attribute
     * @param mixed $value
     * @return boolean
     */
    public function setAttribute($attribute, $value) {
        return $this->connection()->setAttribute($attribute, $value);
    }

}
