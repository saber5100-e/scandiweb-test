<?php

namespace App\Models;

use mysqli;

class ClothingAttribute extends AttributesModel {
    public static function getAttributes(string $productId, mysqli $conn): array {
        $raw = Attributes::fetchRawAttributes($productId, $conn);
        return array_map(fn($row) => new self($row), $raw);
    }
}