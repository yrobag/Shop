<?php

namespace Shop;

class User extends DbModel
{

    private $id;
    private $email;
    private $name;
    private $surname;
    private $pass;
    private $addres;
    
    ////////////////////////////////////////////////////////////////////////////
    public function __construct($newId=null, $newName=null, $newSurname=null, $newEmail=null, $newAddress=null, $newPassword=null)
    {
        $this->id = $newId;
        $this->name = $newName;
        $this->surname = $newSurname;
        $this->email = $newEmail;
        $this->addres = $newAddress;
        $this->pass = $newPassword;
    }
    ////////////////////////////////////////////////////////////////////////////
    public static function CreateUser($email, $password, $name, $surname, $address)
    {
        $sqlStatement = "Select * from User where email = :email";
        $conn = User::getConnection();
        $stmt = $conn->prepare($sqlStatement);
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() == 0) {
            //inserting user to db
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sqlStatement = "INSERT INTO User(name, surname, email, address, pass) values (:name, :surname, :email, :address, :pass)";
            $stmt = $conn->prepare($sqlStatement);
            $result = $stmt->execute([
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'address' => $address,
                'pass' => $hashed_password
            ]);


            if ($result === TRUE) {
                //entery was added to DB so we can return new object
                return new User($conn->lastInsertId(), $name, $surname, $email, $address, $hashed_password);
            }
            //Something went wrong with insert new User to database
        }
        //there is user with this name in db
        return false;
    }

    public static function AuthenticateUser($email, $password)
    {
        $sqlStatement = "Select * from User where email = :email";
        $stmt = User::getConnection()->prepare($sqlStatement);
        $stmt->execute([
            'email' => $email
        ]);
        if ($stmt->rowCount() == 1) {
            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
            $user = new User($userData['id'], $userData['name'], $userData['surname'], $userData['email'], $userData['address'], $userData['pass']);

            if ($user->authenticate($password)) {
                //User is authenticated - we can return him
                return $user;
            }
            return false;
        }
        //there is no user with this name in db or User was not authenticated
        return null;
    }

    public static function DeleteUser(User $user)
    {

        $sql = "DELETE FROM User WHERE id= :id";
        $stmt = User::getConnection()->prepare($sql);
        $result = $stmt->execute([
            'id' => $user->getId()
        ]);
        if ($result) {
            return true;
        }
    }
    ////////////////////////////////////////////////////////////////////////////
    public function authenticate($password)
    {
        $hashed_pass = $this->pass;
        if (password_verify($password, $hashed_pass)) {
            //User is verified
            return true;
        }
        return false;
    }

    public function saveToDB()
    {
        $sql = "UPDATE User SET email = :email, name= :name, surname= :surname, address= :address, pass= :pass WHERE id= :id";
        $stmt = User::getConnection()->prepare($sql);
        $result = $stmt->execute([
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'address' => $this->addres,
            'pass' => $this->pass,
            'email' => $this->email
        ]);
        if ($result) {
            return true;
        }
    }
   
    ////////////////////////////////////////////////////////////////////////////
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getAddres()
    {
        return $this->addres;
    }
    
    /**
     * Metoda zwraca wszystkie wiadomości wysłane do użytkownika
     * @return array
     */
    public function getAllMessagesSentToUser()
    {
        $messages = [];
        
        return $messages;
    }
    
    ///////////////////////////////////////////////////////////////////////////
    public function setEmail($email)
    {
        //Must check if new email is not id DB
        $sqlStatement = "Select * from User where email = :email";
        $conn = User::getConnection();
        $stmt = $conn->prepare($sqlStatement);
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() == 0) {
            $this->email = $email;
        }
    }
    
     public function setNewPass($newPass, $oldPass)
    {
        if ($this->authenticate($oldPass)) {
            $this->pass = password_hash($newPass, PASSWORD_BCRYPT);
            return true;
        }
        return false;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function setAddres($addres)
    {
        $this->addres = $addres;
    }
    ////////////////////////////////////////////////////////////////////////////


}
