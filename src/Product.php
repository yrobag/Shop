<?php

namespace Shop;

use Shop\Exceptions\NotEnoughProductQtyException;

class Product extends DbModel
{

    /**
     * Stała określająca ID obiektu nieistniejącego w bazie.
     */
    const NON_EXISTING_ID = -1;

    /**
     *
     * @var int ID przedmiotu
     */
    public $id;

    /**
     *
     * @var string Nazwa przedmiotu
     */
    public $name;

    /**
     *
     * @var string Opis przedmiotu
     */
    public $description;

    /**
     *
     * @var float Cena przedmiotu
     */
    public $price;

    /**
     *
     * @var int Ilość przedmiotu w magazynie
     */
    public $stock;

    //////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->id = self::NON_EXISTING_ID;
        $this->name = (string) '';
        $this->description = (string) '';
        $this->price = (float) 0.00;
        $this->stock = (int) 0;
    }

    //////////////////////////////////////////////////////////////

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getStockQty()
    {
        return $this->stock;
    }

    //////////////////////////////////////////////////////////////

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    //////////////////////////////////////////////////////////////

    public function addQtyToStock($qty)
    {
        $this->stock += $qty;
        return $this;
    }

    public function removeQtyFromStock($qty)
    {
        if ($qty > $this->stock) {
            throw new NotEnoughProductQtyException('There is not enough products in stock.');
        } else {
            $this->stock -= $qty;
            return $this;
        }
    }

    public function addPicture($link)
    {
        
    }

    public function getAllPictures()
    {
        $pictures = [];

        return $pictures;
    }
    
    public function saveToDB()
    {
        $conn = Product::getConnection();
        
        if ($this->id == self::NON_EXISTING_ID) {
            //Saving new product to DB
            $stmt = $conn->prepare(
                    'INSERT INTO Product(name, description, price, stock) VALUES (:name, :description, :price, :stock)'
            );

            $result = $stmt->execute(
                    [
                        'name' => $this->name, 
                        'description' => $this->description, 
                        'price' => $this->price,
                        'stock' => $this->stock
                    ]
            );

            if ($result === true) {
                $this->id = $conn->lastInsertId();

                return true;
            }
        } else {
            //Updating product in DB
            $stmt = $conn->prepare(
                    'UPDATE Product SET name=:name, description=:description, price=:price, stock=:stock WHERE id=:id'
            );

            $result = $stmt->execute(
                    [
                        'name' => $this->name, 
                        'description' => $this->description,
                        'price' => $this->price, 
                        'stock' => $this->stock, 
                        'id' => $this->id
                    ]
            );

            if ($result === true) {

                return true;
            }
        }

        return false;
    }

}
