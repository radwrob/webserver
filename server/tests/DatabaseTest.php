<?php

/*
 * Author: Radoslaw Wrobel
 * radoslaw.wrobel@gmx.com
 * License: MIT
 */

require_once '../vendor/autoload.php';
require_once '../src/connect.inc.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {

    protected $data = array();
    protected $pdo;

    public function setUp() {
        parent::setUp();
        $this->data["users"] = array(
            'password' => 'secret',
            'login' => 'test'
        );
        $stmt = $this->getMock('PDOStatement', array('execute', 'fetchAll'));
        $stmt->expects($this->any())
                ->method('execute')
                ->will($this->returnValue(true));
        $stmt->expects($this->any())
                ->method('fetchAll')
                ->will($this->returnValue($this->data["users"]));
        $this->pdo = $this->getMock('PDO', array('prepare'), array('sqlite:dbname=:memory'), 'PDOMock', true);
        $this->pdo->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($stmt));
    }

    public function testResultset() {
        $object = new Database(HOST, DB_USER, DB_PASS, DB_NAME, $this->pdo);
        $object->query('SELECT * FROM users');
        $result = $object->resultset();
        $this->assertEquals('secrettest', $result['password'] . $result['login']);
    }

    public function testGetError() {
        $object = new Database(HOST, DB_USER, DB_PASS, DB_NAME, $this->pdo);
        $this->assertEquals(null, $object->getError());
    }

}
