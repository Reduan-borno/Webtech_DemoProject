<?php
/**
 * Database Class
 * Handles database connection using PDO
 * Implements Singleton pattern
 */

class Database {
    private static $instance = null;
    private $connection;
    private $statement;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST .  ';dbname=' . DB_NAME .  ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO:: ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prepare a query
     */
    public function query($sql) {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * Bind values to prepared statement
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO:: PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO:: PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Execute the prepared statement
     */
    public function execute() {
        return $this->statement->execute();
    }

    /**
     * Get all results as array
     */
    public function resultSet() {
        $this->execute();
        return $this->statement->fetchAll();
    }

    /**
     * Get single result
     */
    public function single() {
        $this->execute();
        return $this->statement->fetch();
    }

    /**
     * Get row count
     */
    public function rowCount() {
        return $this->statement->rowCount();
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}
}