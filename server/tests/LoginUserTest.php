<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once '../vendor/autoload.php';
require_once '../src/LoginUser.php';

class LoginUserTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LoginUser
     */
    protected $object;
    protected $pdo;
    protected $pdoBadLogin;

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
    public function setUp() {
        parent::setUp();
        $this->pdo = $this->getMock('PDO', array('prepare'), array('sqlite:dbname=:memory'), 'PDOMock', true);
        $this->pdo->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($this->preparePDOMock()));
        $this->pdoBadLogin = $this->getMock('PDO', array('prepare'), array('sqlite:dbname=:memory'), 'PDOMock', true);
        $this->pdoBadLogin->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($this->preparePDOMock(0)));
    }

    public function testCheckData() {
        $this->object = new LoginUser('test', 'secret', $this->pdo);
        $this->assertEquals(true, $this->object->checkData());
        $this->object = new LoginUser('test', 'bad_pass', $this->pdo);
        $this->assertEquals(false, $this->object->checkData());
        $this->object = new LoginUser('bad_login', 'secret', $this->pdoBadLogin);
        $this->assertEquals(false, $this->object->checkData());
    }

    public function testGetLoginStatus() {
        $this->object = new LoginUser('test', 'secret', $this->pdo);
        $this->object->checkData();
        $this->assertEquals(false, $this->object->getLoginStatus());
        $this->object = new LoginUser('bad_login', 'secret', $this->pdoBadLogin);
        $this->object->checkData();
        $this->assertEquals(true, $this->object->getLoginStatus());
    }

    public function testGetPassStatus() {
        $this->object = new LoginUser('test', 'secret', $this->pdo);
        $this->object->checkData();
        $this->assertEquals(false, $this->object->getPassStatus());
        $this->object = new LoginUser('123456789', 'bad_pass', $this->pdo);
        $this->object->checkData();
        $this->assertEquals(true, $this->object->getPassStatus());
    }

}
