<?php

namespace App\Models;

use App\Database\Database;
use mysqli;

class Product extends ProductModel {
    public static function findAll(): array {
        return array_map([ProductFactory::class, 'create'], self::getRawRows());
    }

    public static function findByCategory(string $category): array {
        return array_map([ProductFactory::class, 'create'], self::getRawRows('WHERE Category = ?', [$category]));
    }

    public static function findById(string $id): ?ProductModel {
        $rows = self::getRawRows('WHERE ID = ?', [$id]);
        return count($rows) ? ProductFactory::create($rows[0]) : null;
    }

    public static function rawFindAll(): array {
        return self::getRawRows();
    }

    public static function rawFindByCategory(string $category): array {
        return self::getRawRows('WHERE Category = ?', [$category]);
    }

    private static function getRawRows(string $where = '', array $params = []): array {
        $conn = Database::getConnection();
        $sql = "SELECT * FROM Products $where";
        $stmt = $conn->prepare($sql);

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row['ID'];
            $row['Products_gallery'] = self::getGallery($id, $conn);
            $row['Products_Attributes'] = array_map(
                fn($attr) => $attr->toArray(),
                AttributeFactory::getByProduct($row['Category'], $id, $conn)
            );
            $row['Product_Prices'] = self::getPrices($id, $conn);

            foreach ($row['Product_Prices'] as &$price) {
                $currencyData = self::getCurrencies($price["Currency_ID"], $conn);
                $price["Currency"] = $currencyData[0] ?? null;
            }
            unset($price);

            $rows[] = $row;
        }

        $stmt->close();
        return $rows;
    }

    private static function getGallery(string $productId, mysqli $conn): array {
        $stmt = $conn->prepare("SELECT * FROM Products_gallery WHERE Product_ID = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $gallery = [];
        while ($row = $result->fetch_assoc()) {
            unset($row["Product_ID"]);
            $gallery[] = $row;
        }

        $stmt->close();
        return $gallery;
    }

    private static function getPrices(string $productId, mysqli $conn): array {
        $stmt = $conn->prepare("SELECT ID, Amount, Currency_ID, __typename FROM Product_Prices WHERE Product_ID = ?");
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

    private static function getCurrencies(int $currencyId, mysqli $conn): array {
        $stmt = $conn->prepare("SELECT * FROM products_currnecy WHERE ID = ?");
        $stmt->bind_param("i", $currencyId);
        $stmt->execute();
        $result = $stmt->get_result();

        $currency = [];
        while ($row = $result->fetch_assoc()) {
            $currency[] = $row;
        }

        $stmt->close();
        return $currency;
    }

    public function toArray(): array {
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
            '__typename' => $this->__typename,
        ];
    }
}