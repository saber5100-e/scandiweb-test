<?php

namespace App\Models;
use App\Database\Database;
use App\Models\Attributes;
use App\Models\ProductModel;
use mysqli;

class Product extends ProductModel {
    public static function findById(string $id): ?self {
        $conn = Database::getConnection();

        $stmt = $conn->prepare("SELECT * FROM Products WHERE ID = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) return null;

        $row["Products_gallery"] = self::getGallery($id, $conn);
        $row["Products_Attributes"] = Attributes::getAttributes($id, $conn);
        $row["Product_Prices"] = self::getPrices($id, $conn);

        return new self($row);
    }

    public static function findAll(): array {
        $conn = Database::getConnection();
        $result = $conn->query("SELECT * FROM Products");

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row["ID"];
            $row["Products_gallery"] = self::getGallery($id, $conn);
            $row["Products_Attributes"] = Attributes::getAttributes($id, $conn);
            $row["Product_Prices"] = self::getPrices($id, $conn);
            $products[] = (new self($row))->toArray();
        }

        return $products;
    }

    public static function findByCategory(string $category): array {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM Products WHERE Category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row["ID"];
            $row["Products_gallery"] = self::getGallery($id, $conn);
            $row["Products_Attributes"] = Attributes::getAttributes($id, $conn);
            $row["Product_Prices"] = self::getPrices($id, $conn);
            $products[] = (new self($row))->toArray();
        }

        $stmt->close();
        return $products;
    }

    protected static function getGallery(string $productId, mysqli $conn): array {
        $stmt = $conn->prepare("SELECT * FROM Products_gallery WHERE Product_ID = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $gallery = [];
        while ($row = $result->fetch_assoc()) {
            $gallery[] = $row;
        }

        $stmt->close();
        return $gallery;
    }

    protected static function getPrices(string $productId, mysqli $conn): array {
        $stmt = $conn->prepare("SELECT * FROM Product_Prices WHERE Product_ID = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }

        $stmt->close();
        return $prices;
    }

    public function toArray(): array
    {
        return [
            'ID' => $this->id,
            'Product_Name' => $this->productName,
            'In_Stock' => $this->in_stock,
            'Description' => $this->description,
            'Category' => $this->category,
            'Brand' => $this->brand,
            'Products_gallery' => $this->gallery,
            'Products_Attributes' => $this->attributes,
            'Product_Prices' => $this->prices,
            '__typename' => 'Product',
        ];
    }
}