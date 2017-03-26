<?php

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\TestCase;
use Shop\User;

class UserTest extends TestCase
{

    protected function getConnection()
    {
        $connection = new PDO(DB_DSN, DB_USER, DB_PASS);
        return new DefaultConnection($connection, DB_NAME);
    }

    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('tests/Shop.xml');
    }

    public function testConstructandGetters()
    {
        $newUser = new User(4, 'newName', 'newSurname', 'newEmail', 'newAddress', 'newPassword');
        $this->assertInstanceOf(User::class, $newUser, 'Creation a new user');
        $this->assertSame($newUser->getId(), 4, 'Correct id');
        $this->assertSame($newUser->getName(), 'newName', 'Correct name');
        $this->assertSame($newUser->getSurname(), 'newSurname', 'Correct surname');
        $this->assertSame($newUser->getAddres(), 'newAddress', 'Correct Address');
        $this->assertSame($newUser->getEmail(), 'newEmail', 'Correct Email');
    }

    public function testSetters()
    {
        $newUser = new User(0, '', '', '', '', '');
        $newUser->setAddres('newAddress');
        $newUser->setSurname('newSurname');
        $newUser->setName('newName');
        $this->assertSame($newUser->getName(), 'newName', 'Correct name');
        $this->assertSame($newUser->getSurname(), 'newSurname', 'Correct surname');
        $this->assertSame($newUser->getAddres(), 'newAddress', 'Correct Address');
        $newUser->setEmail('tom@black.com'); //This Email is already in DB
        $this->assertSame('', $newUser->getEmail(), 'Failed to check email');
        $newUser->setEmail('tom@black2.com'); //This Email is already in DB
        $this->assertSame('tom@black2.com', $newUser->getEmail(), 'Success to check email');
    }

    public function testCreateUser()
    {
        //create user with existing e-mail
        $newUser = User::CreateUser('tom@black.com', 'abc', 'random_name', 'random_surname', 'address');
        $this->assertFalse($newUser, 'User with this e-mail already exist in DB');
        $newUser = User::CreateUser('tom2@black.com', 'abc', 'random_name', 'random_surname', 'address');
        $this->assertInstanceOf(User::class, $newUser, 'Succesfull creation of new user');
    }

    public function testAuthenticateUser()
    {
        $nonExistingUser = User::AuthenticateUser('wrong', 'abc');
        $this->assertNull($nonExistingUser, 'There is no user with this email i DB');
        $userWithWrongPass = User::AuthenticateUser('tom@black.com', 'wrong');
        $this->assertFalse($userWithWrongPass, 'Login with wrong password');
        $correctLogin = User::AuthenticateUser('tom@black.com', 'abc');
        $this->assertInstanceOf(User::class, $correctLogin, 'Correct Login operation');
    }

    public function testDeleteUser()
    {
        $user = User::AuthenticateUser('tom@black.com', 'abc');
        $result = User::DeleteUser($user);
        $this->assertTrue($result, 'There is no such user to delete');
        $checkIfDelete = User::AuthenticateUser('tom@black.com', 'abc');
        $this->assertNull($checkIfDelete);
    }

    public function testSaveToDB()
    {
        $newUser = User::AuthenticateUser('tom@black.com', 'abc');
        $newUser->setName('newName');
        $newUser->saveToDB();
        $changedUser = User::AuthenticateUser('tom@black.com', 'abc');
        $this->assertSame($newUser->getName(), $changedUser->getName(), 'Success to change name and save it in DB');
    }

    public function testSettingNewPass()
    {
        $user = User::AuthenticateUser('tom@black.com', 'abc');
        $user->setNewPass('123', 'wrong');
        $user->saveToDB();
        $user2 = User::AuthenticateUser('tom@black.com', 'abc');
        $this->assertInstanceOf(User::class, $user2, 'Fail to change pass');
        $user->setNewPass('123', 'abc');
        $user->saveToDB();
        $user3 = User::AuthenticateUser('tom@black.com', 'abc');
        $this->assertFalse($user3, 'Fail to login with old pass after setting new');
        $user4 = User::AuthenticateUser('tom@black.com', '123');
        $this->assertInstanceOf(User::class, $user4, 'Success to login with new pass');
    }


}
