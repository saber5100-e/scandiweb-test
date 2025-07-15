<?php

namespace App\Models;

use App\Database\Database;
use mysqli;

class Product extends ProductModel
{
    public static function findAll(): array
    {
        return array_map([ProductFactory::class, 'create'], self::getRawRows());
    }

    public static function findByCategory(string $category): array
    {
        return array_map([ProductFactory::class, 'create'], self::getRawRows('WHERE category = ?', [$category]));
    }

    public static function findById(string $id): ?ProductModel
    {
        $rows = self::getRawRows('WHERE ID = ?', [$id]);
        return count($rows) ? ProductFactory::create($rows[0]) : null;
    }

    public static function rawFindAll(): array
    {
        return self::getRawRows();
    }

    public static function rawFindByCategory(string $category): array
    {
        return self::getRawRows('WHERE category = ?', [$category]);
    }

    private static function getRawRows(string $where = '', array $params = []): array
    {
        $conn = Database::getConnection();
        $sql = "SELECT * FROM products $where";
        $stmt = $conn->prepare($sql);

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $row['products_gallery'] = self::getGallery($id, $conn);
            $row['products_attributes'] = array_map(
                fn($attr) => $attr->toArray(),
                AttributeFactory::getByProduct($row['category'], $id, $conn)
            );
            $row['product_prices'] = self::getPrices($id, $conn);

            foreach ($row['product_prices'] as &$price) {
                $currencyData = self::getCurrencies($price["currency_id"], $conn);
                $price["currency"] = $currencyData[0] ?? null;
            }
            unset($price);

            $rows[] = $row;
        }

        $stmt->close();
        return $rows;
    }

    private static function getGallery(string $productId, mysqli $conn): array
    {
        $stmt = $conn->prepare("SELECT * FROM products_gallery WHERE product_id = ?");
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

    private static function getPrices(string $productId, mysqli $conn): array
    {
        $stmt = $conn->prepare("SELECT id, amount, currency_id, __typename FROM product_prices WHERE product_id = ?");
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

    private static function getCurrencies(int $currencyId, mysqli $conn): array
    {
        $stmt = $conn->prepare("SELECT * FROM products_currnecy WHERE id = ?");
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->productName,
            'in_stock' => $this->inStock,
            'description' => $this->description,
            'category' => $this->category,
            'brand' => $this->brand,
            'products_gallery' => $this->gallery,
            'products_attributes' => $this->attributes,
            'product_prices' => $this->prices,
            '__typename' => $this->typeName,
        ];
    }
}
