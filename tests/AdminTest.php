<?php

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\TestCase;
use Shop\Admin;

class AdminTest extends TestCase
{
    //put your code here
    protected function getConnection()
    {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        return new DefaultConnection($pdo, DB_NAME);
    }

    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('tests/Shop.xml');
    }
    
    public function testCreateAdmin()
    {
        $admin = new Admin();
        $this->assertInstanceOf(Admin::class, $admin, 'Admin should be instance of class Admin.');
    }
    
    public function testSettersAndGetters(
            $name = 'Admin Joe', 
            $email = 'admin@admin.pl',
            $pass = 'qwerty')
    {
        $admin = new Admin();
        
        $admin->setName($name);
        $this->assertSame($name, $admin->getName(), 'Name should be identical to ' . $name);
        
        $admin->setEmail($email);
        $this->assertSame($email, $admin->getEmail(), 'Email should be identical to ' . $email);
        
        $admin->setPass($pass);
        $this->assertTrue(password_verify($pass, $admin->getHashPass()), 'HashPass should match admin pass.');
    }
    
    public function testSavingToDb()
    {
        $admin = new Admin();
        
        $admin->saveToDB();
        
        $id = 1;
        $admin = Admin::loadAdminById($id);
        // Must be assertEquals because saveToDB set ID as string, not integer
        $this->assertEquals($id, $admin->getId(), 'ID should be equal to ' . $id);
    }

}
