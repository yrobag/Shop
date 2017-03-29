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
        $this->id = self::NON_EXISTING_ID;
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
                    $this->products[$i]['price'] = $product->price;
                    $this->products[$i]['quantity'] += $quantity;
                    return true;
                }
            }
        }
        $this->products[] = ['productId' => $product->id, 'price' => $product->price, 'quantity' => $quantity];
        return true;
    }

    public function removeProduct(Product $product)
    {
        if (count($this->products) > 0) {
            for ($i = 0; $i < count($this->products); $i++) {
                if ($this->products[$i]['productId'] === $product->id) {
                    unset($this->products[$i]);
                    $this->products = array_values($this->products);
                    $this->calculateTotalPrice();
                    return true;
                }
            }
        }
        return false;
    }

    public function calculateTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }
        $this->totalPrice = $totalPrice;
        return $totalPrice;
    }

    public function saveToDB()
    {
        $conn = Product::getConnection();

        if ($this->id == self::NON_EXISTING_ID) {
            //Saving new order to DB
            $stmt = $conn->prepare(
                    'INSERT INTO Order(userId, totalPrice, status) VALUES (:userId, :totalPrice, :status)'
            );

            $result = $stmt->execute(
                    [
                        'userId' => $this->userId,
                        'totalPrice' => $this->calculateTotalPrice(),
                        'status' => self::ESTABLISHED_STATUS
                    ]
            );
            //Creating new records in OrderProduct Table
            $stmt = $conn->prepare(
                    'INSERT INTO OrderProduct(productId, price, quantity, orderId) VALUES (:productId, :price, :quantity, :orderId)'
            );
            foreach ($this->products as $product) {
                $stmt->execute(
                        [
                            'productId' => $product['productId'],
                            'price' => $product['price'],
                            'quantity' => $product['quantity'],
                            'orderId' => $this->id
                        ]
                );
            }

            if ($result === true) {
                $this->id = $conn->lastInsertId();
                return true;
            }
        }
        return false;
    }

    public function loadAllProducts()
    {
        $conn = Product::getConnection();
        $stmt = $conn->prepare(
                'SELECT * FROM OrderProduct WHERE orderId=:id'
        );

        $stmt->execute(['id' => $this->id]);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($products as $product){
            $this->products[]=['productId' => $product['productId'], 'price'=>$product['price'], 'quantity'=>$product['quantity']];
        }
    }

    ////////////////////////////////////////////////////////////////////////////


    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function changeStatus($status)
    {
        if ($this->id != self::NON_EXISTING_ID) {

            $possibleStatus = [self::WAITING_STATUS, self::REALIZED_STATUS, self::PAID_STATUS, self::ESTABLISHED_STATUS];
            if (in_array($status, $possibleStatus)) {
                $this->status = $status;
                $conn = Product::getConnection();
                $stmt = $conn->prepare(
                        'UPDATE Order SET status=:status WHERE id=:id'
                );

                $result = $stmt->execute(
                        [
                            'status' => $this->status,
                            'id' => $this->id
                        ]
                );
                if ($result === true) {
                    $this->id = $conn->lastInsertId();
                    return true;
                }
            }
        }
        return false;
    }

}
