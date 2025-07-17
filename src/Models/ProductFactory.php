<?php

namespace App\Models;

class ProductFactory
{
    private static array $map = [
        'Tech' => TechProduct::class,
        'Clothes' => ClothingProduct::class,
    ];
    public static function create(array $row): ProductModel
    {
        $type = $row['category'] ?? '';
        $class = self::$map[$type] ?? Product::class;
        return new $class($row);
    }
    public static function findByCategoryOrAll(?string $category): array
    {
        $rows = (!$category || strtolower($category) === 'all')
            ? Product::rawFindAll()
            : Product::rawFindByCategory($category);
        return array_map([self::class, 'create'], $rows);
    }
}
