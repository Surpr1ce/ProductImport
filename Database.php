<?php

class Database {
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password, $options = [])
    {
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function selectProduct(string $sku): bool | array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE sku = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStock(string $sku, int $newStock): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = :stock WHERE sku = :sku");
        $stmt->bindParam(':stock', $newStock);
        $stmt->bindParam(':sku', $sku);
        return $stmt->execute();
    }

    public function insertProduct(string $sku, string $ean, string $name, string $shortDesc, string $manufacturer, float $price, int $stock): bool
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