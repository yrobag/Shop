<?php

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\TestCase;
use Shop\Exceptions\NotEnoughProductQtyException;
use Shop\Product;

class ProductTest extends TestCase
{
    protected function getConnection()
    {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        return new DefaultConnection($pdo, DB_NAME);
    }

    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('tests/Shop.xml');
    }
    
    public function testCreateProduct()
    {
        $product = new Product();
        $this->assertInstanceOf(Product::class, $product, 'Product should be instance of class Product.');
    }
    
    public function testSettersAndGetters(
            $name = 'Sample Product A', 
            $description = 'Incredible quality product at a reasonable price',
            $price = 9.99)
    {
        $product = new Product();
        
        $product->setName($name);
        $this->assertSame($name, $product->getName(), 'Name should be identical to ' . $name);
        
        $product->setDescription($description);
        $this->assertSame($description, $product->getDescription(), 'Description should be identical to ' . $description);
        
        $product->setPrice($price);
        $this->assertSame($price, $product->getPrice(), 'Price should be identical to ' . $price);
    }
    
    public function testStockQuantityChange()
    {
        $product = new Product();
        
        // add to stock
        $qty1 = 3;
        $product->addQtyToStock($qty1);
        $this->assertSame($qty1, $product->getStockQty(), 'Quantity should be identical to ' . $qty1);
        
        // remove from stock - there is enough qty
        $qty2 = 3;
        $product->removeQtyFromStock($qty2);
        $this->assertSame($qty1 - $qty2, $product->getStockQty(), 'Quantity should be identical to ' . ($qty1 - $qty2));
        
        // remove from stock - there isn't enough qty
        $this->expectException(NotEnoughProductQtyException::class);
        
        $qty3 = 2;
        $product->removeQtyFromStock($qty3);
    }
    
    public function testSavingToDb()
    {
        $product = new Product();
        
        $product->saveToDB();
        
        $id = 1;
        $product = (new Product)->findById($id);
        // Must be assertEquals because findById set ID as string, not integer
        $this->assertEquals($id, $product->getId(), 'ID should be equal to ' . $id);
    }
}
