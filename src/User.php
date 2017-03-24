<?php

class User extends DbModel 
{
    private $id;
    private $email;
    private $name;
    private $surname;
    private $pass;
    private $addres;
    
    private function __construct($newId, $newName, $newSurname, $newEmail, $newAddress, $newPassword){
        $this->id = $newId;
        $this->name = $newName;
        $this->surname = $newSurname;
        $this->email = $newEmail;
        $this->addres= $newAddress;
        $this->password = $newPassword;
    }
    
    
}
