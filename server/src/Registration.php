<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once 'connect.inc.php';

class Registration {

    private $login;
    protected $pass1;
    protected $pass2;
    private $statute;
    private $phpUnitTest;
    protected $database;
    protected $loginSyntaxError;
    protected $loginLengthError;    
    protected $loginAccessError;
    protected $passLengthError;
    protected $passEqError;
    private $statuteError;
    protected $regSuccess;
    protected $serverError = false;

    public function __construct($login, $pass1, $pass2, $statute = false, $dbHandler = null) {
        $this->login = $login;
        $this->pass1 = $pass1;
        $this->pass2 = $pass2;
        $this->statute = $statute;
        $this->phpUnitTest = ($dbHandler === null) ? FALSE : TRUE;
        $this->database = new Database(HOST, DB_USER, DB_PASS, DB_NAME, $dbHandler);
        if ($this->database->getError() === null) {
            $this->checkRegisterData();
            $this->checkAddUser();
        } else {
            $this->serverError = true;
        }
    }

    protected function checkPasswordLength() {
        if ((mb_strlen($this->pass1, 'UTF-8') < 8)) {
            return false; //Password can not be shorter than 8 characters
        } else {
            return true;
        }
    }

    protected function checkEqualityOfPasswords() {
        if ($this->pass1 != $this->pass2) {
            return false; //Passwords are not identical
        } else {
            return true;
        }
    }

    protected function checkSyntaxOfUserLogin() {
        //Nick can only consist of letters and numbers
        return ctype_alnum($this->login);
    }
    
    protected function checkLengthOfUserLogin() {
        //Nick must have between 3 and 20 characters
        return (mb_strlen($this->login, 'UTF-8') < 3) || (mb_strlen($this->login, 'UTF-8') > 20) ? false : true;
    }    

    protected function getLoginNum() {
        $this->database->query('SELECT login FROM users WHERE login=:login');
        $this->database->bind(':login', $this->login);
        $this->database->single();
        return $this->database->rowCount();
    }

    protected function isLoginAvailable() {
        if ($this->getLoginNum() > 0) {
            return false; //There is already a user with this nick. Choose another.
        } else {
            return true;
        }
    }

    private function getStatute() {
        return $this->statute;
    }

    protected function checkRegisterData() {
        $this->passLengthError = !$this->checkPasswordLength();
        $this->passEqError = !$this->checkEqualityOfPasswords();
        $this->loginSyntaxError = !$this->checkSyntaxOfUserLogin();
        $this->loginLengthError = !$this->checkLengthOfUserLogin();
        $this->loginAccessError = !$this->isLoginAvailable();
        $this->statuteError = !$this->getStatute();
    }

    protected function addNewUserToBase() {
        $this->database->query('INSERT INTO users VALUES (:id,:login,:pass)');
        $this->database->bind(':id', null);
        $this->database->bind(':login', $this->login);
        $this->database->bind(':pass', $this->getHashPass());
        $this->database->execute();
        if ($this->database->rowCount()) {
            return true;
        }
        return false;
    }

    protected function checkAddUser() {
        if ($this->passLengthError ||
                $this->passEqError ||
                $this->loginSyntaxError ||
                $this->loginLengthError ||
                $this->loginAccessError ||
                $this->statuteError) {
            $this->regSuccess = false;
        } else {
            if ($this->phpUnitTest == TRUE) {
                $this->regSuccess = true;
            } else {
                $this->regSuccess = $this->addNewUserToBase();
            }
        }
    }

    public function getHashPass() {
        return password_hash($this->pass1, PASSWORD_DEFAULT);
    }

    public function getLoginSyntaxStatus() {
        return $this->loginSyntaxError;
    }
    
    public function getLoginLengthStatus() {
        return $this->loginLengthError;
    }     

    public function getLoginAccessStatus() {
        return $this->loginAccessError;
    }

    public function getPassLengthStatus() {
        return $this->passLengthError;
    }

    public function getPassEqualityStatus() {
        return $this->passEqError;
    }

    public function getStatuteStatus() {
        return $this->statuteError;
    }

    public function getServerStatus() {
        return $this->serverError;
    }

    public function getRegSuccess() {
        return $this->regSuccess;
    }
    
    public function getLogin() {
        return $this->login;
    }    

}
