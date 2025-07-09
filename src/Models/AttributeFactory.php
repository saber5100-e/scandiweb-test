<?php

namespace App\Models;

use mysqli;

class AttributeFactory {
    private static array $map = [
        'tech' => TechAttribute::class,
        'clothes' => ClothingAttribute::class,
        'default' => Attributes::class,
    ];

    public static function getByProduct(string $category, string $productId, mysqli $conn): array {
        $categoryKey = strtolower($category);
        $class = self::$map[$categoryKey] ?? self::$map['default'];
        return $class::getAttributes($productId, $conn);
    }
}