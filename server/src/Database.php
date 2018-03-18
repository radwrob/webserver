<?php

/*
 * Author: Philip Brown
 * Edit: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * More info: http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/
 */

class Database {

    private $stmt;
    private $dbHandler;
    private $error;

    public function __construct($host, $dbUser, $dbPass, $dbName, $dbHandler = null) {
        // Set DSN
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
        // Set options
        $options = array(
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        if ($dbHandler === null) {
            // Create a new PDO instanace
            try {
                $this->dbHandler = new PDO($dsn, $dbUser, $dbPass, $options);
            }
            // Catch any errors
            catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        } else {
            $this->dbHandler = $dbHandler;
        }
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        try {
            $this->stmt->bindValue($param, $value, $type);
        }
        // Catch any errors
        catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function query($query) {
        try {
            $this->stmt = $this->dbHandler->prepare($query);
        }
        // Catch any errors
        catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultset() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function lastInsertId() {
        return $this->dbHandler->lastInsertId();
    }

    public function beginTransaction() {
        return $this->dbHandler->beginTransaction();
    }

    public function endTransaction() {
        return $this->dbHandler->commit();
    }

    public function cancelTransaction() {
        return $this->dbHandler->rollBack();
    }

    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }

    public function getError() {
        return $this->error;
    }

}
