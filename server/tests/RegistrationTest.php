<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once '../vendor/autoload.php';
require_once '../src/Registration.php';

class RegistrationTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Registration
     */
    protected $objectCorrect;
    protected $objectFailed;
    protected $pdo;
    protected $pdoUsedLogin;

    private function preparePDOMock($RowNum = 1) {
        $data = array();
        $data["users"] = array(
            'password' => password_hash('secret', PASSWORD_DEFAULT),
            'login' => 'test'
        );
        $stmt = $this->getMock('PDOStatement', array('execute', 'rowCount', 'fetchAll', 'fetch'));
        $stmt->expects($this->any())
                ->method('execute')
                ->will($this->returnValue(true));
        $stmt->expects($this->any())
                ->method('rowCount')
                ->will($this->returnValue($RowNum));
        $stmt->expects($this->any())
                ->method('fetch')
                ->will($this->returnValue($data["users"]));
        return $stmt;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->pdo = $this->getMock('PDO', array('prepare'), array('sqlite:dbname=:memory'), 'PDOMock', true);
        $this->pdo->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($this->preparePDOMock(0)));
        $this->pdoUsedLogin = $this->getMock('PDO', array('prepare'), array('sqlite:dbname=:memory'), 'PDOMock', true);
        $this->pdoUsedLogin->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($this->preparePDOMock()));
        $this->objectCorrect = new Registration("test", "password", "password", true, $this->pdo);
        $this->objectFailed = new Registration("1`", "pass", "password", false, $this->pdoUsedLogin);
    }

    public function testGetHashPass() {
        $hash = $this->objectCorrect->getHashPass();
        $result = password_verify('password', $hash);
        $this->assertEquals(true, $result);
    }

    public function testGetLoginSyntaxStatus() {
        $this->assertEquals(false, $this->objectCorrect->getLoginSyntaxStatus());
        $this->assertEquals(true, $this->objectFailed->getLoginSyntaxStatus());
    }

    public function testGetLoginLengthStatus() {
        $this->assertEquals(false, $this->objectCorrect->getLoginLengthStatus());
        $this->assertEquals(true, $this->objectFailed->getLoginLengthStatus());
    }    
    
    public function testGetLoginAccessStatus() {
        $this->assertEquals(false, $this->objectCorrect->getLoginAccessStatus());
        $this->assertEquals(true, $this->objectFailed->getLoginAccessStatus());
    }

    public function testGetPassLengthStatus() {
        $this->assertEquals(false, $this->objectCorrect->getPassLengthStatus());
        $this->assertEquals(true, $this->objectFailed->getPassLengthStatus());
    }

    public function testGetPassEqualityStatus() {
        $this->assertEquals(false, $this->objectCorrect->getPassEqualityStatus());
        $this->assertEquals(true, $this->objectFailed->getPassEqualityStatus());
    }

    public function testGetStatuteStatus() {
        $this->assertEquals(false, $this->objectCorrect->getStatuteStatus());
        $this->assertEquals(true, $this->objectFailed->getStatuteStatus());
    }

    public function testGetServerStatus() {
        $this->assertEquals(false, $this->objectCorrect->getServerStatus());
    }

    public function testGetRegSuccess() {
        $this->assertEquals(false, $this->objectFailed->getRegSuccess());
        $this->assertEquals(true, $this->objectCorrect->getRegSuccess());        
    }

}
