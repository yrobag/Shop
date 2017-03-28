<?php

namespace Shop;

/**
 * Description of newPHPClass
 */
class Admin extends DbModel
{

    /**
     * Stała określająca ID obiektu nieistniejącego w bazie.
     */
    const NON_EXISTING_ID = -1;

    /**
     *
     * @var int ID administratora
     */
    protected $id;

    /**
     *
     * @var string Nazwa administratora
     */
    protected $name;

    /**
     *
     * @var string Adres email administratora
     */
    protected $email;

    /**
     *
     * @var string Zahaszowane hasło administratora
     */
    protected $hashPass;
    
    ////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->id = self::NON_EXISTING_ID;
        $this->username = '';
        $this->email = '';
        $this->hashPass = '';
    }

    ////////////////////////////////////////////////////////////////////

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getHashPass()
    {
        return $this->hashPass;
    }

    ////////////////////////////////////////////////////////////////////

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Setter hasła. Przyjmuje hasło w postaci stringa, hashuje je i przypisuje
     * do atrybutu $hashPass.
     * @param string $pass Hasło administratora
     * @return $this
     */
    public function setPass($pass)
    {
        $this->hashPass = password_hash($pass, PASSWORD_BCRYPT);
        return $this;
    }

    ////////////////////////////////////////////////////////////////////

    public static function LoginAdmin($email, $password)
    {
        
    }

    /**
     * Zapisuje nowego administratora do bazy lub aktualizuje istniejącego.
     * @return boolean
     */
    public function saveToDB()
    {
        $conn = Admin::getConnection();
        
        if ($this->id == self::NON_EXISTING_ID) {
            //Saving new admin to DB
            $stmt = $conn->prepare(
                    'INSERT INTO `Admin`(name, email, hashPass) VALUES (:name, :email, :hashPass)'
            );

            $result = $stmt->execute(
                    [
                        'name' => $this->name, 
                        'email' => $this->email, 
                        'hashPass' => $this->hashPass
                    ]
            );

            if ($result === true) {
                $this->id = $conn->lastInsertId();

                return true;
            }
        } else {
            //Updating admin in DB
            $stmt = $conn->prepare(
                    'UPDATE `Admin` SET name=:name, email=:email, hashPass=:hashPass WHERE id=:id'
            );

            $result = $stmt->execute(
                    [
                        'name' => $this->name, 
                        'email' => $this->email,
                        'hashPass' => $this->hashPass, 
                        'id' => $this->id
                    ]
            );

            if ($result === true) {

                return true;
            }
        }

        return false;
    }
    
    /**
     * Wczytuje z bazy danych administratora o podanym ID i zwraca obiekt Administrator
     * lub null, jeżeli podanego ID nie ma w bazie.
     * @param int $id ID administratora
     * @return \Admin|null
     */
    static public function loadAdminById($id)
    {
        $conn = Admin::getConnection();
        
        $stmt = $conn->prepare('SELECT * FROM `Admin` WHERE id=:id');
        $result = $stmt->execute(['id' => $id]);

        if ($result === true && $stmt->rowCount() > 0) {

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $loadedAdmin = new Admin();
            $loadedAdmin->id = $row['id'];
            $loadedAdmin->name = $row['name'];
            $loadedAdmin->email = $row['email'];
            $loadedAdmin->hashPass = $row['hashPass'];

            return $loadedAdmin;
        }

        return null;
    }

    public function DeleteAdmin()
    {
        
    }
    
    public function sendMessageToUser($userId, $orderId, $text)
    {
        $message = new Message();
        
        $message->setUserId($userId);
        $message->setAdminId($this->getId());
        $message->setOrderId($orderId);
        $message->setText($text);
        
        $message->send();
        
        // return true|false
    }

}
