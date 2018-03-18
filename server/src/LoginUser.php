<?php

/*
* Author: Radoslaw Wrobel
* radoslaw.wrobel@gmx.com
* License: MIT
*/

require_once 'connect.inc.php';

class LoginUser {

    protected $login;
    protected $pass;
    protected $database;
    protected $serverError;
    protected $loginError;
    protected $passError;
    protected $hash;

    public function __construct($login, $pass, $dbHandler = null) {
        $this->login = $login;
        $this->pass = $pass;
        $this->database = new Database(HOST, DB_USER, DB_PASS, DB_NAME, $dbHandler);
        $this->serverError = $this->database->getError() === null ? false : true;
        $this->getBaseData();
        $this->checkLogin();
        $this->checkPass();        
    }

    protected function getBaseData() {
        if (!$this->serverError) {
            $this->database->query('SELECT password FROM users WHERE login=:login');
            $this->database->bind(':login', $this->login);
            $result = $this->database->single();
            $this->hash = $result['password'];
        }
    }

    protected function checkPass() {
        return $this->passError = !(password_verify($this->pass, $this->hash));
    }

    protected function checkLogin() {
        $this->loginError = true;
        if (!$this->serverError) {
            if ($this->database->rowCount() > 0) {
                $this->loginError = false;
            }
        }
        return !$this->loginError;
    }

    public function checkData() {
        if (!$this->serverError && !$this->loginError && !$this->passError) {
            return true;
        }
        return false;
    }
    
    public function getLoginStatus() {
        return $this->loginError;
    }

    public function getPassStatus() {
        return $this->passError;
    }   
    
    public function getServerStatus() {
        return $this->serverError;
    }        
    
}
