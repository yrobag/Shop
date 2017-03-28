<?php

namespace Shop;

class Message extends DbModel
{

    /**
     * Stała określająca ID obiektu nieistniejącego w bazie.
     */
    const NON_EXISTING_ID = -1;

    /**
     *
     * @var int ID wiadomości
     */
    protected $id;

    /**
     *
     * @var int ID użytkownika, do którego wysyłana jest wiadomość
     */
    protected $userId;

    /**
     *
     * @var int ID admina, który wysyła wiadomość
     */
    protected $adminId;

    /**
     *
     * @var int ID zamówienia, którego dotyczy wiadomość
     */
    protected $orderId;

    /**
     *
     * @var string Treść wiadomości
     */
    protected $text;

    ////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->id = self::NON_EXISTING_ID;
        $this->userId = self::NON_EXISTING_ID;
        $this->adminId = self::NON_EXISTING_ID;
        $this->orderId = self::NON_EXISTING_ID;
        $this->text = '';
    }

    ////////////////////////////////////////////////////////////////////

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getAdminId()
    {
        return $this->adminId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getText()
    {
        return $this->text;
    }

    ////////////////////////////////////////////////////////////////////

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
        return $this;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////

    public function send()
    {
        // save to DB
        // send email
        // return true|false
    }

}
