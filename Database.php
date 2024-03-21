<?php

class Database {
    private $pdo;

    public function __construct($dsn, $username, $password, $options = [])
    {
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function selectProduct($sku)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE sku = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStock($sku, $newStock): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = :stock WHERE sku = :sku");
        $stmt->bindParam(':stock', $newStock);
        $stmt->bindParam(':sku', $sku);
        return $stmt->execute();
    }

    public function insertProduct($sku, $ean, $name, $shortDesc, $manufacturer, $price, $stock): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (sku, ean, name, shortDesc, manufacturer, price, stock) VALUES (:sku, :ean, :name, :shortDesc, :manufacturer, :price, :stock)");
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':ean', $ean);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shortDesc', $shortDesc);
        $stmt->bindParam(':manufacturer', $manufacturer);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        return $stmt->execute();
    }
}