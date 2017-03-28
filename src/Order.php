<?php

namespace Shop;

class Order extends DbModel
{
    /*
     * Stałe opisujące status zamówienia
     */

    const WAITING_STATUS = 'Waiting';
    const ESTABLISHED_STATUS = 'Established';
    const REALIZED_STATUS = 'Realized';
    const PAID_STATUS = 'Opłacone';

    /*
     * Stała określająca ID obiektu nieistniejącego w bazie.
     */
    const NON_EXISTING_ID = -1;

    /**
     *
     * @var int ID przedmiotu
     */
    private $id;

    /**
     *
     * @var int Id użytkownika, który złożył zamówienie
     */
    private $userId;

    /**
     *
     * @var string oreśla status zamówienia
     */
    private $status;

    /**
     *
     * @var array tablica dwuwymiarowa zawierająca id i ilość każdego produktu 
     */
    private $products;

    /**
     *
     * @var float wartośc zamówienia 
     */
    private $totalPrice;

    ////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->id = NON_EXISTING_ID;
        $this->products = [];
        $this->userId = NON_EXISTING_ID;
        $this->totalPrice = (float) 0.00;
        $this->status = WAITING_STATUS;
    }

    ////////////////////////////////////////////////////////////////////////////
    public function addProduct(Product $product, $quantity)
    {
        if (count($this->products) > 0) {
            for ($i = 0; $i < count($this->products); $i++) {
                if ($this->products[$i]['productId'] === $product->id) {
                    $this->products[$i]['price']=$product->price;
                    $this->products[$i]['quantity']+=$quantity;
                    return true;
                }
            }
        }
        $this->products[]=['productId'=>$product->id, 'price' => $product->price, 'quantity'=>$quantity];
        return true;
    }
    
    public function removeProduct(Product $product)
    {
        if (count($this->products) > 0) {
            for ($i = 0; $i < count($this->products); $i++) {
                if ($this->products[$i]['productId'] === $product->id) {
                    unset($this->products[$i]);
                    $this->products= array_values($this->products);
                    return true;
                }
            }
        }
        return false;
    }
    
//    public function loadProducts
//    public function saveToDB
//    static public function calcTotalPrice
    

    ////////////////////////////////////////////////////////////////////////////
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    
    /**
     * Metoda zwraca wszystkie wiadomości wysłane w związku z tym zamówieniem
     * @return array
     */
    public function getAllMessagesSentInThisOrder()
    {
        $messages = [];
        
        return $messages;
    }

    ////////////////////////////////////////////////////////////////////////////


    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setStatus($status)
    {
        $possibleStatus = [WAITING_STATUS, REALIZED_STATUS, PAID_STATUS, ESTABLISHED_STATUS];
        if (in_array($status, $possibleStatus)) {
            $this->status = $status;
            return true;
        }
        return false;
    }


}
